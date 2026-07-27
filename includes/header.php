<?php
/**
 * Public site header.
 *
 * Expected optional variables set before including:
 *   $pageTitle, $pageDescription, $activeNav, $bodyClass
 */

require_once __DIR__ . '/functions.php';

setLanguage();

$lang            = currentLang();
$pageTitle       = $pageTitle ?? setting('site_name', SITE_NAME);
$pageDescription = $pageDescription ?? t('footer_about');
$activeNav       = $activeNav ?? '';
$bodyClass       = $bodyClass ?? '';
$user            = currentUser();

$navItems = [
    'home'        => ['label' => t('nav_home'),        'url' => url('index.php')],
    'courses'     => ['label' => t('nav_courses'),     'url' => url('khoa-hoc.php')],
    'instruments' => ['label' => t('nav_instruments'), 'url' => url('khoa-hoc.php') . '#instruments'],
    'instructors' => ['label' => t('nav_instructors'), 'url' => url('giang-vien.php')],
    'path'        => ['label' => t('nav_learning_path'), 'url' => url('index.php') . '#levels'],
    'library'     => ['label' => t('nav_library'),     'url' => url('thu-vien.php')],
    'community'   => ['label' => t('nav_community'),   'url' => url('cong-dong.php')],
    'about'       => ['label' => t('nav_about'),       'url' => url('ve-chung-toi.php')],
];
?>
<!DOCTYPE html>
<html lang="<?= e($lang) ?>">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($pageTitle) ?> | <?= e(setting('site_name', SITE_NAME)) ?></title>
<meta name="description" content="<?= e(excerpt($pageDescription, 300)) ?>">
<meta property="og:title" content="<?= e($pageTitle) ?>">
<meta property="og:description" content="<?= e(excerpt($pageDescription, 300)) ?>">
<meta property="og:type" content="website">
<meta property="og:site_name" content="<?= e(setting('site_name', SITE_NAME)) ?>">
<link rel="icon" type="image/svg+xml" href="<?= e(asset('images/logo.svg')) ?>">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800;900&amp;family=Poppins:wght@400;500;600;700;800&amp;display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?= e(asset('css/style.css')) ?>">
</head>
<body class="<?= e($bodyClass) ?>">
<a class="skip-link" href="#main"><?= e(t('nav_home')) ?></a>

<header class="site-header" id="siteHeader">
  <div class="container site-header__inner">
    <a class="brand" href="<?= e(url('index.php')) ?>">
      <img class="brand__logo" src="<?= e(asset('images/logo.svg')) ?>" alt="<?= e(setting('site_name', SITE_NAME)) ?>" width="46" height="46">
      <span class="brand__text">
        <span class="brand__name"><?= e(setting('site_name', SITE_NAME)) ?></span>
        <span class="brand__tagline"><?= e(t('brand_tagline')) ?></span>
      </span>
    </a>

    <button class="nav-toggle" type="button" aria-label="Menu" aria-expanded="false" data-nav-toggle>
      <span></span><span></span><span></span>
    </button>

    <nav class="main-nav" id="mainNav" aria-label="Main">
      <ul class="main-nav__list">
        <?php foreach ($navItems as $key => $item): ?>
          <li>
            <a class="main-nav__link<?= $activeNav === $key ? ' is-active' : '' ?>" href="<?= e($item['url']) ?>"><?= e($item['label']) ?></a>
          </li>
        <?php endforeach; ?>
      </ul>

      <div class="header-actions">
        <div class="lang-switch" role="group" aria-label="Language">
          <a class="lang-switch__btn<?= $lang === 'vi' ? ' is-active' : '' ?>" href="<?= e(langSwitchUrl('vi')) ?>">VI</a>
          <a class="lang-switch__btn<?= $lang === 'en' ? ' is-active' : '' ?>" href="<?= e(langSwitchUrl('en')) ?>">EN</a>
        </div>

        <?php if ($user): ?>
          <a class="btn btn--ghost btn--sm" href="<?= e(url('profile.php')) ?>"><?= e($user['name']) ?></a>
          <?php if ((int) $user['is_admin'] === 1): ?>
            <a class="btn btn--ghost btn--sm" href="<?= e(url('admin/index.php')) ?>"><?= e(t('nav_admin')) ?></a>
          <?php endif; ?>
          <a class="btn btn--primary btn--sm" href="<?= e(url('logout.php')) ?>"><?= e(t('nav_logout')) ?></a>
        <?php else: ?>
          <a class="btn btn--ghost btn--sm" href="<?= e(url('login.php')) ?>"><?= e(t('nav_login')) ?></a>
          <a class="btn btn--primary btn--sm" href="<?= e(url('register.php')) ?>"><?= e(t('nav_register')) ?></a>
        <?php endif; ?>
      </div>
    </nav>
  </div>
</header>

<?php $flashes = getFlashes(); ?>
<?php if ($flashes): ?>
  <div class="container flash-stack">
    <?php foreach ($flashes as $flash): ?>
      <div class="alert alert--<?= e($flash['type']) ?>"><?= e($flash['message']) ?></div>
    <?php endforeach; ?>
  </div>
<?php endif; ?>

<main id="main">
