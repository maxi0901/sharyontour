<?php
require __DIR__ . '/config/bootstrap.php';

$ticketId = trim((string) ($_GET['id'] ?? $_GET['token'] ?? ''));
$ticket = $ticketId !== '' ? getTicketByTicketId($ticketId) : null;

$pageTitle = $ticket ? 'Dein S-ART Ticket' : 'Ticket nicht gefunden';
require __DIR__ . '/includes/header.php';


$shortId = $ticket ? strtoupper(substr(preg_replace('/[^a-zA-Z0-9]/', '', (string) $ticket['ticket_id']), 0, 8)) : '';
$entryTime = $ticket ? normalizeEventTime($ticket['event_time'] ?? null) : null;
?>

<section class="section container ticket-show">
  <?php if (!empty($_GET['new'])): ?>
    <div class="form-flash form-flash-success reveal">
      Ticket erfolgreich erstellt! Eine Bestätigung mit dem Link wurde an deine E-Mail gesendet.
    </div>
  <?php endif; ?>

  <?php if ($ticket && $ticket['status'] === 'active'): ?>
    <div class="ticket-stage">
      <div class="ticket-stage-bg" aria-hidden="true">
        <div class="ticket-stage-glow ticket-stage-glow-pink"></div>
        <div class="ticket-stage-glow ticket-stage-glow-green"></div>
      </div>

      <div class="ticket-card-real reveal">
        <div class="ticket-card-stub">
          <div class="ticket-stub-top">
            <img src="/assets/img/s-art-logo.svg" alt="S-ART" class="ticket-stub-logo">
            <span class="ticket-stub-tag">ADMIT ONE</span>
          </div>

          <div class="ticket-stub-event">
            <p class="kicker">CONTAINER OPENING</p>
            <h2><?= e($ticket['event_title']) ?></h2>
            <p class="muted"><?= formatDateLong($ticket['event_date']) ?> · <?= e($ticket['city']) ?></p>
          </div>

          <div class="ticket-stub-grid">
            <div>
              <span class="ticket-meta-label">Datum</span>
              <strong><?= formatDate($ticket['event_date']) ?></strong>
            </div>
            <?php if ($entryTime !== null): ?>
              <div>
                <span class="ticket-meta-label">Einlass</span>
                <strong><?= e($entryTime) ?> Uhr</strong>
              </div>
            <?php endif; ?>
            <div>
              <span class="ticket-meta-label">Ort</span>
              <strong><?= e($ticket['city']) ?></strong>
            </div>
            <div>
              <span class="ticket-meta-label">Sitz</span>
              <strong>FREIE WAHL</strong>
            </div>
          </div>

          <div class="ticket-stub-name">
            <span class="ticket-meta-label">Ausgestellt für</span>
            <strong><?= e($ticket['name'] ?: $ticket['email']) ?></strong>
            <p class="muted"><?= e($ticket['email']) ?></p>
          </div>

          <div class="ticket-stub-footer">
            <span class="ticket-meta-label">Ticket-Nr.</span>
            <strong class="ticket-stub-number"><?= e($shortId) ?></strong>
          </div>
        </div>

        <div class="ticket-card-perforation" aria-hidden="true">
          <span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span>
        </div>

        <div class="ticket-card-tearoff">
          <p class="ticket-tearoff-id">ID · <?= e($shortId) ?></p>
          <p class="muted ticket-tearoff-note">Gültig nur in Verbindung mit gültigem Ausweis.</p>
        </div>
      </div>

      <div class="ticket-actions reveal">
        <a class="btn btn-primary" href="/ticket-pdf.php?id=<?= urlencode($ticket['ticket_id']) ?>">PDF herunterladen</a>
        <a class="btn btn-ghost" href="/ticket-ics.php?id=<?= urlencode($ticket['ticket_id']) ?>">In Kalender speichern (.ics)</a>
      </div>

      <p class="muted ticket-help reveal">Bewahre den Link sicher auf — darüber kannst du dein Ticket jederzeit aufrufen. Der genaue Standort wird rechtzeitig bekanntgegeben.</p>
    </div>
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
