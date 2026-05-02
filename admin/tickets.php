<?php
require __DIR__ . '/../config/bootstrap.php';
require __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../config/mail.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && verifyCsrf($_POST['csrf_token'] ?? null)) {
    $action = $_POST['action'] ?? '';
    $id = (int) ($_POST['id'] ?? 0);

    if ($action === 'disable' && $id) {
        $pdo->prepare('UPDATE tickets SET status="disabled" WHERE id=:id')->execute(['id' => $id]);
    } elseif ($action === 'enable' && $id) {
        $pdo->prepare('UPDATE tickets SET status="active" WHERE id=:id')->execute(['id' => $id]);
    } elseif ($action === 'delete' && $id) {
        $pdo->prepare('DELETE FROM tickets WHERE id=:id')->execute(['id' => $id]);
    } elseif ($action === 'resend_mail' && $id) {
        $row = fetchOne('SELECT t.*, e.id AS event_id, e.title, e.event_date FROM tickets t INNER JOIN events e ON e.id = t.event_id WHERE t.id = :id LIMIT 1', ['id' => $id]);
        if ($row) {
            $event = ['id' => (int) $row['event_id'], 'title' => $row['title'], 'event_date' => $row['event_date']];
            sendTicketMail($pdo, $row['email'], $row['ticket_id'], $row['name'] ?? null, $event);
        }
    }

    header('Location: /admin/tickets.php' . (!empty($_GET['event']) ? '?event=' . (int) $_GET['event'] : ''));
    exit;
}

if (isset($_GET['export']) && $_GET['export'] === 'csv') {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="s-art-tickets-' . date('Ymd-His') . '.csv"');
    $out = fopen('php://output', 'w');
    fputcsv($out, ['id', 'event', 'event_date', 'email', 'name', 'ticket_id', 'status', 'created_at', 'mail_sent_at', 'mail_opened_at', 'ticket_opened_at', 'last_click_at', 'click_count']);
    $rows = fetchAll('SELECT t.*, e.title AS event_title, e.event_date FROM tickets t INNER JOIN events e ON e.id=t.event_id ORDER BY t.created_at DESC');
    foreach ($rows as $r) {
        fputcsv($out, [
            $r['id'], $r['event_title'], $r['event_date'], $r['email'], $r['name'], $r['ticket_id'],
            $r['status'], $r['created_at'],
            $r['mail_sent_at'] ?? '', $r['mail_opened_at'] ?? '', $r['ticket_opened_at'] ?? '',
            $r['last_click_at'] ?? '', $r['click_count'] ?? 0,
        ]);
    }
    fclose($out);
    exit;
}

$eventFilter = (int) ($_GET['event'] ?? 0);

$sql = 'SELECT t.*, e.title AS event_title, e.event_date, e.max_tickets FROM tickets t INNER JOIN events e ON e.id=t.event_id';
$params = [];
if ($eventFilter) {
    $sql .= ' WHERE t.event_id=:e';
    $params['e'] = $eventFilter;
}
$sql .= ' ORDER BY t.created_at DESC';
$rows = fetchAll($sql, $params);

$events = fetchAll('SELECT id, title, event_date, max_tickets FROM events WHERE is_opening=1 OR id IN (SELECT event_id FROM tickets) ORDER BY event_date DESC');
$opening = getOpeningEvent();
$ticketsTotal = $opening ? countTicketsForEvent((int) $opening['id']) : 0;
$maxTickets = $opening ? (int) $opening['max_tickets'] : 600;

$totalSent   = 0;
$totalOpened = 0;
$totalClicks = 0;
foreach ($rows as $r) {
    if (!empty($r['mail_sent_at']))   $totalSent++;
    if (!empty($r['mail_opened_at'])) $totalOpened++;
    $totalClicks += (int) ($r['click_count'] ?? 0);
}

$shortTime = static function (?string $dt): string {
    if (!$dt) return '';
    return substr($dt, 5, 11);
};

$pageTitle = 'Tickets · Admin';
$adminPage = 'tickets';
require __DIR__ . '/_header.php';
?>

<div class="admin-page-head">
  <h1>Tickets</h1>
  <a class="btn btn-ghost btn-sm" href="/admin/tickets.php?export=csv">CSV exportieren</a>
</div>

<?php if ($opening): ?>
  <div class="admin-card admin-highlight">
    <p class="kicker"><?= e($opening['title']) ?></p>
    <h2><?= $ticketsTotal ?> / <?= $maxTickets ?> Tickets</h2>
    <div class="admin-progress"><span style="width: <?= min(100, ($ticketsTotal / max($maxTickets, 1)) * 100) ?>%"></span></div>
  </div>
<?php endif; ?>

