<?php
/**
 * Homepage.
 */
require_once __DIR__ . '/includes/functions.php';

setLanguage();

$levels      = dbAll('SELECT * FROM levels ORDER BY sort_order ASC, id ASC');
$features    = dbAll('SELECT * FROM features WHERE is_active = 1 ORDER BY sort_order ASC, id ASC LIMIT 5');
$courses     = dbAll(
    'SELECT c.*, l.name_vi AS level_vi, l.name_en AS level_en, l.color AS level_color
     FROM courses c LEFT JOIN levels l ON l.id = c.level_id
     WHERE c.is_active = 1 AND c.is_featured = 1
     ORDER BY c.sort_order ASC, c.id ASC LIMIT 6'
);
$instructors = dbAll('SELECT * FROM instructors WHERE is_active = 1 ORDER BY sort_order ASC, id ASC LIMIT 3');
$posts       = dbAll('SELECT * FROM posts WHERE is_published = 1 ORDER BY created_at DESC LIMIT 3');

$countCourses     = (int) dbValue('SELECT COUNT(*) FROM courses WHERE is_active = 1');
$countInstructors = (int) dbValue('SELECT COUNT(*) FROM instructors WHERE is_active = 1');
$countStudents    = max(1200, (int) dbValue('SELECT COUNT(*) FROM users'));

$pageTitle       = t('hero_heading');
$pageDescription = t('hero_subtext');
$activeNav       = 'home';

require __DIR__ . '/includes/header.php';
?>

<section class="hero">
  <div class="container hero__inner">
    <div class="hero__content">
      <span class="hero__label"><?= icon('star', 'icon icon--sm') ?><?= e(t('hero_label')) ?></span>
      <h1 class="hero__title"><?= e(t('hero_heading')) ?></h1>
      <p class="hero__text"><?= e(t('hero_subtext')) ?></p>
      <div class="hero__actions">
        <a class="btn btn--primary btn--lg" href="<?= e(url('khoa-hoc.php')) ?>"><?= e(t('hero_cta_primary')) ?><?= icon('arrow', 'icon icon--sm') ?></a>
        <a class="btn btn--outline btn--lg" href="<?= e(url('ve-chung-toi.php')) ?>"><?= e(t('hero_cta_secondary')) ?></a>
      </div>

      <div class="level-badges">
        <?php foreach ($levels as $level): ?>
          <a class="level-badge level-badge--<?= e(levelColorClass($level['color'])) ?>" href="<?= e(url('khoa-hoc.php?level=' . (int) $level['id'])) ?>">
            <span class="level-badge__dot"><?= icon('music', 'icon icon--sm') ?></span>
            <span>
              <span class="level-badge__name"><?= e(localized($level, 'name')) ?></span>
              <span class="level-badge__age"><?= e($level['age_range']) ?></span>
            </span>
          </a>
        <?php endforeach; ?>
      </div>
    </div>

    <div class="hero__art">
      <span class="hero__blob" aria-hidden="true"></span>
      <span class="hero__note hero__note--1" aria-hidden="true">&#9835;</span>
      <span class="hero__note hero__note--2" aria-hidden="true">&#9834;</span>
      <span class="hero__note hero__note--3" aria-hidden="true">&#9835;</span>
      <img class="hero__characters" src="<?= e(asset('images/hero-characters.svg')) ?>" alt="<?= e(t('hero_heading')) ?>" width="760" height="470">
    </div>
  </div>
</section>

<section class="section--tight">
  <div class="container">
    <div class="stats reveal">
      <div class="stat">
        <div class="stat__value" data-count="<?= (int) $countStudents ?>" data-suffix="+">0</div>
        <div class="stat__label"><?= e(t('stat_students')) ?></div>
      </div>
      <div class="stat">
        <div class="stat__value" data-count="<?= $countCourses ?>" data-suffix="+">0</div>
        <div class="stat__label"><?= e(t('stat_courses')) ?></div>
      </div>
      <div class="stat">
        <div class="stat__value" data-count="<?= $countInstructors ?>" data-suffix="+">0</div>
        <div class="stat__label"><?= e(t('stat_instructors')) ?></div>
      </div>
      <div class="stat">
        <div class="stat__value" data-count="10" data-suffix="+">0</div>
        <div class="stat__label"><?= e(t('stat_years')) ?></div>
      </div>
    </div>
  </div>
</section>

