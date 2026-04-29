<?php
require_once __DIR__ . '/../config/bootstrap.php';
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
      <a class="icon-btn spraycan-btn" href="/tour.php" aria-label="Spray on Tour">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" width="22" height="22">
          <rect x="9" y="8.6" width="6.4" height="12.4" rx="1.4"/>
          <rect x="10.4" y="4.4" width="3.6" height="2.4" rx="0.4"/>
          <line x1="12.2" y1="6.8" x2="12.2" y2="8.6"/>
          <line x1="9.2" y1="13.2" x2="15.2" y2="13.2"/>
          <circle cx="18.4" cy="3.6" r="0.7" fill="currentColor" stroke="none"/>
          <circle cx="20.6" cy="5.4" r="0.5" fill="currentColor" stroke="none"/>
          <circle cx="17.6" cy="6" r="0.45" fill="currentColor" stroke="none"/>
          <circle cx="20" cy="2.6" r="0.4" fill="currentColor" stroke="none"/>
        </svg>
        <span class="spraycan-pulse" aria-hidden="true"></span>
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
