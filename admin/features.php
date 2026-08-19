<?php
/**
 * Admin: homepage feature blocks (inline CRUD).
 */
require_once __DIR__ . '/includes/auth-check.php';

$icons = ['route', 'laptop', 'teacher', 'library', 'chart', 'music', 'star', 'clock', 'user', 'check', 'globe'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrf()) {
        setFlash(t('msg_csrf'), 'error');
        redirect('admin/features.php');
    }

    $action = (string) ($_POST['action'] ?? '');
    $id     = (int) ($_POST['id'] ?? 0);

    if ($action === 'delete' && $id > 0) {
        dbDelete('features', $id);
        setFlash(t('msg_deleted'), 'success');
    } elseif ($action === 'save') {
        $iconName = (string) ($_POST['icon'] ?? 'star');
        $data = [
            'icon'           => in_array($iconName, $icons, true) ? $iconName : 'star',
            'title_vi'       => trim((string) ($_POST['title_vi'] ?? '')),
            'title_en'       => trim((string) ($_POST['title_en'] ?? '')),
            'description_vi' => trim((string) ($_POST['description_vi'] ?? '')),
            'description_en' => trim((string) ($_POST['description_en'] ?? '')),
            'sort_order'     => (int) ($_POST['sort_order'] ?? 0),
            'is_active'      => isset($_POST['is_active']) ? 1 : 0,
        ];

        if ($data['title_vi'] === '' && $data['title_en'] === '') {
            setFlash(t('msg_required_fields'), 'error');
        } else {
            if ($id > 0) {
                dbUpdate('features', $data, $id);
            } else {
                dbInsert('features', $data);
            }
            setFlash(t('msg_saved'), 'success');
        }
    }

    redirect('admin/features.php');
}

$features = dbAll('SELECT * FROM features ORDER BY sort_order ASC, id ASC');

$adminTitle  = t('admin_features');
$adminActive = 'features';
require __DIR__ . '/includes/admin-header.php';
?>

<section class="panel">
  <div class="panel__head"><h2 class="panel__title"><?= e(t('admin_features')) ?> (<?= count($features) ?>)</h2></div>
  <div class="panel__body">
    <?php if (!$features): ?>
      <p class="text-muted"><?= e(t('admin_no_items')) ?></p>
    <?php endif; ?>

    <?php foreach ($features as $feature): ?>
      <form method="post" action="<?= e(url('admin/features.php')) ?>" style="border:1px solid var(--line);border-radius:14px;padding:20px;margin-bottom:18px">
        <?= csrfField() ?>
        <input type="hidden" name="id" value="<?= (int) $feature['id'] ?>">
        <div style="display:flex;align-items:center;gap:14px;margin-bottom:14px">
          <span class="admin-card__icon"><?= icon($feature['icon'] ?: 'star', 'icon') ?></span>
          <strong><?= e(localized($feature, 'title')) ?></strong>
        </div>
        <div class="grid grid--4">
          <div class="form-group">
            <label>Icon</label>
            <select name="icon">
              <?php foreach ($icons as $iconName): ?>
                <option value="<?= e($iconName) ?>" <?= $feature['icon'] === $iconName ? 'selected' : '' ?>><?= e($iconName) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="form-group">
            <label>Tiêu đề (VI)</label>
            <input type="text" name="title_vi" value="<?= e($feature['title_vi']) ?>">
          </div>
          <div class="form-group">
            <label>Title (EN)</label>
            <input type="text" name="title_en" value="<?= e($feature['title_en']) ?>">
          </div>
          <div class="form-group">
            <label>Sort order</label>
            <input type="number" name="sort_order" value="<?= (int) $feature['sort_order'] ?>">
          </div>
        </div>
        <div class="grid grid--2">
          <div class="form-group">
            <label>Mô tả (VI)</label>
            <textarea name="description_vi" rows="3"><?= e($feature['description_vi']) ?></textarea>
          </div>
          <div class="form-group">
            <label>Description (EN)</label>
            <textarea name="description_en" rows="3"><?= e($feature['description_en']) ?></textarea>
          </div>
        </div>
        <label class="check"><input type="checkbox" name="is_active" value="1" <?= (int) $feature['is_active'] === 1 ? 'checked' : '' ?>> <?= e(t('admin_active')) ?></label>
        <div class="row-actions">
          <button class="btn btn--primary btn--sm" type="submit" name="action" value="save"><?= e(t('btn_save')) ?></button>
          <button class="btn btn--danger btn--sm" type="submit" name="action" value="delete"
                  onclick="return confirm('<?= e(t('admin_confirm_delete')) ?>')"><?= e(t('admin_delete')) ?></button>
        </div>
      </form>
    <?php endforeach; ?>
  </div>
</section>

<section class="panel">
  <div class="panel__head"><h2 class="panel__title"><?= e(t('admin_add_new')) ?></h2></div>
  <div class="panel__body">
    <form method="post" action="<?= e(url('admin/features.php')) ?>">
      <?= csrfField() ?>
      <input type="hidden" name="id" value="0">
      <input type="hidden" name="action" value="save">
      <div class="grid grid--4">
        <div class="form-group">
          <label>Icon</label>
          <select name="icon">
            <?php foreach ($icons as $iconName): ?>
              <option value="<?= e($iconName) ?>"><?= e($iconName) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="form-group">
          <label>Tiêu đề (VI) <span class="required">*</span></label>
          <input type="text" name="title_vi">
        </div>
        <div class="form-group">
          <label>Title (EN)</label>
          <input type="text" name="title_en">
        </div>
        <div class="form-group">
          <label>Sort order</label>
          <input type="number" name="sort_order" value="0">
        </div>
      </div>
      <div class="grid grid--2">
        <div class="form-group">
          <label>Mô tả (VI)</label>
          <textarea name="description_vi" rows="3"></textarea>
        </div>
        <div class="form-group">
          <label>Description (EN)</label>
          <textarea name="description_en" rows="3"></textarea>
        </div>
      </div>
      <label class="check"><input type="checkbox" name="is_active" value="1" checked> <?= e(t('admin_active')) ?></label>
      <button class="btn btn--primary" type="submit"><?= e(t('admin_add_new')) ?></button>
    </form>
  </div>
</section>

<?php require __DIR__ . '/includes/admin-footer.php'; ?>
