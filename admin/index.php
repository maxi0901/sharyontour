<?php
$pageTitle = 'S-ART Admin';
$adminPage = 'dashboard';
require __DIR__ . '/_header.php';

$opening = getOpeningEvent();
$ticketsTotal = $opening ? countTicketsForEvent((int) $opening['id']) : 0;
$maxTickets = $opening ? (int) $opening['max_tickets'] : 600;

$counts = [
    'Events'      => (int) (fetchOne('SELECT COUNT(*) c FROM events')['c'] ?? 0),
    'Tickets'     => (int) (fetchOne('SELECT COUNT(*) c FROM tickets WHERE status="active"')['c'] ?? 0),
    'Galerie-Bilder' => (int) (fetchOne('SELECT COUNT(*) c FROM galleries')['c'] ?? 0),
    'Newsletter'  => (int) (fetchOne('SELECT COUNT(*) c FROM newsletter_subscribers')['c'] ?? 0),
];
?>

<h1>Dashboard</h1>

<?php if ($opening): ?>
<div class="admin-card admin-highlight">
  <p class="kicker">CONTAINER OPENING KASSEL</p>
  <h2><?= $ticketsTotal ?> / <?= $maxTickets ?> Tickets</h2>
  <div class="admin-progress">
    <span style="width: <?= min(100, ($ticketsTotal / max($maxTickets, 1)) * 100) ?>%"></span>
  </div>
  <p class="muted"><?= max(0, $maxTickets - $ticketsTotal) ?> Tickets verbleibend</p>
</div>
<?php endif; ?>

<div class="admin-grid">
  <?php foreach ($counts as $k => $v): ?>
    <div class="admin-card">
      <p class="kicker"><?= e($k) ?></p>
      <h2><?= $v ?></h2>
    </div>
  <?php endforeach; ?>
</div>

<?php require __DIR__ . '/_footer.php'; ?>
