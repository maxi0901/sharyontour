<?php
require __DIR__ . '/../config/bootstrap.php';
require __DIR__ . '/../includes/csrf.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && verifyCsrf($_POST['csrf_token'] ?? null)) {
    $action = $_POST['action'] ?? '';
    $id = (int) ($_POST['id'] ?? 0);
    if ($action === 'delete' && $id) {
        $stmt = $pdo->prepare('DELETE FROM events WHERE id=:id');
        $stmt->execute(['id' => $id]);
    } elseif ($action === 'toggle_status' && $id) {
        $stmt = $pdo->prepare("UPDATE events SET status = IF(status='past','upcoming','past') WHERE id=:id");
        $stmt->execute(['id' => $id]);
    }
    header('Location: /admin/events.php');
    exit;
}

$pageTitle = 'Events · Admin';
$adminPage = 'events';
require __DIR__ . '/_header.php';

$rows = fetchAll('SELECT * FROM events ORDER BY event_date DESC');
?>

<div class="admin-page-head">
  <h1>Events</h1>
  <a class="btn btn-primary btn-sm" href="/admin/event-edit.php">+ Neues Event</a>
</div>

<table class="admin-table">
  <thead>
    <tr><th>Datum</th><th>Titel</th><th>Stadt</th><th>Status</th><th>Opening</th><th>Tickets</th><th></th></tr>
  </thead>
  <tbody>
    <?php foreach ($rows as $r):
      $ticketsCount = (int) $r['is_opening'] === 1
          ? countTicketsForEvent((int) $r['id'])
          : 0;
    ?>
      <tr>
        <td><?= e($r['event_date']) ?></td>
        <td><?= e($r['title']) ?></td>
        <td><?= e($r['city']) ?></td>
        <td><span class="status-pill status-<?= e($r['status']) ?>"><?= e($r['status']) ?></span></td>
        <td><?= (int) $r['is_opening'] === 1 ? '★' : '' ?></td>
        <td><?= (int) $r['is_opening'] === 1 ? $ticketsCount . ' / ' . (int) $r['max_tickets'] : '—' ?></td>
        <td class="admin-row-actions">
          <a class="text-link" href="/admin/event-edit.php?id=<?= (int) $r['id'] ?>">Bearbeiten</a>
          <form method="post" style="display:inline" onsubmit="return confirm('Status umschalten?')">
            <?= csrfField() ?>
            <input type="hidden" name="action" value="toggle_status">
            <input type="hidden" name="id" value="<?= (int) $r['id'] ?>">
            <button class="text-link" type="submit">Status togglen</button>
          </form>
          <form method="post" style="display:inline" onsubmit="return confirm('Event wirklich löschen?')">
            <?= csrfField() ?>
            <input type="hidden" name="action" value="delete">
            <input type="hidden" name="id" value="<?= (int) $r['id'] ?>">
            <button class="text-link danger" type="submit">Löschen</button>
          </form>
        </td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>

<?php require __DIR__ . '/_footer.php'; ?>
