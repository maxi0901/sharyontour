<?php
$pageTitle = 'S-ART / Shary on Tour';
require __DIR__ . '/includes/header.php';

$currentLocation = fetchOne("SELECT * FROM tour_locations WHERE status='current' ORDER BY date_from DESC LIMIT 1");
$events = fetchAll("SELECT * FROM events WHERE status='upcoming' ORDER BY event_date ASC LIMIT 3");
$artworks = fetchAll("SELECT * FROM artworks WHERE is_visible=1 ORDER BY sort_order ASC, created_at DESC LIMIT 6");
?>

<section class="hero" id="top">
  <div class="hero-bg" aria-hidden="true"></div>
  <div class="container hero-layout">
    <div class="hero-copy reveal">
      <p class="kicker">SHARY ON TOUR</p>
      <h1>POP-ART VON <span class="text-pink">SHARYAR</span> <span class="text-green">AZHDARI</span></h1>
      <p class="subline">Cinematic Street-Art Energy für Events, Live-Erlebnisse und Sammler mit Anspruch.</p>
      <div class="cta-row">
        <a class="btn btn-primary" href="#events">EVENTS ENTDECKEN</a>
        <a class="btn btn-ghost" href="#newsletter">TICKET SICHERN</a>
      </div>
    </div>
    <div class="hero-art reveal" aria-hidden="true">
      <span class="hero-tag">S-ART</span>
    </div>
  </div>
</section>

<?php if ($currentLocation): ?>
<section class="container section-compact reveal">
  <article class="location-strip neon-frame">
    <div class="location-main">
      <p class="meta">AKTUELLER STANDORT</p>
      <h2><?= e($currentLocation['title']) ?></h2>
      <p><?= e($currentLocation['address']) ?></p>
      <p class="date"><?= formatDate($currentLocation['date_from']) ?><?= $currentLocation['date_to'] ? ' – ' . formatDate($currentLocation['date_to']) : '' ?></p>
    </div>
    <?php if (!empty($currentLocation['google_maps_url'])): ?>
      <a class="btn btn-dark" target="_blank" rel="noopener" href="<?= e($currentLocation['google_maps_url']) ?>">AUF GOOGLE MAPS ÖFFNEN</a>
    <?php endif; ?>
  </article>
</section>
<?php endif; ?>

<section class="container" id="events">
  <div class="section-heading reveal">
    <h2>AKTUELLE EVENTS</h2>
    <a href="/tour.php">ALLE EVENTS ANSEHEN →</a>
  </div>
  <div class="card-grid reveal-group">
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
    <?php if (empty($events)): ?>
      <p class="muted">Neue Events werden bald bekannt gegeben.</p>
    <?php endif; ?>
  </div>
</section>

<section class="container">
  <div class="section-heading reveal">
    <h2>AUSGEWÄHLTE KUNSTWERKE</h2>
    <a href="/kunstwerke.php">ALLE KUNSTWERKE ANSEHEN →</a>
  </div>
  <div class="artworks-grid reveal-group">
    <?php foreach ($artworks as $art): ?>
      <article class="artwork-card reveal">
        <div class="artwork-img-wrap">
          <img src="<?= e($art['image_path']) ?>" alt="<?= e($art['title']) ?>" loading="lazy">
        </div>
      </article>
    <?php endforeach; ?>
    <?php if (empty($artworks)): ?>
      <p class="muted">Kunstwerke werden in Kürze hinzugefügt.</p>
    <?php endif; ?>
  </div>
</section>

<section class="container" id="newsletter">
  <div class="newsletter-box reveal neon-frame">
    <div>
      <p class="meta">GRATIS TICKET + NEWSLETTER</p>
      <h2>Exklusive Vorverkaufs-Infos direkt in dein Postfach</h2>
      <p>Erhalte Pop-up Termine, neue Werke und digitale Ticket-Freischaltung.</p>
    </div>
    <form method="post" action="/newsletter-submit.php" class="newsletter-form">
      <?= csrfField() ?>
      <input type="hidden" name="source" value="homepage">
      <input type="email" name="email" placeholder="Deine E-Mail-Adresse" required>
      <input type="text" name="first_name" placeholder="Vorname (optional)">
      <label class="check">
        <input type="checkbox" name="consent_privacy" value="1" required>
        Ich stimme der Datenschutzerklärung zu.
      </label>
      <button class="btn btn-primary" type="submit">JETZT ANMELDEN</button>
    </form>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
