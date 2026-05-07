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

<section class="section container booking-services" aria-labelledby="booking-services-title">
  <div class="booking-services__head reveal">
    <p class="kicker">DIENSTLEISTUNGEN</p>
    <h2 id="booking-services-title">SHARY FÜR DEINEN AUFTRITT</h2>
    <p class="subline">Wähle das passende S-ART Erlebnis für dein Event, deine Marke oder deinen Standort.</p>
  </div>

  <div class="booking-service-grid">
    <article class="booking-service-card neon-card" style="--service-index: 0">
      <span class="booking-service-card__number">01</span>
      <p class="ticket-meta-label">BOOKING</p>
      <h3>Shary für dein Event</h3>
      <p>Persönlicher Auftritt von Shary mit S-ART Energie für Events, Openings, Firmenfeiern und besondere Momente.</p>
    </article>

    <article class="booking-service-card neon-card booking-service-card--wide" style="--service-index: 1">
      <span class="booking-service-card__number">02</span>
      <p class="ticket-meta-label">MOBILE GALERIE</p>
      <h3>Mobile Galerie mit 2 Containern</h3>
      <p>Zwei mobile S-ART Container als auffällige Galerie-Installation direkt auf deinem Gelände oder Event.</p>
      <ul class="booking-service-card__facts">
        <li>2 Container</li>
        <li>1× Life Action inklusive</li>
      </ul>
    </article>

    <article class="booking-service-card neon-card" style="--service-index: 2">
      <span class="booking-service-card__number">03</span>
      <p class="ticket-meta-label">LIVE PERFORMANCE</p>
      <h3>Life Action von Shary</h3>
      <p>Shary gestaltet live vor Ort und macht den kreativen Prozess zum Highlight deines Events.</p>
    </article>

    <article class="booking-service-card neon-card" style="--service-index: 3">
      <span class="booking-service-card__number">04</span>
      <p class="ticket-meta-label">BRAND CUSTOMIZING</p>
      <h3>Personalisierung deiner Marke</h3>
      <p>Individuelle S-ART Veredelung für Produkte und Markenauftritte – z. B. Schaumwein besprühen wie beim Messinghof.</p>
    </article>

    <article class="booking-service-card neon-card" style="--service-index: 4">
      <span class="booking-service-card__number">05</span>
      <p class="ticket-meta-label">AUTOMATEN VERMIETUNG</p>
      <h3>S-Art Automaten</h3>
      <p>Vermietung von S-ART Automaten für Skull Liquid und S-ART Produkte als interaktiver Blickfang.</p>
    </article>
  </div>
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
