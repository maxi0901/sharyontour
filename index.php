<?php
$pageTitle = 'S-ART / Shary on Tour';
require __DIR__ . '/includes/header.php';

$currentLocation = fetchOne("SELECT * FROM tour_locations WHERE status='current' ORDER BY date_from DESC LIMIT 1");
$events = fetchAll("SELECT * FROM events WHERE status='upcoming' ORDER BY event_date ASC LIMIT 3");
$artworks = fetchAll("SELECT * FROM artworks WHERE is_visible=1 ORDER BY sort_order ASC, created_at DESC LIMIT 4");
?>
<section class="hero reveal">
  <div class="container">
    <p class="hero-label">SHARY ON TOUR</p>
    <h1>POP-ART VON <span>SHARYAR AZHDARI</span></h1>
    <p class="subline">Cinematic street-art energy für Events, Live-Erlebnisse und Sammler mit Anspruch.</p>
    <div class="cta-row">
      <a class="btn btn-primary" href="#events">EVENTS ENTDECKEN</a>
      <a class="btn btn-secondary" href="#newsletter">TICKET SICHERN</a>
    </div>
  </div>
</section>
<section class="container reveal intro-block">
  <p class="kicker">ORIGINALE POP-ART VON SHARY</p>
  <h2>POP-ART VON <span>SHARYAR AZHDARI</span></h2>
  <p class="lead center">Moderne, urbane Kunst mit starker Farbe, Emotion und internationaler Präsenz — ideal für Sammler und Designliebhaber.</p>
</section>
<section class="container reveal location-highlight">
  <h2>Aktueller Standort</h2>
  <?php if ($currentLocation): ?>
    <div class="highlight-card neon-frame">
      <p class="meta">LOCATION HIGHLIGHT</p>
      <h3><?= e($currentLocation['title']) ?> — <?= e($currentLocation['city']) ?></h3>
      <p><?= e($currentLocation['address']) ?></p>
      <p><?= formatDate($currentLocation['date_from']) ?><?= $currentLocation['date_to'] ? ' bis ' . formatDate($currentLocation['date_to']) : '' ?></p>
      <?php if (!empty($currentLocation['google_maps_url'])): ?><a class="btn btn-secondary" target="_blank" rel="noopener" href="<?= e($currentLocation['google_maps_url']) ?>">Auf Google Maps öffnen</a><?php endif; ?>
    </div>
  <?php else: ?><p>Aktuell wird ein neuer Tour-Stopp vorbereitet.</p><?php endif; ?>
</section>
<section class="container reveal" id="events">
  <h2>Aktuelle Events</h2>
  <div class="card-grid">
    <?php foreach ($events as $event): ?>
      <article class="card event-card">
        <p class="meta"><?= formatDate($event['event_date']) ?> · <?= e($event['city']) ?></p>
        <h3><?= e($event['title']) ?></h3>
        <p><?= e($event['description_short']) ?></p>
        <a class="text-link" href="/booking.php">Tickets & Infos ↗</a>
      </article>
    <?php endforeach; ?>
  </div>
</section>
<section class="container reveal">
  <h2>Ausgewählte Kunstwerke</h2>
  <div class="card-grid artworks">
    <?php foreach ($artworks as $art): ?>
      <article class="card artwork-card neon-frame">
        <img src="<?= e($art['image_path']) ?>" alt="<?= e($art['title']) ?>">
        <h3><?= e($art['title']) ?></h3>
      </article>
    <?php endforeach; ?>
  </div>
</section>
<section class="container reveal newsletter-box" id="newsletter">
  <h2>Gratis Ticket + Newsletter</h2>
  <p class="lead">Erhalte exklusive Vorverkaufs-Infos, Pop-up Termine und digitale Ticket-Freischaltung.</p>
  <form method="post" action="/newsletter-submit.php">
    <?= csrfField() ?>
    <input type="hidden" name="source" value="homepage">
    <label>E-Mail* <input type="email" name="email" required></label>
    <label>Vorname <input type="text" name="first_name"></label>
    <label class="check"><input type="checkbox" name="consent_privacy" value="1" required> Ich stimme der Datenschutzerklärung zu.</label>
    <button class="btn btn-primary" type="submit">Ticket jetzt erstellen</button>
  </form>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
