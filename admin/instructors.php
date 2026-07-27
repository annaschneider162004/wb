<?php
/**
 * Admin: instructor listing.
 */
require_once __DIR__ . '/includes/auth-check.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrf()) {
        setFlash(t('msg_csrf'), 'error');
    } else {
        $id = (int) ($_POST['id'] ?? 0);
        if (($_POST['action'] ?? '') === 'delete' && $id > 0) {
            dbDelete('instructors', $id);
            setFlash(t('msg_deleted'), 'success');
        }
        if (($_POST['action'] ?? '') === 'toggle' && $id > 0) {
            $current = (int) dbValue('SELECT is_active FROM instructors WHERE id = ?', [$id]);
            dbUpdate('instructors', ['is_active' => $current === 1 ? 0 : 1], $id);
            setFlash(t('msg_saved'), 'success');
        }
    }
    redirect('admin/instructors.php');
}

$instructors = dbAll('SELECT i.*, (SELECT COUNT(*) FROM courses c WHERE c.instructor_id = i.id) AS course_count
                      FROM instructors i ORDER BY i.sort_order ASC, i.id ASC');

$adminTitle  = t('admin_instructors');
$adminActive = 'instructors';
require __DIR__ . '/includes/admin-header.php';
?>

<section class="panel">
  <div class="panel__head">
    <h2 class="panel__title"><?= e(t('admin_instructors')) ?> (<?= count($instructors) ?>)</h2>
    <a class="btn btn--primary btn--sm" href="<?= e(url('admin/instructor-edit.php')) ?>"><?= e(t('admin_add_new')) ?></a>
  </div>
  <div class="table-wrap">
    <table class="data">
      <thead>
        <tr>
          <th></th>
          <th><?= e(t('field_name')) ?></th>
          <th><?= e(t('admin_title')) ?></th>
          <th><?= e(t('field_email')) ?></th>
          <th><?= e(t('admin_courses')) ?></th>
          <th><?= e(t('admin_status')) ?></th>
          <th><?= e(t('admin_actions')) ?></th>
        </tr>
      </thead>
      <tbody>
      <?php if (!$instructors): ?>
        <tr><td colspan="7"><?= e(t('admin_no_items')) ?></td></tr>
      <?php endif; ?>
      <?php foreach ($instructors as $instructor): ?>
        <tr>
          <td><img class="thumb" src="<?= e(imageUrl($instructor['photo'], 'assets/images/logo.svg')) ?>" alt=""></td>
          <td>
            <strong><?= e($instructor['name']) ?></strong><br>
            <span class="text-muted" style="font-size:.82rem"><?= e($instructor['slug']) ?></span>
          </td>
          <td><?= e(localized($instructor, 'title')) ?></td>
          <td><?= e($instructor['email'] ?: '—') ?></td>
          <td><?= (int) $instructor['course_count'] ?></td>
          <td>
            <span class="pill pill--<?= (int) $instructor['is_active'] === 1 ? 'on' : 'off' ?>">
              <?= e((int) $instructor['is_active'] === 1 ? t('admin_active') : t('admin_inactive')) ?>
            </span>
          </td>
          <td>
            <div class="row-actions">
              <a class="btn btn--sm btn--outline" href="<?= e(url('admin/instructor-edit.php?id=' . (int) $instructor['id'])) ?>"><?= e(t('admin_edit')) ?></a>
              <form method="post" action="<?= e(url('admin/instructors.php')) ?>">
                <?= csrfField() ?>
                <input type="hidden" name="action" value="toggle">
                <input type="hidden" name="id" value="<?= (int) $instructor['id'] ?>">
                <button class="btn btn--sm btn--ghost" type="submit"><?= e((int) $instructor['is_active'] === 1 ? t('admin_inactive') : t('admin_active')) ?></button>
              </form>
              <form method="post" action="<?= e(url('admin/instructors.php')) ?>" data-confirm="<?= e(t('admin_confirm_delete')) ?>">
                <?= csrfField() ?>
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="id" value="<?= (int) $instructor['id'] ?>">
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
