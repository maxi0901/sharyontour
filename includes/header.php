<?php

declare(strict_types=1);

require_once __DIR__ . '/functions.php';

$pageTitle = $pageTitle ?? 'Shary on Tour / S-Art';
$currentPage = $currentPage ?? '';
?>
<!doctype html>
<html lang="de">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?= e($pageTitle) ?></title>
    <meta name="description" content="S-Art Tour verbindet Pop-Art, Urban Culture und Live-Events in einem dynamischen Kunsterlebnis." />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Archivo+Black&family=Bungee&family=JetBrains+Mono:wght@400;700&family=Manrope:wght@400;600;800&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="/assets/css/style.css" />
  </head>
  <body>
    <header class="site-header">
      <div class="container nav-wrap">
        <a class="brand" href="/index.php">SHARY <span>ON TOUR</span></a>
        <button class="nav-toggle" aria-label="Navigation öffnen" data-nav-toggle>☰</button>
        <nav class="site-nav" data-nav>
          <a href="/index.php" class="<?= $currentPage === 'home' ? 'active' : '' ?>">Home</a>
          <a href="/tour.php" class="<?= $currentPage === 'tour' ? 'active' : '' ?>">Tour</a>
          <a href="/kunstwerke.php" class="<?= $currentPage === 'artworks' ? 'active' : '' ?>">Kunstwerke</a>
          <a href="/vergangene-events.php" class="<?= $currentPage === 'past' ? 'active' : '' ?>">Vergangene Events</a>
          <a href="/booking.php" class="<?= $currentPage === 'booking' ? 'active' : '' ?>">Booking</a>
        </nav>
      </div>
    </header>
    <main>
<?php $flash = get_flash(); ?>
<?php if ($flash): ?>
      <div class="container">
        <div class="alert alert-<?= e($flash['type']) ?>"><?= e($flash['message']) ?></div>
      </div>
<?php endif; ?>
