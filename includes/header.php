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
  <link href="https://fonts.googleapis.com/css2?family=Archivo+Black&family=Bungee&family=JetBrains+Mono:wght@400;600&family=Manrope:wght@400;500;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
<header class="site-header">
  <div class="container nav-wrap">
    <a class="logo" href="/index.php">S-ART <span>Shary on Tour</span></a>
    <button class="nav-toggle" aria-label="Navigation öffnen">☰</button>
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
