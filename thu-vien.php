<?php
/**
 * Library / blog listing page.
 */
require_once __DIR__ . '/includes/functions.php';

setLanguage();

$search  = trim((string) ($_GET['q'] ?? ''));
$page    = max(1, (int) ($_GET['page'] ?? 1));
$perPage = 9;

$where  = ['p.is_published = 1'];
$params = [];

if ($search !== '') {
    $where[] = '(p.title_vi LIKE ? OR p.title_en LIKE ? OR p.excerpt_vi LIKE ? OR p.excerpt_en LIKE ?)';
    $like    = '%' . $search . '%';
    $params  = array_merge($params, [$like, $like, $like, $like]);
}

$whereSql = implode(' AND ', $where);
$total    = (int) dbValue("SELECT COUNT(*) FROM posts p WHERE $whereSql", $params);
$p        = paginate($total, $perPage, $page);

$posts = dbAll(
    "SELECT p.*, u.name AS author_name FROM posts p
     LEFT JOIN users u ON u.id = p.author_id
     WHERE $whereSql ORDER BY p.created_at DESC
     LIMIT {$p['perPage']} OFFSET {$p['offset']}",
    $params
);

$pageTitle       = t('library_title');
$pageDescription = t('library_subtitle');
$activeNav       = 'library';

require __DIR__ . '/includes/header.php';
?>

<section class="page-hero">
  <div class="container">
    <nav class="breadcrumb" aria-label="Breadcrumb">
      <a href="<?= e(url('index.php')) ?>"><?= e(t('home')) ?></a><span>/</span><span><?= e(t('nav_library')) ?></span>
    </nav>
    <h1><?= e(t('library_title')) ?></h1>
    <p><?= e(t('library_subtitle')) ?></p>
  </div>
</section>

<section class="section">
  <div class="container">
    <div class="filters">
      <div></div>
      <form class="search-form" method="get" action="<?= e(url('thu-vien.php')) ?>">
        <input type="search" name="q" value="<?= e($search) ?>" placeholder="<?= e(t('search_placeholder')) ?>" aria-label="<?= e(t('search')) ?>">
        <button class="btn btn--primary" type="submit"><?= e(t('search')) ?></button>
      </form>
    </div>

    <?php if (!$posts): ?>
      <div class="empty-state"><?= e(t('no_results')) ?></div>
    <?php else: ?>
      <div class="grid grid--3">
        <?php foreach ($posts as $post): ?>
          <a class="post-card reveal" href="<?= e(url('bai-viet-chi-tiet.php?slug=' . urlencode($post['slug']))) ?>">
            <div class="post-card__media">
              <img src="<?= e(imageUrl($post['thumbnail'], 'assets/images/course-piano.svg')) ?>" alt="<?= e(localized($post, 'title')) ?>" loading="lazy">
            </div>
            <div class="post-card__body">
              <span class="post-card__date"><?= e(formatDate($post['created_at'])) ?><?= $post['author_name'] ? ' · ' . e($post['author_name']) : '' ?></span>
              <h3 class="post-card__title"><?= e(localized($post, 'title')) ?></h3>
              <p class="post-card__excerpt"><?= e(excerpt(localized($post, 'excerpt'), 150)) ?></p>
              <span class="post-card__more"><?= e(t('read_more')) ?><?= icon('arrow', 'icon icon--sm') ?></span>
            </div>
          </a>
        <?php endforeach; ?>
      </div>

      <?= renderPagination($p, url('thu-vien.php')) ?>
    <?php endif; ?>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
