<?php
require_once __DIR__ . '/../config/bootstrap.php';
require_once __DIR__ . '/../includes/csrf.php';

$adminPage = $adminPage ?? '';
?><!doctype html>
<html lang="de">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= e($pageTitle ?? 'S-ART Admin') ?></title>
  <link rel="stylesheet" href="/assets/css/style.css">
  <link rel="icon" type="image/svg+xml" href="/assets/img/logo/s-art-logo-dark.svg">
</head>
<body class="admin-body">
<header class="admin-header">
  <div class="container admin-nav">
    <a class="admin-logo" href="/admin/index.php">
      <img src="/assets/img/logo/s-art-logo-dark.svg" alt="S-ART" class="logo-img-sm">
      <span>ADMIN</span>
    </a>
    <nav>
      <a href="/admin/events.php" class="<?= $adminPage === 'events' ? 'is-active' : '' ?>">Events</a>
      <a href="/admin/galleries.php" class="<?= $adminPage === 'galleries' ? 'is-active' : '' ?>">Galerien</a>
      <a href="/admin/tickets.php" class="<?= $adminPage === 'tickets' ? 'is-active' : '' ?>">Tickets</a>
      <a href="/admin/newsletter.php" class="<?= $adminPage === 'newsletter' ? 'is-active' : '' ?>">Newsletter</a>
      <a href="/index.php" class="muted">↗ Website</a>
    </nav>
  </div>
</header>
<main class="admin-main">
<div class="container">
