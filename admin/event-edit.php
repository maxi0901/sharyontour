<?php
require __DIR__ . '/../config/bootstrap.php';
require __DIR__ . '/../includes/csrf.php';

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$item = [
    'title' => '', 'slug' => '', 'event_date' => '', 'event_time' => '',
    'city' => '', 'location_name' => '', 'address' => '',
    'description_short' => '', 'description_long' => '', 'image_path' => '',
    'status' => 'upcoming', 'is_opening' => 0, 'max_tickets' => 0,
    'google_maps_url' => '',
];
if ($id) {
    $item = fetchOne('SELECT * FROM events WHERE id=:id', ['id' => $id]) ?? $item;
}

$error = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrf($_POST['csrf_token'] ?? null)) {
        $error = 'CSRF Fehler.';
    } else {
        $slug = trim($_POST['slug'] ?? '') ?: createSlug($_POST['title'] ?? '');
        $imagePath = trim($_POST['image_path'] ?? '');

        if (isset($_POST['remove_image']) && $_POST['remove_image'] === '1') {
            $imagePath = '';
        }

        if (!empty($_FILES['image_file']['name']) && (int) ($_FILES['image_file']['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE) {
            $upload = $_FILES['image_file'];
            if ((int) $upload['error'] !== UPLOAD_ERR_OK) {
                $error = 'Bild-Upload fehlgeschlagen (Code ' . (int) $upload['error'] . ').';
            } elseif ($upload['size'] > 8 * 1024 * 1024) {
                $error = 'Bild ist größer als 8 MB.';
            } else {
                $finfo = function_exists('finfo_open') ? finfo_open(FILEINFO_MIME_TYPE) : false;
                $mime = $finfo ? finfo_file($finfo, $upload['tmp_name']) : ($upload['type'] ?? '');
                if ($finfo) finfo_close($finfo);

                $extByMime = [
                    'image/jpeg' => 'jpg',
                    'image/png'  => 'png',
                    'image/webp' => 'webp',
                    'image/gif'  => 'gif',
                ];
                if (!isset($extByMime[$mime])) {
                    $error = 'Bildformat nicht unterstützt (nur JPG, PNG, WEBP, GIF).';
                } else {
                    $ext = $extByMime[$mime];
                    $base = $slug !== '' ? $slug : 'event';
                    $filename = $base . '-' . date('YmdHis') . '-' . substr(bin2hex(random_bytes(3)), 0, 6) . '.' . $ext;
                    $targetDir = __DIR__ . '/../assets/img/events';
                    if (!is_dir($targetDir)) {
                        @mkdir($targetDir, 0755, true);
                    }
                    $targetFile = $targetDir . '/' . $filename;
                    if (!move_uploaded_file($upload['tmp_name'], $targetFile)) {
                        $error = 'Bild konnte nicht gespeichert werden.';
                    } else {
                        $imagePath = '/assets/img/events/' . $filename;
                    }
                }
            }
        }
    }

    if (!isset($error) || $error === null) {
        $data = [
            'title' => trim($_POST['title'] ?? ''),
            'slug' => $slug,
            'event_date' => $_POST['event_date'] ?? null,
            'event_time' => ($_POST['event_time'] ?? '') ?: null,
            'city' => trim($_POST['city'] ?? ''),
            'location_name' => trim($_POST['location_name'] ?? '') ?: null,
            'address' => trim($_POST['address'] ?? '') ?: null,
            'description_short' => trim($_POST['description_short'] ?? ''),
            'description_long' => trim($_POST['description_long'] ?? ''),
            'image_path' => $imagePath !== '' ? $imagePath : null,
            'status' => in_array($_POST['status'] ?? 'upcoming', ['upcoming', 'past', 'draft'], true) ? $_POST['status'] : 'upcoming',
            'is_opening' => isset($_POST['is_opening']) ? 1 : 0,
            'max_tickets' => (int) ($_POST['max_tickets'] ?? 0),
            'google_maps_url' => trim($_POST['google_maps_url'] ?? '') ?: null,
        ];

        if ($id) {
            $stmt = $pdo->prepare('UPDATE events SET title=:title,slug=:slug,event_date=:event_date,event_time=:event_time,city=:city,location_name=:location_name,address=:address,description_short=:description_short,description_long=:description_long,image_path=:image_path,status=:status,is_opening=:is_opening,max_tickets=:max_tickets,google_maps_url=:google_maps_url WHERE id=:id');
            $data['id'] = $id;
            $stmt->execute($data);
        } else {
            $stmt = $pdo->prepare('INSERT INTO events (title,slug,event_date,event_time,city,location_name,address,description_short,description_long,image_path,status,is_opening,max_tickets,google_maps_url) VALUES (:title,:slug,:event_date,:event_time,:city,:location_name,:address,:description_short,:description_long,:image_path,:status,:is_opening,:max_tickets,:google_maps_url)');
            $stmt->execute($data);
        }
        header('Location: /admin/events.php');
        exit;
    }
}

$pageTitle = ($id ? 'Event bearbeiten' : 'Neues Event') . ' · Admin';
$adminPage = 'events';
require __DIR__ . '/_header.php';
?>

<div class="admin-page-head">
  <h1><?= $id ? 'Event bearbeiten' : 'Neues Event' ?></h1>
  <a class="text-link" href="/admin/events.php">← Zurück</a>
</div>

<?php if ($error): ?><div class="form-flash form-flash-error"><?= e($error) ?></div><?php endif; ?>

<form method="post" class="admin-form" enctype="multipart/form-data">
  <?= csrfField() ?>

  <div class="form-grid-2">
    <label class="field"><span>Titel *</span><input name="title" required value="<?= e($item['title']) ?>"></label>
    <label class="field"><span>Slug</span><input name="slug" value="<?= e($item['slug']) ?>"></label>
  </div>

  <div class="form-grid-3">
    <label class="field"><span>Datum *</span><input type="date" name="event_date" required value="<?= e($item['event_date']) ?>"></label>
    <label class="field"><span>Uhrzeit</span><input type="time" name="event_time" value="<?= e((string) $item['event_time']) ?>"></label>
    <label class="field"><span>Stadt *</span><input name="city" required value="<?= e($item['city']) ?>"></label>
  </div>

  <div class="form-grid-2">
    <label class="field"><span>Location-Name</span><input name="location_name" value="<?= e((string) $item['location_name']) ?>"></label>
    <label class="field"><span>Adresse</span><input name="address" value="<?= e((string) $item['address']) ?>"></label>
  </div>

  <label class="field"><span>Google Maps URL</span><input name="google_maps_url" value="<?= e((string) $item['google_maps_url']) ?>"></label>

  <label class="field"><span>Kurzbeschreibung</span><textarea name="description_short"><?= e((string) $item['description_short']) ?></textarea></label>
  <label class="field"><span>Lange Beschreibung</span><textarea name="description_long" rows="5"><?= e((string) $item['description_long']) ?></textarea></label>

  <div class="event-image-field">
    <span class="field-label">Event-Bild</span>
    <div class="event-image-preview">
      <?php if (!empty($item['image_path'])): ?>
        <img src="<?= e((string) $item['image_path']) ?>" alt="Aktuelles Event-Bild">
      <?php else: ?>
        <div class="media-fallback" aria-hidden="true"></div>
        <p class="muted">Noch kein Bild – auf der Seite wird der Platzhalter angezeigt.</p>
      <?php endif; ?>
    </div>
    <label class="field">
      <span>Neues Bild hochladen (JPG, PNG, WEBP, GIF · max. 8 MB)</span>
      <input type="file" name="image_file" accept="image/jpeg,image/png,image/webp,image/gif">
    </label>
    <label class="field"><span>oder Bildpfad manuell (optional)</span><input name="image_path" value="<?= e((string) $item['image_path']) ?>" placeholder="/assets/img/events/..."></label>
    <?php if (!empty($item['image_path'])): ?>
      <label class="check check-inline">
        <input type="checkbox" name="remove_image" value="1">
        Bild entfernen (leeres Feld speichern)
      </label>
    <?php endif; ?>
  </div>

  <div class="form-grid-3">
    <label class="field">
      <span>Status</span>
      <select name="status">
        <?php foreach (['upcoming', 'past', 'draft'] as $s): ?>
          <option value="<?= $s ?>" <?= $item['status'] === $s ? 'selected' : '' ?>><?= $s ?></option>
        <?php endforeach; ?>
      </select>
    </label>
    <label class="field check-field">
      <input type="checkbox" name="is_opening" value="1" <?= (int) $item['is_opening'] === 1 ? 'checked' : '' ?>>
      <span>Haupt-Event (Ticket-Funktion)</span>
    </label>
    <label class="field"><span>Max Tickets</span><input type="number" name="max_tickets" min="0" value="<?= (int) $item['max_tickets'] ?>"></label>
  </div>

  <div>
    <button class="btn btn-primary" type="submit">Speichern</button>
    <a class="btn btn-ghost" href="/admin/events.php">Abbrechen</a>
  </div>
</form>

<?php require __DIR__ . '/_footer.php'; ?>
