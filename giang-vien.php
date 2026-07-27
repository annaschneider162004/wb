<?php
/**
 * Instructors listing page.
 */
require_once __DIR__ . '/includes/functions.php';

setLanguage();

$instructors = dbAll('SELECT * FROM instructors WHERE is_active = 1 ORDER BY sort_order ASC, id ASC');

$pageTitle       = t('instructors_page_title');
$pageDescription = t('instructors_page_subtitle');
$activeNav       = 'instructors';

require __DIR__ . '/includes/header.php';
?>

<section class="page-hero">
  <div class="container">
    <nav class="breadcrumb" aria-label="Breadcrumb">
      <a href="<?= e(url('index.php')) ?>"><?= e(t('home')) ?></a><span>/</span><span><?= e(t('nav_instructors')) ?></span>
    </nav>
    <h1><?= e(t('instructors_page_title')) ?></h1>
    <p><?= e(t('instructors_page_subtitle')) ?></p>
  </div>
</section>

<section class="section">
  <div class="container">
    <?php if (!$instructors): ?>
      <div class="empty-state"><?= e(t('no_results')) ?></div>
    <?php else: ?>
      <div class="grid grid--3">
        <?php foreach ($instructors as $instructor): ?>
          <a class="instructor-card reveal" href="<?= e(url('giang-vien-chi-tiet.php?slug=' . urlencode($instructor['slug']))) ?>">
            <div class="instructor-card__photo">
              <img src="<?= e(imageUrl($instructor['photo'], 'assets/images/course-singing.svg')) ?>" alt="<?= e($instructor['name']) ?>" loading="lazy">
            </div>
            <div class="instructor-card__body">
              <h3 class="instructor-card__name"><?= e($instructor['name']) ?></h3>
              <p class="instructor-card__role"><?= e(localized($instructor, 'title')) ?></p>
              <p class="instructor-card__bio"><?= e(excerpt(localized($instructor, 'bio'), 150)) ?></p>
            </div>
          </a>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
