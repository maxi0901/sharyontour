<?php
require __DIR__ . '/../includes/functions.php';
$counts = [
  'events' => fetchOne('SELECT COUNT(*) c FROM events')['c'] ?? 0,
  'artworks' => fetchOne('SELECT COUNT(*) c FROM artworks')['c'] ?? 0,
  'locations' => fetchOne('SELECT COUNT(*) c FROM tour_locations')['c'] ?? 0,
  'subscribers' => fetchOne('SELECT COUNT(*) c FROM newsletter_subscribers')['c'] ?? 0,
];
?><!doctype html><html lang="de"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><link rel="stylesheet" href="/assets/css/style.css"><title>Admin</title></head><body class="admin"><div class="container"><h1>Admin Dashboard</h1><nav><a href="/admin/events.php">Events</a> · <a href="/admin/artworks.php">Kunstwerke</a> · <a href="/admin/locations.php">Standorte</a> · <a href="/admin/subscribers.php">Subscriber</a></nav><div class="card-grid"><?php foreach ($counts as $k => $v): ?><article class="card"><h3><?= e(ucfirst($k)) ?></h3><p><?= (int) $v ?></p></article><?php endforeach; ?></div></div></body></html>
