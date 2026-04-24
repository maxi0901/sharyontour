<?php
$pageTitle = 'Digitales Ticket';
require __DIR__ . '/includes/header.php';
$token = trim((string) ($_GET['token'] ?? ''));
$ticket = null;
if ($token !== '') {
    $ticket = fetchOne('SELECT * FROM newsletter_subscribers WHERE ticket_token=:token LIMIT 1', ['token' => $token]);
}
?>
<section class="container page-intro reveal">
  <h1>Dein S-ART Ticket</h1>
  <?php if (!empty($_GET['msg'])): ?><p class="flash"><?= e((string) $_GET['msg']) ?></p><?php endif; ?>
  <?php if ($ticket): ?>
    <article class="ticket-card">
      <p class="meta">Ticket-ID: <?= e($ticket['ticket_token']) ?></p>
      <h2><?= e($ticket['first_name'] ?: $ticket['email']) ?></h2>
      <p>Kein Kauf erforderlich.</p>
      <p>Dieses Ticket wurde automatisch nach Newsletter-Anmeldung erstellt.</p>
      <p>Zeige dieses Ticket bei Tour-Events vor.</p>
      <a class="btn btn-blue" href="/index.php">Zurück zur Startseite</a>
    </article>
  <?php else: ?>
    <p>Ticket nicht gefunden.</p>
  <?php endif; ?>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
