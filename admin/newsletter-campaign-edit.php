<?php
require_once __DIR__ . '/auth.php';
requireAdminLogin();
require __DIR__ . '/../config/bootstrap.php';
require __DIR__ . '/../includes/csrf.php';
require __DIR__ . '/../includes/newsletter-log.php';
require __DIR__ . '/../config/mail.php';

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$campaign = [
    'id' => 0,
    'subject' => '',
    'body_html' => '',
    'body_text' => '',
    'status' => 'draft',
    'recipients_total' => 0,
    'sent_count' => 0,
    'failed_count' => 0,
    'created_at' => null,
    'sent_at' => null,
];

if ($id) {
    $loaded = fetchOne('SELECT * FROM newsletter_campaigns WHERE id=:id', ['id' => $id]);
    if ($loaded) {
        $campaign = array_merge($campaign, $loaded);
    } else {
        header('Location: /admin/newsletter-campaigns.php');
        exit;
    }
}

$flash = null;
$flashType = 'success';
$sendReport = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrf($_POST['csrf_token'] ?? null)) {
        $flash = 'CSRF-Token ungültig. Bitte Seite neu laden.';
        $flashType = 'error';
    } else {
        $action = $_POST['action'] ?? 'save';
        $subject = trim((string) ($_POST['subject'] ?? ''));
        $bodyHtml = (string) ($_POST['body_html'] ?? '');
        $bodyText = trim((string) ($_POST['body_text'] ?? ''));
        $bodyTextStored = $bodyText !== '' ? $bodyText : null;

        if ($action === 'save' || $action === 'test' || $action === 'send') {
                if ($id) {
                    $stmt = $pdo->prepare(
                        'UPDATE newsletter_campaigns
                            SET subject=:s, body_html=:h, body_text=:t
                          WHERE id=:id'
                    );
                    $stmt->execute(['s' => $subject, 'h' => $bodyHtml, 't' => $bodyTextStored, 'id' => $id]);
                } else {
                    $stmt = $pdo->prepare(
                        'INSERT INTO newsletter_campaigns (subject, body_html, body_text, status)
                         VALUES (:s, :h, :t, "draft")'
                    );
                    $stmt->execute(['s' => $subject, 'h' => $bodyHtml, 't' => $bodyTextStored]);
                    $id = (int) $pdo->lastInsertId();
                }

                $campaign = fetchOne('SELECT * FROM newsletter_campaigns WHERE id=:id', ['id' => $id]) ?: $campaign;

                if ($action === 'save') {
                    $flash = 'Kampagne gespeichert.';
                    $flashType = 'success';
                } elseif ($action === 'test') {
                    $testEmail = strtolower(trim((string) ($_POST['test_email'] ?? '')));
                    if (!filter_var($testEmail, FILTER_VALIDATE_EMAIL)) {
                        $flash = 'Bitte eine gültige Test-E-Mail-Adresse angeben.';
                        $flashType = 'error';
                    } else {
                        $testToken = bin2hex(random_bytes(32));
                        $result = sendNewsletterCampaignMail(
                            $testEmail,
                            '[TEST] ' . $subject,
                            $bodyHtml,
                            $bodyTextStored,
                            $testToken
                        );
                        if ($result['success']) {
                            $flash = 'Testmail an ' . $testEmail . ' verschickt.';
                            $flashType = 'success';
                            logNewsletterEvent('campaign_test_sent', ['campaign_id' => $id, 'email' => $testEmail]);
                        } else {
                            $flash = 'Testmail fehlgeschlagen: ' . ($result['error'] ?? 'Unbekannter Fehler');
                            $flashType = 'error';
                            logNewsletterEvent('campaign_test_failed', [
                                'campaign_id' => $id,
                                'email' => $testEmail,
                                'error' => $result['error'] ?? 'unknown',
                            ]);
                        }
                    }
                } elseif ($action === 'send') {
                    $confirm = $_POST['confirm_send'] ?? '';
                    if ($confirm !== 'SENDEN') {
                        $flash = 'Sicherheits-Bestätigung fehlt. Bitte tippe "SENDEN" in das Bestätigungsfeld, um den Versand auszulösen.';
                        $flashType = 'error';
                    } elseif (!hasColumn('newsletter_subscribers', 'status')) {
                        $flash = 'Datenbank-Migration noch nicht abgeschlossen – status-Spalte fehlt. Bitte Seite einmal neu laden.';
                        $flashType = 'error';
                    } elseif (in_array(($campaign['status'] ?? 'draft'), ['sent', 'sending'], true)) {
                        $flash = 'Diese Kampagne wurde bereits versendet bzw. läuft aktuell.';
                        $flashType = 'error';
                    } else {
                        logNewsletterEvent('campaign_send_start', ['campaign_id' => $id, 'subject' => $subject]);
                        $sendReport = sendNewsletterCampaign($pdo, $id, $subject, $bodyHtml, $bodyTextStored);
                        $campaign = fetchOne('SELECT * FROM newsletter_campaigns WHERE id=:id', ['id' => $id]) ?: $campaign;
                        $flash = 'Versand abgeschlossen: ' . $sendReport['sent'] . ' gesendet, ' . $sendReport['failed'] . ' fehlgeschlagen.';
                        $flashType = $sendReport['failed'] > 0 ? 'error' : 'success';
                        logNewsletterEvent('campaign_send_done', [
                            'campaign_id' => $id,
                            'total' => $sendReport['total'],
                            'sent' => $sendReport['sent'],
                            'failed' => $sendReport['failed'],
                        ]);
                    }
            }
        }
    }
}

