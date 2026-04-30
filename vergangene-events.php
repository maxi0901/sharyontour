<?php
require __DIR__ . '/config/bootstrap.php';

$pageTitle = 'Galerie · S-ART';
require __DIR__ . '/includes/header.php';

$today = date('Y-m-d');
$events = fetchAll("SELECT e.*, COUNT(g.id) AS image_count
                    FROM events e
                    LEFT JOIN galleries g ON g.event_id = e.id
                    WHERE e.status='past' OR e.event_date < :t
                    GROUP BY e.id
                    ORDER BY e.event_date DESC", ['t' => $today]);
?>

<section class="section container page-intro reveal">
  <p class="kicker">ARCHIV</p>
  <h1>GALERIE</h1>
  <p class="subline">Bilder und Eindrücke aus vergangenen S-ART Events.</p>
</section>

<section class="section container reveal">
  <?php if (!empty($events)): ?>
    <div class="gallery-overview">
      <?php foreach ($events as $ev):
        $cover = fetchOne('SELECT image_path FROM galleries WHERE event_id=:e ORDER BY sort_order ASC, id ASC LIMIT 1', ['e' => $ev['id']]);
        $coverImg = $cover['image_path'] ?? $ev['image_path'] ?? null;
      ?>
        <a class="gallery-overview-item" href="/galerie.php?event=<?= (int) $ev['id'] ?>">
          <div class="gallery-overview-media">
            <?php if ($coverImg && file_exists($_SERVER['DOCUMENT_ROOT'] . $coverImg)): ?>
              <img src="<?= e($coverImg) ?>" alt="<?= e($ev['title']) ?>" loading="lazy">
            <?php else: ?>
              <div class="event-slide-placeholder"><span><?= e(strtoupper(substr($ev['title'], 0, 2))) ?></span></div>
            <?php endif; ?>
          </div>
          <div class="gallery-overview-body">
            <p class="meta"><?= formatDate($ev['event_date']) ?> · <?= e($ev['city']) ?></p>
            <h3><?= e($ev['title']) ?></h3>
            <p class="muted"><?= (int) $ev['image_count'] ?> Bilder</p>
          </div>
        </a>
      <?php endforeach; ?>
    </div>
  <?php else: ?>
    <p class="muted">Noch keine vergangenen Events vorhanden.</p>
  <?php endif; ?>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
