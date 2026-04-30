<?php $siteConfig = $siteConfig ?? (require __DIR__ . '/site-config.php'); ?>
</main>
<footer class="site-footer">
  <div class="container footer-grid">
    <div class="footer-brand">
      <img src="/assets/img/s-art-logo.svg" alt="S-ART" class="footer-logo">
      <p class="footer-brand-copy">Pop-Art goes viral. Bekannt aus „Die Geissens".</p>
    </div>
    <div class="footer-links">
      <a href="/impressum.php">Impressum</a>
      <a href="/datenschutz.php">Datenschutz</a>
      <a href="/booking.php">Kontakt</a>
      <a href="<?= e($siteConfig['shop_url']) ?>" target="_blank" rel="noopener noreferrer">Shop</a>
    </div>
    <div class="footer-contact">
      <p><strong><?= e($siteConfig['contact']['gallery_name']) ?></strong></p>
      <p><?= e($siteConfig['contact']['street']) ?><br><?= e($siteConfig['contact']['postal_city']) ?></p>
      <p><a href="tel:<?= e($siteConfig['contact']['phone_href']) ?>"><?= e($siteConfig['contact']['phone_display']) ?></a></p>
      <p><a href="mailto:<?= e($siteConfig['contact']['email']) ?>"><?= e($siteConfig['contact']['email']) ?></a></p>
      <?php foreach ($siteConfig['opening_hours'] as $day => $hours): ?>
        <p><span><?= e($day) ?>:</span> <?= e($hours) ?></p>
      <?php endforeach; ?>
    </div>
    <div class="footer-social" aria-label="Social Links">
      <a href="<?= e($siteConfig['social']['instagram']['url']) ?>" target="_blank" rel="noopener noreferrer" aria-label="Instagram">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"/><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/></svg>
      </a>
      <a href="<?= e($siteConfig['social']['tiktok']['url']) ?>" target="_blank" rel="noopener noreferrer" aria-label="TikTok">
        <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M19.59 6.69a4.83 4.83 0 0 1-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 0 1-2.88 2.5 2.89 2.89 0 0 1-2.89-2.89 2.89 2.89 0 0 1 2.89-2.89c.28 0 .54.04.79.1V9.01a6.27 6.27 0 0 0-.79-.05 6.34 6.34 0 0 0-6.34 6.34 6.34 6.34 0 0 0 6.34 6.34 6.34 6.34 0 0 0 6.33-6.34V8.69a8.18 8.18 0 0 0 4.78 1.52V6.75a4.85 4.85 0 0 1-1.01-.06z"/></svg>
      </a>
    </div>
  </div>
  <div class="container footer-meta"><p>&copy; <?= date('Y') ?> S-ART · Shary on Tour</p></div>
</footer>
<script src="/assets/js/main.js" defer></script>
</body>
</html>
