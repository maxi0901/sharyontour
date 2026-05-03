<?php
require_once __DIR__ . '/auth.php';

if (isAdminLoggedIn()) {
    header('Location: /admin/');
    exit;
}

$error = '';
$redirect = (string) ($_GET['redirect'] ?? '/admin/');
if (!str_starts_with($redirect, '/admin/')) {
    $redirect = '/admin/';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = (string) ($_POST['password'] ?? '');
    $redirect = (string) ($_POST['redirect'] ?? '/admin/');
    if (!str_starts_with($redirect, '/admin/')) {
        $redirect = '/admin/';
    }

    if (password_verify($password, ADMIN_PASSWORD_HASH)) {
        session_regenerate_id(true);
        $_SESSION['admin_logged_in'] = true;
        header('Location: ' . $redirect);
        exit;
    }

    $error = 'Falsches Passwort. Bitte erneut versuchen.';
}
?>
<!doctype html>
<html lang="de">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Admin Login</title>
  <style>
    *{box-sizing:border-box}
    body{font-family:system-ui,-apple-system,Segoe UI,Roboto,sans-serif;background:#0f0f12;color:#f5f6f8;display:grid;place-items:center;min-height:100vh;margin:0;padding:1rem}
    .card{width:min(420px,100%);background:#171a20;border:1px solid #2a2f39;border-radius:16px;padding:1.5rem}
    h1{margin:0 0 1rem;font-size:1.35rem}
    label{display:block;font-size:.9rem;margin:.6rem 0 .35rem;color:#c9ced8}
    input{width:100%;padding:.75rem .9rem;border-radius:10px;border:1px solid #343b49;background:#0f131b;color:#fff;font-size:16px}
    button{margin-top:1rem;width:100%;padding:.75rem .9rem;border:none;border-radius:10px;background:#5a7fff;color:#fff;font-weight:600;cursor:pointer}
    .error{background:#3b1a1a;color:#ffc6c6;border:1px solid #7f2d2d;border-radius:10px;padding:.6rem .75rem;margin-bottom:.8rem}
    @media (max-width: 480px){
      body{padding:.75rem;align-items:start}
      .card{margin-top:8vh;border-radius:12px;padding:1rem}
      h1{font-size:1.2rem}
      label{font-size:.85rem}
      input,button{padding:.72rem .8rem}
    }
  </style>
</head>
<body>
  <form class="card" method="post">
    <h1>Admin Login</h1>
    <?php if ($error): ?>
      <div class="error"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div>
    <?php endif; ?>
    <label for="password">Passwort</label>
    <input id="password" name="password" type="password" required autofocus>
    <input type="hidden" name="redirect" value="<?= htmlspecialchars($redirect, ENT_QUOTES, 'UTF-8') ?>">
    <button type="submit">Einloggen</button>
  </form>
</body>
</html>
