<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/functions.php';
require_admin_bootstrap();

$subscribers = db()->query('SELECT * FROM newsletter_subscribers ORDER BY created_at DESC')->fetchAll();
$logs = db()->query('SELECT * FROM ticket_logs ORDER BY sent_at DESC LIMIT 100')->fetchAll();
?>
<!doctype html>
<html lang="de"><head><meta charset="UTF-8" /><meta name="viewport" content="width=device-width, initial-scale=1.0" /><title>Admin Subscriber</title><link rel="stylesheet" href="/assets/css/style.css" /></head>
<body><div class="admin-layout"><aside class="admin-nav"><h2>S-Art Admin</h2><a href="/admin/index.php">Dashboard</a><a href="/admin/events.php">Events</a><a href="/admin/artworks.php">Kunstwerke</a><a href="/admin/locations.php">Standorte</a><a href="/admin/subscribers.php">Subscriber</a></aside><main class="admin-main">
<h1 class="section-title">Newsletter Subscriber</h1>
<div class="table-wrap"><table><thead><tr><th>ID</th><th>E-Mail</th><th>Name</th><th>Quelle</th><th>Ticket</th><th>Versendet</th><th>Erstellt</th></tr></thead><tbody>
<?php foreach ($subscribers as $sub): ?>
<tr><td><?= e((string) $sub['id']) ?></td><td><?= e($sub['email']) ?></td><td><?= e($sub['first_name']) ?></td><td><?= e($sub['source']) ?></td><td><a href="/ticket.php?token=<?= e($sub['ticket_token']) ?>">Token</a></td><td><?= e($sub['ticket_sent_at']) ?></td><td><?= e($sub['created_at']) ?></td></tr>
<?php endforeach; ?>
</tbody></table></div>

<h2>Ticket Logs</h2>
<div class="table-wrap"><table><thead><tr><th>Zeit</th><th>E-Mail</th><th>Status</th><th>Fehler</th></tr></thead><tbody>
<?php foreach ($logs as $log): ?>
<tr><td><?= e($log['sent_at']) ?></td><td><?= e($log['email']) ?></td><td><?= e($log['status']) ?></td><td><?= e($log['error_message']) ?></td></tr>
<?php endforeach; ?>
</tbody></table></div>
</main></div></body></html>
