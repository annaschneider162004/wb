<?php
/**
 * Course detail page.
 */
require_once __DIR__ . '/includes/functions.php';

setLanguage();

$slug = trim((string) ($_GET['slug'] ?? ''));

$course = $slug === '' ? null : dbOne(
    'SELECT c.*, l.name_vi AS level_vi, l.name_en AS level_en, l.color AS level_color, l.age_range,
            i.name AS instructor_name, i.slug AS instructor_slug, i.photo AS instructor_photo,
            i.title_vi AS instructor_title_vi, i.title_en AS instructor_title_en
     FROM courses c
     LEFT JOIN levels l ON l.id = c.level_id
     LEFT JOIN instructors i ON i.id = c.instructor_id
     WHERE c.slug = ? AND c.is_active = 1',
    [$slug]
);

if (!$course) {
    http_response_code(404);
    $pageTitle = t('msg_not_found');
    require __DIR__ . '/includes/header.php';
    echo '<section class="section"><div class="container"><div class="empty-state"><h2>404</h2><p>' . e(t('msg_not_found')) . '</p>'
        . '<a class="btn btn--primary" href="' . e(url('khoa-hoc.php')) . '">' . e(t('nav_courses')) . '</a></div></div></section>';
    require __DIR__ . '/includes/footer.php';
    exit;
}

$related = dbAll(
    'SELECT c.*, l.name_vi AS level_vi, l.name_en AS level_en, l.color AS level_color
     FROM courses c LEFT JOIN levels l ON l.id = c.level_id
     WHERE c.is_active = 1 AND c.id <> ? AND (c.level_id = ? OR c.level_id IS NULL)
     ORDER BY c.sort_order ASC LIMIT 3',
    [(int) $course['id'], (int) $course['level_id']]
);

$pageTitle       = $course['meta_title'] ?: localized($course, 'title');
$pageDescription = $course['meta_description'] ?: localized($course, 'description');
$activeNav       = 'courses';

require __DIR__ . '/includes/header.php';
?>

<section class="page-hero">
  <div class="container">
    <nav class="breadcrumb" aria-label="Breadcrumb">
      <a href="<?= e(url('index.php')) ?>"><?= e(t('home')) ?></a><span>/</span>
      <a href="<?= e(url('khoa-hoc.php')) ?>"><?= e(t('nav_courses')) ?></a><span>/</span>
      <span><?= e(localized($course, 'title')) ?></span>
    </nav>
    <h1><?= e(localized($course, 'title')) ?></h1>
    <p><?= e(localized($course, 'subtitle')) ?></p>
  </div>
</section>

