</main>

<footer class="site-footer">
  <div class="container site-footer__grid">
    <div class="site-footer__col">
      <a class="brand brand--footer" href="<?= e(url('index.php')) ?>">
        <img class="brand__logo" src="<?= e(asset('images/logo.svg')) ?>" alt="<?= e(setting('site_name', SITE_NAME)) ?>" width="46" height="46">
        <span class="brand__text">
          <span class="brand__name"><?= e(setting('site_name', SITE_NAME)) ?></span>
          <span class="brand__tagline"><?= e(t('brand_tagline')) ?></span>
        </span>
      </a>
      <p class="site-footer__about"><?= e(t('footer_about')) ?></p>
      <div class="social">
        <?php if (setting('facebook_url')): ?>
          <a class="social__link" href="<?= e(setting('facebook_url')) ?>" target="_blank" rel="noopener" aria-label="Facebook"><?= icon('facebook', 'icon icon--sm') ?></a>
        <?php endif; ?>
        <?php if (setting('youtube_url')): ?>
          <a class="social__link" href="<?= e(setting('youtube_url')) ?>" target="_blank" rel="noopener" aria-label="YouTube"><?= icon('youtube', 'icon icon--sm') ?></a>
        <?php endif; ?>
        <?php if (setting('zalo_url')): ?>
          <a class="social__link" href="<?= e(setting('zalo_url')) ?>" target="_blank" rel="noopener" aria-label="Zalo"><?= icon('zalo', 'icon icon--sm') ?></a>
        <?php endif; ?>
      </div>
    </div>

    <div class="site-footer__col">
      <h3 class="site-footer__title"><?= e(t('footer_links')) ?></h3>
      <ul class="site-footer__list">
        <li><a href="<?= e(url('index.php')) ?>"><?= e(t('nav_home')) ?></a></li>
        <li><a href="<?= e(url('khoa-hoc.php')) ?>"><?= e(t('nav_courses')) ?></a></li>
        <li><a href="<?= e(url('giang-vien.php')) ?>"><?= e(t('nav_instructors')) ?></a></li>
        <li><a href="<?= e(url('thu-vien.php')) ?>"><?= e(t('nav_library')) ?></a></li>
        <li><a href="<?= e(url('cong-dong.php')) ?>"><?= e(t('nav_community')) ?></a></li>
        <li><a href="<?= e(url('ve-chung-toi.php')) ?>"><?= e(t('nav_about')) ?></a></li>
        <li><a href="<?= e(url('lien-he.php')) ?>"><?= e(t('nav_contact')) ?></a></li>
      </ul>
    </div>

    <div class="site-footer__col">
      <h3 class="site-footer__title"><?= e(t('footer_courses')) ?></h3>
      <ul class="site-footer__list">
        <?php
        $footerCourses = [];
        try {
            $footerCourses = dbAll('SELECT slug, title_vi, title_en FROM courses WHERE is_active = 1 ORDER BY sort_order ASC LIMIT 6');
        } catch (Throwable $e) {
            $footerCourses = [];
        }
        ?>
        <?php foreach ($footerCourses as $fc): ?>
          <li><a href="<?= e(url('khoa-hoc-chi-tiet.php?slug=' . urlencode($fc['slug']))) ?>"><?= e(localized($fc, 'title')) ?></a></li>
        <?php endforeach; ?>
      </ul>
    </div>

    <div class="site-footer__col">
      <h3 class="site-footer__title"><?= e(t('footer_contact')) ?></h3>
      <ul class="site-footer__list site-footer__list--contact">
        <li><?= icon('pin', 'icon icon--sm') ?><span><?= e(setting('address')) ?></span></li>
        <li><?= icon('phone', 'icon icon--sm') ?><a href="tel:<?= e(preg_replace('/\s+/', '', setting('phone'))) ?>"><?= e(setting('phone')) ?></a></li>
        <li><?= icon('mail', 'icon icon--sm') ?><a href="mailto:<?= e(setting('contact_email')) ?>"><?= e(setting('contact_email')) ?></a></li>
        <li><?= icon('clock', 'icon icon--sm') ?><span><?= e(setting('working_hours')) ?></span></li>
      </ul>
    </div>
  </div>

  <div class="site-footer__bottom">
    <div class="container">
      <p>&copy; <?= date('Y') ?> <?= e(t('footer_copyright')) ?></p>
    </div>
  </div>
</footer>

<button class="to-top" type="button" aria-label="Top" data-to-top><?= icon('arrow', 'icon icon--sm') ?></button>

<script src="<?= e(asset('js/main.js')) ?>" defer></script>
</body>
</html>
