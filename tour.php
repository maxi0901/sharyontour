<?php
$pageTitle = 'Tour';
require __DIR__ . '/includes/header.php';
$current = fetchOne("SELECT * FROM tour_locations WHERE status='current' ORDER BY date_from DESC LIMIT 1");
$upcoming = fetchAll("SELECT * FROM tour_locations WHERE status='upcoming' ORDER BY date_from ASC");
$past = fetchAll("SELECT * FROM tour_locations WHERE status='past' ORDER BY date_from DESC");
?>
<section class="container page-intro reveal"><h1>Tour-Standorte</h1></section>
<section class="container reveal">
  <h2>Jetzt</h2>
  <?php if ($current): ?><div class="highlight-card"><h3><?= e($current['title']) ?> · <?= e($current['city']) ?></h3><p><?= e($current['address']) ?></p></div><?php else: ?><p>Kein aktueller Standort veröffentlicht.</p><?php endif; ?>
</section>
<section class="container reveal">
  <h2>Kommende Stopps</h2>
  <div class="timeline"><?php foreach ($upcoming as $row): ?><article><strong><?= formatDate($row['date_from']) ?></strong><h3><?= e($row['title']) ?> · <?= e($row['city']) ?></h3><p><?= e($row['description'] ?? '') ?></p><?php if ($row['google_maps_url']): ?><a href="<?= e($row['google_maps_url']) ?>" target="_blank" rel="noopener">Maps ↗</a><?php endif; ?></article><?php endforeach; ?></div>
</section>
<section class="container reveal">
  <h2>Vergangene Stopps</h2>
  <div class="timeline"><?php foreach ($past as $row): ?><article><strong><?= formatDate($row['date_from']) ?></strong><h3><?= e($row['title']) ?> · <?= e($row['city']) ?></h3></article><?php endforeach; ?></div>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
