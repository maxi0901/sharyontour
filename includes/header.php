<?php
require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/csrf.php';
?><!doctype html>
<html lang="de">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= e($pageTitle ?? 'S-ART / Shary on Tour') ?></title>
  <meta name="description" content="S-ART – Shary on Tour: Pop-Art, urbane Kunstwerke und Tour-Events.">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Anton&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
<header class="site-header">
  <div class="container nav-wrap">
    <a class="logo" href="/index.php" aria-label="Startseite">
      <span class="logo-mark">S-<span class="logo-art">ART</span></span>
      <span class="logo-sub">SHARY ON TOUR</span>
    </a>

    <nav class="main-nav" id="mainNav">
      <a class="<?= isActivePage('index.php') ?>" href="/index.php">Home</a>
      <a class="<?= isActivePage('tour.php') ?>" href="/tour.php">Events</a>
      <a class="<?= isActivePage('kunstwerke.php') ?>" href="/kunstwerke.php">Kunstwerke</a>
      <a class="<?= isActivePage('vergangene-events.php') ?>" href="/vergangene-events.php">Über Shary</a>
      <a class="<?= isActivePage('booking.php') ?>" href="/booking.php">Kontakt</a>
    </nav>

    <div class="header-actions">
      <a class="icon-btn" href="/booking.php" aria-label="Ticket und Booking" style="border-color: rgba(255,35,72,.5);">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" width="20" height="20">
          <circle cx="12" cy="12" r="10"/>
          <path d="M16.2 7.8l-2.1 6.3-6.3 2.1 2.1-6.3 6.3-2.1z" fill="#ff2348" stroke="none"/>
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
