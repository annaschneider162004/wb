<?php
/**
 * Blog post detail page.
 */
require_once __DIR__ . '/includes/functions.php';

setLanguage();

$slug = trim((string) ($_GET['slug'] ?? ''));
$post = $slug === '' ? null : dbOne(
    'SELECT p.*, u.name AS author_name FROM posts p
     LEFT JOIN users u ON u.id = p.author_id
     WHERE p.slug = ? AND p.is_published = 1',
    [$slug]
);

if (!$post) {
    http_response_code(404);
    $pageTitle = t('msg_not_found');
    require __DIR__ . '/includes/header.php';
    echo '<section class="section"><div class="container"><div class="empty-state"><h2>404</h2><p>' . e(t('msg_not_found')) . '</p>'
        . '<a class="btn btn--primary" href="' . e(url('thu-vien.php')) . '">' . e(t('nav_library')) . '</a></div></div></section>';
    require __DIR__ . '/includes/footer.php';
    exit;
}

$related = dbAll(
    'SELECT * FROM posts WHERE is_published = 1 AND id <> ? ORDER BY created_at DESC LIMIT 3',
    [(int) $post['id']]
);

$pageTitle       = $post['meta_title'] ?: localized($post, 'title');
$pageDescription = $post['meta_description'] ?: localized($post, 'excerpt');
$activeNav       = 'library';

require __DIR__ . '/includes/header.php';
?>

<section class="page-hero">
  <div class="container">
    <nav class="breadcrumb" aria-label="Breadcrumb">
      <a href="<?= e(url('index.php')) ?>"><?= e(t('home')) ?></a><span>/</span>
      <a href="<?= e(url('thu-vien.php')) ?>"><?= e(t('nav_library')) ?></a><span>/</span>
      <span><?= e(excerpt(localized($post, 'title'), 60)) ?></span>
    </nav>
    <h1><?= e(localized($post, 'title')) ?></h1>
    <p><?= e(t('published_on')) ?>: <?= e(formatDate($post['created_at'])) ?><?= $post['author_name'] ? ' · ' . e($post['author_name']) : '' ?></p>
  </div>
</section>

<section class="section">
  <div class="container detail-layout">
    <div>
      <div class="detail-media">
        <img src="<?= e(imageUrl($post['thumbnail'], 'assets/images/course-piano.svg')) ?>" alt="<?= e(localized($post, 'title')) ?>">
      </div>

      <article class="detail-card rich-text">
        <p><strong><?= e(localized($post, 'excerpt')) ?></strong></p>
        <?= safeHtml(localized($post, 'content')) ?>
      </article>
    </div>

    <aside>
      <div class="sidebar-card">
        <h3><?= e(t('related_posts')) ?></h3>
        <ul class="meta-list">
          <?php foreach ($related as $item): ?>
            <li style="display:block">
              <a href="<?= e(url('bai-viet-chi-tiet.php?slug=' . urlencode($item['slug']))) ?>"><?= e(localized($item, 'title')) ?></a>
              <div class="form-hint"><?= e(formatDate($item['created_at'])) ?></div>
            </li>
          <?php endforeach; ?>
        </ul>
        <a class="btn btn--outline btn--block" href="<?= e(url('thu-vien.php')) ?>"><?= e(t('nav_library')) ?></a>
      </div>
    </aside>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
