<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/functions.php';

$currentPage = 'artworks';
$pageTitle = 'Kunstwerke | S-Art';
$stmt = db()->query('SELECT * FROM artworks WHERE is_visible = 1 ORDER BY sort_order ASC, created_at DESC');
$artworks = $stmt->fetchAll();

require __DIR__ . '/includes/header.php';
?>
<section class="section container reveal">
  <h1 class="section-title">Kunstwerke / Galerie</h1>
  <div class="grid art-grid">
<?php foreach ($artworks as $art): ?>
    <article class="card art-card">
      <img src="<?= e($art['image_path'] ?: '/assets/img/placeholder-artwork.jpg') ?>" alt="<?= e($art['title']) ?>" />
      <h3><?= e($art['title']) ?></h3>
      <p><?= e($art['description']) ?></p>
      <p class="art-meta">Collection: <?= e($art['collection_name']) ?> · <?= e((string) $art['year']) ?></p>
    </article>
<?php endforeach; ?>
<?php if (!$artworks): ?>
    <p>Noch keine Kunstwerke veröffentlicht.</p>
<?php endif; ?>
  </div>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