<section class="section section--light" id="features">
  <div class="container">
    <div class="section-head reveal">
      <span class="section-head__eyebrow"><?= e(setting('site_name', SITE_NAME)) ?></span>
      <h2 class="section-head__title"><?= e(t('features_title')) ?></h2>
      <p class="section-head__text"><?= e(t('features_subtitle')) ?></p>
    </div>

    <div class="feature-grid">
      <?php foreach ($features as $feature): ?>
        <article class="feature reveal">
          <div class="feature__icon"><?= icon($feature['icon'] ?: 'music', 'icon icon--lg') ?></div>
          <h3 class="feature__title"><?= e(localized($feature, 'title')) ?></h3>
          <p class="feature__text"><?= e(localized($feature, 'description')) ?></p>
        </article>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<section class="section" id="courses">
  <div class="container">
    <div class="section-head reveal">
      <span class="section-head__eyebrow"><?= e(t('nav_courses')) ?></span>
      <h2 class="section-head__title"><?= e(t('courses_title')) ?></h2>
      <p class="section-head__text"><?= e(t('courses_subtitle')) ?></p>
    </div>

    <div class="course-grid">
      <?php foreach ($courses as $course): ?>
        <a class="course-card reveal" href="<?= e(url('khoa-hoc-chi-tiet.php?slug=' . urlencode($course['slug']))) ?>">
          <?php if (!empty($course['level_vi'])): ?>
            <span class="badge badge--<?= e(levelColorClass($course['level_color'])) ?>">
              <?= e(currentLang() === 'en' ? $course['level_en'] : $course['level_vi']) ?>
            </span>
          <?php endif; ?>
          <div class="course-card__media">
            <img src="<?= e(imageUrl($course['thumbnail'])) ?>" alt="<?= e(localized($course, 'title')) ?>" loading="lazy">
            <span class="course-card__overlay"></span>
          </div>
          <div class="course-card__body">
            <h3 class="course-card__title"><?= e(localized($course, 'title')) ?></h3>
            <p class="course-card__subtitle"><?= e(localized($course, 'subtitle')) ?></p>
            <div class="course-card__meta">
              <span><?= e(formatPrice($course['price'])) ?></span>
              <?php if ($course['duration']): ?><span><?= e($course['duration']) ?></span><?php endif; ?>
            </div>
          </div>
        </a>
      <?php endforeach; ?>
    </div>

    <p style="text-align:center;margin-top:36px">
      <a class="btn btn--outline btn--lg" href="<?= e(url('khoa-hoc.php')) ?>"><?= e(t('courses_view_all')) ?></a>
    </p>
  </div>
</section>

<section class="section section--light" id="levels">
  <div class="container">
    <div class="section-head reveal">
      <span class="section-head__eyebrow"><?= e(t('nav_learning_path')) ?></span>
      <h2 class="section-head__title"><?= e(t('levels_title')) ?></h2>
      <p class="section-head__text"><?= e(t('levels_subtitle')) ?></p>
    </div>

    <div class="grid grid--3">
      <?php foreach ($levels as $level): ?>
        <article class="level-card level-card--<?= e(levelColorClass($level['color'])) ?> reveal">
          <span class="level-card__age"><?= e($level['age_range']) ?></span>
          <h3><?= e(localized($level, 'name')) ?></h3>
          <p class="level-card__text"><?= e(localized($level, 'description')) ?></p>
          <p style="margin:16px 0 0"><a class="post-card__more" href="<?= e(url('khoa-hoc.php?level=' . (int) $level['id'])) ?>"><?= e(t('read_more')) ?><?= icon('arrow', 'icon icon--sm') ?></a></p>
        </article>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<section class="section">
  <div class="container">
    <div class="section-head reveal">
      <span class="section-head__eyebrow"><?= e(t('nav_instructors')) ?></span>
      <h2 class="section-head__title"><?= e(t('instructors_title')) ?></h2>
      <p class="section-head__text"><?= e(t('instructors_subtitle')) ?></p>
    </div>

    <div class="grid grid--3">
      <?php foreach ($instructors as $instructor): ?>
        <a class="instructor-card reveal" href="<?= e(url('giang-vien-chi-tiet.php?slug=' . urlencode($instructor['slug']))) ?>">
          <div class="instructor-card__photo">
            <img src="<?= e(imageUrl($instructor['photo'], 'assets/images/course-singing.svg')) ?>" alt="<?= e($instructor['name']) ?>" loading="lazy">
          </div>
          <div class="instructor-card__body">
            <h3 class="instructor-card__name"><?= e($instructor['name']) ?></h3>
            <p class="instructor-card__role"><?= e(localized($instructor, 'title')) ?></p>
            <p class="instructor-card__bio"><?= e(excerpt(localized($instructor, 'bio'), 120)) ?></p>
          </div>
        </a>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<section class="section section--light">
  <div class="container">
    <div class="section-head reveal">
      <span class="section-head__eyebrow"><?= e(t('nav_library')) ?></span>
      <h2 class="section-head__title"><?= e(t('posts_title')) ?></h2>
      <p class="section-head__text"><?= e(t('posts_subtitle')) ?></p>
    </div>

    <div class="grid grid--3">
      <?php foreach ($posts as $post): ?>
        <a class="post-card reveal" href="<?= e(url('bai-viet-chi-tiet.php?slug=' . urlencode($post['slug']))) ?>">
          <div class="post-card__media">
            <img src="<?= e(imageUrl($post['thumbnail'], 'assets/images/course-piano.svg')) ?>" alt="<?= e(localized($post, 'title')) ?>" loading="lazy">
          </div>
          <div class="post-card__body">
            <span class="post-card__date"><?= e(formatDate($post['created_at'])) ?></span>
            <h3 class="post-card__title"><?= e(localized($post, 'title')) ?></h3>
            <p class="post-card__excerpt"><?= e(excerpt(localized($post, 'excerpt'), 120)) ?></p>
            <span class="post-card__more"><?= e(t('read_more')) ?><?= icon('arrow', 'icon icon--sm') ?></span>
          </div>
        </a>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<section class="section">
  <div class="container">
    <div class="cta reveal">
      <h2><?= e(t('cta_title')) ?></h2>
      <p><?= e(t('cta_subtext')) ?></p>
      <a class="btn btn--white btn--lg" href="<?= e(url('register.php')) ?>"><?= e(t('cta_button')) ?></a>
    </div>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
