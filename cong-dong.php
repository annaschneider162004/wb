<?php
/**
 * Community page.
 */
require_once __DIR__ . '/includes/functions.php';

setLanguage();

$isEn = currentLang() === 'en';

$blocks = $isEn ? [
    ['icon' => 'teacher',  'title' => 'Weekly practice groups',  'text' => 'Join a small online group every week, practise together and get feedback from a mentor.'],
    ['icon' => 'music',    'title' => 'Monthly mini concerts',   'text' => 'Every month our members perform live on stage or online. Everyone is welcome, whatever the level.'],
    ['icon' => 'library',  'title' => 'Sheet music exchange',    'text' => 'Members share arrangements, backing tracks and practice sheets in our shared library.'],
    ['icon' => 'chart',    'title' => '30-day challenges',       'text' => 'Practise every day for 30 days with the club and track your streak together with friends.'],
] : [
    ['icon' => 'teacher',  'title' => 'Nhóm luyện tập hàng tuần', 'text' => 'Tham gia nhóm nhỏ trực tuyến mỗi tuần, cùng luyện tập và nhận nhận xét từ người hướng dẫn.'],
    ['icon' => 'music',    'title' => 'Mini concert hàng tháng',  'text' => 'Mỗi tháng học viên được biểu diễn trên sân khấu hoặc trực tuyến. Mọi trình độ đều được chào đón.'],
    ['icon' => 'library',  'title' => 'Kho bản nhạc chia sẻ',     'text' => 'Thành viên chia sẻ bản phối, beat đệm và tài liệu luyện tập trong thư viện chung.'],
    ['icon' => 'chart',    'title' => 'Thử thách 30 ngày',        'text' => 'Luyện tập mỗi ngày trong 30 ngày cùng câu lạc bộ và theo dõi chuỗi ngày cùng bạn bè.'],
];

$testimonials = $isEn ? [
    ['name' => 'Mai Anh', 'role' => 'Student, Level 2', 'quote' => 'I used to be shy about singing. After six months at the club I performed solo at our mini concert.'],
    ['name' => 'Hoàng Nam', 'role' => 'Parent', 'quote' => 'My son practises the recorder every day without being asked. The teachers make every lesson fun.'],
    ['name' => 'Thu Hà', 'role' => 'Student, Level 3', 'quote' => 'The production course helped me release my first original track on streaming platforms.'],
] : [
    ['name' => 'Mai Anh', 'role' => 'Học viên Cấp 2', 'quote' => 'Trước đây mình rất ngại hát. Sau sáu tháng ở câu lạc bộ, mình đã hát solo trong mini concert.'],
    ['name' => 'Hoàng Nam', 'role' => 'Phụ huynh', 'quote' => 'Con trai tôi tự giác tập sáo mỗi ngày. Các thầy cô làm cho mỗi buổi học đều thật vui.'],
    ['name' => 'Thu Hà', 'role' => 'Học viên Cấp 3', 'quote' => 'Khóa sản xuất âm nhạc giúp mình phát hành ca khúc đầu tay trên các nền tảng nghe nhạc.'],
];

$pageTitle       = t('community_title');
$pageDescription = t('community_subtitle');
$activeNav       = 'community';

require __DIR__ . '/includes/header.php';
?>

<section class="page-hero">
  <div class="container">
    <nav class="breadcrumb" aria-label="Breadcrumb">
      <a href="<?= e(url('index.php')) ?>"><?= e(t('home')) ?></a><span>/</span><span><?= e(t('nav_community')) ?></span>
    </nav>
    <h1><?= e(t('community_title')) ?></h1>
    <p><?= e(t('community_subtitle')) ?></p>
  </div>
</section>

<section class="section">
  <div class="container">
    <div class="grid grid--4">
      <?php foreach ($blocks as $block): ?>
        <article class="community-card reveal">
          <div class="community-card__icon"><?= icon($block['icon'], 'icon icon--lg') ?></div>
          <h3><?= e($block['title']) ?></h3>
          <p style="color:var(--muted);margin:0"><?= e($block['text']) ?></p>
        </article>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<section class="section section--light">
  <div class="container">
    <div class="section-head">
      <span class="section-head__eyebrow"><?= e($isEn ? 'Member stories' : 'Câu chuyện học viên') ?></span>
      <h2 class="section-head__title"><?= e($isEn ? 'What our members say' : 'Học viên nói gì về chúng tôi') ?></h2>
    </div>

    <div class="grid grid--3">
      <?php foreach ($testimonials as $item): ?>
        <article class="testimonial reveal">
          <p class="testimonial__quote">“<?= e($item['quote']) ?>”</p>
          <div class="testimonial__author">
            <span class="testimonial__avatar"><?= e(mb_substr($item['name'], 0, 1, 'UTF-8')) ?></span>
            <span>
              <span class="testimonial__name"><?= e($item['name']) ?></span><br>
              <span class="testimonial__role"><?= e($item['role']) ?></span>
            </span>
          </div>
        </article>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<section class="section">
  <div class="container">
    <div class="cta reveal">
      <h2><?= e($isEn ? 'Become a club member' : 'Trở thành thành viên câu lạc bộ') ?></h2>
      <p><?= e(t('cta_subtext')) ?></p>
      <a class="btn btn--white btn--lg" href="<?= e(url('register.php')) ?>"><?= e(t('nav_register')) ?></a>
    </div>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
