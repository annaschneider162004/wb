<?php
/**
 * Admin: post listing.
 */
require_once __DIR__ . '/includes/auth-check.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrf()) {
        setFlash(t('msg_csrf'), 'error');
    } else {
        $id = (int) ($_POST['id'] ?? 0);
        if (($_POST['action'] ?? '') === 'delete' && $id > 0) {
            dbDelete('posts', $id);
            setFlash(t('msg_deleted'), 'success');
        }
        if (($_POST['action'] ?? '') === 'toggle' && $id > 0) {
            $current = (int) dbValue('SELECT is_published FROM posts WHERE id = ?', [$id]);
            dbUpdate('posts', ['is_published' => $current === 1 ? 0 : 1], $id);
            setFlash(t('msg_saved'), 'success');
        }
    }
    redirect('admin/posts.php');
}

$posts = dbAll('SELECT p.*, u.name AS author_name
                FROM posts p LEFT JOIN users u ON u.id = p.author_id
                ORDER BY p.created_at DESC, p.id DESC');

$adminTitle  = t('admin_posts');
$adminActive = 'posts';
require __DIR__ . '/includes/admin-header.php';
?>

<section class="panel">
  <div class="panel__head">
    <h2 class="panel__title"><?= e(t('admin_posts')) ?> (<?= count($posts) ?>)</h2>
    <a class="btn btn--primary btn--sm" href="<?= e(url('admin/post-edit.php')) ?>"><?= e(t('admin_add_new')) ?></a>
  </div>
  <div class="table-wrap">
    <table class="data">
      <thead>
        <tr>
          <th></th>
          <th><?= e(t('admin_title')) ?></th>
          <th><?= e(t('instructor')) ?></th>
          <th><?= e(t('published_on')) ?></th>
          <th><?= e(t('admin_status')) ?></th>
          <th><?= e(t('admin_actions')) ?></th>
        </tr>
      </thead>
      <tbody>
      <?php if (!$posts): ?>
        <tr><td colspan="6"><?= e(t('admin_no_items')) ?></td></tr>
      <?php endif; ?>
      <?php foreach ($posts as $post): ?>
        <tr>
          <td><img class="thumb" src="<?= e(imageUrl($post['thumbnail'])) ?>" alt=""></td>
          <td>
            <strong><?= e(localized($post, 'title')) ?></strong><br>
            <span class="text-muted" style="font-size:.82rem"><?= e($post['slug']) ?></span>
          </td>
          <td><?= e($post['author_name'] ?: '—') ?></td>
          <td><?= e(formatDate($post['created_at'])) ?></td>
          <td>
            <span class="pill pill--<?= (int) $post['is_published'] === 1 ? 'on' : 'off' ?>">
              <?= e((int) $post['is_published'] === 1 ? t('admin_published') : t('admin_draft')) ?>
            </span>
          </td>
          <td>
            <div class="row-actions">
              <a class="btn btn--sm btn--outline" href="<?= e(url('admin/post-edit.php?id=' . (int) $post['id'])) ?>"><?= e(t('admin_edit')) ?></a>
              <form method="post" action="<?= e(url('admin/posts.php')) ?>">
                <?= csrfField() ?>
                <input type="hidden" name="action" value="toggle">
                <input type="hidden" name="id" value="<?= (int) $post['id'] ?>">
                <button class="btn btn--sm btn--ghost" type="submit"><?= e((int) $post['is_published'] === 1 ? t('admin_draft') : t('admin_published')) ?></button>
              </form>
              <form method="post" action="<?= e(url('admin/posts.php')) ?>" data-confirm="<?= e(t('admin_confirm_delete')) ?>">
                <?= csrfField() ?>
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="id" value="<?= (int) $post['id'] ?>">
                <button class="btn btn--sm btn--danger" type="submit"><?= e(t('admin_delete')) ?></button>
              </form>
            </div>
          </td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</section>

<?php require __DIR__ . '/includes/admin-footer.php'; ?>
