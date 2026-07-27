<?php
/**
 * Admin: create / edit an instructor.
 */
require_once __DIR__ . '/includes/auth-check.php';

$id     = (int) ($_GET['id'] ?? 0);
$errors = [];

$instructor = [
    'id' => 0, 'slug' => '', 'name' => '', 'title_vi' => '', 'title_en' => '', 'bio_vi' => '', 'bio_en' => '',
    'photo' => '', 'email' => '', 'is_active' => 1, 'sort_order' => 0,
];

if ($id > 0) {
    $existing = dbOne('SELECT * FROM instructors WHERE id = ?', [$id]);
    if (!$existing) {
        setFlash(t('msg_not_found'), 'error');
        redirect('admin/instructors.php');
    }
    $instructor = array_merge($instructor, $existing);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach (['slug', 'name', 'title_vi', 'title_en', 'bio_vi', 'bio_en', 'email'] as $field) {
        $instructor[$field] = trim((string) ($_POST[$field] ?? ''));
    }
    $instructor['sort_order'] = (int) ($_POST['sort_order'] ?? 0);
    $instructor['is_active']  = isset($_POST['is_active']) ? 1 : 0;

    if (!verifyCsrf()) {
        $errors[] = t('msg_csrf');
    }
    if ($instructor['name'] === '') {
        $errors[] = t('msg_required_fields');
    }
    if ($instructor['email'] !== '' && !filter_var($instructor['email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = t('msg_invalid_email');
    }

    if (!$errors) {
        $baseSlug = $instructor['slug'] !== '' ? $instructor['slug'] : $instructor['name'];
        $instructor['slug'] = uniqueSlug('instructors', slugify($baseSlug), $id);

        try {
            $instructor['photo'] = handleImageUpload('photo', $instructor['photo']);
        } catch (RuntimeException $e) {
            $errors[] = $e->getMessage();
        }
    }

    if (!$errors) {
        $data = [
            'slug' => $instructor['slug'], 'name' => $instructor['name'],
            'title_vi' => $instructor['title_vi'], 'title_en' => $instructor['title_en'],
            'bio_vi' => $instructor['bio_vi'], 'bio_en' => $instructor['bio_en'],
            'photo' => $instructor['photo'], 'email' => $instructor['email'],
            'is_active' => $instructor['is_active'], 'sort_order' => $instructor['sort_order'],
        ];

        if ($id > 0) {
            dbUpdate('instructors', $data, $id);
        } else {
            $id = dbInsert('instructors', $data);
        }
        setFlash(t('msg_saved'), 'success');
        redirect('admin/instructor-edit.php?id=' . $id);
    }
}

$adminTitle  = ($id > 0 ? t('admin_edit') : t('admin_add_new')) . ' — ' . t('admin_instructors');
$adminActive = 'instructors';
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
      <a class="btn btn--sm btn--ghost" href="<?= e(url('admin/instructors.php')) ?>">&larr; <?= e(t('back')) ?></a>
    </div>
    <div class="panel__body">
      <div class="grid grid--2">
        <div class="form-group">
          <label for="name"><?= e(t('field_name')) ?> <span class="required">*</span></label>
          <input type="text" id="name" name="name" data-slug-source="slug" value="<?= e($instructor['name']) ?>">
        </div>
        <div class="form-group">
          <label for="slug">Slug</label>
          <input type="text" id="slug" name="slug" value="<?= e($instructor['slug']) ?>">
        </div>
        <div class="form-group">
          <label for="title_vi">Chức danh (VI)</label>
          <input type="text" id="title_vi" name="title_vi" value="<?= e($instructor['title_vi']) ?>">
        </div>
        <div class="form-group">
          <label for="title_en">Title (EN)</label>
          <input type="text" id="title_en" name="title_en" value="<?= e($instructor['title_en']) ?>">
        </div>
        <div class="form-group">
          <label for="bio_vi">Giới thiệu (VI)</label>
          <textarea id="bio_vi" name="bio_vi" rows="8"><?= e($instructor['bio_vi']) ?></textarea>
        </div>
        <div class="form-group">
          <label for="bio_en">Bio (EN)</label>
          <textarea id="bio_en" name="bio_en" rows="8"><?= e($instructor['bio_en']) ?></textarea>
        </div>
      </div>

      <div class="grid grid--3">
        <div class="form-group">
          <label for="email"><?= e(t('field_email')) ?></label>
          <input type="email" id="email" name="email" value="<?= e($instructor['email']) ?>">
        </div>
        <div class="form-group">
          <label for="sort_order">Sort order</label>
          <input type="number" id="sort_order" name="sort_order" value="<?= (int) $instructor['sort_order'] ?>">
        </div>
        <div class="form-group">
          <label><?= e(t('admin_status')) ?></label>
          <label class="check"><input type="checkbox" name="is_active" value="1" <?= (int) $instructor['is_active'] === 1 ? 'checked' : '' ?>> <?= e(t('admin_active')) ?></label>
        </div>
      </div>

      <div class="form-group">
        <label for="photo">Photo</label>
        <img data-preview-current src="<?= e(imageUrl($instructor['photo'], 'assets/images/logo.svg')) ?>" alt="" style="width:120px;height:120px;object-fit:cover;border-radius:50%;display:block;margin-bottom:10px">
        <canvas data-preview-target width="120" height="120" hidden style="width:120px;height:120px;border-radius:50%;display:block;margin-bottom:10px;background:var(--green-050)"></canvas>
        <input type="file" id="photo" name="photo" accept="image/*" data-preview>
      </div>

      <button class="btn btn--primary" type="submit"><?= e(t('btn_save')) ?></button>
    </div>
  </section>
</form>

<?php require __DIR__ . '/includes/admin-footer.php'; ?>