<div class="admin-card">
  <p class="kicker">Mail-Tracking</p>
  <p>
    <strong><?= $totalSent ?></strong> Mails gesendet ·
    <strong><?= $totalOpened ?></strong> geöffnet ·
    <strong><?= $totalClicks ?></strong> Link-Klicks gesamt
    <?php if (count($rows) > 0): ?>
      · Öffnungsrate <strong><?= $totalSent > 0 ? round($totalOpened / $totalSent * 100) : 0 ?>%</strong>
    <?php endif; ?>
  </p>
</div>

<form method="get" class="admin-filter">
  <label>Event:
    <select name="event" onchange="this.form.submit()">
      <option value="0">Alle</option>
      <?php foreach ($events as $ev): ?>
        <option value="<?= (int) $ev['id'] ?>" <?= $eventFilter === (int) $ev['id'] ? 'selected' : '' ?>>
          <?= e($ev['event_date']) ?> · <?= e($ev['title']) ?>
        </option>
      <?php endforeach; ?>
    </select>
  </label>
</form>

<table class="admin-table">
  <thead>
    <tr>
      <th>Datum</th>
      <th>Event</th>
      <th>E-Mail</th>
      <th>Name</th>
      <th>Ticket-ID</th>
      <th>Status</th>
      <th>Mail</th>
      <th>Klicks</th>
      <th></th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($rows as $r): ?>
      <?php
        $mailSent   = $r['mail_sent_at']   ?? null;
        $mailOpened = $r['mail_opened_at'] ?? null;
        $clicks     = (int) ($r['click_count'] ?? 0);
        $lastClick  = $r['last_click_at']  ?? null;
      ?>
      <tr>
        <td><?= e(substr($r['created_at'], 0, 16)) ?></td>
        <td><?= e($r['event_title']) ?></td>
        <td><?= e($r['email']) ?></td>
        <td><?= e($r['name'] ?? '') ?></td>
        <td><code><?= e(substr($r['ticket_id'], 0, 8)) ?>…</code></td>
        <td><span class="status-pill status-<?= e($r['status']) ?>"><?= e($r['status']) ?></span></td>
        <td>
          <?php if ($mailOpened): ?>
            <span class="status-pill status-active" title="geöffnet am <?= e($mailOpened) ?>">geöffnet · <?= e($shortTime($mailOpened)) ?></span>
          <?php elseif ($mailSent): ?>
            <span class="status-pill" title="gesendet am <?= e($mailSent) ?>">gesendet · <?= e($shortTime($mailSent)) ?></span>
          <?php else: ?>
            <span class="muted">—</span>
          <?php endif; ?>
        </td>
        <td>
          <?php if ($clicks > 0): ?>
            <strong><?= $clicks ?></strong>
            <?php if ($lastClick): ?>
              <span class="muted"> · <?= e($shortTime($lastClick)) ?></span>
            <?php endif; ?>
          <?php else: ?>
            <span class="muted">0</span>
          <?php endif; ?>
        </td>
        <td class="admin-row-actions">
          <a class="text-link" href="/admin/ticket-detail.php?id=<?= (int) $r['id'] ?>">Details</a>
          <a class="text-link" href="/ticket.php?id=<?= e($r['ticket_id']) ?>" target="_blank">Anzeigen</a>
          <form method="post" style="display:inline" onsubmit="return confirm('Mail erneut an <?= e($r['email']) ?> senden?')">
            <?= csrfField() ?>
            <input type="hidden" name="action" value="resend_mail">
            <input type="hidden" name="id" value="<?= (int) $r['id'] ?>">
            <button class="text-link" type="submit">Mail erneut senden</button>
          </form>
          <?php if ($r['status'] === 'active'): ?>
            <form method="post" style="display:inline" onsubmit="return confirm('Ticket deaktivieren?')">
              <?= csrfField() ?>
              <input type="hidden" name="action" value="disable">
              <input type="hidden" name="id" value="<?= (int) $r['id'] ?>">
              <button class="text-link" type="submit">Deaktivieren</button>
            </form>
          <?php else: ?>
            <form method="post" style="display:inline">
              <?= csrfField() ?>
              <input type="hidden" name="action" value="enable">
              <input type="hidden" name="id" value="<?= (int) $r['id'] ?>">
              <button class="text-link" type="submit">Aktivieren</button>
            </form>
          <?php endif; ?>
          <form method="post" style="display:inline" onsubmit="return confirm('Ticket endgültig löschen?')">
            <?= csrfField() ?>
            <input type="hidden" name="action" value="delete">
            <input type="hidden" name="id" value="<?= (int) $r['id'] ?>">
            <button class="text-link danger" type="submit">Löschen</button>
          </form>
        </td>
      </tr>
    <?php endforeach; ?>
    <?php if (!$rows): ?>
      <tr><td colspan="9" class="muted">Noch keine Tickets vorhanden.</td></tr>
    <?php endif; ?>
  </tbody>
</table>

<?php require __DIR__ . '/_footer.php'; ?>
