<?php
/**
 * Admin: members management.
 */
require_once __DIR__ . '/includes/auth-check.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrf()) {
        setFlash(t('msg_csrf'), 'error');
        redirect('admin/members.php');
    }

    $action = (string) ($_POST['action'] ?? '');
    $id     = (int) ($_POST['id'] ?? 0);

    if ($id === (int) $adminUser['id']) {
        setFlash(t('msg_admin_required'), 'error');
        redirect('admin/members.php');
    }

    if ($action === 'delete' && $id > 0) {
        dbDelete('users', $id);
        setFlash(t('msg_deleted'), 'success');
    } elseif ($action === 'toggle' && $id > 0) {
        $current = (int) dbValue('SELECT is_active FROM users WHERE id = ?', [$id]);
        dbUpdate('users', ['is_active' => $current === 1 ? 0 : 1], $id);
        setFlash(t('msg_saved'), 'success');
    } elseif ($action === 'role' && $id > 0) {
        $current = (int) dbValue('SELECT is_admin FROM users WHERE id = ?', [$id]);
        dbUpdate('users', ['is_admin' => $current === 1 ? 0 : 1], $id);
        setFlash(t('msg_saved'), 'success');
    } elseif ($action === 'reset' && $id > 0) {
        $newPassword = (string) ($_POST['new_password'] ?? '');
        if (strlen($newPassword) < 8) {
            setFlash(t('msg_password_short'), 'error');
        } else {
            dbUpdate('users', ['password' => password_hash($newPassword, PASSWORD_DEFAULT)], $id);
            setFlash(t('msg_password_updated'), 'success');
        }
    }

    redirect('admin/members.php');
}

$search  = trim((string) ($_GET['q'] ?? ''));
$params  = [];
$where   = '';
if ($search !== '') {
    $where  = 'WHERE name LIKE ? OR email LIKE ?';
    $params = ['%' . $search . '%', '%' . $search . '%'];
}

$members = dbAll("SELECT * FROM users $where ORDER BY id DESC", $params);

$adminTitle  = t('admin_members');
$adminActive = 'members';
require __DIR__ . '/includes/admin-header.php';
?>

<section class="panel">
  <div class="panel__head">
    <h2 class="panel__title"><?= e(t('admin_members')) ?> (<?= count($members) ?>)</h2>
    <form method="get" action="<?= e(url('admin/members.php')) ?>" style="display:flex;gap:8px">
      <input type="search" name="q" value="<?= e($search) ?>" placeholder="<?= e(t('search_placeholder')) ?>" style="max-width:230px">
      <button class="btn btn--outline btn--sm" type="submit"><?= e(t('search')) ?></button>
    </form>
  </div>
  <div class="table-wrap">
    <table class="data">
      <thead>
        <tr>
          <th><?= e(t('field_name')) ?></th>
          <th><?= e(t('field_email')) ?></th>
          <th><?= e(t('field_phone')) ?></th>
          <th><?= e(t('member_since')) ?></th>
          <th><?= e(t('admin_status')) ?></th>
          <th><?= e(t('admin_actions')) ?></th>
        </tr>
      </thead>
      <tbody>
      <?php if (!$members): ?>
        <tr><td colspan="6"><?= e(t('admin_no_items')) ?></td></tr>
      <?php endif; ?>
      <?php foreach ($members as $member): ?>
        <?php $isSelf = (int) $member['id'] === (int) $adminUser['id']; ?>
        <tr>
          <td><strong><?= e($member['name']) ?></strong></td>
          <td><?= e($member['email']) ?></td>
          <td><?= e($member['phone'] ?: '—') ?></td>
          <td><?= e(formatDate($member['created_at'])) ?></td>
          <td>
            <span class="pill pill--<?= (int) $member['is_active'] === 1 ? 'on' : 'off' ?>">
              <?= e((int) $member['is_active'] === 1 ? t('admin_active') : t('admin_inactive')) ?>
            </span>
            <?php if ((int) $member['is_admin'] === 1): ?>
              <span class="pill pill--admin">Admin</span>
            <?php endif; ?>
          </td>
          <td>
            <?php if ($isSelf): ?>
              <span class="text-muted">—</span>
            <?php else: ?>
              <div class="row-actions">
                <form method="post" action="<?= e(url('admin/members.php')) ?>">
                  <?= csrfField() ?>
                  <input type="hidden" name="action" value="toggle">
                  <input type="hidden" name="id" value="<?= (int) $member['id'] ?>">
                  <button class="btn btn--sm btn--ghost" type="submit"><?= e((int) $member['is_active'] === 1 ? t('admin_inactive') : t('admin_active')) ?></button>
                </form>
                <form method="post" action="<?= e(url('admin/members.php')) ?>">
                  <?= csrfField() ?>
                  <input type="hidden" name="action" value="role">
                  <input type="hidden" name="id" value="<?= (int) $member['id'] ?>">
                  <button class="btn btn--sm btn--outline" type="submit"><?= (int) $member['is_admin'] === 1 ? 'Member' : 'Admin' ?></button>
                </form>
                <form method="post" action="<?= e(url('admin/members.php')) ?>" style="display:flex;gap:6px">
                  <?= csrfField() ?>
                  <input type="hidden" name="action" value="reset">
                  <input type="hidden" name="id" value="<?= (int) $member['id'] ?>">
                  <input type="password" name="new_password" placeholder="<?= e(t('field_new_password')) ?>" style="max-width:160px" minlength="8">
                  <button class="btn btn--sm btn--outline" type="submit"><?= e(t('btn_save')) ?></button>
                </form>
                <form method="post" action="<?= e(url('admin/members.php')) ?>" data-confirm="<?= e(t('admin_confirm_delete')) ?>">
                  <?= csrfField() ?>
                  <input type="hidden" name="action" value="delete">
                  <input type="hidden" name="id" value="<?= (int) $member['id'] ?>">
                  <button class="btn btn--sm btn--danger" type="submit"><?= e(t('admin_delete')) ?></button>
                </form>
              </div>
            <?php endif; ?>
          </td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</section>

<?php require __DIR__ . '/includes/admin-footer.php'; ?>
