<?php
$pageTitle = 'S-ART / Shary on Tour';
require __DIR__ . '/includes/header.php';

$currentEvent = fetchOne("
    SELECT *
    FROM events
    WHERE status = 'upcoming'
      AND event_date >= CURDATE()
    ORDER BY event_date ASC
    LIMIT 1
");
$events = fetchAll("
    SELECT *
    FROM events
    WHERE status = 'upcoming'
      AND event_date >= CURDATE()
    ORDER BY event_date ASC
    LIMIT 3
");
$artworks = fetchAll("SELECT * FROM artworks WHERE is_visible=1 ORDER BY sort_order ASC, created_at DESC LIMIT 12");
?>

<section class="section hero" id="hero">
  <div class="hero-bg" aria-hidden="true"></div>

  <div class="container hero-layout">
    <div class="hero-copy reveal">
      <h1 class="hero-brand-title hero-brand-title--simple">
        <span>Shary on Tour</span>
      </h1>
      <p class="subline">Internationaler Eventkalender</p>

      <div class="cta-row">
        <a class="btn btn-primary" href="#events">EVENTS ENTDECKEN &nbsp;→</a>
        <a class="btn btn-ghost" href="/ticket-buchen.php">TICKET SICHERN &nbsp;
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="16" height="16" aria-hidden="true">
            <circle cx="12" cy="12" r="10"/>
            <polyline points="12 8 16 12 12 16"/>
            <line x1="8" y1="12" x2="16" y2="12"/>
          </svg>
        </a>
      </div>
    </div>

  </div>
</section>

<?php if ($currentEvent):
  $currentIsOpening = (int) ($currentEvent['is_opening'] ?? 0) === 1 || ($currentEvent['slug'] ?? '') === 'container-opening-kassel';
  if ($currentIsOpening) {
      $currentSold = countTicketsForEvent((int) $currentEvent['id']);
      $currentMax  = (int) ($currentEvent['max_tickets'] ?? 600);
      if ($currentMax <= 0) {
          $currentMax = 600;
      }
      $currentTrackerLabel = ticketTrackerLabel($currentSold, $currentMax);
      $currentTrackerDanger = $currentSold >= $currentMax
          || ($currentSold >= TICKET_TRACKER_THRESHOLD && ($currentMax - $currentSold) < 100);
  }
?>
<section class="section container section-compact reveal" id="current-event">
  <article class="location-strip neon-frame">
    <div class="location-left">
      <p class="meta">AKTUELLES EVENT</p>
      <div class="location-inner">
        <div class="location-icon-box">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="22" height="22" aria-hidden="true">
            <path d="M21 10c0 7-9 13-9 13S3 17 3 10a9 9 0 0 1 18 0z"/>
            <circle cx="12" cy="10" r="3"/>
          </svg>
        </div>
        <div class="location-main">
          <h2><?= e($currentEvent['title']) ?></h2>
          <p><?= e((string) ($currentEvent['location_name'] ?: $currentEvent['city'])) ?></p>
          <p class="date"><?= formatDate($currentEvent['event_date']) ?></p>
          <?php if ($currentIsOpening): ?>
            <p class="ticket-tracker<?= $currentTrackerDanger ? ' text-danger' : '' ?>"><?= e($currentTrackerLabel) ?></p>
          <?php endif; ?>
        </div>
      </div>
    </div>
    <?php if ($currentIsOpening): ?>
      <button class="btn btn-primary js-ticket-btn" type="button" data-event-id="<?= (int) $currentEvent['id'] ?>">GRATIS TICKET SICHERN &nbsp;→</button>
    <?php else: ?>
      <a class="btn btn-dark" target="_blank" rel="noopener" href="<?= e(eventGoogleMapsUrl($currentEvent)) ?>">AUF GOOGLE MAPS ÖFFNEN &nbsp;→</a>
    <?php endif; ?>
  </article>
</section>
<?php endif; ?>

<section class="section container" id="events">
  <div class="section-heading reveal">
    <h2>AKTUELLE EVENTS</h2>
    <a href="/tour.php">ALLE EVENTS IM ÜBERBLICK →</a>
  </div>
  <div class="carousel-wrap reveal-group">
    <div class="card-grid events-scroll">
      <?php foreach ($events as $event):
        $isOpening = (int) ($event['is_opening'] ?? 0) === 1 || ($event['slug'] ?? '') === 'container-opening-kassel';
      ?>
        <article class="card event-card reveal <?= $isOpening ? 'is-opening' : '' ?>">
          <div class="card-media">
            <?php if (!empty($event['image_path'])): ?>
              <img src="<?= e($event['image_path']) ?>" alt="<?= e($event['title']) ?>" loading="lazy">
            <?php else: ?>
              <div class="media-fallback"></div>
            <?php endif; ?>
          </div>
          <p class="meta"><?= formatDate($event['event_date']) ?> · <?= e($event['city']) ?></p>
          <?php if ($isOpening): ?><span class="badge badge-opening">HAUPT-EVENT</span><?php endif; ?>
          <h3><?= e($event['title']) ?></h3>
          <p><?= e($event['description_short']) ?></p>
          <div class="events-item-actions">
            <a class="btn btn-ghost btn-sm" target="_blank" rel="noopener" href="<?= e(eventGoogleMapsUrl($event)) ?>">Standort</a>
            <?php if ($isOpening): ?>
              <button class="btn btn-primary btn-sm js-ticket-btn" type="button" data-event-id="<?= (int) $event['id'] ?>">Gratis Ticket sichern</button>
              <span class="muted js-ticket-stock" data-event-id="<?= (int) $event['id'] ?>"></span>
            <?php endif; ?>
          </div>
        </article>
      <?php endforeach; ?>
      <?php if (empty($events)): ?>
        <p class="muted">Neue Events werden bald bekannt gegeben.</p>
      <?php endif; ?>
    </div>
    <button class="carousel-btn carousel-btn-prev is-hidden" data-scroll="events-scroll" data-direction="prev" aria-label="Vorherige Events anzeigen">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" width="20" height="20" aria-hidden="true">
        <polyline points="15 18 9 12 15 6"/>
      </svg>
    </button>
    <button class="carousel-btn carousel-btn-next" data-scroll="events-scroll" data-direction="next" aria-label="Weitere Events anzeigen">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" width="20" height="20" aria-hidden="true">
        <polyline points="9 18 15 12 9 6"/>
      </svg>
    </button>
  </div>
  <div class="event-dots" data-event-dots aria-label="Event Navigation"></div>
</section>

<section class="section container" id="artworks">
  <article class="shop-teaser neon-frame reveal">
    <div>
      <p class="meta">SHOP</p>
      <h3>Originale & Prints im S-ART Shop</h3>
      <p>Entdecke limitierte Kunstwerke und sichere dir dein S-Art Original oder Kunstdruck</p>
    </div>
    <a class="btn btn-primary" href="<?= e($siteConfig['shop_url']) ?>" target="_blank" rel="noopener noreferrer">ZUM SHOP &nbsp;→</a>
  </article>
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
        <h2>NEWSLETTER-ANMELDUNG</h2>
        <p>Bleib auf dem Laufenden: Tour-Stopps, neue Drops und Pop-up-Termine direkt in dein Postfach.</p>
      </div>
    </div>
    <form method="post" action="/newsletter-submit.php" class="newsletter-form">
      <?php
        $nl = $_GET['nl'] ?? null;
        $nlMessages = [
            'pending'      => ['type' => 'success', 'text' => 'Bitte bestätige deine Anmeldung über den Link in der E-Mail.'],
            'ok'           => ['type' => 'success', 'text' => 'Danke! Du erhältst gleich eine Bestätigungs-Mail – bitte den Link darin anklicken.'],
            'confirmed'    => ['type' => 'success', 'text' => 'Anmeldung bestätigt – du bist jetzt im Verteiler.'],
            'unsubscribed' => ['type' => 'success', 'text' => 'Du wurdest erfolgreich abgemeldet.'],
            'already'      => ['type' => 'info',    'text' => 'Diese Adresse ist bereits angemeldet.'],
            'invalid'      => ['type' => 'error',   'text' => 'Bitte eine gültige E-Mail-Adresse angeben.'],
            'consent'      => ['type' => 'error',   'text' => 'Bitte stimme der Datenschutzerklärung zu.'],
            'error'        => ['type' => 'error',   'text' => 'Es ist ein technisches Problem aufgetreten – bitte später erneut versuchen.'],
        ];
      ?>
      <?php if (is_string($nl) && isset($nlMessages[$nl])): $msg = $nlMessages[$nl]; ?>
        <div class="form-flash form-flash-<?= e($msg['type']) ?>"><?= e($msg['text']) ?></div>
      <?php endif; ?>
      <?= csrfField() ?>
      <input type="hidden" name="source" value="homepage">
      <input type="hidden" name="first_name" value="">
      <div class="newsletter-email-row">
        <input type="email" name="email" placeholder="Deine E-Mail-Adresse" required>
        <button class="btn btn-primary" type="submit">ANMELDEN</button>
      </div>
      <label class="check">
        <input type="checkbox" name="consent_privacy" value="1" required>
        Ich stimme der <a href="/datenschutz.php" class="privacy-link">Datenschutzerklärung</a> zu.
      </label>
    </form>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