/**
 * Iterate all confirmed subscribers and send the campaign mail to each
 * one individually (no CC/BCC). Logs every recipient to newsletter_send_log
 * and updates campaign counters at the end.
 */
function sendNewsletterCampaign(PDO $pdo, int $campaignId, string $subject, string $bodyHtml, ?string $bodyText): array
{
    @set_time_limit(0);

    $subscribers = fetchAll(
        "SELECT id, email, unsubscribe_token
           FROM newsletter_subscribers
          WHERE status = 'confirmed'
            AND email IS NOT NULL
            AND email != ''"
    );

    $total = count($subscribers);
    $pdo->prepare(
        "UPDATE newsletter_campaigns
            SET status='sending', recipients_total=:t, sent_count=0, failed_count=0
          WHERE id=:id"
    )->execute(['t' => $total, 'id' => $campaignId]);

    $sent = 0;
    $failed = 0;
    $failures = [];

    $logStmt = $pdo->prepare(
        'INSERT INTO newsletter_send_log (campaign_id, subscriber_id, email, status, error_message)
         VALUES (:c, :s, :e, :st, :err)'
    );

    foreach ($subscribers as $sub) {
        $token = $sub['unsubscribe_token'];
        if ($token === null || $token === '') {
            $token = bin2hex(random_bytes(32));
            $pdo->prepare('UPDATE newsletter_subscribers SET unsubscribe_token=:t WHERE id=:id')
                ->execute(['t' => $token, 'id' => (int) $sub['id']]);
        }

        $result = sendNewsletterCampaignMail(
            (string) $sub['email'],
            $subject,
            $bodyHtml,
            $bodyText,
            (string) $token
        );

        if ($result['success']) {
            $sent++;
            $logStmt->execute([
                'c' => $campaignId,
                's' => (int) $sub['id'],
                'e' => $sub['email'],
                'st' => 'sent',
                'err' => null,
            ]);
        } else {
            $failed++;
            $err = $result['error'] ?? 'unknown';
            $failures[] = ['email' => $sub['email'], 'error' => $err];
            $logStmt->execute([
                'c' => $campaignId,
                's' => (int) $sub['id'],
                'e' => $sub['email'],
                'st' => 'failed',
                'err' => $err,
            ]);
            logNewsletterEvent('campaign_recipient_failed', [
                'campaign_id' => $campaignId,
                'subscriber_id' => (int) $sub['id'],
                'email' => $sub['email'],
                'error' => $err,
            ]);
        }
    }

    $finalStatus = ($total > 0 && $sent === 0) ? 'failed' : 'sent';
    $pdo->prepare(
        "UPDATE newsletter_campaigns
            SET status=:st, sent_count=:s, failed_count=:f, sent_at=NOW()
          WHERE id=:id"
    )->execute(['st' => $finalStatus, 's' => $sent, 'f' => $failed, 'id' => $campaignId]);

    return ['sent' => $sent, 'failed' => $failed, 'total' => $total, 'failures' => $failures];
}

$confirmedCount = (int) (fetchOne("SELECT COUNT(*) AS c FROM newsletter_subscribers WHERE status='confirmed'")['c'] ?? 0);

$logRows = [];
if ($id) {
    $logRows = fetchAll(
        'SELECT * FROM newsletter_send_log WHERE campaign_id=:c ORDER BY id DESC LIMIT 100',
        ['c' => $id]
    );
}

