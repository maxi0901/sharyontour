<?php
require __DIR__ . '/config/bootstrap.php';

$eventId = (int) ($_GET['event'] ?? 0);
$event = $eventId ? fetchOne('SELECT * FROM events WHERE id=:id', ['id' => $eventId]) : null;
$images = $event ? fetchAll('SELECT * FROM galleries WHERE event_id=:e ORDER BY sort_order ASC, id ASC', ['e' => $eventId]) : [];

$pageTitle = $event ? 'Galerie · ' . $event['title'] : 'Galerie';
require __DIR__ . '/includes/header.php';
?>

<section class="section container page-intro reveal">
  <a class="text-link" href="/vergangene-events.php">← Alle Galerien</a>
  <?php if ($event): ?>
    <p class="kicker"><?= formatDate($event['event_date']) ?> · <?= e($event['city']) ?></p>
    <h1><?= e($event['title']) ?></h1>
    <p class="subline"><?= e($event['description_long'] ?: $event['description_short']) ?></p>
  <?php else: ?>
    <h1>Galerie nicht gefunden</h1>
  <?php endif; ?>
</section>

<?php if ($event): ?>
<section class="section container reveal">
  <?php if (!empty($images)): ?>
    <div class="gallery-grid">
      <?php foreach ($images as $img): ?>
        <a class="gallery-item" href="<?= e($img['image_path']) ?>" target="_blank" rel="noopener">
          <img src="<?= e($img['image_path']) ?>" alt="<?= e($img['caption'] ?: $event['title']) ?>" loading="lazy">
          <?php if (!empty($img['caption'])): ?><span class="gallery-caption"><?= e($img['caption']) ?></span><?php endif; ?>
        </a>
      <?php endforeach; ?>
    </div>
  <?php else: ?>
    <p class="muted">Bilder werden bald hochgeladen.</p>
  <?php endif; ?>
</section>
<?php endif; ?>

<?php require __DIR__ . '/includes/footer.php'; ?>
