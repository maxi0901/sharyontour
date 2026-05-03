<?php
require_once __DIR__ . '/auth.php';
requireAdminLogin();

require __DIR__ . '/../config/bootstrap.php';
require __DIR__ . '/../includes/csrf.php';

if (isset($_GET['export']) && $_GET['export'] === 'csv') {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="s-art-clicks-' . date('Ymd-His') . '.csv"');
    $out = fopen('php://output', 'w');
    fputcsv($out, ['id', 'created_at', 'event_type', 'direction', 'page_path', 'ip_address', 'user_agent']);
    $rows = fetchAll('SELECT * FROM click_logs ORDER BY id DESC');
    foreach ($rows as $r) {
        fputcsv($out, [
            $r['id'],
            $r['created_at'],
            $r['event_type'],
            $r['direction'] ?? '',
            $r['page_path'] ?? '',
            $r['ip_address'] ?? '',
            $r['user_agent'] ?? '',
        ]);
    }
    fclose($out);
    exit;
}

$totalRow = fetchOne('SELECT COUNT(*) AS c FROM click_logs');
$total    = (int) ($totalRow['c'] ?? 0);

$todayRow = fetchOne('SELECT COUNT(*) AS c FROM click_logs WHERE DATE(created_at) = CURDATE()');
$today    = (int) ($todayRow['c'] ?? 0);

$weekRow  = fetchOne('SELECT COUNT(*) AS c FROM click_logs WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)');
$week     = (int) ($weekRow['c'] ?? 0);

$perPage = 50;
$page    = max(1, (int) ($_GET['page'] ?? 1));
$offset  = ($page - 1) * $perPage;
$pages   = max(1, (int) ceil($total / $perPage));

$rows = fetchAll(
    'SELECT * FROM click_logs ORDER BY id DESC LIMIT ' . (int) $perPage . ' OFFSET ' . (int) $offset
);

$shortUa = static function (?string $ua): string {
    if (!$ua) return '';
    if (preg_match('/(Edg|OPR|Chrome|Safari|Firefox)\/[\d\.]+/', $ua, $m)) {
        return $m[0];
    }
    return substr($ua, 0, 40);
};

$pageTitle = 'Klicks · Admin';
$adminPage = 'clicks';
require __DIR__ . '/_header.php';
?>

<div class="admin-page-head">
  <h1>Klicks</h1>
  <a class="btn btn-ghost btn-sm" href="/admin/clicks.php?export=csv">CSV exportieren</a>
</div>

<div class="admin-grid">
  <div class="admin-card">
    <p class="kicker">Klicks heute</p>
    <h2><?= $today ?></h2>
  </div>
  <div class="admin-card">
    <p class="kicker">Letzte 7 Tage</p>
    <h2><?= $week ?></h2>
  </div>
  <div class="admin-card">
    <p class="kicker">Gesamt</p>
    <h2><?= $total ?></h2>
  </div>
</div>

<div class="admin-table-wrap">
<table class="admin-table">
  <thead>
    <tr>
      <th>Zeitstempel</th>
      <th>Typ</th>
      <th>Richtung</th>
      <th>Seite</th>
      <th>IP</th>
      <th>Browser</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($rows as $r): ?>
      <tr>
        <td><?= e(substr((string) $r['created_at'], 0, 19)) ?></td>
        <td><?= e((string) $r['event_type']) ?></td>
        <td><?= e((string) ($r['direction'] ?? '')) ?></td>
        <td><?= e((string) ($r['page_path'] ?? '')) ?></td>
        <td><span class="muted"><?= e((string) ($r['ip_address'] ?? '')) ?></span></td>
        <td><span class="muted"><?= e($shortUa($r['user_agent'] ?? null)) ?></span></td>
      </tr>
    <?php endforeach; ?>
    <?php if (!$rows): ?>
      <tr><td colspan="6" class="muted">Noch keine Klicks aufgezeichnet.</td></tr>
    <?php endif; ?>
  </tbody>
</table>
</div>

<?php if ($pages > 1): ?>
<nav class="admin-pagination" aria-label="Seiten">
  <?php if ($page > 1): ?>
    <a class="btn btn-ghost btn-sm" href="?page=<?= $page - 1 ?>">← Zurück</a>
  <?php endif; ?>
  <span class="muted">Seite <?= $page ?> / <?= $pages ?></span>
  <?php if ($page < $pages): ?>
    <a class="btn btn-ghost btn-sm" href="?page=<?= $page + 1 ?>">Weiter →</a>
  <?php endif; ?>
</nav>
<?php endif; ?>

<?php require __DIR__ . '/_footer.php'; ?>
