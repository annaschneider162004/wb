<?php
/**
 * Admin: create / edit a course.
 */
require_once __DIR__ . '/includes/auth-check.php';

$id     = (int) ($_GET['id'] ?? 0);
$errors = [];

$course = [
    'id' => 0, 'slug' => '', 'title_vi' => '', 'title_en' => '', 'subtitle_vi' => '', 'subtitle_en' => '',
    'description_vi' => '', 'description_en' => '', 'level_id' => '', 'instructor_id' => '', 'thumbnail' => '',
    'price' => '0', 'duration' => '', 'is_featured' => 0, 'is_active' => 1,
    'meta_title' => '', 'meta_description' => '', 'sort_order' => 0,
];

if ($id > 0) {
    $existing = dbOne('SELECT * FROM courses WHERE id = ?', [$id]);
    if (!$existing) {
        setFlash(t('msg_not_found'), 'error');
        redirect('admin/courses.php');
    }
    $course = array_merge($course, $existing);
}

$levels      = dbAll('SELECT * FROM levels ORDER BY sort_order ASC, id ASC');
$instructors = dbAll('SELECT * FROM instructors ORDER BY sort_order ASC, id ASC');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach (['slug', 'title_vi', 'title_en', 'subtitle_vi', 'subtitle_en', 'description_vi', 'description_en',
              'duration', 'meta_title', 'meta_description'] as $field) {
        $course[$field] = trim((string) ($_POST[$field] ?? ''));
    }
    $course['level_id']      = $_POST['level_id'] !== '' ? (int) $_POST['level_id'] : null;
    $course['instructor_id'] = $_POST['instructor_id'] !== '' ? (int) $_POST['instructor_id'] : null;
    $course['price']         = (float) ($_POST['price'] ?? 0);
    $course['sort_order']    = (int) ($_POST['sort_order'] ?? 0);
    $course['is_featured']   = isset($_POST['is_featured']) ? 1 : 0;
    $course['is_active']     = isset($_POST['is_active']) ? 1 : 0;

    if (!verifyCsrf()) {
        $errors[] = t('msg_csrf');
    }
    if ($course['title_vi'] === '' && $course['title_en'] === '') {
        $errors[] = t('msg_required_fields');
    }

    if (!$errors) {
        $baseSlug = $course['slug'] !== '' ? $course['slug'] : ($course['title_vi'] ?: $course['title_en']);
        $course['slug'] = uniqueSlug('courses', slugify($baseSlug), $id);

        try {
            $course['thumbnail'] = handleImageUpload('thumbnail', $course['thumbnail']);
        } catch (RuntimeException $e) {
            $errors[] = $e->getMessage();
        }
    }

    if (!$errors) {
        $data = [
            'slug' => $course['slug'], 'title_vi' => $course['title_vi'], 'title_en' => $course['title_en'],
            'subtitle_vi' => $course['subtitle_vi'], 'subtitle_en' => $course['subtitle_en'],
            'description_vi' => $course['description_vi'], 'description_en' => $course['description_en'],
            'level_id' => $course['level_id'], 'instructor_id' => $course['instructor_id'],
            'thumbnail' => $course['thumbnail'], 'price' => $course['price'], 'duration' => $course['duration'],
            'is_featured' => $course['is_featured'], 'is_active' => $course['is_active'],
            'meta_title' => $course['meta_title'], 'meta_description' => $course['meta_description'],
            'sort_order' => $course['sort_order'],
        ];

        if ($id > 0) {
            dbUpdate('courses', $data, $id);
        } else {
            $id = dbInsert('courses', $data);
        }
        setFlash(t('msg_saved'), 'success');
        redirect('admin/course-edit.php?id=' . $id);
    }
}

