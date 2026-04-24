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
      <a class="icon-btn" href="/booking.php" aria-label="Ticket und Booking">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
          <path d="M4 20c2.7 0 6-1.1 6-7.2V4.9c0-.5-.4-.9-.9-.9H5c-.6 0-1 .4-1 1v3.3"/>
          <path d="M9 3c0 0 2 1.2 2 4"/>
          <path d="M3 9h7"/>
          <path d="M1 18c0 1.7 1.4 3 3 3 2.3 0 3-1.2 3-3 0-2.7-3-4-3-4s-3 1.3-3 4z" fill="currentColor" opacity="0.6"/>
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
