<?php
require __DIR__ . '/config/bootstrap.php';

$pageTitle = 'Events · S-ART';
require __DIR__ . '/includes/header.php';

$today = date('Y-m-d');
$upcoming = fetchAll("SELECT * FROM events WHERE status='upcoming' AND event_date >= :t ORDER BY event_date ASC", ['t' => $today]);
$past = fetchAll("SELECT * FROM events WHERE status='past' OR event_date < :t ORDER BY event_date DESC", ['t' => $today]);
?>

<section class="section container page-intro reveal">
  <p class="kicker">SHARY ON TOUR</p>
  <h1>EVENTS</h1>
  <p class="subline">Alle Stopps der Tour 2026 — vom Pop-up Maifest bis zum großen Container Opening Kassel.</p>
</section>

<?php if ($upcoming): ?>
<section class="section container reveal">
  <div class="section-heading"><h2>KOMMEND</h2></div>

  <div class="events-list">
    <?php foreach ($upcoming as $ev):
      $isOpening = (int) $ev['is_opening'] === 1;
      $isTicketOpening = $isOpening;
    ?>
      <article class="events-item <?= $isOpening ? 'is-opening' : '' ?>">
        <div class="events-item-date">
          <strong><?= formatDate($ev['event_date']) ?></strong>
          <span><?= e($ev['city']) ?></span>
        </div>
        <div class="events-item-body">
          <?php if ($isOpening): ?><span class="badge badge-opening">HAUPT-EVENT</span><?php endif; ?>
          <h3><?= e($ev['title']) ?></h3>
          <p><?= e($ev['description_short']) ?></p>

          <div class="events-item-actions">
            <button
              class="btn btn-ghost btn-sm js-location-btn"
              type="button"
              data-event-name="<?= e($ev['title']) ?>"
              data-event-location="<?= e((string) ($ev['location_name'] ?: $ev['city'])) ?>"
              data-event-address="<?= e((string) ($ev['address'] ?? '')) ?>"
              data-event-is-opening="<?= $isOpening ? '1' : '0' ?>"
              data-event-ticket-opening="<?= $isTicketOpening ? '1' : '0' ?>"
            >Standort</button>
            <?php if ($isTicketOpening): ?>
                <button class="btn btn-primary btn-sm js-ticket-btn" type="button" data-event-id="<?= (int) $ev['id'] ?>">Gratis Ticket sichern</button>
                <span class="muted js-ticket-stock" data-event-id="<?= (int) $ev['id'] ?>"></span>
            <?php endif; ?>
          </div>
        </div>
      </article>
    <?php endforeach; ?>
  </div>
</section>
<?php endif; ?>

<?php if ($past): ?>
<section class="section container reveal">
  <div class="section-heading"><h2>VERGANGEN</h2></div>

  <div class="events-list events-list-past">
    <?php foreach ($past as $ev): ?>
      <a class="events-item is-past" href="/galerie.php?event=<?= (int) $ev['id'] ?>">
        <div class="events-item-date">
          <strong><?= formatDate($ev['event_date']) ?></strong>
          <span><?= e($ev['city']) ?></span>
        </div>
        <div class="events-item-body">
          <span class="badge badge-past">VERGANGEN</span>
          <h3><?= e($ev['title']) ?></h3>
          <p><?= e($ev['description_short']) ?></p>
          <span class="text-link">Bildergalerie ansehen →</span>
        </div>
      </a>
    <?php endforeach; ?>
  </div>
</section>
<?php endif; ?>

<?php require __DIR__ . '/includes/footer.php'; ?>
