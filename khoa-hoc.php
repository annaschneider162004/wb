<?php
/**
 * Courses listing page.
 */
require_once __DIR__ . '/includes/functions.php';

setLanguage();

$levelId = isset($_GET['level']) ? (int) $_GET['level'] : 0;
$search  = trim((string) ($_GET['q'] ?? ''));
$page    = max(1, (int) ($_GET['page'] ?? 1));
$perPage = 9;

$where  = ['c.is_active = 1'];
$params = [];

if ($levelId > 0) {
    $where[]  = 'c.level_id = ?';
    $params[] = $levelId;
}
if ($search !== '') {
    $where[]  = '(c.title_vi LIKE ? OR c.title_en LIKE ? OR c.description_vi LIKE ? OR c.description_en LIKE ?)';
    $like     = '%' . $search . '%';
    $params   = array_merge($params, [$like, $like, $like, $like]);
}

$whereSql = implode(' AND ', $where);
$total    = (int) dbValue("SELECT COUNT(*) FROM courses c WHERE $whereSql", $params);
$p        = paginate($total, $perPage, $page);

$courses = dbAll(
    "SELECT c.*, l.name_vi AS level_vi, l.name_en AS level_en, l.color AS level_color
     FROM courses c LEFT JOIN levels l ON l.id = c.level_id
     WHERE $whereSql
     ORDER BY c.sort_order ASC, c.id ASC
     LIMIT {$p['perPage']} OFFSET {$p['offset']}",
    $params
);

$levels = dbAll('SELECT * FROM levels ORDER BY sort_order ASC, id ASC');

$pageTitle       = t('courses_page_title');
$pageDescription = t('courses_page_subtitle');
$activeNav       = 'courses';

require __DIR__ . '/includes/header.php';
?>

<section class="page-hero">
  <div class="container">
    <nav class="breadcrumb" aria-label="Breadcrumb">
      <a href="<?= e(url('index.php')) ?>"><?= e(t('home')) ?></a><span>/</span><span><?= e(t('nav_courses')) ?></span>
    </nav>
    <h1><?= e(t('courses_page_title')) ?></h1>
    <p><?= e(t('courses_page_subtitle')) ?></p>
  </div>
</section>

<section class="section" id="instruments">
  <div class="container">
    <div class="filters">
      <div class="filter-pills">
        <a class="filter-pill<?= $levelId === 0 ? ' is-active' : '' ?>" href="<?= e(url('khoa-hoc.php')) ?>"><?= e(t('filter_all')) ?></a>
        <?php foreach ($levels as $level): ?>
          <a class="filter-pill<?= $levelId === (int) $level['id'] ? ' is-active' : '' ?>" href="<?= e(url('khoa-hoc.php?level=' . (int) $level['id'])) ?>">
            <?= e(localized($level, 'name')) ?> &middot; <?= e($level['age_range']) ?>
          </a>
        <?php endforeach; ?>
      </div>

      <form class="search-form" method="get" action="<?= e(url('khoa-hoc.php')) ?>">
        <?php if ($levelId > 0): ?><input type="hidden" name="level" value="<?= $levelId ?>"><?php endif; ?>
        <input type="search" name="q" value="<?= e($search) ?>" placeholder="<?= e(t('search_placeholder')) ?>" aria-label="<?= e(t('search')) ?>">
        <button class="btn btn--primary" type="submit"><?= e(t('search')) ?></button>
      </form>
    </div>

    <?php if (!$courses): ?>
      <div class="empty-state"><?= e(t('no_results')) ?></div>
    <?php else: ?>
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

      <?= renderPagination($p, url('khoa-hoc.php')) ?>
    <?php endif; ?>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
