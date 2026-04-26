<?php
$pageTitle = 'Booking & Kontakt';
require __DIR__ . '/includes/header.php';
?>

<section class="container page-intro reveal">
  <p class="kicker">KONTAKT</p>
  <h1>Booking &amp; Anfragen</h1>
  <p class="subline">Du willst S-ART in deiner Stadt, auf deinem Event oder in deiner Location?</p>
</section>

<section class="container section-compact reveal">
  <div class="highlight-card neon-frame" style="padding: 1.8rem 2rem; max-width: 640px;">
    <div style="display:flex; align-items:center; gap:1rem; margin-bottom:1.2rem;">
      <div class="location-icon-box" style="color:var(--pink);">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" width="22" height="22">
          <path d="M18.37 2.63 14 7l-1.59-1.59a2 2 0 0 0-2.82 0L8 7l9 9 1.59-1.59a2 2 0 0 0 0-2.82L17 10l4.37-4.37a2.12 2.12 0 1 0-3-3z"/>
          <path d="M9 8c-2 2.5-2 5-2 5-3 0-5-2-5-2 1 1.5 2 2 2 4a2 2 0 0 0 4 0c0-1.5.5-2 1-3"/>
        </svg>
      </div>
      <h2 style="font-size:clamp(1.4rem,3vw,2rem);">DIREKTKONTAKT</h2>
    </div>

    <div style="display:grid; gap:.9rem; margin-bottom:1.4rem;">
      <a href="mailto:booking@sharyontour.example" style="display:flex; align-items:center; gap:.6rem; color:var(--pink); font-weight:700; font-size:1rem;">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" width="18" height="18"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
        booking@sharyontour.example
      </a>
      <a href="tel:+49123456789" style="display:flex; align-items:center; gap:.6rem; color:var(--green); font-weight:700; font-size:1rem;">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" width="18" height="18"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 12a19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 3.6 1.26h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L7.91 8.96a16 16 0 0 0 6 6l.96-.96a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
        +49 123 456789
      </a>
    </div>

    <div style="display:flex; gap:.6rem; flex-wrap:wrap; margin-bottom:1.4rem;">
      <a href="#" style="color:#d9dae0; font-size:.85rem; font-weight:600;">Instagram ↗</a>
      <span style="color:rgba(255,255,255,.3);">·</span>
      <a href="#" style="color:#d9dae0; font-size:.85rem; font-weight:600;">TikTok ↗</a>
      <span style="color:rgba(255,255,255,.3);">·</span>
      <a href="#" style="color:#d9dae0; font-size:.85rem; font-weight:600;">LinkedIn ↗</a>
    </div>

    <a class="btn btn-primary" href="mailto:booking@sharyontour.example?subject=Tour-Stopp%20anfragen">
      Tour-Stopp anfragen &nbsp;→
    </a>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
