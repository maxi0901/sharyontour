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
    <link href="https://fonts.googleapis.com/css2?family=Archivo:wght@500;700;800&family=Syne:wght@700;800&family=JetBrains+Mono:wght@400;700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="/assets/css/style.css" />
  </head>
  <body>
    <a href="#main-content" class="skip-link">Zum Inhalt springen</a>
    <header class="site-header">
      <div class="container nav-wrap">
        <a class="brand" href="/index.php" aria-label="Zur Startseite">
          <span class="brand-main">SHARY</span>
          <span class="brand-sub">ON TOUR</span>
        </a>
        <button class="nav-toggle" aria-expanded="false" aria-label="Navigation öffnen" data-nav-toggle>☰</button>
        <nav class="site-nav" data-nav>
          <a href="/index.php" class="<?= $currentPage === 'home' ? 'active' : '' ?>">Home</a>
          <a href="/tour.php" class="<?= $currentPage === 'tour' ? 'active' : '' ?>">Tour</a>
          <a href="/kunstwerke.php" class="<?= $currentPage === 'artworks' ? 'active' : '' ?>">Kunstwerke</a>
          <a href="/vergangene-events.php" class="<?= $currentPage === 'past' ? 'active' : '' ?>">Archiv</a>
          <a href="/booking.php" class="<?= $currentPage === 'booking' ? 'active' : '' ?>">Booking</a>
        </nav>
      </div>
    </header>
    <main id="main-content">
<?php $flash = get_flash(); ?>
<?php if ($flash): ?>
      <div class="container">
        <div class="alert alert-<?= e($flash['type']) ?>"><?= e($flash['message']) ?></div>
      </div>
<?php endif; ?>
