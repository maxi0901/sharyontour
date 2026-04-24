<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/functions.php';

$currentPage = 'booking';
$pageTitle = 'Booking | S-Art Tour';

require __DIR__ . '/includes/header.php';
?>
<section class="section container reveal">
  <h1 class="section-title">Booking & Kontakt</h1>
  <p>Für Kooperationen, Presse und Venue-Anfragen: <a href="mailto:booking@example.com">booking@example.com</a></p>
  <form class="form-grid" method="post" action="#" onsubmit="event.preventDefault();alert('Danke! Bitte nutze aktuell die E-Mail-Adresse.');">
    <label>Name
      <input type="text" name="name" />
    </label>
    <label>E-Mail
      <input type="email" name="email" />
    </label>
    <label style="grid-column: 1 / -1;">Nachricht
      <textarea name="message"></textarea>
    </label>
    <button type="submit">Anfrage senden</button>
  </form>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
