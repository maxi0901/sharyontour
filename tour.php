<?php
$pageTitle = 'Events & Tour';
require __DIR__ . '/includes/header.php';
$current  = fetchOne("SELECT * FROM tour_locations WHERE status='current' ORDER BY date_from DESC LIMIT 1");
$upcoming = fetchAll("SELECT * FROM tour_locations WHERE status='upcoming' ORDER BY date_from ASC");
$past     = fetchAll("SELECT * FROM tour_locations WHERE status='past' ORDER BY date_from DESC");
$events   = fetchAll("SELECT * FROM events WHERE status='upcoming' ORDER BY event_date ASC");
?>

<section class="container page-intro reveal">
  <p class="kicker">SHARY ON TOUR</p>
  <h1>Tour-Standorte</h1>
  <p class="subline">Alle aktuellen und kommenden Stopps der mobilen S-ART Galerie.</p>
</section>

<?php if ($current): ?>
<section class="container section-compact reveal">
  <article class="location-strip neon-frame">
    <div class="location-left">
      <p class="meta">AKTUELLER STANDORT</p>
      <div class="location-inner">
        <div class="location-icon-box">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="22" height="22" aria-hidden="true">
            <path d="M21 10c0 7-9 13-9 13S3 17 3 10a9 9 0 0 1 18 0z"/>
            <circle cx="12" cy="10" r="3"/>
          </svg>
        </div>
        <div class="location-main">
          <h2><?= e($current['title']) ?></h2>
          <p><?= e($current['address']) ?></p>
          <p class="date muted"><?= formatDate($current['date_from']) ?><?= $current['date_to'] ? ' – ' . formatDate($current['date_to']) : '' ?></p>
        </div>
      </div>
    </div>
    <?php if (!empty($current['google_maps_url'])): ?>
      <a class="btn btn-dark" target="_blank" rel="noopener" href="<?= e($current['google_maps_url']) ?>">AUF GOOGLE MAPS ÖFFNEN &nbsp;→</a>
    <?php endif; ?>
  </article>
</section>
<?php endif; ?>

<?php if (!empty($events)): ?>
<section class="container" id="events">
  <div class="section-heading reveal">
    <h2>AKTUELLE EVENTS</h2>
    <a href="/vergangene-events.php">ALLE EVENTS →</a>
  </div>
  <div class="card-grid events-scroll reveal-group">
    <?php foreach ($events as $event): ?>
      <article class="card event-card reveal">
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
        <a class="text-link" href="/booking.php">Tickets &amp; Infos ↗</a>
      </article>
    <?php endforeach; ?>
  </div>
</section>
<?php endif; ?>

<?php if (!empty($upcoming)): ?>
<section class="container reveal">
  <div class="section-heading">
    <h2>KOMMENDE STOPPS</h2>
  </div>
  <div class="timeline">
    <?php foreach ($upcoming as $row): ?>
      <article>
        <strong><?= formatDate($row['date_from']) ?></strong>
        <h3><?= e($row['title']) ?> · <?= e($row['city']) ?></h3>
        <?php if (!empty($row['description'])): ?>
          <p><?= e($row['description']) ?></p>
        <?php endif; ?>
        <?php if (!empty($row['google_maps_url'])): ?>
          <a class="text-link" href="<?= e($row['google_maps_url']) ?>" target="_blank" rel="noopener">Maps ↗</a>
        <?php endif; ?>
      </article>
    <?php endforeach; ?>
    <?php if (empty($upcoming)): ?>
      <p class="muted">Neue Stopps werden bald bekanntgegeben.</p>
    <?php endif; ?>
  </div>
</section>
<?php endif; ?>

<?php if (!empty($past)): ?>
<section class="container reveal">
  <div class="section-heading">
    <h2>VERGANGENE STOPPS</h2>
  </div>
  <div class="timeline">
    <?php foreach ($past as $row): ?>
      <article>
        <strong><?= formatDate($row['date_from']) ?></strong>
        <h3><?= e($row['title']) ?> · <?= e($row['city']) ?></h3>
      </article>
    <?php endforeach; ?>
  </div>
</section>
<?php endif; ?>

<?php require __DIR__ . '/includes/footer.php'; ?>
