<?php
/**
 * Admin layout header. Requires auth-check.php to have been included first.
 */
if (!isset($adminUser)) {
    require_once __DIR__ . '/auth-check.php';
}

$adminTitle  = $adminTitle ?? t('admin_dashboard');
$adminActive = $adminActive ?? 'dashboard';

$adminNav = [
    'dashboard'   => ['admin/index.php', 'chart', t('admin_dashboard')],
    'courses'     => ['admin/courses.php', 'music', t('admin_courses')],
    'posts'       => ['admin/posts.php', 'library', t('admin_posts')],
    'instructors' => ['admin/instructors.php', 'teacher', t('admin_instructors')],
    'levels'      => ['admin/levels.php', 'route', t('admin_levels')],
    'features'    => ['admin/features.php', 'star', t('admin_features')],
    'members'     => ['admin/members.php', 'user', t('admin_members')],
    'settings'    => ['admin/settings.php', 'globe', t('admin_settings')],
];
?>
<!DOCTYPE html>
<html lang="<?= e(currentLang()) ?>">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="robots" content="noindex, nofollow">
  <title><?= e($adminTitle) ?> — <?= e(SITE_NAME) ?></title>
  <link rel="icon" type="image/svg+xml" href="<?= e(asset('assets/images/logo.svg')) ?>">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800&amp;family=Poppins:wght@500;600;700;800&amp;display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?= e(asset('assets/css/style.css')) ?>">
</head>
<body class="admin-body">
<div class="admin-layout">
  <aside class="admin-sidebar">
    <a class="brand" href="<?= e(url('admin/index.php')) ?>">
      <img class="brand__logo" src="<?= e(asset('assets/images/logo.svg')) ?>" alt="<?= e(SITE_NAME) ?>" width="42" height="42">
      <span class="brand__text">
        <span class="brand__name"><?= e(SITE_NAME) ?></span>
        <span class="brand__tagline"><?= e(t('admin_panel')) ?></span>
      </span>
    </a>

    <nav class="admin-nav">
      <?php foreach ($adminNav as $key => [$path, $iconName, $label]): ?>
        <a class="admin-nav__link<?= $adminActive === $key ? ' is-active' : '' ?>" href="<?= e(url($path)) ?>">
          <?= icon($iconName, 'icon icon--sm') ?><span><?= e($label) ?></span>
        </a>
      <?php endforeach; ?>

      <div class="admin-nav__sep"><?= e(SITE_NAME) ?></div>
      <a class="admin-nav__link" href="<?= e(url('index.php')) ?>" target="_blank" rel="noopener">
        <?= icon('globe', 'icon icon--sm') ?><span><?= e(t('admin_view_site')) ?></span>
      </a>
      <a class="admin-nav__link" href="<?= e(url('admin/logout.php')) ?>">
        <?= icon('arrow', 'icon icon--sm') ?><span><?= e(t('nav_logout')) ?></span>
      </a>
    </nav>
  </aside>

  <main class="admin-main">
    <div class="admin-topbar">
      <h1><?= e($adminTitle) ?></h1>
      <div class="admin-topbar__right" style="display:flex;align-items:center;gap:14px">
        <span class="lang-switch">
          <a class="lang-switch__btn<?= currentLang() === 'vi' ? ' is-active' : '' ?>" href="<?= e(langSwitchUrl('vi')) ?>">VI</a>
          <a class="lang-switch__btn<?= currentLang() === 'en' ? ' is-active' : '' ?>" href="<?= e(langSwitchUrl('en')) ?>">EN</a>
        </span>
        <span style="font-weight:700"><?= e($adminUser['name']) ?></span>
      </div>
    </div>

    <div class="admin-content">
      <?php foreach (getFlashes() as $flash): ?>
        <div class="alert alert--<?= e($flash['type']) ?>"><?= e($flash['message']) ?></div>
      <?php endforeach; ?>
