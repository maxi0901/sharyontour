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
  <span class="highlight-chip">POP · URBAN · LUXURY</span>
  <h1>SHARY ON TOUR</h1>
  <p>Eine dynamische S-Art Plattform für Container-Standorte, Live-Events, Galerie-Momente und digitale Ticket-Registrierung – ohne Kaufpflicht.</p>
</section>

<section class="marquee">
  <p> S-ART TOUR · LIVE ART EXPERIENCE · CONTAINER MOVES · LIMITED DROPS · NO PURCHASE REQUIRED · </p>
  <p> S-ART TOUR · LIVE ART EXPERIENCE · CONTAINER MOVES · LIMITED DROPS · NO PURCHASE REQUIRED · </p>
</section>

<section class="section container reveal">
  <h2 class="section-title">Aktueller Standort</h2>
  <div class="location-box">
<?php if ($currentLocation): ?>
    <h3><?= e($currentLocation['title']) ?></h3>
    <p><?= e($currentLocation['description'] ?? 'Der Container ist unterwegs und bereit für die nächste Session.') ?></p>
    <p class="location-meta"><?= e($currentLocation['city']) ?> · <?= e($currentLocation['address']) ?> · <?= e($currentLocation['date_from']) ?> bis <?= e($currentLocation['date_to']) ?></p>
<?php if (!empty($currentLocation['google_maps_url'])): ?>
    <a class="btn" href="<?= e($currentLocation['google_maps_url']) ?>" target="_blank" rel="noopener">Google Maps</a>
<?php endif; ?>
<?php else: ?>
    <p>Aktuell ist noch kein Standort veröffentlicht. Folge dem Newsletter für Updates.</p>
<?php endif; ?>
  </div>
</section>

<section class="section container reveal">
  <h2 class="section-title">Tour-Idee</h2>
  <p class="intro-copy">S-Art bringt Kunst in Bewegung: wechselnde Pop-Up-Spaces, kuratierte Werke und immersive Event-Momente in urbanen Locations.</p>
</section>

<section class="section container reveal">
  <h2 class="section-title">Aktuelle Events</h2>
  <div class="grid events">
<?php foreach ($upcomingEvents as $event): ?>
    <article class="card">
      <?php if ((int) $event['is_featured'] === 1): ?><span class="badge">Featured</span><?php endif; ?>
      <h3><?= e($event['title']) ?></h3>
      <p><strong><?= e($event['event_date']) ?></strong> · <?= e($event['event_time']) ?></p>
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
  <h2 class="section-title">Ausgewählte Kunstwerke</h2>
  <div class="grid art-grid">
<?php foreach ($featuredArtworks as $art): ?>
    <article class="card art-card">
      <img src="<?= e($art['image_path'] ?: '/assets/img/placeholder-artwork.jpg') ?>" alt="<?= e($art['title']) ?>" />
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

<section class="section container reveal">
  <div class="newsletter-cta">
    <h2 class="section-title">Newsletter / Ticket sichern</h2>
    <p>Erhalte Tour-Updates und dein digitales S-Art Ticket direkt per E-Mail.</p>
    <form action="/newsletter-submit.php" method="post" class="form-grid">
      <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>" />
      <input type="hidden" name="source" value="homepage" />
      <label>E-Mail*
        <input type="email" name="email" required value="<?= e(old('email')) ?>" />
      </label>
      <label>Vorname (optional)
        <input type="text" name="first_name" value="<?= e(old('first_name')) ?>" />
      </label>
      <label style="grid-column: 1 / -1;">
        <input type="checkbox" name="consent_privacy" value="1" required /> Ich akzeptiere die Datenschutzhinweise.
      </label>
      <button type="submit">Ticket anfordern</button>
    </form>
  </div>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
