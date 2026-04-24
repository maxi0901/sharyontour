<?php
$pageTitle = 'S-ART / Shary on Tour';
require __DIR__ . '/includes/header.php';

$currentLocation = fetchOne("SELECT * FROM tour_locations WHERE status='current' ORDER BY date_from DESC LIMIT 1");
$events = fetchAll("SELECT * FROM events WHERE status='upcoming' ORDER BY event_date ASC LIMIT 3");
$artworks = fetchAll("SELECT * FROM artworks WHERE is_visible=1 ORDER BY sort_order ASC, created_at DESC LIMIT 6");
?>

<!-- ── Hero ────────────────────────────────────────────────────── -->
<section class="hero">
  <div class="hero-spray" aria-hidden="true"></div>
  <div class="parallax-layer" id="heroParallax" aria-hidden="true"></div>
  <div class="container">
    <p class="hero-label reveal">SHARY ON TOUR</p>
    <h1 class="reveal" style="transition-delay:80ms">
      POP-ART VON<br>
      <span class="text-pink">SHARYAR</span><br>
      <span class="text-green">AZHDARI</span>
    </h1>
    <p class="subline reveal" style="transition-delay:160ms">Cinematic street-art energy für Events, Live-Erlebnisse und Sammler mit Anspruch.</p>
    <div class="cta-row reveal" style="transition-delay:240ms">
      <a class="btn btn-primary" href="#events">EVENTS ENTDECKEN &nbsp;→</a>
      <a class="btn btn-secondary" href="#newsletter">TICKET SICHERN &nbsp;◎</a>
    </div>
  </div>
</section>

<div class="spray-divider" aria-hidden="true"></div>

<!-- ── Aktueller Standort ────────────────────────────────────────── -->
<?php if ($currentLocation): ?>
<section class="container reveal location-highlight" style="padding-top:2rem;padding-bottom:1.5rem;">
  <div class="highlight-card neon-frame">
    <p class="meta">&#9679; AKTUELLER STANDORT</p>
    <div style="display:flex;align-items:flex-start;gap:1.2rem;flex-wrap:wrap;">
      <div style="flex:1;min-width:200px;">
        <h3><?= e($currentLocation['title']) ?></h3>
        <p style="margin:0.3rem 0 0.15rem;color:var(--text-soft);"><?= e($currentLocation['address']) ?></p>
        <p style="margin:0;color:var(--muted);font-size:0.85rem;">
          <?= formatDate($currentLocation['date_from']) ?><?= $currentLocation['date_to'] ? ' – ' . formatDate($currentLocation['date_to']) : '' ?>
        </p>
      </div>
      <?php if (!empty($currentLocation['google_maps_url'])): ?>
      <a class="btn btn-dark" target="_blank" rel="noopener" href="<?= e($currentLocation['google_maps_url']) ?>" style="white-space:nowrap;align-self:center;">
        AUF GOOGLE MAPS ÖFFNEN &nbsp;→
      </a>
      <?php endif; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<div class="spray-divider" aria-hidden="true"></div>

<!-- ── Aktuelle Events ───────────────────────────────────────────── -->
<section class="container" id="events" style="padding-top:3rem;">
  <div class="section-heading reveal">
    <h2>AKTUELLE EVENTS</h2>
    <a href="/tour.php">ALLE EVENTS ANSEHEN &nbsp;→</a>
  </div>
  <div class="card-grid reveal-group">
    <?php foreach ($events as $event): ?>
      <article class="card event-card reveal">
        <?php if (!empty($event['image_path'])): ?>
          <div style="border-radius:12px;overflow:hidden;margin-bottom:0.9rem;aspect-ratio:16/9;">
            <img src="<?= e($event['image_path']) ?>" alt="<?= e($event['title']) ?>" style="width:100%;height:100%;object-fit:cover;">
          </div>
        <?php endif; ?>
        <p class="meta"><?= formatDate($event['event_date']) ?> &nbsp;·&nbsp; <?= e($event['city']) ?></p>
        <h3><?= e($event['title']) ?></h3>
        <p style="color:var(--text-soft);font-size:0.9rem;line-height:1.6;margin:0.5rem 0 0;"><?= e($event['description_short']) ?></p>
        <a class="text-link" href="/booking.php">Tickets &amp; Infos &nbsp;↗</a>
      </article>
    <?php endforeach; ?>
    <?php if (empty($events)): ?>
      <p style="color:var(--muted);">Neue Events werden bald bekannt gegeben.</p>
    <?php endif; ?>
  </div>
