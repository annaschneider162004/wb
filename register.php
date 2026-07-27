<?php
/**
 * User registration.
 */
require_once __DIR__ . '/includes/functions.php';

setLanguage();

if (isLoggedIn()) {
    redirect('profile.php');
}

$errors = [];
$values = ['name' => '', 'email' => '', 'phone' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $values['name']  = trim((string) ($_POST['name'] ?? ''));
    $values['email'] = trim((string) ($_POST['email'] ?? ''));
    $values['phone'] = trim((string) ($_POST['phone'] ?? ''));
    $password        = (string) ($_POST['password'] ?? '');
    $passwordConfirm = (string) ($_POST['password_confirm'] ?? '');

    if (!verifyCsrf()) {
        $errors[] = t('msg_csrf');
    }
    if ($values['name'] === '' || $values['email'] === '' || $password === '') {
        $errors[] = t('msg_required_fields');
    }
    if ($values['email'] !== '' && !filter_var($values['email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = t('msg_invalid_email');
    }
    if ($password !== '' && strlen($password) < 8) {
        $errors[] = t('msg_password_short');
    }
    if ($password !== $passwordConfirm) {
        $errors[] = t('msg_password_mismatch');
    }
    if (!$errors && dbOne('SELECT id FROM users WHERE email = ?', [$values['email']])) {
        $errors[] = t('msg_email_exists');
    }

    if (!$errors) {
        $userId = dbInsert('users', [
            'name'     => $values['name'],
            'email'    => $values['email'],
            'phone'    => $values['phone'],
            'password' => password_hash($password, PASSWORD_DEFAULT),
            'is_admin' => 0,
            'is_active' => 1,
        ]);
        loginUser($userId);
        setFlash(t('msg_register_success'), 'success');
        redirect('profile.php');
    }
}

$pageTitle       = t('register_title');
$pageDescription = t('register_subtitle');
$activeNav       = '';

require __DIR__ . '/includes/header.php';
?>

<section class="section">
  <div class="container">
    <div class="form-card">
      <div class="section-head" style="margin-bottom:26px">
        <h1 style="font-size:1.8rem"><?= e(t('register_title')) ?></h1>
        <p class="section-head__text"><?= e(t('register_subtitle')) ?></p>
      </div>

      <?php foreach ($errors as $error): ?>
        <div class="alert alert--error"><?= e($error) ?></div>
      <?php endforeach; ?>

      <form method="post" action="<?= e(url('register.php')) ?>" novalidate>
        <?= csrfField() ?>
        <div class="form-group">
          <label for="name"><?= e(t('field_name')) ?> <span class="required">*</span></label>
          <input type="text" id="name" name="name" value="<?= e($values['name']) ?>" required autocomplete="name">
        </div>
        <div class="form-group">
          <label for="email"><?= e(t('field_email')) ?> <span class="required">*</span></label>
          <input type="email" id="email" name="email" value="<?= e($values['email']) ?>" required autocomplete="email">
        </div>
        <div class="form-group">
          <label for="phone"><?= e(t('field_phone')) ?></label>
          <input type="tel" id="phone" name="phone" value="<?= e($values['phone']) ?>" autocomplete="tel">
        </div>
        <div class="form-group">
          <label for="password"><?= e(t('field_password')) ?> <span class="required">*</span></label>
          <input type="password" id="password" name="password" required autocomplete="new-password" minlength="8">
          <p class="form-hint"><?= e(t('msg_password_short')) ?></p>
        </div>
        <div class="form-group">
          <label for="password_confirm"><?= e(t('field_password_confirm')) ?> <span class="required">*</span></label>
          <input type="password" id="password_confirm" name="password_confirm" required autocomplete="new-password">
        </div>
        <button class="btn btn--primary btn--block btn--lg" type="submit"><?= e(t('btn_register')) ?></button>
      </form>

      <p style="text-align:center;margin:20px 0 0">
        <?= e(t('have_account')) ?> <a href="<?= e(url('login.php')) ?>"><?= e(t('nav_login')) ?></a>
      </p>
    </div>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
