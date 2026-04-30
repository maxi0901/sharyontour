<?php
require __DIR__ . '/../config/bootstrap.php';
require __DIR__ . '/../includes/csrf.php';

$eventId = (int) ($_GET['event'] ?? 0);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && verifyCsrf($_POST['csrf_token'] ?? null)) {
    $action = $_POST['action'] ?? '';

    if ($action === 'upload' && $eventId && !empty($_FILES['images']['name'][0])) {
        $uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/assets/img/gallery/event-' . $eventId;
        if (!is_dir($uploadDir)) @mkdir($uploadDir, 0775, true);

        foreach ($_FILES['images']['tmp_name'] as $idx => $tmp) {
            if (!$tmp || $_FILES['images']['error'][$idx] !== UPLOAD_ERR_OK) continue;

            $original = $_FILES['images']['name'][$idx];
            $ext = strtolower(pathinfo($original, PATHINFO_EXTENSION));
            if (!in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif'], true)) continue;

            $info = @getimagesize($tmp);
            if ($info === false) continue;

            $fileName = bin2hex(random_bytes(6)) . '.' . $ext;
            $target = $uploadDir . '/' . $fileName;
            if (!move_uploaded_file($tmp, $target)) continue;

            $publicPath = '/assets/img/gallery/event-' . $eventId . '/' . $fileName;
            $stmt = $pdo->prepare('INSERT INTO galleries (event_id, image_path, sort_order) VALUES (:e, :p, :o)');
            $stmt->execute(['e' => $eventId, 'p' => $publicPath, 'o' => $idx]);
        }
    }

    if ($action === 'delete') {
        $imgId = (int) ($_POST['image_id'] ?? 0);
        if ($imgId) {
            $row = fetchOne('SELECT image_path FROM galleries WHERE id=:id', ['id' => $imgId]);
            if ($row) {
                $disk = $_SERVER['DOCUMENT_ROOT'] . $row['image_path'];
                if (is_file($disk)) @unlink($disk);
            }
            $pdo->prepare('DELETE FROM galleries WHERE id=:id')->execute(['id' => $imgId]);
        }
    }

    if ($action === 'sort') {
        $order = $_POST['order'] ?? [];
        if (is_array($order)) {
            $stmt = $pdo->prepare('UPDATE galleries SET sort_order=:o WHERE id=:id');
            foreach ($order as $idx => $imgId) {
                $stmt->execute(['o' => (int) $idx, 'id' => (int) $imgId]);
            }
        }
    }

    header('Location: /admin/galleries.php' . ($eventId ? '?event=' . $eventId : ''));
    exit;
}

$events = fetchAll('SELECT * FROM events ORDER BY event_date DESC');
$current = $eventId ? fetchOne('SELECT * FROM events WHERE id=:id', ['id' => $eventId]) : null;
$images = $eventId ? fetchAll('SELECT * FROM galleries WHERE event_id=:e ORDER BY sort_order ASC, id ASC', ['e' => $eventId]) : [];

$pageTitle = 'Galerien · Admin';
$adminPage = 'galleries';
require __DIR__ . '/_header.php';
?>

<div class="admin-page-head">
  <h1>Galerien</h1>
</div>

<form method="get" class="admin-filter">
  <label>Event:
    <select name="event" onchange="this.form.submit()">
      <option value="0">— Event wählen —</option>
      <?php foreach ($events as $ev): ?>
        <option value="<?= (int) $ev['id'] ?>" <?= $eventId === (int) $ev['id'] ? 'selected' : '' ?>>
          <?= e($ev['event_date']) ?> · <?= e($ev['title']) ?>
        </option>
      <?php endforeach; ?>
    </select>
  </label>
</form>

<?php if ($current): ?>
  <h2><?= e($current['title']) ?> <span class="muted"><?= count($images) ?> Bilder</span></h2>

  <form method="post" enctype="multipart/form-data" class="admin-form admin-upload">
    <?= csrfField() ?>
    <input type="hidden" name="action" value="upload">
    <label class="field">
      <span>Bilder hochladen (mehrere möglich)</span>
      <input type="file" name="images[]" accept="image/*" multiple required>
    </label>
    <button class="btn btn-primary" type="submit">Hochladen</button>
  </form>

  <?php if ($images): ?>
    <div class="admin-gallery-grid">
      <?php foreach ($images as $img): ?>
        <div class="admin-gallery-item">
          <img src="<?= e($img['image_path']) ?>" alt="" loading="lazy">
          <form method="post" onsubmit="return confirm('Bild wirklich löschen?')">
            <?= csrfField() ?>
            <input type="hidden" name="action" value="delete">
            <input type="hidden" name="image_id" value="<?= (int) $img['id'] ?>">
            <button class="btn btn-ghost btn-sm" type="submit">Löschen</button>
          </form>
        </div>
      <?php endforeach; ?>
    </div>
  <?php else: ?>
    <p class="muted">Noch keine Bilder.</p>
  <?php endif; ?>
<?php else: ?>
  <p class="muted">Wähle ein Event aus, um Bilder zu verwalten.</p>
<?php endif; ?>

<?php require __DIR__ . '/_footer.php'; ?>