$pageTitle = ($id ? 'Kampagne bearbeiten' : 'Neue Kampagne') . ' · Admin';
$adminPage = 'newsletter-campaigns';
require __DIR__ . '/_header.php';
?>

<div class="admin-page-head">
  <h1><?= $id ? 'Kampagne bearbeiten' : 'Neue Kampagne' ?></h1>
  <a class="text-link" href="/admin/newsletter-campaigns.php">← Zurück zur Liste</a>
</div>

<?php if ($flash): ?>
  <div class="form-flash form-flash-<?= e($flashType) ?>"><?= e($flash) ?></div>
<?php endif; ?>

<?php if ($sendReport && !empty($sendReport['failures'])): ?>
  <details class="admin-card" style="margin-bottom:1rem;">
    <summary><?= count($sendReport['failures']) ?> Fehlermeldungen anzeigen</summary>
    <ul class="muted" style="margin-top:.6rem;font-size:.85rem;">
      <?php foreach ($sendReport['failures'] as $f): ?>
        <li><?= e($f['email']) ?> – <?= e($f['error']) ?></li>
      <?php endforeach; ?>
    </ul>
  </details>
<?php endif; ?>

<form method="post" class="admin-form">
  <?= csrfField() ?>
  <input type="hidden" name="action" value="save" id="campaignAction">

  <label class="field">
    <span>Betreff</span>
    <input name="subject" maxlength="255" value="<?= e($campaign['subject']) ?>">
  </label>

  <label class="field">
    <span>HTML-Inhalt</span>
    <textarea name="body_html" rows="14"><?= e($campaign['body_html']) ?></textarea>
  </label>

  <label class="field">
    <span>Text-Version (optional, sonst wird HTML stripped)</span>
    <textarea name="body_text" rows="6"><?= e((string) ($campaign['body_text'] ?? '')) ?></textarea>
  </label>

  <p class="muted" style="font-size:.82rem;">Der Abmelde-Link wird beim Versand automatisch unter dem Inhalt eingefügt.</p>

  <div class="campaign-actions">
    <button class="btn btn-ghost btn-sm" type="submit" onclick="document.getElementById('campaignAction').value='save';return true;">Entwurf speichern</button>
  </div>

  <fieldset class="campaign-fieldset">
    <legend>Testmail</legend>
    <div class="field-row">
      <label class="field field-grow">
        <span>Test-E-Mail-Adresse</span>
        <input type="email" name="test_email" placeholder="dein@admin-mail.de">
      </label>
      <button class="btn btn-ghost btn-sm" type="submit" onclick="document.getElementById('campaignAction').value='test';return true;">Testmail senden</button>
    </div>
  </fieldset>

  <fieldset class="campaign-fieldset campaign-fieldset-danger">
    <legend>Versand an alle bestätigten Empfänger</legend>
    <p>Aktuell <strong><?= $confirmedCount ?></strong> bestätigte Abonnenten. Tippe zur Bestätigung das Wort <strong>SENDEN</strong> in das Feld und klicke auf den Button.</p>
    <div class="field-row">
      <label class="field field-grow">
        <span>Bestätigung (genau "SENDEN")</span>
        <input type="text" name="confirm_send" placeholder="SENDEN" autocomplete="off">
      </label>
      <button class="btn btn-primary btn-sm" type="submit"
              onclick="document.getElementById('campaignAction').value='send';return confirm('Newsletter jetzt an <?= $confirmedCount ?> bestätigte Empfänger senden?');">
        Newsletter jetzt senden →
      </button>
    </div>
  </fieldset>
</form>

<?php if ($id && $logRows): ?>
  <h2 style="margin-top:2rem;">Versandlog (letzte 100)</h2>
  <div class="admin-table-wrap">
    <table class="admin-table">
      <thead><tr><th>Zeit</th><th>E-Mail</th><th>Status</th><th>Fehler</th></tr></thead>
      <tbody>
        <?php foreach ($logRows as $l): ?>
          <tr>
            <td><?= e(substr((string) $l['sent_at'], 0, 19)) ?></td>
            <td><?= e($l['email']) ?></td>
            <td><span class="status-pill status-<?= e($l['status']) ?>"><?= e($l['status']) ?></span></td>
            <td class="muted"><?= e((string) ($l['error_message'] ?? '')) ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
<?php endif; ?>

<?php require __DIR__ . '/_footer.php'; ?>
