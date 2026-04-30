<?php
require __DIR__ . '/config/bootstrap.php';

$pageTitle = 'S-ART · Shary on Tour';
require __DIR__ . '/includes/header.php';

$today = date('Y-m-d');
$opening = getOpeningEvent();

$upcomingEvents = fetchAll(
    "SELECT * FROM events WHERE status='upcoming' AND event_date >= :today ORDER BY event_date ASC"
    , ['today' => $today]
);
$pastEvents = fetchAll(
    "SELECT * FROM events WHERE status='past' OR (event_date < :today) ORDER BY event_date DESC LIMIT 6"
    , ['today' => $today]
);

$soldTickets = $opening ? countTicketsForEvent((int) $opening['id']) : 0;
$maxTickets = $opening ? (int) $opening['max_tickets'] : 600;
$remaining = max(0, $maxTickets - $soldTickets);
$lowStock = $remaining > 0 && $remaining < 100;
$soldOut = $remaining <= 0;
?>

<section class="hero">
  <div class="hero-bg" aria-hidden="true">
    <div class="hero-bg-grid"></div>
    <div class="hero-bg-stripe"></div>
  </div>
  <div class="container hero-inner">
    <div class="hero-copy reveal">
      <p class="kicker">URBAN ART MOVEMENT</p>
      <h1>POP-ART<br><span class="text-red">GOES VIRAL</span></h1>
      <p class="subline">Cinematic Street-Art Weg von Sharyar Azhdari. Bekannt aus „Die Geissens". Live, laut und limitiert.</p>
      <div class="cta-row">
        <?php if ($opening): ?>
          <a class="btn btn-primary" href="/ticket-buchen.php">GRATIS TICKET SICHERN →</a>
        <?php endif; ?>
        <a class="btn btn-ghost" href="#events">EVENTS ENTDECKEN →</a>
      </div>
      <?php if ($opening): ?>
        <div class="hero-ticker <?= $soldOut ? 'is-sold' : ($lowStock ? 'is-low' : '') ?>">
          <span class="dot"></span>
          <?php if ($soldOut): ?>
            <strong>AUSVERKAUFT</strong> · Container Opening Kassel
          <?php else: ?>
            <strong>Noch <?= $remaining ?> von <?= $maxTickets ?> Tickets</strong> · Container Opening Kassel
          <?php endif; ?>
        </div>
      <?php endif; ?>
    </div>
  </div>
</section>

<?php if ($opening): ?>
<section class="section container reveal" id="opening">
  <a class="opening-banner" href="/ticket-buchen.php">
    <div class="opening-banner-inner">
      <div>
        <p class="kicker">HAUPT-EVENT</p>
        <h2>CONTAINER OPENING<br><span class="text-red">KASSEL</span></h2>
        <p class="opening-meta"><?= formatDateLong($opening['event_date']) ?> · Kassel</p>
        <p class="opening-note">Der genaue Standort wird rechtzeitig bekanntgegeben.</p>
      </div>
      <div class="opening-cta">
        <?php if ($soldOut): ?>
          <span class="btn btn-disabled">AUSVERKAUFT</span>
        <?php else: ?>
          <span class="btn btn-primary">GRATIS TICKET →</span>
          <p class="opening-stock <?= $lowStock ? 'is-low' : '' ?>">
            Noch <?= $remaining ?> von <?= $maxTickets ?> verfügbar
          </p>
        <?php endif; ?>
      </div>
    </div>
  </a>
</section>
<?php endif; ?>

