<?php
require __DIR__ . '/../includes/functions.php';
require __DIR__ . '/../includes/csrf.php';
$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$item = ['title'=>'','slug'=>'','event_date'=>'','event_time'=>'','city'=>'','location_name'=>'','address'=>'','description_short'=>'','description_long'=>'','image_path'=>'','status'=>'draft','is_featured'=>0];
if ($id) { $item = fetchOne('SELECT * FROM events WHERE id=:id',['id'=>$id]) ?? $item; }
if ($_SERVER['REQUEST_METHOD']==='POST') {
  if (!verifyCsrf($_POST['csrf_token'] ?? null)) { exit('CSRF error'); }
  $data = [
    'title'=>trim($_POST['title'] ?? ''), 'slug'=>trim($_POST['slug'] ?? '') ?: createSlug($_POST['title'] ?? ''), 'event_date'=>$_POST['event_date'] ?? null,
    'event_time'=>($_POST['event_time'] ?? '') ?: null, 'city'=>trim($_POST['city'] ?? ''), 'location_name'=>trim($_POST['location_name'] ?? ''), 'address'=>trim($_POST['address'] ?? '') ?: null,
    'description_short'=>trim($_POST['description_short'] ?? ''), 'description_long'=>trim($_POST['description_long'] ?? ''), 'image_path'=>trim($_POST['image_path'] ?? '') ?: null,
    'status'=>in_array($_POST['status'] ?? 'draft',['upcoming','past','draft'],true) ? $_POST['status'] : 'draft', 'is_featured'=>isset($_POST['is_featured']) ? 1 : 0
  ];
  if ($id) {
    $stmt=$pdo->prepare('UPDATE events SET title=:title,slug=:slug,event_date=:event_date,event_time=:event_time,city=:city,location_name=:location_name,address=:address,description_short=:description_short,description_long=:description_long,image_path=:image_path,status=:status,is_featured=:is_featured WHERE id=:id');
    $data['id']=$id; $stmt->execute($data);
  } else {
    $stmt=$pdo->prepare('INSERT INTO events (title,slug,event_date,event_time,city,location_name,address,description_short,description_long,image_path,status,is_featured) VALUES (:title,:slug,:event_date,:event_time,:city,:location_name,:address,:description_short,:description_long,:image_path,:status,:is_featured)');
    $stmt->execute($data);
  }
  header('Location: /admin/events.php');exit;
}
?><!doctype html><html lang="de"><head><meta charset="utf-8"><link rel="stylesheet" href="/assets/css/style.css"><title>Event edit</title></head><body class="admin"><div class="container"><h1>Event <?= $id?'bearbeiten':'neu' ?></h1><form method="post"><?= csrfField() ?><label>Titel<input name="title" value="<?= e($item['title']) ?>" required></label><label>Slug<input name="slug" value="<?= e($item['slug']) ?>"></label><label>Datum<input type="date" name="event_date" value="<?= e($item['event_date']) ?>" required></label><label>Uhrzeit<input type="time" name="event_time" value="<?= e((string)$item['event_time']) ?>"></label><label>Stadt<input name="city" value="<?= e($item['city']) ?>" required></label><label>Location<input name="location_name" value="<?= e($item['location_name']) ?>" required></label><label>Adresse<input name="address" value="<?= e((string)$item['address']) ?>"></label><label>Short<textarea name="description_short" required><?= e($item['description_short']) ?></textarea></label><label>Long<textarea name="description_long" required><?= e($item['description_long']) ?></textarea></label><label>Bildpfad<input name="image_path" value="<?= e((string)$item['image_path']) ?>"></label><label>Status<select name="status"><?php foreach(['upcoming','past','draft'] as $s):?><option value="<?= $s ?>" <?= $item['status']===$s?'selected':'' ?>><?= $s ?></option><?php endforeach;?></select></label><label><input type="checkbox" name="is_featured" value="1" <?= (int)$item['is_featured']===1?'checked':'' ?>> Featured</label><button class="btn btn-pink">Speichern</button></form></div></body></html>
