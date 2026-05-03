(function () {
  'use strict';

  var config = window.SART_COOKIE_CONSENT_CONFIG || {};
  var storageKey = config.storageKey || 'sart_cookie_consent';
  var version = config.version || '1.0';
  var expiryDays = Number(config.expiryDays || 183);
  var expiryMs = expiryDays * 24 * 60 * 60 * 1000;

  var root = document.querySelector('[data-cookie-consent]');
  if (!root) return;

  var categories = root.querySelector('[data-cookie-categories]');
  var saveBtn = root.querySelector('[data-cookie-action="save"]');
  var settingsBtn = root.querySelector('[data-cookie-action="settings"]');
  var toggles = {
    statistics: root.querySelector('[data-cookie-toggle="statistics"]'),
    marketing: root.querySelector('[data-cookie-toggle="marketing"]')
  };

  function nowTs() {
    return Math.floor(Date.now() / 1000);
  }

  function defaultConsent() {
    return {
      necessary: true,
      statistics: false,
      marketing: false,
      timestamp: nowTs(),
      version: version
    };
  }

  function isValidConsent(data) {
    if (!data || typeof data !== 'object') return false;
    if (data.necessary !== true) return false;
    if (typeof data.statistics !== 'boolean' || typeof data.marketing !== 'boolean') return false;
    if (typeof data.timestamp !== 'number') return false;
    if (!data.version) return false;

    var ageMs = Date.now() - (data.timestamp * 1000);
    if (data.version !== version) return false;
    return ageMs >= 0 && ageMs <= expiryMs;
  }

  function readConsent() {
    try {
      var raw = localStorage.getItem(storageKey);
      return raw ? JSON.parse(raw) : null;
    } catch (e) {
      return null;
    }
  }

  function writeConsent(consent) {
    try {
      localStorage.setItem(storageKey, JSON.stringify(consent));
    } catch (e) {
      // no-op for private mode limitations
    }
  }

  function showBanner() {
    root.hidden = false;
  }

  function hideBanner() {
    root.hidden = true;
  }

  function openSettings() {
    categories.hidden = false;
    saveBtn.hidden = false;
    settingsBtn.hidden = true;
  }

  function closeSettings() {
    categories.hidden = true;
    saveBtn.hidden = true;
    settingsBtn.hidden = false;
  }

  function applyConsent(consent) {
    window.dispatchEvent(new CustomEvent('sart:cookie-consent-updated', { detail: consent }));
    if (consent.statistics) {
      // Placeholder: load statistics scripts only after explicit consent.
    }
    if (consent.marketing) {
      // Placeholder: load marketing/external media scripts only after explicit consent.
    }
  }

  function saveConsent(partial) {
    var consent = {
      necessary: true,
      statistics: !!partial.statistics,
      marketing: !!partial.marketing,
      timestamp: nowTs(),
      version: version
    };
    writeConsent(consent);
    applyConsent(consent);
    hideBanner();
    closeSettings();
  }

  root.addEventListener('click', function (event) {
    var button = event.target.closest('[data-cookie-action]');
    if (!button) return;
    var action = button.getAttribute('data-cookie-action');

    if (action === 'settings') openSettings();
    if (action === 'reject') saveConsent({ statistics: false, marketing: false });
    if (action === 'accept') saveConsent({ statistics: true, marketing: true });
    if (action === 'save') {
      saveConsent({
        statistics: !!(toggles.statistics && toggles.statistics.checked),
        marketing: !!(toggles.marketing && toggles.marketing.checked)
      });
    }
  });

  document.addEventListener('click', function (event) {
    var opener = event.target.closest('[data-open-cookie-settings]');
    if (!opener) return;
    event.preventDefault();

    var current = readConsent();
    if (toggles.statistics) toggles.statistics.checked = !!(current && current.statistics);
    if (toggles.marketing) toggles.marketing.checked = !!(current && current.marketing);

    showBanner();
    openSettings();
  });

  var existing = readConsent();
  if (isValidConsent(existing)) {
    applyConsent(existing);
    hideBanner();
  } else {
    var defaults = defaultConsent();
    if (toggles.statistics) toggles.statistics.checked = defaults.statistics;
    if (toggles.marketing) toggles.marketing.checked = defaults.marketing;
    showBanner();
  }
})();
