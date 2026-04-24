<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/functions.php';

$currentPage = 'tour';
$pageTitle = 'S-Art Tour Standorte';
$stmt = db()->query("SELECT * FROM tour_locations WHERE status IN ('current','upcoming','past') ORDER BY FIELD(status,'current','upcoming','past'), date_from ASC");
$locations = $stmt->fetchAll();

require __DIR__ . '/includes/header.php';
?>
<section class="section container reveal">
  <h1 class="section-title">Tour & Container-Standorte</h1>
  <div class="grid events">
<?php foreach ($locations as $location): ?>
    <article class="card">
      <span class="badge"><?= e(strtoupper($location['status'])) ?></span>
      <h3><?= e($location['title']) ?></h3>
      <p><?= e($location['city']) ?> · <?= e($location['address']) ?></p>
      <p><?= e($location['date_from']) ?> bis <?= e($location['date_to']) ?></p>
      <p><?= e($location['description']) ?></p>
    </article>
<?php endforeach; ?>
  </div>
</section>

<section class="section container reveal">
  <div class="newsletter-cta">
    <h2 class="section-title">Tour-Alerts erhalten</h2>
    <form action="/newsletter-submit.php" method="post" class="form-grid">
      <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>" />
      <input type="hidden" name="source" value="tour-page" />
      <label>E-Mail*
        <input type="email" name="email" required />
      </label>
      <label>Vorname
        <input type="text" name="first_name" />
      </label>
      <label style="grid-column: 1 / -1;"><input type="checkbox" name="consent_privacy" value="1" required /> Datenschutz akzeptieren</label>
      <button type="submit">Ticket sichern</button>
    </form>
  </div>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
