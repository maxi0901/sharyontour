<?php
$pageTitle = 'Vergangene Events';
require __DIR__ . '/includes/header.php';
$events = fetchAll("SELECT * FROM events WHERE status='past' ORDER BY event_date DESC");
?>

<section class="container page-intro reveal">
  <p class="kicker">ARCHIV</p>
  <h1>Vergangene Events</h1>
  <p class="subline">Alle bisherigen Auftritte und Ausstellungen der S-ART Tour.</p>
</section>

<section class="container reveal">
  <div class="card-grid">
    <?php foreach ($events as $event): ?>
      <article class="card event-card">
        <div class="card-media">
          <?php if (!empty($event['image_path'])): ?>
            <img src="<?= e($event['image_path']) ?>" alt="<?= e($event['title']) ?>" loading="lazy">
          <?php else: ?>
            <div class="media-fallback"></div>
          <?php endif; ?>
        </div>
        <p class="meta"><?= formatDate($event['event_date']) ?> · <?= e($event['city']) ?></p>
        <h3><?= e($event['title']) ?></h3>
        <p><?= e($event['description_short']) ?></p>
      </article>
    <?php endforeach; ?>
    <?php if (empty($events)): ?>
      <p class="muted">Noch keine vergangenen Events vorhanden.</p>
    <?php endif; ?>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