$adminTitle  = ($id > 0 ? t('admin_edit') : t('admin_add_new')) . ' — ' . t('admin_courses');
$adminActive = 'courses';
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
      <a class="btn btn--sm btn--ghost" href="<?= e(url('admin/courses.php')) ?>">&larr; <?= e(t('back')) ?></a>
    </div>
    <div class="panel__body">
      <div class="grid grid--2">
        <div class="form-group">
          <label for="title_vi">Tiêu đề (VI) <span class="required">*</span></label>
          <input type="text" id="title_vi" name="title_vi" data-slug-source="slug" value="<?= e($course['title_vi']) ?>">
        </div>
        <div class="form-group">
          <label for="title_en">Title (EN)</label>
          <input type="text" id="title_en" name="title_en" value="<?= e($course['title_en']) ?>">
        </div>
        <div class="form-group">
          <label for="subtitle_vi">Mô tả ngắn (VI)</label>
          <input type="text" id="subtitle_vi" name="subtitle_vi" value="<?= e($course['subtitle_vi']) ?>">
        </div>
        <div class="form-group">
          <label for="subtitle_en">Subtitle (EN)</label>
          <input type="text" id="subtitle_en" name="subtitle_en" value="<?= e($course['subtitle_en']) ?>">
        </div>
      </div>

      <div class="form-group">
        <label for="slug">Slug</label>
        <input type="text" id="slug" name="slug" value="<?= e($course['slug']) ?>">
        <p class="form-hint">/khoa-hoc/&lt;slug&gt;</p>
      </div>

      <div class="grid grid--2">
        <div class="form-group">
          <label for="description_vi">Nội dung (VI)</label>
          <textarea id="description_vi" name="description_vi" rows="10"><?= e($course['description_vi']) ?></textarea>
        </div>
        <div class="form-group">
          <label for="description_en">Content (EN)</label>
          <textarea id="description_en" name="description_en" rows="10"><?= e($course['description_en']) ?></textarea>
        </div>
      </div>
    </div>
  </section>

  <section class="panel">
    <div class="panel__head"><h2 class="panel__title"><?= e(t('admin_settings')) ?></h2></div>
    <div class="panel__body">
      <div class="grid grid--3">
        <div class="form-group">
          <label for="level_id"><?= e(t('level')) ?></label>
          <select id="level_id" name="level_id">
            <option value="">—</option>
            <?php foreach ($levels as $level): ?>
              <option value="<?= (int) $level['id'] ?>" <?= (int) $course['level_id'] === (int) $level['id'] ? 'selected' : '' ?>>
                <?= e(localized($level, 'name')) ?> (<?= e($level['age_range']) ?>)
              </option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="form-group">
          <label for="instructor_id"><?= e(t('instructor')) ?></label>
          <select id="instructor_id" name="instructor_id">
            <option value="">—</option>
            <?php foreach ($instructors as $instructor): ?>
              <option value="<?= (int) $instructor['id'] ?>" <?= (int) $course['instructor_id'] === (int) $instructor['id'] ? 'selected' : '' ?>>
                <?= e($instructor['name']) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="form-group">
          <label for="duration"><?= e(t('duration')) ?></label>
          <input type="text" id="duration" name="duration" value="<?= e($course['duration']) ?>">
        </div>
        <div class="form-group">
          <label for="price"><?= e(t('price')) ?> (VND)</label>
          <input type="number" id="price" name="price" step="1000" min="0" value="<?= e((string) $course['price']) ?>">
        </div>
        <div class="form-group">
          <label for="sort_order">Sort order</label>
          <input type="number" id="sort_order" name="sort_order" value="<?= (int) $course['sort_order'] ?>">
        </div>
        <div class="form-group">
          <label><?= e(t('admin_status')) ?></label>
          <label class="check"><input type="checkbox" name="is_active" value="1" <?= (int) $course['is_active'] === 1 ? 'checked' : '' ?>> <?= e(t('admin_active')) ?></label>
          <label class="check"><input type="checkbox" name="is_featured" value="1" <?= (int) $course['is_featured'] === 1 ? 'checked' : '' ?>> <?= e(t('admin_featured')) ?></label>
        </div>
      </div>

      <div class="form-group">
        <label for="thumbnail">Thumbnail</label>
        <img data-preview-current src="<?= e(imageUrl($course['thumbnail'])) ?>" alt="" style="width:180px;height:120px;object-fit:cover;border-radius:12px;display:block;margin-bottom:10px">
        <canvas data-preview-target width="180" height="120" hidden style="width:180px;height:120px;border-radius:12px;display:block;margin-bottom:10px;background:var(--green-050)"></canvas>
        <input type="file" id="thumbnail" name="thumbnail" accept="image/*" data-preview>
      </div>

      <div class="grid grid--2">
        <div class="form-group">
          <label for="meta_title">Meta title</label>
          <input type="text" id="meta_title" name="meta_title" value="<?= e($course['meta_title']) ?>">
        </div>
        <div class="form-group">
          <label for="meta_description">Meta description</label>
          <textarea id="meta_description" name="meta_description" rows="3"><?= e($course['meta_description']) ?></textarea>
        </div>
      </div>

      <button class="btn btn--primary" type="submit"><?= e(t('btn_save')) ?></button>
    </div>
  </section>
</form>

<?php require __DIR__ . '/includes/admin-footer.php'; ?>
