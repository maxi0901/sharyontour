<?php
$pageTitle = 'S-ART / Shary on Tour';
require __DIR__ . '/includes/header.php';

$currentLocation = fetchOne("SELECT * FROM tour_locations WHERE status='current' ORDER BY date_from DESC LIMIT 1");
$events = fetchAll("SELECT * FROM events WHERE status='upcoming' ORDER BY event_date ASC LIMIT 3");
$artworks = fetchAll("SELECT * FROM artworks WHERE is_visible=1 ORDER BY sort_order ASC, created_at DESC LIMIT 4");
?>
<section class="hero reveal">
  <div class="container">
    <p class="kicker">Urban Art Experience</p>
    <h1>SHARY ON TOUR</h1>
    <p class="subline">Zwei Container. Zwei Kunstwerke. Eine Tour.</p>
    <div class="cta-row">
      <a class="btn btn-pink" href="#events">Aktuelle Events ansehen</a>
      <a class="btn btn-outline" href="#newsletter">Ticket sichern</a>
    </div>
  </div>
</section>
<div class="marquee"><span>Berlin • Hamburg • Köln • Stuttgart • München • Frankfurt • Leipzig • Dortmund •</span></div>
<section class="container reveal location-highlight">
  <h2>Aktueller Standort</h2>
  <?php if ($currentLocation): ?>
    <div class="highlight-card">
      <h3><?= e($currentLocation['title']) ?> — <?= e($currentLocation['city']) ?></h3>
      <p><?= e($currentLocation['address']) ?></p>
      <p><?= formatDate($currentLocation['date_from']) ?><?= $currentLocation['date_to'] ? ' bis ' . formatDate($currentLocation['date_to']) : '' ?></p>
      <?php if (!empty($currentLocation['google_maps_url'])): ?><a class="btn btn-blue" target="_blank" rel="noopener" href="<?= e($currentLocation['google_maps_url']) ?>">Auf Google Maps öffnen</a><?php endif; ?>
    </div>
  <?php else: ?><p>Aktuell wird ein neuer Tour-Stopp vorbereitet.</p><?php endif; ?>
</section>
<section class="container reveal">
  <h2>Die Idee</h2>
  <p class="lead">S-ART bringt Kunst aus der Galerie direkt in die Stadt. Zwei mobile Container werden zur Bühne für mutige Werke, Begegnungen und Live-Events.</p>
</section>
<section class="container reveal" id="events">
  <h2>Aktuelle Events</h2>
  <div class="card-grid">
    <?php foreach ($events as $event): ?>
      <article class="card">
        <p class="meta"><?= formatDate($event['event_date']) ?> · <?= e($event['city']) ?></p>
        <h3><?= e($event['title']) ?></h3>
        <p><?= e($event['description_short']) ?></p>
      </article>
    <?php endforeach; ?>
  </div>
</section>
<section class="container reveal">
  <h2>Ausgewählte Kunstwerke</h2>
  <div class="card-grid artworks">
    <?php foreach ($artworks as $art): ?>
      <article class="card artwork-card">
        <img src="<?= e($art['image_path']) ?>" alt="<?= e($art['title']) ?>">
        <h3><?= e($art['title']) ?></h3>
      </article>
    <?php endforeach; ?>
  </div>
</section>
<section class="container reveal newsletter-box" id="newsletter">
  <h2>Gratis Ticket + Newsletter</h2>
  <form method="post" action="/newsletter-submit.php">
    <?= csrfField() ?>
    <input type="hidden" name="source" value="homepage">
    <label>E-Mail* <input type="email" name="email" required></label>
    <label>Vorname <input type="text" name="first_name"></label>
    <label class="check"><input type="checkbox" name="consent_privacy" value="1" required> Ich stimme der Datenschutzerklärung zu.</label>
    <button class="btn btn-yellow" type="submit">Ticket jetzt erstellen</button>
  </form>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
