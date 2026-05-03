<?php
$cookieConsentConfig = [
    'storageKey' => 'sart_cookie_consent',
    'version' => '1.0',
    'expiryDays' => 183,
    'privacyUrl' => '/datenschutz.php',
];
?>
<div class="cookie-consent" data-cookie-consent hidden>
  <div class="cookie-consent__backdrop" data-cookie-settings-open></div>
  <section class="cookie-consent__panel" role="dialog" aria-modal="true" aria-labelledby="cookieConsentTitle">
    <h2 id="cookieConsentTitle">Cookie-Einstellungen</h2>
    <p>
      Wir verwenden technisch notwendige Cookies, damit diese Website sicher funktioniert.
      Optionale Cookies für Statistik oder Marketing werden nur nach Ihrer aktiven Zustimmung geladen.
      Weitere Informationen finden Sie in unserer
      <a href="/datenschutz.php">Datenschutzerklärung</a>.
    </p>

    <div class="cookie-consent__categories" data-cookie-categories hidden>
      <label class="cookie-consent__category is-required">
        <span>
          <strong>Notwendig</strong>
          <small>Session, CSRF-Schutz, Login/Admin und Speicherung Ihrer Cookie-Entscheidung.</small>
        </span>
        <input type="checkbox" checked disabled aria-disabled="true">
      </label>
      <label class="cookie-consent__category">
        <span>
          <strong>Statistik</strong>
          <small>Aktuell nicht aktiv. Vorbereitung für spätere Statistik-Tools.</small>
        </span>
        <input type="checkbox" data-cookie-toggle="statistics">
      </label>
      <label class="cookie-consent__category">
        <span>
          <strong>Marketing / externe Medien</strong>
          <small>Aktuell nicht aktiv. Vorbereitung für TikTok, Instagram, Meta Pixel etc.</small>
        </span>
        <input type="checkbox" data-cookie-toggle="marketing">
      </label>
    </div>

    <div class="cookie-consent__actions">
      <button type="button" class="btn btn-ghost" data-cookie-action="settings">Einstellungen</button>
      <button type="button" class="btn btn-ghost" data-cookie-action="reject">Ablehnen</button>
      <button type="button" class="btn btn-primary" data-cookie-action="save" hidden>Auswahl speichern</button>
      <button type="button" class="btn btn-primary" data-cookie-action="accept">Alle akzeptieren</button>
    </div>
  </section>
</div>

<script>
window.SART_COOKIE_CONSENT_CONFIG = <?= json_encode($cookieConsentConfig, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?>;
</script>
