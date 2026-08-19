<?php
/**
 * Admin: site settings + contact messages.
 */
require_once __DIR__ . '/includes/auth-check.php';

$settingKeys = [
    'site_name'            => ['text', 'Site name'],
    'site_tagline_vi'      => ['text', 'Tagline (VI)'],
    'site_tagline_en'      => ['text', 'Tagline (EN)'],
    'contact_email'        => ['text', 'Contact email'],
    'phone'                => ['text', 'Phone'],
    'address'              => ['text', 'Address'],
    'working_hours'        => ['text', 'Working hours'],
    'google_map_embed_url' => ['textarea', 'Google Maps embed URL'],
    'facebook_url'         => ['text', 'Facebook URL'],
    'youtube_url'          => ['text', 'YouTube URL'],
    'zalo_url'             => ['text', 'Zalo URL'],
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrf()) {
        setFlash(t('msg_csrf'), 'error');
        redirect('admin/settings.php');
    }

    $action = (string) ($_POST['action'] ?? 'settings');

    if ($action === 'delete_message') {
        $messageId = (int) ($_POST['id'] ?? 0);
        if ($messageId > 0) {
            dbDelete('contact_messages', $messageId);
            setFlash(t('msg_deleted'), 'success');
        }
    } elseif ($action === 'read_message') {
        $messageId = (int) ($_POST['id'] ?? 0);
        if ($messageId > 0) {
            $current = (int) dbValue('SELECT is_read FROM contact_messages WHERE id = ?', [$messageId]);
            dbUpdate('contact_messages', ['is_read' => $current === 1 ? 0 : 1], $messageId);
        }
    } else {
        foreach (array_keys($settingKeys) as $key) {
            saveSetting($key, trim((string) ($_POST[$key] ?? '')));
        }
        setFlash(t('msg_saved'), 'success');
    }

    redirect('admin/settings.php');
}

$messages = dbAll('SELECT * FROM contact_messages ORDER BY id DESC LIMIT 50');

$adminTitle  = t('admin_settings');
$adminActive = 'settings';
require __DIR__ . '/includes/admin-header.php';
?>

<section class="panel">
  <div class="panel__head"><h2 class="panel__title"><?= e(t('admin_settings')) ?></h2></div>
  <div class="panel__body">
    <form method="post" action="<?= e(url('admin/settings.php')) ?>">
      <?= csrfField() ?>
      <input type="hidden" name="action" value="settings">
      <div class="grid grid--2">
        <?php foreach ($settingKeys as $key => [$type, $label]): ?>
          <div class="form-group">
            <label for="<?= e($key) ?>"><?= e($label) ?></label>
            <?php if ($type === 'textarea'): ?>
              <textarea id="<?= e($key) ?>" name="<?= e($key) ?>" rows="3"><?= e(setting($key)) ?></textarea>
            <?php else: ?>
              <input type="text" id="<?= e($key) ?>" name="<?= e($key) ?>" value="<?= e(setting($key)) ?>">
            <?php endif; ?>
          </div>
        <?php endforeach; ?>
      </div>
      <button class="btn btn--primary" type="submit"><?= e(t('btn_save')) ?></button>
    </form>
  </div>
</section>

<section class="panel">
  <div class="panel__head"><h2 class="panel__title"><?= e(t('admin_messages')) ?> (<?= count($messages) ?>)</h2></div>
  <div class="table-wrap">
    <table class="data">
      <thead>
        <tr>
          <th><?= e(t('field_name')) ?></th>
          <th><?= e(t('field_subject')) ?></th>
          <th><?= e(t('field_message')) ?></th>
          <th><?= e(t('published_on')) ?></th>
          <th><?= e(t('admin_actions')) ?></th>
        </tr>
      </thead>
      <tbody>
      <?php if (!$messages): ?>
        <tr><td colspan="5"><?= e(t('admin_no_items')) ?></td></tr>
      <?php endif; ?>
      <?php foreach ($messages as $message): ?>
        <tr>
          <td>
            <strong><?= e($message['name']) ?></strong><br>
            <span class="text-muted" style="font-size:.82rem"><?= e($message['email']) ?></span><br>
            <span class="text-muted" style="font-size:.82rem"><?= e($message['phone'] ?: '') ?></span>
          </td>
          <td><?= e($message['subject'] ?: '—') ?></td>
          <td style="max-width:420px;white-space:pre-line"><?= e($message['message']) ?></td>
          <td><?= e(formatDate($message['created_at'])) ?></td>
          <td>
            <div class="row-actions">
              <form method="post" action="<?= e(url('admin/settings.php')) ?>">
                <?= csrfField() ?>
                <input type="hidden" name="action" value="read_message">
                <input type="hidden" name="id" value="<?= (int) $message['id'] ?>">
                <button class="btn btn--sm btn--ghost" type="submit">
                  <?= (int) $message['is_read'] === 1 ? '●' : '○' ?>
                </button>
              </form>
              <form method="post" action="<?= e(url('admin/settings.php')) ?>" data-confirm="<?= e(t('admin_confirm_delete')) ?>">
                <?= csrfField() ?>
                <input type="hidden" name="action" value="delete_message">
                <input type="hidden" name="id" value="<?= (int) $message['id'] ?>">
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
