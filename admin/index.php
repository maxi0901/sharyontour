<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/functions.php';
require_admin_bootstrap();

$counts = [
    'events' => (int) db()->query('SELECT COUNT(*) FROM events')->fetchColumn(),
    'artworks' => (int) db()->query('SELECT COUNT(*) FROM artworks')->fetchColumn(),
    'locations' => (int) db()->query('SELECT COUNT(*) FROM tour_locations')->fetchColumn(),
    'subscribers' => (int) db()->query('SELECT COUNT(*) FROM newsletter_subscribers')->fetchColumn(),
];
?>
<!doctype html>
<html lang="de">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Admin Dashboard</title>
  <link rel="stylesheet" href="/assets/css/style.css" />
</head>
<body>
<div class="admin-layout">
  <aside class="admin-nav">
    <h2>S-Art Admin</h2>
    <a href="/admin/index.php">Dashboard</a>
    <a href="/admin/events.php">Events</a>
    <a href="/admin/artworks.php">Kunstwerke</a>
    <a href="/admin/locations.php">Standorte</a>
    <a href="/admin/subscribers.php">Subscriber</a>
    <a href="/index.php">Zur Website</a>
  </aside>
  <main class="admin-main">
    <h1 class="section-title">Dashboard</h1>
    <div class="grid events">
      <article class="card"><h3>Events</h3><p><?= e((string) $counts['events']) ?></p></article>
      <article class="card"><h3>Kunstwerke</h3><p><?= e((string) $counts['artworks']) ?></p></article>
      <article class="card"><h3>Standorte</h3><p><?= e((string) $counts['locations']) ?></p></article>
      <article class="card"><h3>Subscriber</h3><p><?= e((string) $counts['subscribers']) ?></p></article>
    </div>
  </main>
</div>
</body>
</html>
