<?php
require __DIR__ . '/config/bootstrap.php';

$ticketId = trim((string) ($_GET['id'] ?? $_GET['token'] ?? ''));
$ticket = $ticketId !== '' ? getTicketByTicketId($ticketId) : null;

$pageTitle = $ticket ? 'Dein S-ART Ticket' : 'Ticket nicht gefunden';
require __DIR__ . '/includes/header.php';

$qrUrl = null;
if ($ticket) {
    $verifyUrl = appUrl('/ticket.php?id=' . urlencode($ticket['ticket_id']));
    $qrUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=320x320&margin=10&bgcolor=0a0a0c&color=ffffff&data=' . urlencode($verifyUrl);
}
?>

<section class="section container ticket-show reveal">
  <?php if (!empty($_GET['new'])): ?>
    <div class="form-flash form-flash-success">
      Ticket erfolgreich erstellt! Eine Bestätigung mit dem Link wurde an deine E-Mail gesendet.
    </div>
  <?php endif; ?>

  <?php if ($ticket && $ticket['status'] === 'active'): ?>
    <div class="ticket-card">
      <div class="ticket-card-head">
        <img src="/assets/img/logo/s-art-logo-dark.svg" alt="S-ART" class="ticket-logo">
        <p class="ticket-kicker">GRATIS TICKET</p>
      </div>

      <div class="ticket-card-body">
        <h2><?= e($ticket['event_title']) ?></h2>
        <div class="ticket-meta-row">
          <div>
            <span class="ticket-meta-label">DATUM</span>
            <strong><?= formatDate($ticket['event_date']) ?></strong>
          </div>
          <div>
            <span class="ticket-meta-label">ORT</span>
            <strong><?= e($ticket['city']) ?></strong>
          </div>
        </div>

        <div class="ticket-name-row">
          <span class="ticket-meta-label">TICKET FÜR</span>
          <strong><?= e($ticket['name'] ?: $ticket['email']) ?></strong>
        </div>

        <div class="ticket-qr">
          <?php if ($qrUrl): ?>
            <img src="<?= e($qrUrl) ?>" alt="QR-Code" loading="lazy">
          <?php endif; ?>
          <p class="ticket-id">ID: <?= e($ticket['ticket_id']) ?></p>
        </div>

        <p class="ticket-note">Der genaue Standort wird rechtzeitig bekanntgegeben.</p>
      </div>

      <div class="ticket-card-actions">
        <a class="btn btn-primary" href="/ticket-ics.php?id=<?= urlencode($ticket['ticket_id']) ?>">📅 Kalender (.ics)</a>
        <a class="btn btn-ghost" href="/ticket-wallet.php?id=<?= urlencode($ticket['ticket_id']) ?>">📱 Apple / Google Wallet</a>
        <a class="btn btn-ghost" href="/ticket-pdf.php?id=<?= urlencode($ticket['ticket_id']) ?>">📄 PDF</a>
      </div>
    </div>

    <p class="muted ticket-help">Bewahre den Link sicher auf – darüber kannst du dein Ticket jederzeit aufrufen.</p>
  <?php elseif ($ticket && $ticket['status'] !== 'active'): ?>
    <h1>Ticket deaktiviert</h1>
    <p>Dieses Ticket wurde deaktiviert. Bei Fragen kontaktiere uns bitte.</p>
  <?php else: ?>
    <h1>Ticket nicht gefunden</h1>
    <p>Bitte prüfe den Link in deiner E-Mail oder erstelle ein neues Ticket.</p>
    <a class="btn btn-primary" href="/ticket-buchen.php">Ticket sichern →</a>
  <?php endif; ?>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
