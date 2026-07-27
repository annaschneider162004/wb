<?php
/**
 * Member profile page: update details and change password.
 */
require_once __DIR__ . '/includes/functions.php';

setLanguage();
requireLogin();

$user   = currentUser();
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = (string) ($_POST['action'] ?? '');

    if (!verifyCsrf()) {
        $errors[] = t('msg_csrf');
    }

    if (!$errors && $action === 'profile') {
        $name  = trim((string) ($_POST['name'] ?? ''));
        $email = trim((string) ($_POST['email'] ?? ''));
        $phone = trim((string) ($_POST['phone'] ?? ''));

        if ($name === '' || $email === '') {
            $errors[] = t('msg_required_fields');
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = t('msg_invalid_email');
        } elseif (dbOne('SELECT id FROM users WHERE email = ? AND id <> ?', [$email, (int) $user['id']])) {
            $errors[] = t('msg_email_exists');
        }

        if (!$errors) {
            $data = ['name' => $name, 'email' => $email, 'phone' => $phone];
            try {
                $avatar = handleImageUpload('avatar', $user['avatar']);
                if ($avatar !== $user['avatar']) {
                    $data['avatar'] = $avatar;
                }
            } catch (RuntimeException $e) {
                $errors[] = $e->getMessage();
            }

            if (!$errors) {
                dbUpdate('users', $data, (int) $user['id']);
                setFlash(t('msg_profile_updated'), 'success');
                redirect('profile.php');
            }
        }
    }

    if (!$errors && $action === 'password') {
        $current = (string) ($_POST['current_password'] ?? '');
        $new     = (string) ($_POST['new_password'] ?? '');
        $confirm = (string) ($_POST['password_confirm'] ?? '');

        if ($current === '' || $new === '') {
            $errors[] = t('msg_required_fields');
        } elseif (!password_verify($current, $user['password'])) {
            $errors[] = t('msg_wrong_password');
        } elseif (strlen($new) < 8) {
            $errors[] = t('msg_password_short');
        } elseif ($new !== $confirm) {
            $errors[] = t('msg_password_mismatch');
        }

        if (!$errors) {
            dbUpdate('users', ['password' => password_hash($new, PASSWORD_DEFAULT)], (int) $user['id']);
            setFlash(t('msg_password_updated'), 'success');
            redirect('profile.php');
        }
    }
}

$pageTitle       = t('profile_title');
$pageDescription = t('profile_info');
$activeNav       = '';

require __DIR__ . '/includes/header.php';
?>

<section class="page-hero">
  <div class="container">
    <nav class="breadcrumb" aria-label="Breadcrumb">
      <a href="<?= e(url('index.php')) ?>"><?= e(t('home')) ?></a><span>/</span><span><?= e(t('profile_title')) ?></span>
    </nav>
    <h1><?= e($user['name']) ?></h1>
    <p><?= e(t('member_since')) ?>: <?= e(formatDate($user['created_at'])) ?></p>
  </div>
</section>

<section class="section">
  <div class="container">
    <?php foreach ($errors as $error): ?>
      <div class="alert alert--error"><?= e($error) ?></div>
    <?php endforeach; ?>

    <div class="grid grid--2">
      <div class="detail-card">
        <h2><?= e(t('profile_info')) ?></h2>
        <form method="post" action="<?= e(url('profile.php')) ?>" enctype="multipart/form-data" novalidate>
          <?= csrfField() ?>
          <input type="hidden" name="action" value="profile">

          <div class="form-group" style="text-align:center">
            <img data-preview-current src="<?= e(imageUrl($user['avatar'], 'assets/images/logo.svg')) ?>" alt="<?= e($user['name']) ?>"
                 style="width:110px;height:110px;border-radius:50%;object-fit:cover;margin:0 auto 12px;background:var(--green-100)">
            <canvas data-preview-target width="110" height="110" hidden style="width:110px;height:110px;border-radius:50%;display:block;margin:0 auto 12px;background:var(--green-100)"></canvas>
            <label for="avatar"><?= e(currentLang() === 'en' ? 'Avatar' : 'Ảnh đại diện') ?></label>
            <input type="file" id="avatar" name="avatar" accept="image/*" data-preview>
          </div>

          <div class="form-group">
            <label for="name"><?= e(t('field_name')) ?> <span class="required">*</span></label>
            <input type="text" id="name" name="name" value="<?= e($user['name']) ?>" required>
          </div>
          <div class="form-group">
            <label for="email"><?= e(t('field_email')) ?> <span class="required">*</span></label>
            <input type="email" id="email" name="email" value="<?= e($user['email']) ?>" required>
          </div>
          <div class="form-group">
            <label for="phone"><?= e(t('field_phone')) ?></label>
            <input type="tel" id="phone" name="phone" value="<?= e($user['phone'] ?? '') ?>">
          </div>
          <button class="btn btn--primary" type="submit"><?= e(t('btn_save')) ?></button>
        </form>
      </div>

      <div class="detail-card">
        <h2><?= e(t('profile_password')) ?></h2>
        <form method="post" action="<?= e(url('profile.php')) ?>" novalidate>
          <?= csrfField() ?>
          <input type="hidden" name="action" value="password">
          <div class="form-group">
            <label for="current_password"><?= e(t('field_current_password')) ?> <span class="required">*</span></label>
            <input type="password" id="current_password" name="current_password" required autocomplete="current-password">
          </div>
          <div class="form-group">
            <label for="new_password"><?= e(t('field_new_password')) ?> <span class="required">*</span></label>
            <input type="password" id="new_password" name="new_password" required autocomplete="new-password" minlength="8">
          </div>
          <div class="form-group">
            <label for="password_confirm"><?= e(t('field_password_confirm')) ?> <span class="required">*</span></label>
            <input type="password" id="password_confirm" name="password_confirm" required autocomplete="new-password">
          </div>
          <button class="btn btn--primary" type="submit"><?= e(t('btn_save')) ?></button>
        </form>

        <?php if ((int) $user['is_admin'] === 1): ?>
          <hr style="border:0;border-top:1px solid var(--line);margin:24px 0">
          <a class="btn btn--outline btn--block" href="<?= e(url('admin/index.php')) ?>"><?= e(t('nav_admin')) ?></a>
        <?php endif; ?>
      </div>
    </div>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
