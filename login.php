<?php
/**
 * User login.
 */
require_once __DIR__ . '/includes/functions.php';

setLanguage();

if (isLoggedIn()) {
    redirect('profile.php');
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
        } else {
            loginUser((int) $user['id']);
            setFlash(t('msg_login_success'), 'success');
            redirect((int) $user['is_admin'] === 1 ? 'admin/index.php' : 'profile.php');
        }
    }
}

$pageTitle       = t('login_title');
$pageDescription = t('login_subtitle');
$activeNav       = '';

require __DIR__ . '/includes/header.php';
?>

<section class="section">
  <div class="container">
    <div class="form-card">
      <div class="section-head" style="margin-bottom:26px">
        <h1 style="font-size:1.8rem"><?= e(t('login_title')) ?></h1>
        <p class="section-head__text"><?= e(t('login_subtitle')) ?></p>
      </div>

      <?php foreach ($errors as $error): ?>
        <div class="alert alert--error"><?= e($error) ?></div>
      <?php endforeach; ?>

      <form method="post" action="<?= e(url('login.php')) ?>" novalidate>
        <?= csrfField() ?>
        <div class="form-group">
          <label for="email"><?= e(t('field_email')) ?> <span class="required">*</span></label>
          <input type="email" id="email" name="email" value="<?= e($email) ?>" required autocomplete="email">
        </div>
        <div class="form-group">
          <label for="password"><?= e(t('field_password')) ?> <span class="required">*</span></label>
          <input type="password" id="password" name="password" required autocomplete="current-password">
        </div>
        <button class="btn btn--primary btn--block btn--lg" type="submit"><?= e(t('btn_login')) ?></button>
      </form>

      <p style="text-align:center;margin:20px 0 0">
        <?= e(t('no_account')) ?> <a href="<?= e(url('register.php')) ?>"><?= e(t('nav_register')) ?></a>
      </p>
    </div>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
