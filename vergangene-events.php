<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/functions.php';

$currentPage = 'past';
$pageTitle = 'Vergangene Events';
$pastEvents = fetch_events('past', 24);

require __DIR__ . '/includes/header.php';
?>
<section class="section container reveal">
  <h1 class="section-title">Vergangene Events</h1>
  <div class="grid events">
<?php foreach ($pastEvents as $event): ?>
    <article class="card">
      <h3><?= e($event['title']) ?></h3>
      <p><?= e($event['event_date']) ?> · <?= e($event['city']) ?></p>
      <p><?= e($event['description_long'] ?: $event['description_short']) ?></p>
      <?php if (!empty($event['image_path'])): ?>
        <img src="<?= e($event['image_path']) ?>" alt="<?= e($event['title']) ?>" style="width:100%;border:2px solid var(--ink);" />
      <?php endif; ?>
    </article>
<?php endforeach; ?>
<?php if (!$pastEvents): ?>
    <p>Archivdaten folgen mit den nächsten Updates.</p>
<?php endif; ?>
  </div>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