<section class="section container" id="events">
  <div class="section-heading reveal">
    <h2>EVENTS</h2>
    <a href="/tour.php" class="text-link">ALLE EVENTS →</a>
  </div>

  <?php
    $allSliderEvents = $upcomingEvents;
    foreach ($pastEvents as $p) {
        $allSliderEvents[] = $p;
    }
  ?>

  <?php if (!empty($allSliderEvents)): ?>
    <div class="event-slider reveal" data-event-slider>
      <div class="event-slider-track">
        <?php foreach ($allSliderEvents as $idx => $ev):
          $isPast = $ev['status'] === 'past' || $ev['event_date'] < $today;
          $isOpening = (int) $ev['is_opening'] === 1;
        ?>
          <article class="event-slide <?= $isPast ? 'is-past' : '' ?> <?= $isOpening ? 'is-opening' : '' ?>" data-slide-index="<?= $idx ?>">
            <?php if ($isOpening): ?>
              <span class="badge badge-opening">HAUPT-EVENT</span>
            <?php elseif ($isPast): ?>
              <span class="badge badge-past">VERGANGEN</span>
            <?php else: ?>
              <span class="badge badge-upcoming">KOMMEND</span>
            <?php endif; ?>

            <div class="event-slide-media">
              <?php if (!empty($ev['image_path']) && file_exists($_SERVER['DOCUMENT_ROOT'] . $ev['image_path'])): ?>
                <img src="<?= e($ev['image_path']) ?>" alt="<?= e($ev['title']) ?>" loading="lazy">
              <?php else: ?>
                <div class="event-slide-placeholder">
                  <span><?= e(strtoupper(substr($ev['title'], 0, 2))) ?></span>
                </div>
              <?php endif; ?>
            </div>

            <div class="event-slide-content">
              <p class="meta"><?= formatDate($ev['event_date']) ?> · <?= e($ev['city']) ?></p>
              <h3><?= e($ev['title']) ?></h3>
              <p class="event-slide-desc"><?= e($ev['description_short']) ?></p>

              <div class="event-slide-actions">
                <?php if ($isOpening && !$soldOut): ?>
                  <a class="btn btn-primary btn-sm" href="/ticket-buchen.php">Gratis Ticket →</a>
                <?php elseif ($isPast): ?>
                  <a class="btn btn-ghost btn-sm" href="/galerie.php?event=<?= (int) $ev['id'] ?>">Bildergalerie →</a>
                <?php elseif (!empty($ev['google_maps_url'])): ?>
                  <a class="btn btn-ghost btn-sm" target="_blank" rel="noopener" href="<?= e($ev['google_maps_url']) ?>">Standort öffnen →</a>
                <?php endif; ?>
              </div>
            </div>
          </article>
        <?php endforeach; ?>
      </div>

      <button class="event-slider-nav prev" type="button" aria-label="Vorheriges Event">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" width="20" height="20"><polyline points="15 18 9 12 15 6"/></svg>
      </button>
      <button class="event-slider-nav next" type="button" aria-label="Nächstes Event">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" width="20" height="20"><polyline points="9 18 15 12 9 6"/></svg>
      </button>

      <div class="event-slider-dots" role="tablist">
        <?php foreach ($allSliderEvents as $idx => $ev): ?>
          <button class="event-slider-dot<?= $idx === 0 ? ' is-active' : '' ?>" data-dot="<?= $idx ?>" aria-label="Event <?= $idx + 1 ?>"></button>
        <?php endforeach; ?>
      </div>
    </div>
  <?php else: ?>
    <p class="muted">Aktuell sind keine Events angekündigt.</p>
  <?php endif; ?>
</section>

<section class="section container reveal" id="story">
  <div class="story-grid">
    <div>
      <p class="kicker">SHARY ON TOUR</p>
      <h2>CINEMATIC<br>STREET-ART WEG</h2>
    </div>
    <div class="story-text">
      <p>Pop-Art, Container-Kultur und Live-Erlebnisse — von der Vernissage bis zum großen Container Opening in Kassel. Bekannt aus „Die Geissens", inszeniert wie eine Premiere.</p>
      <p>Jeder Stopp ist ein Statement. Jedes Werk ein Snapshot urbaner Energie.</p>
    </div>
  </div>
</section>

<section class="section container" id="newsletter">
  <div class="newsletter-box reveal">
    <div class="newsletter-left">
      <p class="kicker">BLEIB IM LOOP</p>
      <h2>INFORMIERE MICH,<br>WENN SHARY IN<br><span class="text-red">MEINER NÄHE</span> IST</h2>
    </div>
    <form method="post" action="/newsletter-submit.php" class="newsletter-form">
      <?= csrfField() ?>
      <label class="field">
        <span>E-Mail</span>
        <input type="email" name="email" placeholder="deine@email.de" required>
      </label>
      <label class="field">
        <span>PLZ / Stadt (optional)</span>
        <input type="text" name="location_optional" placeholder="z. B. 34117 Kassel">
      </label>
      <label class="check">
        <input type="checkbox" name="consent_privacy" value="1" required>
        Ich stimme der <a href="/datenschutz.php" class="privacy-link">Datenschutzerklärung</a> zu.
      </label>
      <button class="btn btn-primary" type="submit">JETZT ANMELDEN →</button>
    </form>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
