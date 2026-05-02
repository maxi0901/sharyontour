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
  <div class="contact-card neon-card">
    <div class="contact-block">
      <span class="ticket-meta-label">GALERIE</span>
      <p><strong><?= e($siteConfig['contact']['gallery_name']) ?></strong></p>
      <p><?= e($siteConfig['contact']['street']) ?><br><?= e($siteConfig['contact']['postal_city']) ?></p>
    </div>
    <div class="contact-block">
      <span class="ticket-meta-label">KONTAKT</span>
      <a class="contact-link" href="mailto:<?= e($siteConfig['contact']['email']) ?>"><?= e($siteConfig['contact']['email']) ?></a>
    </div>
    <div class="contact-block">
      <span class="ticket-meta-label">SOCIAL & SHOP</span>
      <div class="social-links">
        <a class="contact-link social-link social-link--instagram" href="<?= e($siteConfig['social']['instagram']['url']) ?>" target="_blank" rel="noopener noreferrer">
          <span class="social-link__label">Instagram</span>
          <span class="social-link__handle"><?= e($siteConfig['social']['instagram']['handle']) ?></span>
        </a>
        <a class="contact-link social-link social-link--tiktok" href="<?= e($siteConfig['social']['tiktok']['url']) ?>" target="_blank" rel="noopener noreferrer">
          <span class="social-link__label">TikTok</span>
          <span class="social-link__handle"><?= e($siteConfig['social']['tiktok']['handle']) ?></span>
        </a>
      </div>
      <a class="btn btn-primary" href="<?= e($siteConfig['shop_url']) ?>" target="_blank" rel="noopener noreferrer">Zum Shop →</a>
    </div>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
