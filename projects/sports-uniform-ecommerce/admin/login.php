<?php
require_once __DIR__ . '/../includes/functions.php';
if (admin_user()) redirect('admin/index.php');

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_csrf();
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $u = fetch_one('SELECT * FROM admin_users WHERE username = ?', [$username]);
    if ($u && password_verify($password, $u['password_hash'])) {
        $_SESSION['admin'] = ['id' => $u['id'], 'name' => $u['name'], 'username' => $u['username']];
        session_regenerate_id(true);
        redirect('admin/index.php');
    }
    $error = 'Invalid username or password.';
}
?>
<!DOCTYPE html><html lang="en"><head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>CMS Login — <?= e(get_setting('site_name')) ?></title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?= url('assets/css/admin.css') ?>">
</head><body class="adm-login">
<form class="loginbox" method="post" action="<?= url('admin/login.php') ?>">
  <?= csrf_field() ?>
  <div class="loginbox__brand">◆ <?= e(get_setting('logo_text')) ?> <span>CMS</span></div>
  <?php if ($error): ?><div class="loginbox__err"><?= e($error) ?></div><?php endif; ?>
  <label>Username<input type="text" name="username" value="admin" autofocus required></label>
  <label>Password<input type="password" name="password" required></label>
  <button type="submit" class="btn btn--brand btn--block btn--lg">Log in</button>
  <p class="loginbox__hint">Demo login — <strong>admin</strong> / <strong>admin123</strong></p>
</form>
</body></html>
