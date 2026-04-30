<?php
require __DIR__ . '/config/bootstrap.php';

$pageTitle = 'S-ART / Shary on Tour';
require __DIR__ . '/includes/header.php';

$currentLocation = fetchOne("SELECT * FROM tour_locations WHERE status='current' ORDER BY date_from DESC LIMIT 1");
$events = fetchAll("SELECT * FROM events WHERE status='upcoming' ORDER BY event_date ASC LIMIT 3");
?>

<section class="section hero" id="hero">
  <div class="hero-bg" aria-hidden="true"></div>

  <div class="container hero-layout">
    <div class="hero-copy reveal">
      <p class="kicker">SHARY ON TOUR</p>
      <h1>POP-ART VON<br><span class="text-pink">SHARYAR</span><br><span class="text-green">AZHDARI</span></h1>
      <p class="subline">Cinematic Street-Art Energy für Events, Live-Erlebnisse und Sammler mit Anspruch.</p>

      <div class="cta-row">
        <a class="btn btn-primary" href="#events">EVENTS ENTDECKEN &nbsp;→</a>
        <a class="btn btn-ghost" href="#newsletter">TICKET SICHERN &nbsp;
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="16" height="16" aria-hidden="true">
            <circle cx="12" cy="12" r="10"/>
            <polyline points="12 8 16 12 12 16"/>
            <line x1="8" y1="12" x2="16" y2="12"/>
          </svg>
        </a>
      </div>
    </div>

    <div class="hero-art reveal">
      <div class="hero-art-portrait">
        <img src="/assets/Img/selfportrait.png" alt="Selbstporträt von Shary" loading="eager">
      </div>
    </div>
  </div>
</section>

<?php if ($currentLocation): ?>
<section class="section container section-compact reveal" id="location">
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
          <h2><?= e($currentLocation['title']) ?></h2>
          <p><?= e($currentLocation['address']) ?></p>
          <p class="date"><?= formatDate($currentLocation['date_from']) ?><?= $currentLocation['date_to'] ? ' – ' . formatDate($currentLocation['date_to']) : '' ?></p>
        </div>
      </div>
    </div>

    <?php if (!empty($currentLocation['google_maps_url'])): ?>
      <a class="btn btn-dark" target="_blank" rel="noopener" href="<?= e($currentLocation['google_maps_url']) ?>">AUF GOOGLE MAPS ÖFFNEN &nbsp;→</a>
    <?php endif; ?>
  </article>
</section>
<?php endif; ?>

<section class="section container" id="events">
  <div class="section-heading reveal">
    <h2>AKTUELLE EVENTS</h2>
    <a href="/tour.php">ALLE EVENTS ANSEHEN →</a>
  </div>

  <div class="carousel-wrap reveal-group">
    <div class="card-grid events-scroll">
      <?php foreach ($events as $event): ?>
        <article class="card event-card reveal">
          <div class="card-media">
            <?php
// Event-Titel in Dateinamen umwandeln
$imageName = strtolower($event['title']); // klein
$imageName = str_replace(['ä','ö','ü','ß'], ['ae','oe','ue','ss'], $imageName);
$imageName = preg_replace('/[^a-z0-9]+/', '-', $imageName); // alles sauber
$imageName = trim($imageName, '-');

// finaler Pfad
$imagePath = "/assets/Img/" . $imageName . ".png";

// fallback wenn Bild nicht existiert
$finalImage = file_exists($_SERVER['DOCUMENT_ROOT'] . $imagePath)
  ? $imagePath
  : "/assets/Img/default-event.png";
?>

<img src="<?= e($finalImage) ?>" alt="<?= e($event['title']) ?>" loading="lazy">
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

    <button class="carousel-btn" data-scroll="events-scroll" aria-label="Weitere Events anzeigen">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" width="20" height="20" aria-hidden="true">
        <polyline points="9 18 15 12 9 6"/>
      </svg>
    </button>
  </div>

  <div class="event-dots">
    <span class="event-dot is-active"></span>
    <span class="event-dot"></span>
    <span class="event-dot"></span>
    <span class="event-dot"></span>
  </div>
</section>

<section class="section container" id="artworks">
  <div class="section-heading reveal">
    <h2>AUSGEWÄHLTE KUNSTWERKE</h2>
    <a href="https://s-art.work/shop/" target="_blank" rel="noopener">ALLE KUNSTWERKE ANSEHEN →</a>
  </div>

  <a class="shop-teaser reveal neon-frame" href="https://s-art.work/shop/" target="_blank" rel="noopener">
    <div>
      <p class="meta">S-ART SHOP</p>
      <h3>Kunstwerke direkt im offiziellen Shop ansehen</h3>
      <p>Alle verfügbaren Werke, Details und Anfragen findest du im S-ART Shop.</p>
    </div>
    <span class="btn btn-primary">ZUM SHOP →</span>
  </a>
</section>

<section class="section container" id="newsletter">
  <div class="newsletter-box reveal neon-frame">
    <div class="newsletter-left">
      <div class="newsletter-icon-box">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round" width="26" height="26" aria-hidden="true">
          <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
          <polyline points="22,6 12,13 2,6"/>
        </svg>
      </div>

      <div>
        <h2>GRATIS TICKET + NEWSLETTER</h2>
        <p>Erhalte exklusive Vorverkaufs-Infos, Pop-up Termine und digitale Ticket-Freischaltung.</p>
      </div>
    </div>

    <form method="post" action="/newsletter-submit.php" class="newsletter-form">
      <?= csrfField() ?>
      <input type="hidden" name="source" value="homepage">
      <input type="hidden" name="first_name" value="">

      <div class="newsletter-email-row">
        <input type="email" name="email" placeholder="Deine E-Mail-Adresse" required>
        <button class="btn btn-primary" type="submit">JETZT ANMELDEN</button>
      </div>

      <label class="check">
        <input type="checkbox" name="consent_privacy" value="1" required>
        Ich stimme der <a href="/datenschutz.php" class="privacy-link">Datenschutzerklärung</a> zu.
      </label>
    </form>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
