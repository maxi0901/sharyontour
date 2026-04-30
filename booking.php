<?php
$pageTitle = 'Kontakt · S-ART';
$siteConfig = require __DIR__ . '/includes/site-config.php';
require __DIR__ . '/includes/header.php';
?>

<section class="section container page-intro reveal">
  <p class="kicker">KONTAKT</p>
  <h1>BOOKING &amp;<br><span class="text-red">ANFRAGEN</span></h1>
  <p class="subline">Du willst S-ART in deiner Stadt, deinem Event oder deiner Location? Hier findest du alle offiziellen Kontaktwege.</p>
</section>

<section class="section container reveal">
  <div class="contact-card">
    <div class="contact-block">
      <span class="ticket-meta-label">GALERIE</span>
      <p><strong><?= e($siteConfig['contact']['gallery_name']) ?></strong></p>
      <p><?= e($siteConfig['contact']['street']) ?><br><?= e($siteConfig['contact']['postal_city']) ?></p>
    </div>
    <div class="contact-block">
      <span class="ticket-meta-label">KONTAKT</span>
      <a class="contact-link" href="tel:<?= e($siteConfig['contact']['phone_href']) ?>"><?= e($siteConfig['contact']['phone_display']) ?></a>
      <a class="contact-link" href="mailto:<?= e($siteConfig['contact']['email']) ?>"><?= e($siteConfig['contact']['email']) ?></a>
    </div>
    <div class="contact-block">
      <span class="ticket-meta-label">ÖFFNUNGSZEITEN</span>
      <?php foreach ($siteConfig['opening_hours'] as $day => $hours): ?>
        <p><strong><?= e($day) ?>:</strong> <?= e($hours) ?></p>
      <?php endforeach; ?>
    </div>
    <div class="contact-block">
      <span class="ticket-meta-label">SOCIAL & SHOP</span>
      <a class="contact-link" href="<?= e($siteConfig['social']['instagram']['url']) ?>" target="_blank" rel="noopener noreferrer">Instagram <?= e($siteConfig['social']['instagram']['handle']) ?></a>
      <a class="contact-link" href="<?= e($siteConfig['social']['tiktok']['url']) ?>" target="_blank" rel="noopener noreferrer">TikTok <?= e($siteConfig['social']['tiktok']['handle']) ?></a>
      <a class="btn btn-primary" href="<?= e($siteConfig['shop_url']) ?>" target="_blank" rel="noopener noreferrer">Zum Shop →</a>
    </div>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
