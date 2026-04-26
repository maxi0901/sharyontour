<?php
$pageTitle = 'Kunstwerke';
require __DIR__ . '/includes/header.php';
$artworks = fetchAll("SELECT * FROM artworks WHERE is_visible=1 ORDER BY sort_order ASC, created_at DESC");
?>

<section class="container page-intro reveal">
  <p class="kicker">KOLLEKTION</p>
  <h1>Kunstwerke</h1>
  <p class="subline">Pop-Art trifft Street-Art Energy – die gesamte Kollektion von Sharyar Azhdari.</p>
</section>

<section class="container reveal">
  <div class="artworks-grid">
    <?php foreach ($artworks as $art): ?>
      <article class="artwork-card">
        <div class="artwork-img-wrap">
          <?php if (!empty($art['image_path'])): ?>
            <img src="<?= e($art['image_path']) ?>" alt="<?= e($art['title']) ?>" loading="lazy">
          <?php else: ?>
            <div class="media-fallback"></div>
          <?php endif; ?>
        </div>
        <?php if (!empty($art['title'])): ?>
          <div style="padding: .6rem .75rem .75rem;">
            <h3 style="font-size: .95rem; margin-bottom: .2rem;"><?= e($art['title']) ?></h3>
            <?php if (!empty($art['collection_name']) || !empty($art['year'])): ?>
              <p class="meta" style="font-size: .72rem; margin: 0;"><?= e($art['collection_name'] ?? '') ?><?= (!empty($art['collection_name']) && !empty($art['year'])) ? ' · ' : '' ?><?= e($art['year'] ?? '') ?></p>
            <?php endif; ?>
          </div>
        <?php endif; ?>
      </article>
    <?php endforeach; ?>
    <?php if (empty($artworks)): ?>
      <p class="muted">Kunstwerke werden in Kürze hinzugefügt.</p>
    <?php endif; ?>
  </div>
</section>

<section class="container" id="newsletter">
  <div class="newsletter-box reveal neon-frame">
    <div class="newsletter-left">
      <div class="newsletter-icon-box">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round" width="26" height="26" aria-hidden="true">
          <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
          <polyline points="22,6 12,13 2,6"/>
        </svg>
      </div>
      <div>
        <h2>GRATIS TICKET + NEWSLETTER</h2>
        <p>Erhalte exklusive Vorverkaufs-Infos, Pop-up Termine und digitale Ticket-Freischaltung.</p>
      </div>
    </div>
    <form method="post" action="/newsletter-submit.php" class="newsletter-form">
      <?= csrfField() ?>
      <input type="hidden" name="source" value="kunstwerke">
      <input type="hidden" name="first_name" value="">
      <div class="newsletter-email-row">
        <input type="email" name="email" placeholder="Deine E-Mail-Adresse" required>
        <button class="btn btn-primary" type="submit">JETZT ANMELDEN</button>
      </div>
      <label class="check">
        <input type="checkbox" name="consent_privacy" value="1" required>
        Ich stimme der <a href="/datenschutz.php" style="color:var(--pink)">Datenschutzerklärung</a> zu.
      </label>
    </form>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