<section class="section">
  <div class="container detail-layout">
    <div>
      <div class="detail-media">
        <img src="<?= e(imageUrl($course['thumbnail'])) ?>" alt="<?= e(localized($course, 'title')) ?>">
      </div>

      <article class="detail-card rich-text">
        <h2><?= e(localized($course, 'title')) ?></h2>
        <?php foreach (preg_split('/\n{2,}/', (string) localized($course, 'description')) as $paragraph): ?>
          <?php if (trim($paragraph) !== ''): ?><p><?= e(trim($paragraph)) ?></p><?php endif; ?>
        <?php endforeach; ?>

        <h3><?= e(currentLang() === 'en' ? 'What you will learn' : 'Bạn sẽ học được gì?') ?></h3>
        <ul class="check-list">
          <li><?= icon('check', 'icon icon--sm') ?><span><?= e(currentLang() === 'en' ? 'Solid technical foundations from the very first lesson.' : 'Nền tảng kỹ thuật vững chắc ngay từ buổi học đầu tiên.') ?></span></li>
          <li><?= icon('check', 'icon icon--sm') ?><span><?= e(currentLang() === 'en' ? 'Basic music theory applied directly to real pieces.' : 'Nhạc lý cơ bản áp dụng trực tiếp vào tác phẩm thực tế.') ?></span></li>
          <li><?= icon('check', 'icon icon--sm') ?><span><?= e(currentLang() === 'en' ? 'A personal practice plan reviewed every week.' : 'Kế hoạch luyện tập cá nhân được rà soát mỗi tuần.') ?></span></li>
          <li><?= icon('check', 'icon icon--sm') ?><span><?= e(currentLang() === 'en' ? 'Regular performance opportunities inside the club.' : 'Cơ hội biểu diễn thường xuyên trong câu lạc bộ.') ?></span></li>
        </ul>
      </article>
    </div>

    <aside>
      <div class="sidebar-card">
        <div class="price-tag"><?= e(formatPrice($course['price'])) ?></div>
        <p class="form-hint" style="margin-bottom:18px"><?= e(t('price')) ?></p>

        <ul class="meta-list">
          <?php if (!empty($course['level_vi'])): ?>
            <li><span><?= e(t('level')) ?></span><span><?= e(currentLang() === 'en' ? $course['level_en'] : $course['level_vi']) ?></span></li>
          <?php endif; ?>
          <?php if (!empty($course['age_range'])): ?>
            <li><span><?= e(t('age_range')) ?></span><span><?= e($course['age_range']) ?></span></li>
          <?php endif; ?>
          <?php if (!empty($course['duration'])): ?>
            <li><span><?= e(t('duration')) ?></span><span><?= e($course['duration']) ?></span></li>
          <?php endif; ?>
          <?php if (!empty($course['instructor_name'])): ?>
            <li><span><?= e(t('instructor')) ?></span><span><a href="<?= e(url('giang-vien-chi-tiet.php?slug=' . urlencode($course['instructor_slug']))) ?>"><?= e($course['instructor_name']) ?></a></span></li>
          <?php endif; ?>
        </ul>

        <a class="btn btn--primary btn--block" href="<?= e(url('lien-he.php?course=' . urlencode($course['slug']))) ?>"><?= e(t('register_course')) ?></a>
        <a class="btn btn--outline btn--block" style="margin-top:10px" href="<?= e(url('khoa-hoc.php')) ?>"><?= e(t('courses_view_all')) ?></a>
      </div>

      <?php if (!empty($course['instructor_name'])): ?>
        <a class="instructor-card" href="<?= e(url('giang-vien-chi-tiet.php?slug=' . urlencode($course['instructor_slug']))) ?>">
          <div class="instructor-card__photo">
            <img src="<?= e(imageUrl($course['instructor_photo'], 'assets/images/course-singing.svg')) ?>" alt="<?= e($course['instructor_name']) ?>" loading="lazy">
          </div>
          <div class="instructor-card__body">
            <h3 class="instructor-card__name"><?= e($course['instructor_name']) ?></h3>
            <p class="instructor-card__role"><?= e(currentLang() === 'en' ? $course['instructor_title_en'] : $course['instructor_title_vi']) ?></p>
          </div>
        </a>
      <?php endif; ?>
    </aside>
  </div>
</section>

<?php if ($related): ?>
<section class="section section--light">
  <div class="container">
    <div class="section-head section-head--left">
      <h2 class="section-head__title"><?= e(t('related_courses')) ?></h2>
    </div>
    <div class="course-grid">
      <?php foreach ($related as $item): ?>
        <a class="course-card" href="<?= e(url('khoa-hoc-chi-tiet.php?slug=' . urlencode($item['slug']))) ?>">
          <?php if (!empty($item['level_vi'])): ?>
            <span class="badge badge--<?= e(levelColorClass($item['level_color'])) ?>"><?= e(currentLang() === 'en' ? $item['level_en'] : $item['level_vi']) ?></span>
          <?php endif; ?>
          <div class="course-card__media">
            <img src="<?= e(imageUrl($item['thumbnail'])) ?>" alt="<?= e(localized($item, 'title')) ?>" loading="lazy">
            <span class="course-card__overlay"></span>
          </div>
          <div class="course-card__body">
            <h3 class="course-card__title"><?= e(localized($item, 'title')) ?></h3>
            <p class="course-card__subtitle"><?= e(localized($item, 'subtitle')) ?></p>
          </div>
        </a>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<?php require __DIR__ . '/includes/footer.php'; ?>
