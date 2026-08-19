<?php
/**
 * Admin dashboard.
 */
require_once __DIR__ . '/includes/auth-check.php';

$stats = [
    ['music', (int) dbValue('SELECT COUNT(*) FROM courses'), t('admin_courses')],
    ['library', (int) dbValue('SELECT COUNT(*) FROM posts'), t('admin_posts')],
    ['teacher', (int) dbValue('SELECT COUNT(*) FROM instructors'), t('admin_instructors')],
    ['user', (int) dbValue('SELECT COUNT(*) FROM users'), t('admin_members')],
];

$recentCourses  = dbAll('SELECT c.*, l.name_vi AS level_vi, l.name_en AS level_en, l.color
                         FROM courses c LEFT JOIN levels l ON l.id = c.level_id
                         ORDER BY c.id DESC LIMIT 5');
$recentPosts    = dbAll('SELECT * FROM posts ORDER BY id DESC LIMIT 5');
$recentMembers  = dbAll('SELECT * FROM users ORDER BY id DESC LIMIT 5');
$recentMessages = dbAll('SELECT * FROM contact_messages ORDER BY id DESC LIMIT 6');

$adminTitle  = t('admin_dashboard');
$adminActive = 'dashboard';
require __DIR__ . '/includes/admin-header.php';
?>

<div class="admin-cards">
  <?php foreach ($stats as [$iconName, $value, $label]): ?>
    <div class="admin-card">
      <span class="admin-card__icon"><?= icon($iconName, 'icon') ?></span>
      <span>
        <span class="admin-card__value"><?= (int) $value ?></span>
        <span class="admin-card__label"><?= e($label) ?></span>
      </span>
    </div>
  <?php endforeach; ?>
</div>

<div class="grid grid--2">
  <section class="panel">
    <div class="panel__head">
      <h2 class="panel__title"><?= e(t('admin_recent')) ?> — <?= e(t('admin_courses')) ?></h2>
      <a class="btn btn--sm btn--outline" href="<?= e(url('admin/courses.php')) ?>"><?= e(t('view_all')) ?></a>
    </div>
    <div class="table-wrap">
      <table class="data">
        <tbody>
        <?php if (!$recentCourses): ?>
          <tr><td><?= e(t('admin_no_items')) ?></td></tr>
        <?php endif; ?>
        <?php foreach ($recentCourses as $course): ?>
          <tr>
            <td style="width:70px"><img class="thumb" src="<?= e(imageUrl($course['thumbnail'])) ?>" alt=""></td>
            <td>
              <strong><?= e(localized($course, 'title')) ?></strong><br>
              <span class="text-muted" style="font-size:.85rem"><?= e(localized($course, 'level') ?: '—') ?></span>
            </td>
            <td style="width:110px">
              <span class="pill pill--<?= (int) $course['is_active'] === 1 ? 'on' : 'off' ?>">
                <?= e((int) $course['is_active'] === 1 ? t('admin_active') : t('admin_inactive')) ?>
              </span>
            </td>
            <td style="width:80px"><a class="btn btn--sm btn--outline" href="<?= e(url('admin/course-edit.php?id=' . (int) $course['id'])) ?>"><?= e(t('admin_edit')) ?></a></td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </section>

  <section class="panel">
    <div class="panel__head">
      <h2 class="panel__title"><?= e(t('admin_recent')) ?> — <?= e(t('admin_posts')) ?></h2>
      <a class="btn btn--sm btn--outline" href="<?= e(url('admin/posts.php')) ?>"><?= e(t('view_all')) ?></a>
    </div>
    <div class="table-wrap">
      <table class="data">
        <tbody>
        <?php if (!$recentPosts): ?>
          <tr><td><?= e(t('admin_no_items')) ?></td></tr>
        <?php endif; ?>
        <?php foreach ($recentPosts as $post): ?>
          <tr>
            <td style="width:70px"><img class="thumb" src="<?= e(imageUrl($post['thumbnail'])) ?>" alt=""></td>
            <td>
              <strong><?= e(localized($post, 'title')) ?></strong><br>
              <span class="text-muted" style="font-size:.85rem"><?= e(formatDate($post['created_at'])) ?></span>
            </td>
            <td style="width:80px"><a class="btn btn--sm btn--outline" href="<?= e(url('admin/post-edit.php?id=' . (int) $post['id'])) ?>"><?= e(t('admin_edit')) ?></a></td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </section>
</div>

<div class="grid grid--2">
  <section class="panel">
    <div class="panel__head">
      <h2 class="panel__title"><?= e(t('admin_recent')) ?> — <?= e(t('admin_members')) ?></h2>
      <a class="btn btn--sm btn--outline" href="<?= e(url('admin/members.php')) ?>"><?= e(t('view_all')) ?></a>
    </div>
    <div class="table-wrap">
      <table class="data">
        <tbody>
        <?php if (!$recentMembers): ?>
          <tr><td><?= e(t('admin_no_items')) ?></td></tr>
        <?php endif; ?>
        <?php foreach ($recentMembers as $member): ?>
          <tr>
            <td>
              <strong><?= e($member['name']) ?></strong><br>
              <span class="text-muted" style="font-size:.85rem"><?= e($member['email']) ?></span>
            </td>
            <td style="width:120px">
              <?php if ((int) $member['is_admin'] === 1): ?>
                <span class="pill pill--admin">Admin</span>
              <?php else: ?>
                <span class="pill pill--on"><?= e(t('admin_members')) ?></span>
              <?php endif; ?>
            </td>
            <td style="width:120px" class="text-muted"><?= e(formatDate($member['created_at'])) ?></td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </section>

  <section class="panel">
    <div class="panel__head">
      <h2 class="panel__title"><?= e(t('admin_messages')) ?></h2>
    </div>
    <div class="table-wrap">
      <table class="data">
        <tbody>
        <?php if (!$recentMessages): ?>
          <tr><td><?= e(t('admin_no_items')) ?></td></tr>
        <?php endif; ?>
        <?php foreach ($recentMessages as $message): ?>
          <tr>
            <td>
              <strong><?= e($message['name']) ?></strong>
              <span class="text-muted" style="font-size:.85rem">&lt;<?= e($message['email']) ?>&gt;</span><br>
              <span style="font-size:.88rem"><?= e($message['subject'] ?: '—') ?></span><br>
              <span class="text-muted" style="font-size:.85rem"><?= e(excerpt($message['message'], 90)) ?></span>
            </td>
            <td style="width:120px" class="text-muted"><?= e(formatDate($message['created_at'])) ?></td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </section>
</div>

<?php require __DIR__ . '/includes/admin-footer.php'; ?>
