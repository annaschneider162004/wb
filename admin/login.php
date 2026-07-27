<?php
/**
 * Admin login screen.
 */
require_once dirname(__DIR__) . '/includes/functions.php';

setLanguage();

if (isAdmin()) {
    redirect('admin/index.php');
}

$errors = [];
$email  = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim((string) ($_POST['email'] ?? ''));
    $password = (string) ($_POST['password'] ?? '');

    if (!verifyCsrf()) {
        $errors[] = t('msg_csrf');
    }
    if ($email === '' || $password === '') {
        $errors[] = t('msg_required_fields');
    }

    if (!$errors) {
        $user = dbOne('SELECT * FROM users WHERE email = ?', [$email]);
        if (!$user || !password_verify($password, $user['password'])) {
            $errors[] = t('msg_login_failed');
        } elseif ((int) $user['is_active'] !== 1) {
            $errors[] = t('msg_account_locked');
        } elseif ((int) $user['is_admin'] !== 1) {
            $errors[] = t('msg_admin_required');
        } else {
            loginUser((int) $user['id']);
            setFlash(t('msg_login_success'), 'success');
            redirect('admin/index.php');
        }
    }
}
?>
<!DOCTYPE html>
<html lang="<?= e(currentLang()) ?>">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="robots" content="noindex, nofollow">
  <title><?= e(t('admin_login_title')) ?> — <?= e(SITE_NAME) ?></title>
  <link rel="icon" type="image/svg+xml" href="<?= e(asset('assets/images/logo.svg')) ?>">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800&amp;family=Poppins:wght@500;600;700;800&amp;display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?= e(asset('assets/css/style.css')) ?>">
</head>
<body class="admin-body">
<div class="admin-login">
  <div class="form-card" style="max-width:430px">
    <div style="text-align:center;margin-bottom:24px">
      <img src="<?= e(asset('assets/images/logo.svg')) ?>" alt="<?= e(SITE_NAME) ?>" width="64" height="64">
      <h1 style="font-size:1.5rem;margin:12px 0 4px"><?= e(t('admin_login_title')) ?></h1>
      <p class="text-muted" style="margin:0"><?= e(SITE_NAME) ?></p>
    </div>

    <?php foreach (getFlashes() as $flash): ?>
      <div class="alert alert--<?= e($flash['type']) ?>"><?= e($flash['message']) ?></div>
    <?php endforeach; ?>
    <?php foreach ($errors as $error): ?>
      <div class="alert alert--error"><?= e($error) ?></div>
    <?php endforeach; ?>

    <form method="post" action="<?= e(url('admin/login.php')) ?>" novalidate>
      <?= csrfField() ?>
      <div class="form-group">
        <label for="email"><?= e(t('field_email')) ?></label>
        <input type="email" id="email" name="email" value="<?= e($email) ?>" required autocomplete="email">
      </div>
      <div class="form-group">
        <label for="password"><?= e(t('field_password')) ?></label>
        <input type="password" id="password" name="password" required autocomplete="current-password">
      </div>
      <button class="btn btn--primary btn--block btn--lg" type="submit"><?= e(t('btn_login')) ?></button>
    </form>

    <p style="text-align:center;margin:18px 0 0">
      <a href="<?= e(url('index.php')) ?>">&larr; <?= e(t('home')) ?></a>
    </p>
  </div>
</div>
</body>
</html>
