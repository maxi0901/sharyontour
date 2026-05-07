<?php
require_once __DIR__ . '/../config/bootstrap.php';
require_once __DIR__ . '/csrf.php';
$siteConfig = require __DIR__ . '/site-config.php';
?><!doctype html>
<html lang="de">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= e($pageTitle ?? 'S-ART / Shary on Tour') ?></title>
  <meta name="description" content="S-ART – Shary on Tour: Pop-Art, Container Opening Kassel, Live Events und Pop-up Vernissagen.">
  <meta name="theme-color" content="#0a0a0c">
  <link rel="icon" type="image/svg+xml" href="/assets/img/s-art-logo.svg">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Anton&family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
<header class="site-header">
  <div class="container nav-wrap">
    <a class="logo-link" href="/index.php" aria-label="S-ART Startseite">
      <img src="/assets/img/s-art-logo.svg" alt="S-ART Logo" class="site-logo">
    </a>

    <nav class="main-nav" id="mainNav" aria-label="Hauptnavigation">
      <a class="<?= isActivePage('index.php') ?>" href="/index.php">Home</a>
      <a class="<?= isActivePage('tour.php') ?>" href="/tour.php">Events</a>
      <a class="<?= isActivePage('vergangene-events.php') ?>" href="/vergangene-events.php">Galerie</a>
      <a class="<?= isActivePage('ueber-shary.php') ?>" href="/ueber-shary.php">Über den Künstler</a>
      <a class="booking-cta <?= isActivePage('booking.php') ?>" href="/booking.php" aria-label="Booking- und Kontaktseite öffnen">
        <svg class="booking-cta__icon" viewBox="0 0 24 24" aria-hidden="true" focusable="false">
          <path class="booking-cta__spray" d="M8.4 7.9h7.2c1.1 0 2 .9 2 2v8.7c0 1.1-.9 2-2 2H8.4c-1.1 0-2-.9-2-2V9.9c0-1.1.9-2 2-2Z"/>
          <path class="booking-cta__cap" d="M9.1 4.9h5.8v3H9.1z"/>
          <path class="booking-cta__nozzle" d="M10.4 3.4h3.2c.5 0 .9.4.9.9v.6H9.5v-.6c0-.5.4-.9.9-.9Z"/>
          <path class="booking-cta__label" d="M8.6 12.4h6.8M8.6 15.2h4.7"/>
          <path class="booking-cta__mist" d="M16.4 5.4c1.8-.8 3.3-1.1 4.8-1.1M17.1 7.1c1.6.1 2.8.4 4.1 1M17.2 3.8c1-.9 1.8-1.5 2.8-2"/>
        </svg>
        <span>Booking</span>
      </a>
      <a class="<?= isActivePage('booking.php') ?>" href="/booking.php">Kontakt</a>
    </nav>

    <div class="header-actions">
      <div class="header-social" aria-label="Social Media">
        <a href="<?= e($siteConfig['social']['instagram']['url']) ?>" target="_blank" rel="noopener noreferrer">Instagram</a>
        <a href="<?= e($siteConfig['social']['tiktok']['url']) ?>" target="_blank" rel="noopener noreferrer">TikTok</a>
      </div>
      <a class="btn btn-ticket-mini" href="/ticket-buchen.php">Gratis Ticket</a>
      <a class="mobile-spraycan-btn spraycan-btn icon-btn" href="/booking.php" aria-label="Booking- und Kontaktseite öffnen">
        <span class="spraycan-pulse" aria-hidden="true"></span>
        <svg class="mobile-spraycan-btn__icon" viewBox="0 0 28 28" aria-hidden="true" focusable="false">
          <path class="mobile-spraycan-btn__nozzle" d="M12.2 5.4h3.6"/>
          <path class="mobile-spraycan-btn__cap" d="M11.2 7.2h5.6v4.1h-5.6z"/>
          <path class="mobile-spraycan-btn__body" d="M10.2 11.2h7.6v10.9c0 .7-.6 1.3-1.3 1.3h-5c-.7 0-1.3-.6-1.3-1.3V11.2Z"/>
          <path class="mobile-spraycan-btn__label" d="M12.3 15.1h3.4M12.3 18.2h3.4"/>
        </svg>
      </a>
      <button class="nav-toggle icon-btn" aria-label="Navigation öffnen" aria-controls="mainNav" aria-expanded="false">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" aria-hidden="true">
          <line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/>
        </svg>
      </button>
    </div>
  </div>
</header>
<main>
