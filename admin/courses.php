<?php
/**
 * Admin: course listing.
 */
require_once __DIR__ . '/includes/auth-check.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrf()) {
        setFlash(t('msg_csrf'), 'error');
    } else {
        $id = (int) ($_POST['id'] ?? 0);
        if (($_POST['action'] ?? '') === 'delete' && $id > 0) {
            dbDelete('courses', $id);
            setFlash(t('msg_deleted'), 'success');
        }
        if (($_POST['action'] ?? '') === 'toggle' && $id > 0) {
            $current = (int) dbValue('SELECT is_active FROM courses WHERE id = ?', [$id]);
            dbUpdate('courses', ['is_active' => $current === 1 ? 0 : 1], $id);
            setFlash(t('msg_saved'), 'success');
        }
    }
    redirect('admin/courses.php');
}

$courses = dbAll('SELECT c.*, l.name_vi AS level_vi, l.name_en AS level_en, l.color AS level_color,
                         i.name AS instructor_name
                  FROM courses c
                  LEFT JOIN levels l ON l.id = c.level_id
                  LEFT JOIN instructors i ON i.id = c.instructor_id
                  ORDER BY c.sort_order ASC, c.id DESC');

$adminTitle  = t('admin_courses');
$adminActive = 'courses';
require __DIR__ . '/includes/admin-header.php';
?>

<section class="panel">
  <div class="panel__head">
    <h2 class="panel__title"><?= e(t('admin_courses')) ?> (<?= count($courses) ?>)</h2>
    <a class="btn btn--primary btn--sm" href="<?= e(url('admin/course-edit.php')) ?>"><?= e(t('admin_add_new')) ?></a>
  </div>
  <div class="table-wrap">
    <table class="data">
      <thead>
        <tr>
          <th></th>
          <th><?= e(t('admin_title')) ?></th>
          <th><?= e(t('level')) ?></th>
          <th><?= e(t('instructor')) ?></th>
          <th><?= e(t('price')) ?></th>
          <th><?= e(t('admin_status')) ?></th>
          <th><?= e(t('admin_actions')) ?></th>
        </tr>
      </thead>
      <tbody>
      <?php if (!$courses): ?>
        <tr><td colspan="7"><?= e(t('admin_no_items')) ?></td></tr>
      <?php endif; ?>
      <?php foreach ($courses as $course): ?>
        <tr>
          <td><img class="thumb" src="<?= e(imageUrl($course['thumbnail'])) ?>" alt=""></td>
          <td>
            <strong><?= e(localized($course, 'title')) ?></strong><br>
            <span class="text-muted" style="font-size:.82rem"><?= e($course['slug']) ?></span>
          </td>
          <td><?= e(localized($course, 'level') ?: '—') ?></td>
          <td><?= e($course['instructor_name'] ?: '—') ?></td>
          <td><?= e(formatPrice($course['price'])) ?></td>
          <td>
            <span class="pill pill--<?= (int) $course['is_active'] === 1 ? 'on' : 'off' ?>">
              <?= e((int) $course['is_active'] === 1 ? t('admin_active') : t('admin_inactive')) ?>
            </span>
            <?php if ((int) $course['is_featured'] === 1): ?>
              <span class="pill pill--admin"><?= e(t('admin_featured')) ?></span>
            <?php endif; ?>
          </td>
          <td>
            <div class="row-actions">
              <a class="btn btn--sm btn--outline" href="<?= e(url('admin/course-edit.php?id=' . (int) $course['id'])) ?>"><?= e(t('admin_edit')) ?></a>
              <form method="post" action="<?= e(url('admin/courses.php')) ?>">
                <?= csrfField() ?>
                <input type="hidden" name="action" value="toggle">
                <input type="hidden" name="id" value="<?= (int) $course['id'] ?>">
                <button class="btn btn--sm btn--ghost" type="submit"><?= e((int) $course['is_active'] === 1 ? t('admin_inactive') : t('admin_active')) ?></button>
              </form>
              <form method="post" action="<?= e(url('admin/courses.php')) ?>" data-confirm="<?= e(t('admin_confirm_delete')) ?>">
                <?= csrfField() ?>
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="id" value="<?= (int) $course['id'] ?>">
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
