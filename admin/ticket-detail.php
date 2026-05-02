<?php
require __DIR__ . '/../config/bootstrap.php';
require __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../config/mail.php';

$id = (int) ($_GET['id'] ?? 0);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && verifyCsrf($_POST['csrf_token'] ?? null)) {
    $postId = (int) ($_POST['id'] ?? 0);
    if (($_POST['action'] ?? '') === 'resend_mail' && $postId) {
        $row = fetchOne('SELECT t.*, e.id AS event_pk, e.title, e.event_date FROM tickets t INNER JOIN events e ON e.id = t.event_id WHERE t.id = :id LIMIT 1', ['id' => $postId]);
        if ($row) {
            $event = ['id' => (int) $row['event_pk'], 'title' => $row['title'], 'event_date' => $row['event_date']];
            sendTicketMail($pdo, $row['email'], $row['ticket_id'], $row['name'] ?? null, $event);
        }
    }
    header('Location: /admin/ticket-detail.php?id=' . $postId);
    exit;
}

$ticket = $id
    ? fetchOne('SELECT t.*, e.title AS event_title, e.event_date, e.city FROM tickets t INNER JOIN events e ON e.id = t.event_id WHERE t.id = :id LIMIT 1', ['id' => $id])
    : null;

$logs = $ticket
    ? fetchAll('SELECT * FROM ticket_logs WHERE ticket_id = :tid ORDER BY created_at DESC, id DESC', ['tid' => $ticket['ticket_id']])
    : [];

$pageTitle = 'Ticket-Details · Admin';
$adminPage = 'tickets';
require __DIR__ . '/_header.php';
?>

<div class="admin-page-head">
  <h1>Ticket-Details</h1>
  <a class="btn btn-ghost btn-sm" href="/admin/tickets.php">← Zurück</a>
</div>

<?php if (!$ticket): ?>
  <div class="admin-card">
    <p class="muted">Ticket nicht gefunden.</p>
  </div>
<?php else: ?>

  <div class="admin-card">
    <p class="kicker"><?= e($ticket['event_title']) ?> · <?= e($ticket['event_date']) ?></p>
    <h2><?= e($ticket['name'] ?? '—') ?></h2>
    <p><strong><?= e($ticket['email']) ?></strong></p>
    <p>Ticket-ID: <code><?= e($ticket['ticket_id']) ?></code></p>
    <p>
      Status: <span class="status-pill status-<?= e($ticket['status']) ?>"><?= e($ticket['status']) ?></span>
      · Erstellt: <?= e(substr($ticket['created_at'], 0, 16)) ?>
      <?php if (!empty($ticket['postal_code'])): ?> · PLZ: <?= e($ticket['postal_code']) ?><?php endif; ?>
    </p>
  </div>

  <div class="admin-card">
    <p class="kicker">Mail- &amp; Klick-Tracking</p>
    <table class="admin-table">
      <tbody>
        <tr><th style="width:30%">Mail gesendet</th><td><?= $ticket['mail_sent_at'] ? e($ticket['mail_sent_at']) : '<span class="muted">—</span>' ?></td></tr>
        <tr><th>Mail geöffnet (Pixel)</th><td><?= $ticket['mail_opened_at'] ? e($ticket['mail_opened_at']) : '<span class="muted">—</span>' ?></td></tr>
        <tr><th>Ticket aufgerufen</th><td><?= $ticket['ticket_opened_at'] ? e($ticket['ticket_opened_at']) : '<span class="muted">—</span>' ?></td></tr>
        <tr><th>Letzter Klick</th><td><?= $ticket['last_click_at'] ? e($ticket['last_click_at']) : '<span class="muted">—</span>' ?></td></tr>
        <tr><th>Klicks gesamt</th><td><strong><?= (int) ($ticket['click_count'] ?? 0) ?></strong></td></tr>
      </tbody>
    </table>
  </div>

  <div class="admin-card">
    <p class="kicker">Aktionen</p>
    <p>
      <a class="btn btn-ghost btn-sm" href="/ticket.php?id=<?= e($ticket['ticket_id']) ?>" target="_blank">Ticket öffnen ↗</a>
      <a class="btn btn-ghost btn-sm" href="/ticket-pdf.php?id=<?= e($ticket['ticket_id']) ?>" target="_blank">PDF ↗</a>
      <a class="btn btn-ghost btn-sm" href="/ticket-ics.php?id=<?= e($ticket['ticket_id']) ?>" target="_blank">.ics ↗</a>
      <a class="btn btn-ghost btn-sm" href="/ticket-wallet.php?id=<?= e($ticket['ticket_id']) ?>" target="_blank">Wallet ↗</a>
    </p>
    <form method="post" onsubmit="return confirm('Mail erneut an <?= e($ticket['email']) ?> senden?')">
      <?= csrfField() ?>
      <input type="hidden" name="action" value="resend_mail">
      <input type="hidden" name="id" value="<?= (int) $ticket['id'] ?>">
      <button class="btn btn-primary btn-sm" type="submit">Mail erneut senden</button>
    </form>
  </div>

  <div class="admin-card">
    <p class="kicker">Aktivitätslog</p>
    <table class="admin-table">
      <thead><tr><th>Zeitpunkt</th><th>Status</th><th>Details</th></tr></thead>
      <tbody>
        <?php foreach ($logs as $log): ?>
          <tr>
            <td><?= e(substr((string) $log['created_at'], 0, 19)) ?></td>
            <td><span class="status-pill"><?= e($log['status']) ?></span></td>
            <td><?= e($log['error_message'] ?? '') ?></td>
          </tr>
        <?php endforeach; ?>
        <?php if (!$logs): ?>
          <tr><td colspan="3" class="muted">Noch keine Aktivität.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

<?php endif; ?>

<?php require __DIR__ . '/_footer.php'; ?>
