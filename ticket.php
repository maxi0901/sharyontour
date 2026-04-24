<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/functions.php';

$token = trim((string) ($_GET['token'] ?? ''));
$ticket = null;
if ($token !== '') {
    $stmt = db()->prepare('SELECT * FROM newsletter_subscribers WHERE ticket_token = :token LIMIT 1');
    $stmt->execute(['token' => $token]);
    $ticket = $stmt->fetch();
}

$currentPage = '';
$pageTitle = 'Digitales Ticket | S-Art';
require __DIR__ . '/includes/header.php';
?>
<section class="section container reveal">
<?php if ($ticket): ?>
  <div class="ticket-wrap">
    <div class="ticket-head">
      <h1 class="section-title">S-Art Digital Ticket</h1>
      <div class="ticket-id">Ticket-ID: <?= e(substr($ticket['ticket_token'], 0, 12)) ?></div>
    </div>
    <p><strong>Gast:</strong> <?= e($ticket['first_name'] ?: $ticket['email']) ?></p>
    <p><strong>E-Mail:</strong> <?= e($ticket['email']) ?></p>
    <p><strong>Hinweis:</strong> Kein Kauf erforderlich. Dieses Ticket dient zur Registrierung für Tour-Updates und Eventzugang nach Verfügbarkeit.</p>
    <p><strong>Tour-Hinweis:</strong> Details zu kommenden Stops findest du auf der Tour-Seite.</p>
  </div>
<?php else: ?>
  <div class="alert alert-error">Ticket nicht gefunden oder ungültiger Token.</div>
<?php endif; ?>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
