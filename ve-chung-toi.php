<?php
/**
 * About us page.
 */
require_once __DIR__ . '/includes/functions.php';

setLanguage();

$isEn        = currentLang() === 'en';
$instructors = dbAll('SELECT * FROM instructors WHERE is_active = 1 ORDER BY sort_order ASC LIMIT 3');
$levels      = dbAll('SELECT * FROM levels ORDER BY sort_order ASC');

$values = $isEn ? [
    ['icon' => 'music',   'title' => 'Music for everyone',  'text' => 'No age, no background and no budget should stand between a person and music.'],
    ['icon' => 'teacher', 'title' => 'Teach with patience', 'text' => 'Every learner progresses at their own pace and deserves encouragement, not pressure.'],
    ['icon' => 'star',    'title' => 'Quality first',       'text' => 'Our curriculum is built by conservatory-trained teachers and reviewed every year.'],
] : [
    ['icon' => 'music',   'title' => 'Âm nhạc cho mọi người', 'text' => 'Không tuổi tác, xuất phát điểm hay điều kiện tài chính nào nên ngăn cách một người với âm nhạc.'],
    ['icon' => 'teacher', 'title' => 'Dạy bằng sự kiên nhẫn', 'text' => 'Mỗi học viên tiến bộ theo nhịp riêng và xứng đáng được khích lệ thay vì áp lực.'],
    ['icon' => 'star',    'title' => 'Chất lượng là trên hết', 'text' => 'Chương trình học được xây dựng bởi giảng viên nhạc viện và rà soát hằng năm.'],
];

$pageTitle       = t('about_title');
$pageDescription = t('about_subtitle');
$activeNav       = 'about';

require __DIR__ . '/includes/header.php';
?>

<section class="page-hero">
  <div class="container">
    <nav class="breadcrumb" aria-label="Breadcrumb">
      <a href="<?= e(url('index.php')) ?>"><?= e(t('home')) ?></a><span>/</span><span><?= e(t('nav_about')) ?></span>
    </nav>
    <h1><?= e(t('about_title')) ?></h1>
    <p><?= e(t('about_subtitle')) ?></p>
  </div>
</section>

<section class="section">
  <div class="container">
    <div class="grid grid--2" style="align-items:center">
      <div class="reveal">
        <span class="section-head__eyebrow"><?= e(t('about_story')) ?></span>
        <h2><?= e($isEn ? 'A music club born from a small classroom' : 'Câu lạc bộ ra đời từ một lớp học nhỏ') ?></h2>
        <?php if ($isEn): ?>
          <p>MusicOfEveryone started in 2015 as a weekend class of nine children learning the recorder in a borrowed room. Today the club teaches thousands of students online and offline, from six-year-old beginners to adults producing their own tracks.</p>
          <p>We believe music education should be joyful, structured and accessible. That is why every programme is split into three clear levels, taught by conservatory-trained instructors and supported by a friendly community.</p>
        <?php else: ?>
          <p>MusicOfEveryone bắt đầu từ năm 2015 với một lớp học cuối tuần gồm chín em nhỏ học sáo recorder trong căn phòng đi mượn. Đến nay câu lạc bộ đã đồng hành cùng hàng nghìn học viên trực tuyến và trực tiếp, từ các bé sáu tuổi cho đến người lớn tự sản xuất bản nhạc của riêng mình.</p>
          <p>Chúng tôi tin rằng giáo dục âm nhạc cần vui vẻ, có hệ thống và dễ tiếp cận. Vì vậy mỗi chương trình được chia thành ba cấp độ rõ ràng, do giảng viên tốt nghiệp nhạc viện giảng dạy và được hỗ trợ bởi một cộng đồng thân thiện.</p>
        <?php endif; ?>
        <a class="btn btn--primary" href="<?= e(url('khoa-hoc.php')) ?>"><?= e(t('courses_view_all')) ?></a>
      </div>
      <div class="reveal">
        <img src="<?= e(asset('images/hero-characters.svg')) ?>" alt="<?= e(t('about_title')) ?>" style="width:100%;height:auto">
      </div>
    </div>
  </div>
</section>

<section class="section section--light">
  <div class="container">
    <div class="grid grid--2">
      <article class="value-card reveal">
        <div class="value-card__icon"><?= icon('star', 'icon icon--lg') ?></div>
        <h3><?= e(t('about_mission')) ?></h3>
        <p><?= e($isEn
            ? 'To make high-quality music education available to every learner in Vietnam, regardless of age or starting point.'
            : 'Mang giáo dục âm nhạc chất lượng cao đến với mọi người học tại Việt Nam, bất kể độ tuổi hay xuất phát điểm.') ?></p>
      </article>
      <article class="value-card reveal">
        <div class="value-card__icon"><?= icon('globe', 'icon icon--lg') ?></div>
        <h3><?= e(t('about_vision')) ?></h3>
        <p><?= e($isEn
            ? 'To become the most trusted online music club in Southeast Asia, where a million people find their own voice.'
            : 'Trở thành câu lạc bộ âm nhạc trực tuyến đáng tin cậy nhất Đông Nam Á, nơi một triệu người tìm thấy tiếng nói riêng của mình.') ?></p>
      </article>
    </div>

    <div class="section-head" style="margin-top:52px">
      <h2 class="section-head__title"><?= e(t('about_values')) ?></h2>
    </div>
    <div class="grid grid--3">
      <?php foreach ($values as $value): ?>
        <article class="value-card reveal">
          <div class="value-card__icon"><?= icon($value['icon'], 'icon icon--lg') ?></div>
          <h3><?= e($value['title']) ?></h3>
          <p><?= e($value['text']) ?></p>
        </article>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<section class="section">
  <div class="container">
    <div class="section-head">
      <h2 class="section-head__title"><?= e(t('levels_title')) ?></h2>
      <p class="section-head__text"><?= e(t('levels_subtitle')) ?></p>
    </div>
    <div class="grid grid--3">
      <?php foreach ($levels as $level): ?>
        <article class="level-card level-card--<?= e(levelColorClass($level['color'])) ?> reveal">
          <span class="level-card__age"><?= e($level['age_range']) ?></span>
          <h3><?= e(localized($level, 'name')) ?></h3>
          <p class="level-card__text"><?= e(localized($level, 'description')) ?></p>
        </article>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<section class="section section--light">
  <div class="container">
    <div class="section-head">
      <h2 class="section-head__title"><?= e(t('instructors_title')) ?></h2>
      <p class="section-head__text"><?= e(t('instructors_subtitle')) ?></p>
    </div>
    <div class="grid grid--3">
      <?php foreach ($instructors as $instructor): ?>
        <a class="instructor-card reveal" href="<?= e(url('giang-vien-chi-tiet.php?slug=' . urlencode($instructor['slug']))) ?>">
          <div class="instructor-card__photo">
            <img src="<?= e(imageUrl($instructor['photo'], 'assets/images/course-singing.svg')) ?>" alt="<?= e($instructor['name']) ?>" loading="lazy">
          </div>
          <div class="instructor-card__body">
            <h3 class="instructor-card__name"><?= e($instructor['name']) ?></h3>
            <p class="instructor-card__role"><?= e(localized($instructor, 'title')) ?></p>
          </div>
        </a>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
