<?php
require_once __DIR__ . '/../config/bootstrap.php';
require_once __DIR__ . '/csrf.php';
?><!doctype html>
<html lang="de">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= e($pageTitle ?? 'S-ART / Shary on Tour') ?></title>
  <meta name="description" content="S-ART – Shary on Tour: Pop-Art, Container Opening Kassel, Live Events und Pop-up Vernissagen.">
  <meta name="theme-color" content="#0a0a0c">
  <link rel="icon" type="image/svg+xml" href="/assets/img/logo/s-art-logo-dark.svg">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Anton&family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
<header class="site-header">
  <div class="container nav-wrap">
    <a class="logo-link" href="/index.php" aria-label="S-ART Startseite">
      <img src="/assets/img/logo/s-art-logo-dark.svg" alt="S-ART" class="logo-img">
      <span class="logo-sub">SHARY ON TOUR</span>
    </a>

    <nav class="main-nav" id="mainNav" aria-label="Hauptnavigation">
      <a class="<?= isActivePage('index.php') ?>" href="/index.php">Home</a>
      <a class="<?= isActivePage('tour.php') ?>" href="/tour.php">Events</a>
      <a class="<?= isActivePage('vergangene-events.php') ?>" href="/vergangene-events.php">Galerie</a>
      <a class="<?= isActivePage('ueber-shary.php') ?>" href="/ueber-shary.php">Über Shary</a>
      <a class="<?= isActivePage('booking.php') ?>" href="/booking.php">Kontakt</a>
    </nav>

    <div class="header-actions">
      <a class="btn btn-ticket-mini" href="/ticket-buchen.php">
        Gratis Ticket
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
