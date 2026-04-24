<?php
require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/csrf.php';
?><!doctype html>
<html lang="de">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= e($pageTitle ?? 'S-ART / Shary on Tour') ?></title>
  <meta name="description" content="Shary on Tour – Pop-Art, Urban Art, Street Art und Events.">
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
    <div class="header-actions">
      <a class="icon-btn cart-btn" href="/booking.php" aria-label="Ticket und Booking">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
          <circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/>
          <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/>
        </svg>
        <span class="cart-badge">0</span>
      </a>
      <button class="nav-toggle icon-btn" aria-label="Navigation öffnen" aria-expanded="false">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" aria-hidden="true">
          <line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/>
        </svg>
      </button>
    </div>
    <nav class="main-nav">
      <a class="<?= isActivePage('index.php') ?>" href="/index.php">Start</a>
      <a class="<?= isActivePage('kunstwerke.php') ?>" href="/kunstwerke.php">Kunstwerke</a>
      <a class="<?= isActivePage('tour.php') ?>" href="/tour.php">Tour</a>
      <a class="<?= isActivePage('vergangene-events.php') ?>" href="/vergangene-events.php">Archiv</a>
      <a class="<?= isActivePage('booking.php') ?>" href="/booking.php">Booking</a>
    </nav>
  </div>
</header>
<main>
