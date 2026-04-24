<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/functions.php';

$currentPage = 'home';
$pageTitle = 'Shary on Tour / S-Art';
$upcomingEvents = fetch_events('upcoming', 6);
$featuredArtworks = fetch_featured_artworks(4);
$currentLocation = fetch_current_location();

require __DIR__ . '/includes/header.php';
?>
<section class="hero container reveal">
  <div class="hero-grid">
    <div>
      <span class="highlight-chip">MOBILE ART EXPERIENCE</span>
      <h1>Street Culture trifft kuratierte S-Art.</h1>
      <p>Shary on Tour macht aus jedem Standort einen temporären Creative-Hub: mit Live-Installationen, Signature-Werken und digitalen Tickets ohne Kaufzwang.</p>
      <div class="hero-actions">
        <a class="btn btn-primary" href="/tour.php">Nächste Stopps</a>
        <a class="btn btn-secondary" href="#newsletter">Ticket sichern</a>
      </div>
      <ul class="hero-facts" aria-label="Highlights">
        <li><strong>20+</strong><span>Pop-Up Cities</span></li>
        <li><strong>100%</strong><span>kostenlose Registrierung</span></li>
        <li><strong>Live</strong><span>Art, Talks & Drops</span></li>
      </ul>
    </div>
    <aside class="hero-panel">
      <h2>Jetzt unterwegs</h2>
<?php if ($currentLocation): ?>
      <h3><?= e($currentLocation['title']) ?></h3>
      <p><?= e($currentLocation['city']) ?> · <?= e($currentLocation['address']) ?></p>
      <p class="mono"><?= e($currentLocation['date_from']) ?> — <?= e($currentLocation['date_to']) ?></p>
<?php if (!empty($currentLocation['google_maps_url'])): ?>
      <a class="btn btn-secondary" href="<?= e($currentLocation['google_maps_url']) ?>" target="_blank" rel="noopener">Route öffnen</a>
<?php endif; ?>
<?php else: ?>
      <p>Der nächste Stop wird gerade vorbereitet. Trag dich im Newsletter ein, um zuerst informiert zu werden.</p>
<?php endif; ?>
    </aside>
  </div>
</section>

<section class="marquee" aria-label="Ticker">
  <p>S-ART TOUR · LIVE ART EXPERIENCE · CONTAINER MOVES · LIMITED DROPS · NO PURCHASE REQUIRED ·</p>
  <p>S-ART TOUR · LIVE ART EXPERIENCE · CONTAINER MOVES · LIMITED DROPS · NO PURCHASE REQUIRED ·</p>
</section>

<section class="section container reveal">
  <div class="section-head">
    <h2 class="section-title">Was dich erwartet</h2>
    <p>Jede City ist anders – der Spirit bleibt gleich: mutig, urban, gemeinschaftlich.</p>
  </div>
  <div class="experience-grid">
    <article class="card tone-yellow">
      <h3>Live Art Sessions</h3>
      <p>Künstler:innen arbeiten vor Ort, erklären Techniken und öffnen den Prozess für das Publikum.</p>
    </article>
    <article class="card tone-pink">
      <h3>Curated Exhibits</h3>
      <p>Ausgewählte S-Art Serien verbinden Pop-Elemente mit moderner Luxus-Ästhetik.</p>
    </article>
    <article class="card tone-blue">
      <h3>Community & Talks</h3>
      <p>Q&A, Networking und Mini-Workshops schaffen echte Begegnungen statt Distanz.</p>
    </article>
  </div>
</section>

<section class="section container reveal">
  <div class="section-head">
    <h2 class="section-title">Nächste Events</h2>
    <a href="/tour.php" class="text-link">Alle Termine ansehen</a>
  </div>
  <div class="grid events">
<?php foreach ($upcomingEvents as $event): ?>
    <article class="card event-card">
      <?php if ((int) $event['is_featured'] === 1): ?><span class="badge">Featured</span><?php endif; ?>
      <h3><?= e($event['title']) ?></h3>
      <p class="mono"><?= e($event['event_date']) ?> · <?= e($event['event_time']) ?></p>
      <p><?= e($event['city']) ?> · <?= e($event['location_name']) ?></p>
      <p><?= e($event['description_short']) ?></p>
    </article>
<?php endforeach; ?>
<?php if (!$upcomingEvents): ?>
    <p>Neue Tourdaten werden bald angekündigt.</p>
<?php endif; ?>
  </div>
</section>

<section class="section container reveal">
  <div class="section-head">
    <h2 class="section-title">Featured Artworks</h2>
    <a href="/kunstwerke.php" class="text-link">Zur gesamten Galerie</a>
  </div>
  <div class="grid art-grid">
<?php foreach ($featuredArtworks as $art): ?>
    <article class="card art-card">
      <img src="<?= e($art['image_path'] ?: '/assets/img/placeholder-artwork.jpg') ?>" alt="<?= e($art['title']) ?>" loading="lazy" />
      <h3><?= e($art['title']) ?></h3>
      <p><?= e($art['description']) ?></p>
      <p class="art-meta"><?= e($art['collection_name']) ?> · <?= e((string) $art['year']) ?></p>
    </article>
<?php endforeach; ?>
<?php if (!$featuredArtworks): ?>
    <p>Galerie-Inhalte folgen in Kürze.</p>
<?php endif; ?>
  </div>
</section>

<section class="section container reveal" id="newsletter">
  <div class="newsletter-cta">
    <div>
      <h2 class="section-title">Newsletter & Digitales Ticket</h2>
      <p>Erhalte Tour-Updates, Standortwechsel und exklusive Event-Hinweise direkt per E-Mail.</p>
    </div>
    <form action="/newsletter-submit.php" method="post" class="form-grid">
      <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>" />
      <input type="hidden" name="source" value="homepage" />
      <label>E-Mail*
        <input type="email" name="email" required value="<?= e(old('email')) ?>" autocomplete="email" />
      </label>
      <label>Vorname (optional)
        <input type="text" name="first_name" value="<?= e(old('first_name')) ?>" autocomplete="given-name" />
      </label>
      <label class="full-width">
        <input type="checkbox" name="consent_privacy" value="1" required /> Ich akzeptiere die Datenschutzhinweise.
      </label>
      <button type="submit">Jetzt registrieren</button>
    </form>
  </div>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
