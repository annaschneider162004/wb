<?php
/**
 * Instructor detail page.
 */
require_once __DIR__ . '/includes/functions.php';

setLanguage();

$slug = trim((string) ($_GET['slug'] ?? ''));
$instructor = $slug === '' ? null : dbOne('SELECT * FROM instructors WHERE slug = ? AND is_active = 1', [$slug]);

if (!$instructor) {
    http_response_code(404);
    $pageTitle = t('msg_not_found');
    require __DIR__ . '/includes/header.php';
    echo '<section class="section"><div class="container"><div class="empty-state"><h2>404</h2><p>' . e(t('msg_not_found')) . '</p>'
        . '<a class="btn btn--primary" href="' . e(url('giang-vien.php')) . '">' . e(t('nav_instructors')) . '</a></div></div></section>';
    require __DIR__ . '/includes/footer.php';
    exit;
}

$courses = dbAll(
    'SELECT c.*, l.name_vi AS level_vi, l.name_en AS level_en, l.color AS level_color
     FROM courses c LEFT JOIN levels l ON l.id = c.level_id
     WHERE c.instructor_id = ? AND c.is_active = 1
     ORDER BY c.sort_order ASC',
    [(int) $instructor['id']]
);

$pageTitle       = $instructor['name'];
$pageDescription = excerpt(localized($instructor, 'bio'), 200);
$activeNav       = 'instructors';

require __DIR__ . '/includes/header.php';
?>

<section class="page-hero">
  <div class="container">
    <nav class="breadcrumb" aria-label="Breadcrumb">
      <a href="<?= e(url('index.php')) ?>"><?= e(t('home')) ?></a><span>/</span>
      <a href="<?= e(url('giang-vien.php')) ?>"><?= e(t('nav_instructors')) ?></a><span>/</span>
      <span><?= e($instructor['name']) ?></span>
    </nav>
    <h1><?= e($instructor['name']) ?></h1>
    <p><?= e(localized($instructor, 'title')) ?></p>
  </div>
</section>

<section class="section">
  <div class="container detail-layout">
    <article class="detail-card rich-text">
      <h2><?= e($instructor['name']) ?></h2>
      <?php foreach (preg_split('/\n{2,}/', (string) localized($instructor, 'bio')) as $paragraph): ?>
        <?php if (trim($paragraph) !== ''): ?><p><?= e(trim($paragraph)) ?></p><?php endif; ?>
      <?php endforeach; ?>
    </article>

    <aside>
      <div class="sidebar-card">
        <div class="detail-media" style="margin-bottom:18px">
          <img src="<?= e(imageUrl($instructor['photo'], 'assets/images/course-singing.svg')) ?>" alt="<?= e($instructor['name']) ?>">
        </div>
        <ul class="meta-list">
          <li><span><?= e(t('instructor')) ?></span><span><?= e($instructor['name']) ?></span></li>
          <li><span><?= e(t('nav_courses')) ?></span><span><?= count($courses) ?></span></li>
          <?php if (!empty($instructor['email'])): ?>
            <li><span><?= e(t('field_email')) ?></span><span><a href="mailto:<?= e($instructor['email']) ?>"><?= e($instructor['email']) ?></a></span></li>
          <?php endif; ?>
        </ul>
        <a class="btn btn--primary btn--block" href="<?= e(url('lien-he.php')) ?>"><?= e(t('contact_now')) ?></a>
      </div>
    </aside>
  </div>
</section>

<?php if ($courses): ?>
<section class="section section--light">
  <div class="container">
    <div class="section-head section-head--left">
      <h2 class="section-head__title"><?= e(t('courses_by')) ?></h2>
    </div>
    <div class="course-grid">
      <?php foreach ($courses as $course): ?>
        <a class="course-card" href="<?= e(url('khoa-hoc-chi-tiet.php?slug=' . urlencode($course['slug']))) ?>">
          <?php if (!empty($course['level_vi'])): ?>
            <span class="badge badge--<?= e(levelColorClass($course['level_color'])) ?>"><?= e(currentLang() === 'en' ? $course['level_en'] : $course['level_vi']) ?></span>
          <?php endif; ?>
          <div class="course-card__media">
            <img src="<?= e(imageUrl($course['thumbnail'])) ?>" alt="<?= e(localized($course, 'title')) ?>" loading="lazy">
            <span class="course-card__overlay"></span>
          </div>
          <div class="course-card__body">
            <h3 class="course-card__title"><?= e(localized($course, 'title')) ?></h3>
            <p class="course-card__subtitle"><?= e(localized($course, 'subtitle')) ?></p>
          </div>
        </a>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<?php require __DIR__ . '/includes/footer.php'; ?>
