<?php

declare(strict_types=1);

require __DIR__ . '/config/bootstrap.php';
require __DIR__ . '/includes/newsletter-log.php';

$token = isset($_GET['token']) ? trim((string) $_GET['token']) : '';
$status = 'invalid';

if ($token !== '' && hasColumn('newsletter_subscribers', 'unsubscribe_token')) {
    $row = fetchOne(
        "SELECT id, status FROM newsletter_subscribers WHERE unsubscribe_token = :t LIMIT 1",
        ['t' => $token]
    );

    if ($row) {
        if (($row['status'] ?? '') === 'unsubscribed') {
            $status = 'already';
            logNewsletterEvent('unsubscribe_already', ['subscriber_id' => (int) $row['id']]);
        } else {
            try {
                $stmt = $pdo->prepare(
                    "UPDATE newsletter_subscribers
                        SET status = 'unsubscribed',
                            unsubscribed_at = NOW(),
                            confirm_token = NULL
                      WHERE id = :id"
                );
                $stmt->execute(['id' => (int) $row['id']]);
                $status = 'ok';
                logNewsletterEvent('unsubscribe_ok', ['subscriber_id' => (int) $row['id']]);
            } catch (Throwable $e) {
                logNewsletterEvent('unsubscribe_db_error', ['subscriber_id' => (int) $row['id'], 'error' => $e->getMessage()]);
                $status = 'error';
            }
        }
    } else {
        logNewsletterEvent('unsubscribe_invalid_token', []);
    }
}

$pageTitle = 'Newsletter abmelden · S-ART';
require __DIR__ . '/includes/header.php';
?>

<section class="section container page-intro reveal">
  <p class="kicker">SHARY ON TOUR · NEWSLETTER</p>
  <?php if ($status === 'ok'): ?>
    <h1>ABMELDUNG <span class="text-green">ERFOLGREICH</span></h1>
    <p class="subline">Du wurdest erfolgreich vom Newsletter abgemeldet. Schade, dass du gehst – du kannst dich jederzeit wieder anmelden.</p>
  <?php elseif ($status === 'already'): ?>
    <h1>BEREITS <span class="text-green">ABGEMELDET</span></h1>
    <p class="subline">Diese Adresse ist nicht mehr im Newsletter-Verteiler.</p>
  <?php elseif ($status === 'error'): ?>
    <h1>TECHNISCHES <span class="text-red">PROBLEM</span></h1>
    <p class="subline">Wir konnten deine Abmeldung gerade nicht speichern. Bitte versuche es später erneut.</p>
  <?php else: ?>
    <h1>LINK <span class="text-red">UNGÜLTIG</span></h1>
    <p class="subline">Der Abmeldelink ist ungültig oder bereits abgelaufen. Bitte nutze den Link aus der zuletzt erhaltenen Newsletter-Mail.</p>
  <?php endif; ?>

  <p style="margin-top:1.5rem;"><a class="btn btn-primary" href="/index.php#newsletter">Zur Startseite →</a></p>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
