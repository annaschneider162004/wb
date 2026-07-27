<?php
/**
 * Admin: levels (inline CRUD).
 */
require_once __DIR__ . '/includes/auth-check.php';

$colors = ['green', 'blue', 'purple'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrf()) {
        setFlash(t('msg_csrf'), 'error');
        redirect('admin/levels.php');
    }

    $action = (string) ($_POST['action'] ?? '');
    $id     = (int) ($_POST['id'] ?? 0);

    if ($action === 'delete' && $id > 0) {
        dbDelete('levels', $id);
        setFlash(t('msg_deleted'), 'success');
    } elseif ($action === 'save') {
        $color = (string) ($_POST['color'] ?? 'green');
        $data  = [
            'name_vi'        => trim((string) ($_POST['name_vi'] ?? '')),
            'name_en'        => trim((string) ($_POST['name_en'] ?? '')),
            'age_range'      => trim((string) ($_POST['age_range'] ?? '')),
            'description_vi' => trim((string) ($_POST['description_vi'] ?? '')),
            'description_en' => trim((string) ($_POST['description_en'] ?? '')),
            'color'          => in_array($color, $colors, true) ? $color : 'green',
            'sort_order'     => (int) ($_POST['sort_order'] ?? 0),
        ];

        if ($data['name_vi'] === '' && $data['name_en'] === '') {
            setFlash(t('msg_required_fields'), 'error');
        } else {
            if ($id > 0) {
                dbUpdate('levels', $data, $id);
            } else {
                dbInsert('levels', $data);
            }
            setFlash(t('msg_saved'), 'success');
        }
    }

    redirect('admin/levels.php');
}

$levels = dbAll('SELECT * FROM levels ORDER BY sort_order ASC, id ASC');

$adminTitle  = t('admin_levels');
$adminActive = 'levels';
require __DIR__ . '/includes/admin-header.php';
?>

<section class="panel">
  <div class="panel__head"><h2 class="panel__title"><?= e(t('admin_levels')) ?> (<?= count($levels) ?>)</h2></div>
  <div class="panel__body">
    <?php if (!$levels): ?>
      <p class="text-muted"><?= e(t('admin_no_items')) ?></p>
    <?php endif; ?>

    <?php foreach ($levels as $level): ?>
      <form method="post" action="<?= e(url('admin/levels.php')) ?>" style="border:1px solid var(--line);border-radius:14px;padding:20px;margin-bottom:18px">
        <?= csrfField() ?>
        <input type="hidden" name="id" value="<?= (int) $level['id'] ?>">
        <div class="grid grid--4">
          <div class="form-group">
            <label>Tên (VI)</label>
            <input type="text" name="name_vi" value="<?= e($level['name_vi']) ?>">
          </div>
          <div class="form-group">
            <label>Name (EN)</label>
            <input type="text" name="name_en" value="<?= e($level['name_en']) ?>">
          </div>
          <div class="form-group">
            <label><?= e(t('age_range')) ?></label>
            <input type="text" name="age_range" value="<?= e($level['age_range']) ?>">
          </div>
          <div class="form-group">
            <label>Color</label>
            <select name="color">
              <?php foreach ($colors as $color): ?>
                <option value="<?= e($color) ?>" <?= $level['color'] === $color ? 'selected' : '' ?>><?= e($color) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>
        <div class="grid grid--2">
          <div class="form-group">
            <label>Mô tả (VI)</label>
            <textarea name="description_vi" rows="3"><?= e($level['description_vi']) ?></textarea>
          </div>
          <div class="form-group">
            <label>Description (EN)</label>
            <textarea name="description_en" rows="3"><?= e($level['description_en']) ?></textarea>
          </div>
        </div>
        <div class="form-group" style="max-width:180px">
          <label>Sort order</label>
          <input type="number" name="sort_order" value="<?= (int) $level['sort_order'] ?>">
        </div>
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
    <form method="post" action="<?= e(url('admin/levels.php')) ?>">
      <?= csrfField() ?>
      <input type="hidden" name="id" value="0">
      <input type="hidden" name="action" value="save">
      <div class="grid grid--4">
        <div class="form-group">
          <label>Tên (VI) <span class="required">*</span></label>
          <input type="text" name="name_vi">
        </div>
        <div class="form-group">
          <label>Name (EN)</label>
          <input type="text" name="name_en">
        </div>
        <div class="form-group">
          <label><?= e(t('age_range')) ?></label>
          <input type="text" name="age_range">
        </div>
        <div class="form-group">
          <label>Color</label>
          <select name="color">
            <?php foreach ($colors as $color): ?>
              <option value="<?= e($color) ?>"><?= e($color) ?></option>
            <?php endforeach; ?>
          </select>
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
      <div class="form-group" style="max-width:180px">
        <label>Sort order</label>
        <input type="number" name="sort_order" value="0">
      </div>
      <button class="btn btn--primary" type="submit"><?= e(t('admin_add_new')) ?></button>
    </form>
  </div>
</section>

<?php require __DIR__ . '/includes/admin-footer.php'; ?>
