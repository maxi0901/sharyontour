<?php

declare(strict_types=1);

require __DIR__ . '/config/bootstrap.php';
require __DIR__ . '/includes/newsletter-log.php';

$token = isset($_GET['token']) ? trim((string) $_GET['token']) : '';
$status = 'invalid';

if ($token !== '' && hasColumn('newsletter_subscribers', 'confirm_token')) {
    $row = fetchOne(
        "SELECT id, status FROM newsletter_subscribers WHERE confirm_token = :t LIMIT 1",
        ['t' => $token]
    );

    if ($row) {
        if (($row['status'] ?? '') === 'confirmed') {
            $status = 'already';
            logNewsletterEvent('confirm_already', ['subscriber_id' => (int) $row['id']]);
        } else {
            try {
                $stmt = $pdo->prepare(
                    "UPDATE newsletter_subscribers
                        SET status = 'confirmed',
                            confirmed_at = NOW(),
                            confirm_token = NULL
                      WHERE id = :id"
                );
                $stmt->execute(['id' => (int) $row['id']]);
                $status = 'ok';
                logNewsletterEvent('confirm_ok', ['subscriber_id' => (int) $row['id']]);
            } catch (Throwable $e) {
                logNewsletterEvent('confirm_db_error', ['subscriber_id' => (int) $row['id'], 'error' => $e->getMessage()]);
                $status = 'error';
            }
        }
    } else {
        logNewsletterEvent('confirm_invalid_token', []);
    }
}

$pageTitle = 'Newsletter bestätigen · S-ART';
require __DIR__ . '/includes/header.php';
?>

<section class="section container page-intro reveal">
  <p class="kicker">SHARY ON TOUR · NEWSLETTER</p>
  <?php if ($status === 'ok'): ?>
    <h1>ANMELDUNG <span class="text-green">BESTÄTIGT</span></h1>
    <p class="subline">Danke! Du bist jetzt für den Newsletter angemeldet und wirst über alle Tour-Stopps und Drops informiert.</p>
  <?php elseif ($status === 'already'): ?>
    <h1>SCHON <span class="text-green">BESTÄTIGT</span></h1>
    <p class="subline">Diese Adresse ist bereits aktiv für den Newsletter eingetragen – nichts weiter zu tun.</p>
  <?php elseif ($status === 'error'): ?>
    <h1>TECHNISCHES <span class="text-red">PROBLEM</span></h1>
    <p class="subline">Wir konnten deine Bestätigung gerade nicht speichern. Bitte versuche den Link später erneut.</p>
  <?php else: ?>
    <h1>LINK <span class="text-red">UNGÜLTIG</span></h1>
    <p class="subline">Der Bestätigungslink ist abgelaufen oder wurde bereits genutzt. Melde dich auf der Startseite einfach erneut für den Newsletter an.</p>
  <?php endif; ?>

  <p style="margin-top:1.5rem;"><a class="btn btn-primary" href="/index.php#newsletter">Zur Startseite →</a></p>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