</section>

<div class="spray-divider" aria-hidden="true"></div>

<!-- ── Ausgewählte Kunstwerke ────────────────────────────────────── -->
<section class="container" style="padding-top:3rem;">
  <div class="section-heading reveal">
    <h2>AUSGEWÄHLTE KUNSTWERKE</h2>
    <a href="/kunstwerke.php">ALLE KUNSTWERKE ANSEHEN &nbsp;→</a>
  </div>
  <div class="artworks-grid reveal-group">
    <?php foreach ($artworks as $art): ?>
      <article class="artwork-card reveal">
        <div class="artwork-img-wrap">
          <img src="<?= e($art['image_path']) ?>" alt="<?= e($art['title']) ?>" loading="lazy">
        </div>
        <div class="artwork-card-info">
          <h3><?= e($art['title']) ?></h3>
          <?php if (!empty($art['collection_name'])): ?>
            <p class="meta" style="margin-bottom:0;"><?= e($art['collection_name']) ?></p>
          <?php endif; ?>
        </div>
      </article>
    <?php endforeach; ?>
    <?php if (empty($artworks)): ?>
      <p style="color:var(--muted);">Kunstwerke werden in Kürze hinzugefügt.</p>
    <?php endif; ?>
  </div>
</section>

<div class="spray-divider" aria-hidden="true"></div>

<!-- ── Intro / Über Shary ────────────────────────────────────────── -->
<section class="container reveal intro-block" style="padding-top:3rem;">
  <p class="kicker">ORIGINALE POP-ART VON SHARY</p>
  <h2>POP-ART VON <span class="text-pink">SHARYAR</span> <span class="text-green">AZHDARI</span></h2>
  <p class="lead center">Moderne, urbane Kunst mit starker Farbe, Emotion und internationaler Präsenz — ideal für Sammler und Designliebhaber.</p>
  <div class="cta-row" style="justify-content:center;margin-top:2rem;">
    <a class="btn btn-dark" href="https://s-art.work" target="_blank" rel="noopener">s-art.work &nbsp;↗</a>
  </div>
  <span class="deco-square" aria-hidden="true"></span>
</section>

<div class="spray-divider" aria-hidden="true"></div>

<!-- ── Newsletter ────────────────────────────────────────────────── -->
<section class="container" id="newsletter" style="padding-bottom:4rem;">
  <div class="newsletter-box reveal">
    <div style="display:flex;align-items:flex-start;gap:1.4rem;flex-wrap:wrap;">
      <div style="flex:1;min-width:240px;">
        <p class="meta">&#9993; GRATIS TICKET</p>
        <h2 style="font-size:clamp(1.5rem,4vw,2.4rem);margin-bottom:0.6rem;">GRATIS TICKET + NEWSLETTER</h2>
        <p class="lead" style="font-size:0.95rem;max-width:42ch;">Erhalte exklusive Vorverkaufs-Infos, Pop-up Termine und digitale Ticket-Freischaltung.</p>
        <form method="post" action="/newsletter-submit.php" style="margin-top:1.4rem;display:grid;gap:0.85rem;max-width:500px;">
          <?= csrfField() ?>
          <input type="hidden" name="source" value="homepage">
          <input type="email" name="email" placeholder="Deine E-Mail-Adresse" required>
          <input type="text" name="first_name" placeholder="Vorname (optional)">
          <label class="check">
            <input type="checkbox" name="consent_privacy" value="1" required>
            Ich stimme der Datenschutzerklärung zu.
          </label>
          <button class="btn btn-primary" type="submit" style="max-width:260px;">JETZT ANMELDEN &nbsp;→</button>
        </form>
      </div>
    </div>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
