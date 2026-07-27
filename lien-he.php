<?php
/**
 * Contact page with message form and Google Maps embed.
 */
require_once __DIR__ . '/includes/functions.php';

setLanguage();

$errors = [];
$values = [
    'name'    => '',
    'email'   => '',
    'phone'   => '',
    'subject' => '',
    'message' => '',
];

if (!empty($_GET['course'])) {
    $course = dbOne('SELECT title_vi, title_en FROM courses WHERE slug = ?', [(string) $_GET['course']]);
    if ($course) {
        $values['subject'] = (currentLang() === 'en' ? 'Enrolment: ' : 'Đăng ký khóa học: ') . localized($course, 'title');
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($values as $key => $_) {
        $values[$key] = trim((string) ($_POST[$key] ?? ''));
    }

    if (!verifyCsrf()) {
        $errors[] = t('msg_csrf');
    }
    if ($values['name'] === '' || $values['email'] === '' || $values['message'] === '') {
        $errors[] = t('msg_required_fields');
    }
    if ($values['email'] !== '' && !filter_var($values['email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = t('msg_invalid_email');
    }

    if (!$errors) {
        dbInsert('contact_messages', [
            'name'    => $values['name'],
            'email'   => $values['email'],
            'phone'   => $values['phone'],
            'subject' => $values['subject'],
            'message' => $values['message'],
        ]);
        setFlash(t('msg_contact_sent'), 'success');
        redirect('lien-he.php');
    }
}

$mapUrl = GOOGLE_MAPS_EMBED_URL !== '' ? GOOGLE_MAPS_EMBED_URL : setting('google_map_embed_url');

$pageTitle       = t('contact_title');
$pageDescription = t('contact_subtitle');
$activeNav       = 'contact';

require __DIR__ . '/includes/header.php';
?>

<section class="page-hero">
  <div class="container">
    <nav class="breadcrumb" aria-label="Breadcrumb">
      <a href="<?= e(url('index.php')) ?>"><?= e(t('home')) ?></a><span>/</span><span><?= e(t('nav_contact')) ?></span>
    </nav>
    <h1><?= e(t('contact_title')) ?></h1>
    <p><?= e(t('contact_subtitle')) ?></p>
  </div>
</section>

<section class="section">
  <div class="container contact-layout">
    <div class="detail-card">
      <h2><?= e(t('btn_send')) ?></h2>

      <?php foreach ($errors as $error): ?>
        <div class="alert alert--error"><?= e($error) ?></div>
      <?php endforeach; ?>

      <form method="post" action="<?= e(url('lien-he.php')) ?>" novalidate>
        <?= csrfField() ?>
        <div class="form-row">
          <div class="form-group">
            <label for="name"><?= e(t('field_name')) ?> <span class="required">*</span></label>
            <input type="text" id="name" name="name" value="<?= e($values['name']) ?>" required>
          </div>
          <div class="form-group">
            <label for="email"><?= e(t('field_email')) ?> <span class="required">*</span></label>
            <input type="email" id="email" name="email" value="<?= e($values['email']) ?>" required>
          </div>
        </div>
        <div class="form-row">
          <div class="form-group">
            <label for="phone"><?= e(t('field_phone')) ?></label>
            <input type="tel" id="phone" name="phone" value="<?= e($values['phone']) ?>">
          </div>
          <div class="form-group">
            <label for="subject"><?= e(t('field_subject')) ?></label>
            <input type="text" id="subject" name="subject" value="<?= e($values['subject']) ?>">
          </div>
        </div>
        <div class="form-group">
          <label for="message"><?= e(t('field_message')) ?> <span class="required">*</span></label>
          <textarea id="message" name="message" required><?= e($values['message']) ?></textarea>
        </div>
        <button class="btn btn--primary btn--lg" type="submit"><?= e(t('btn_send')) ?></button>
      </form>
    </div>

    <aside>
      <div class="detail-card">
        <h2><?= e(t('contact_info')) ?></h2>
        <ul class="contact-info">
          <li><?= icon('pin') ?><span><strong><?= e(t('address')) ?></strong><span><?= e(setting('address')) ?></span></span></li>
          <li><?= icon('phone') ?><span><strong><?= e(t('phone')) ?></strong><a href="tel:<?= e(preg_replace('/\s+/', '', setting('phone'))) ?>"><?= e(setting('phone')) ?></a></span></li>
          <li><?= icon('mail') ?><span><strong><?= e(t('field_email')) ?></strong><a href="mailto:<?= e(setting('contact_email')) ?>"><?= e(setting('contact_email')) ?></a></span></li>
          <li><?= icon('clock') ?><span><strong><?= e(t('working_hours')) ?></strong><span><?= e(setting('working_hours')) ?></span></span></li>
        </ul>

        <div class="social">
          <?php if (setting('facebook_url')): ?><a class="social__link" style="background:var(--green-700)" href="<?= e(setting('facebook_url')) ?>" target="_blank" rel="noopener" aria-label="Facebook"><?= icon('facebook', 'icon icon--sm') ?></a><?php endif; ?>
          <?php if (setting('youtube_url')): ?><a class="social__link" style="background:var(--green-700)" href="<?= e(setting('youtube_url')) ?>" target="_blank" rel="noopener" aria-label="YouTube"><?= icon('youtube', 'icon icon--sm') ?></a><?php endif; ?>
          <?php if (setting('zalo_url')): ?><a class="social__link" style="background:var(--green-700)" href="<?= e(setting('zalo_url')) ?>" target="_blank" rel="noopener" aria-label="Zalo"><?= icon('zalo', 'icon icon--sm') ?></a><?php endif; ?>
        </div>
      </div>

      <?php if ($mapUrl): ?>
        <div class="map-embed">
          <iframe src="<?= e($mapUrl) ?>" title="Google Maps" loading="lazy" referrerpolicy="no-referrer-when-downgrade" allowfullscreen></iframe>
        </div>
      <?php endif; ?>
    </aside>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
