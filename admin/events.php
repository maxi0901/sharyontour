<?php
require __DIR__ . '/../includes/functions.php';
$status = $_GET['status'] ?? '';
$sql = 'SELECT * FROM events';
$params = [];
if (in_array($status, ['upcoming', 'past', 'draft'], true)) {
    $sql .= ' WHERE status=:status';
    $params['status'] = $status;
}
$sql .= ' ORDER BY event_date DESC';
$rows = fetchAll($sql, $params);
?><!doctype html><html lang="de"><head><meta charset="utf-8"><link rel="stylesheet" href="/assets/css/style.css"><title>Admin Events</title></head><body class="admin"><div class="container"><h1>Events</h1><a class="btn" href="/admin/event-edit.php">+ Neu</a><form method="get"><select name="status"><option value="">Alle</option><option value="upcoming">upcoming</option><option value="past">past</option><option value="draft">draft</option></select><button>Filtern</button></form><table class="admin-table"><tr><th>Datum</th><th>Titel</th><th>Status</th><th></th></tr><?php foreach($rows as $r): ?><tr><td><?= e($r['event_date']) ?></td><td><?= e($r['title']) ?></td><td><?= e($r['status']) ?></td><td><a href="/admin/event-edit.php?id=<?= (int)$r['id'] ?>">Bearbeiten</a></td></tr><?php endforeach; ?></table></div></body></html>
