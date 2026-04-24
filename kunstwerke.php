<?php
$pageTitle = 'Kunstwerke';
require __DIR__ . '/includes/header.php';
$artworks = fetchAll("SELECT * FROM artworks WHERE is_visible=1 ORDER BY sort_order ASC, created_at DESC");
?>
<section class="container page-intro reveal"><h1>Kunstwerke</h1><p>Galerie aller sichtbaren Arbeiten.</p></section>
<section class="container reveal">
  <div class="card-grid artworks">
    <?php foreach ($artworks as $art): ?>
    <article class="card artwork-card">
      <img src="<?= e($art['image_path']) ?>" alt="<?= e($art['title']) ?>">
      <h3><?= e($art['title']) ?></h3>
      <p class="meta"><?= e($art['collection_name']) ?><?= $art['year'] ? ' · ' . e($art['year']) : '' ?></p>
      <p><?= e($art['description']) ?></p>
    </article>
    <?php endforeach; ?>
  </div>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
