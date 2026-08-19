<?php
/**
 * Admin: create / edit a post.
 */
require_once __DIR__ . '/includes/auth-check.php';

$id     = (int) ($_GET['id'] ?? 0);
$errors = [];

$post = [
    'id' => 0, 'slug' => '', 'title_vi' => '', 'title_en' => '', 'excerpt_vi' => '', 'excerpt_en' => '',
    'content_vi' => '', 'content_en' => '', 'thumbnail' => '', 'author_id' => (int) $adminUser['id'],
    'is_published' => 1, 'meta_title' => '', 'meta_description' => '',
];

if ($id > 0) {
    $existing = dbOne('SELECT * FROM posts WHERE id = ?', [$id]);
    if (!$existing) {
        setFlash(t('msg_not_found'), 'error');
        redirect('admin/posts.php');
    }
    $post = array_merge($post, $existing);
}

$authors = dbAll('SELECT id, name FROM users ORDER BY name ASC');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach (['slug', 'title_vi', 'title_en', 'excerpt_vi', 'excerpt_en', 'content_vi', 'content_en',
              'meta_title', 'meta_description'] as $field) {
        $post[$field] = trim((string) ($_POST[$field] ?? ''));
    }
    $post['author_id']    = $_POST['author_id'] !== '' ? (int) $_POST['author_id'] : null;
    $post['is_published'] = isset($_POST['is_published']) ? 1 : 0;

    if (!verifyCsrf()) {
        $errors[] = t('msg_csrf');
    }
    if ($post['title_vi'] === '' && $post['title_en'] === '') {
        $errors[] = t('msg_required_fields');
    }

    if (!$errors) {
        $baseSlug   = $post['slug'] !== '' ? $post['slug'] : ($post['title_vi'] ?: $post['title_en']);
        $post['slug'] = uniqueSlug('posts', slugify($baseSlug), $id);

        try {
            $post['thumbnail'] = handleImageUpload('thumbnail', $post['thumbnail']);
        } catch (RuntimeException $e) {
            $errors[] = $e->getMessage();
        }
    }

    if (!$errors) {
        $data = [
            'slug' => $post['slug'], 'title_vi' => $post['title_vi'], 'title_en' => $post['title_en'],
            'excerpt_vi' => $post['excerpt_vi'], 'excerpt_en' => $post['excerpt_en'],
            'content_vi' => $post['content_vi'], 'content_en' => $post['content_en'],
            'thumbnail' => $post['thumbnail'], 'author_id' => $post['author_id'],
            'is_published' => $post['is_published'], 'meta_title' => $post['meta_title'],
            'meta_description' => $post['meta_description'],
        ];

        if ($id > 0) {
            dbUpdate('posts', $data, $id);
        } else {
            $id = dbInsert('posts', $data);
        }
        setFlash(t('msg_saved'), 'success');
        redirect('admin/post-edit.php?id=' . $id);
    }
}

$adminTitle  = ($id > 0 ? t('admin_edit') : t('admin_add_new')) . ' — ' . t('admin_posts');
$adminActive = 'posts';
require __DIR__ . '/includes/admin-header.php';
?>

<?php foreach ($errors as $error): ?>
  <div class="alert alert--error"><?= e($error) ?></div>
<?php endforeach; ?>

<form method="post" action="" enctype="multipart/form-data">
  <?= csrfField() ?>

  <section class="panel">
    <div class="panel__head">
      <h2 class="panel__title"><?= e($adminTitle) ?></h2>
      <a class="btn btn--sm btn--ghost" href="<?= e(url('admin/posts.php')) ?>">&larr; <?= e(t('back')) ?></a>
    </div>
    <div class="panel__body">
      <div class="grid grid--2">
        <div class="form-group">
          <label for="title_vi">Tiêu đề (VI) <span class="required">*</span></label>
          <input type="text" id="title_vi" name="title_vi" data-slug-source="slug" value="<?= e($post['title_vi']) ?>">
        </div>
        <div class="form-group">
          <label for="title_en">Title (EN)</label>
          <input type="text" id="title_en" name="title_en" value="<?= e($post['title_en']) ?>">
        </div>
      </div>

      <div class="form-group">
        <label for="slug">Slug</label>
        <input type="text" id="slug" name="slug" value="<?= e($post['slug']) ?>">
        <p class="form-hint">/bai-viet/&lt;slug&gt;</p>
      </div>

      <div class="grid grid--2">
        <div class="form-group">
          <label for="excerpt_vi">Tóm tắt (VI)</label>
          <textarea id="excerpt_vi" name="excerpt_vi" rows="3"><?= e($post['excerpt_vi']) ?></textarea>
        </div>
        <div class="form-group">
          <label for="excerpt_en">Excerpt (EN)</label>
          <textarea id="excerpt_en" name="excerpt_en" rows="3"><?= e($post['excerpt_en']) ?></textarea>
        </div>
        <div class="form-group">
          <label for="content_vi">Nội dung (VI)</label>
          <textarea id="content_vi" name="content_vi" rows="14"><?= e($post['content_vi']) ?></textarea>
          <p class="form-hint">HTML cơ bản được phép: &lt;p&gt; &lt;h2&gt; &lt;ul&gt; &lt;a&gt; &lt;img&gt; &lt;iframe&gt;</p>
        </div>
        <div class="form-group">
          <label for="content_en">Content (EN)</label>
          <textarea id="content_en" name="content_en" rows="14"><?= e($post['content_en']) ?></textarea>
        </div>
      </div>
    </div>
  </section>

  <section class="panel">
    <div class="panel__head"><h2 class="panel__title"><?= e(t('admin_settings')) ?></h2></div>
    <div class="panel__body">
      <div class="grid grid--3">
        <div class="form-group">
          <label for="author_id">Author</label>
          <select id="author_id" name="author_id">
            <option value="">—</option>
            <?php foreach ($authors as $author): ?>
              <option value="<?= (int) $author['id'] ?>" <?= (int) $post['author_id'] === (int) $author['id'] ? 'selected' : '' ?>>
                <?= e($author['name']) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="form-group">
          <label><?= e(t('admin_status')) ?></label>
          <label class="check"><input type="checkbox" name="is_published" value="1" <?= (int) $post['is_published'] === 1 ? 'checked' : '' ?>> <?= e(t('admin_published')) ?></label>
        </div>
        <div class="form-group">
          <label for="thumbnail">Thumbnail</label>
          <img data-preview-current src="<?= e(imageUrl($post['thumbnail'])) ?>" alt="" style="width:180px;height:120px;object-fit:cover;border-radius:12px;display:block;margin-bottom:10px">
          <canvas data-preview-target width="180" height="120" hidden style="width:180px;height:120px;border-radius:12px;display:block;margin-bottom:10px;background:var(--green-050)"></canvas>
          <input type="file" id="thumbnail" name="thumbnail" accept="image/*" data-preview>
        </div>
      </div>

      <div class="grid grid--2">
        <div class="form-group">
          <label for="meta_title">Meta title</label>
          <input type="text" id="meta_title" name="meta_title" value="<?= e($post['meta_title']) ?>">
        </div>
        <div class="form-group">
          <label for="meta_description">Meta description</label>
          <textarea id="meta_description" name="meta_description" rows="3"><?= e($post['meta_description']) ?></textarea>
        </div>
      </div>

      <button class="btn btn--primary" type="submit"><?= e(t('btn_save')) ?></button>
    </div>
  </section>
</form>

<?php require __DIR__ . '/includes/admin-footer.php'; ?>
