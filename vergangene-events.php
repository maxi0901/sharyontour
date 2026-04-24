<?php
$pageTitle = 'Vergangene Events';
require __DIR__ . '/includes/header.php';
$events = fetchAll("SELECT * FROM events WHERE status='past' ORDER BY event_date DESC");
?>
<section class="container page-intro reveal"><h1>Vergangene Events</h1></section>
<section class="container reveal"><div class="card-grid"><?php foreach ($events as $event): ?><article class="card"><?php if ($event['image_path']): ?><img src="<?= e($event['image_path']) ?>" alt="<?= e($event['title']) ?>"><?php endif; ?><p class="meta"><?= formatDate($event['event_date']) ?> · <?= e($event['city']) ?></p><h3><?= e($event['title']) ?></h3><p><?= e($event['description_short']) ?></p></article><?php endforeach; ?></div></section>
<?php require __DIR__ . '/includes/footer.php'; ?>
