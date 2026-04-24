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
      <span class="logo-mark">S·ART</span>
      <span class="logo-sub">SHARY ON TOUR</span>
    </a>
    <div class="header-actions">
      <a class="icon-btn" href="/booking.php" aria-label="Ticket und Booking">
        <span aria-hidden="true">🛒</span>
      </a>
      <button class="nav-toggle icon-btn" aria-label="Navigation öffnen" aria-expanded="false">☰</button>
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
