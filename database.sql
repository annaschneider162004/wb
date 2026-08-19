-- =====================================================================
-- MusicOfEveryone (Music Club) - Database schema and seed data
-- MySQL 5.7+ / MariaDB 10.2+
--
-- Import via cPanel phpMyAdmin, or:
--   mysql -u USER -p DATABASE < database.sql
--
-- Default accounts (change immediately after install!):
--   Admin : admin@musicofeveryone.com / Admin@123456
--   User  : user@example.com         / User@123456
--
-- To regenerate password hashes:
--   php -r "echo password_hash('Admin@123456', PASSWORD_DEFAULT);"
-- or simply run setup-passwords.php once from the browser/CLI.
-- =====================================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS `contact_messages`;
DROP TABLE IF EXISTS `site_settings`;
DROP TABLE IF EXISTS `features`;
DROP TABLE IF EXISTS `posts`;
DROP TABLE IF EXISTS `courses`;
DROP TABLE IF EXISTS `instructors`;
DROP TABLE IF EXISTS `levels`;
DROP TABLE IF EXISTS `users`;

SET FOREIGN_KEY_CHECKS = 1;

-- ---------------------------------------------------------------------
-- Users
-- ---------------------------------------------------------------------
CREATE TABLE `users` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(255) NOT NULL,
  `email` VARCHAR(255) UNIQUE NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  `phone` VARCHAR(50) DEFAULT NULL,
  `is_admin` TINYINT(1) DEFAULT 0,
  `is_active` TINYINT(1) DEFAULT 1,
  `avatar` VARCHAR(255) DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- Levels (Cấp 1, Cấp 2, Cấp 3)
-- ---------------------------------------------------------------------
CREATE TABLE `levels` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name_vi` VARCHAR(100),
  `name_en` VARCHAR(100),
  `age_range` VARCHAR(50),
  `description_vi` TEXT,
  `description_en` TEXT,
  `color` VARCHAR(20) DEFAULT 'green',
  `sort_order` INT DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- Instructors
-- ---------------------------------------------------------------------
CREATE TABLE `instructors` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `slug` VARCHAR(255) UNIQUE,
  `name` VARCHAR(255),
  `title_vi` VARCHAR(255),
  `title_en` VARCHAR(255),
  `bio_vi` TEXT,
  `bio_en` TEXT,
  `photo` VARCHAR(255),
  `email` VARCHAR(255),
  `is_active` TINYINT(1) DEFAULT 1,
  `sort_order` INT DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- Courses
-- ---------------------------------------------------------------------
CREATE TABLE `courses` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `slug` VARCHAR(255) UNIQUE,
  `title_vi` VARCHAR(255),
  `title_en` VARCHAR(255),
  `subtitle_vi` VARCHAR(255),
  `subtitle_en` VARCHAR(255),
  `description_vi` TEXT,
  `description_en` TEXT,
  `level_id` INT DEFAULT NULL,
  `instructor_id` INT DEFAULT NULL,
  `thumbnail` VARCHAR(255),
  `price` DECIMAL(10,2) DEFAULT 0,
  `duration` VARCHAR(100),
  `is_featured` TINYINT(1) DEFAULT 0,
  `is_active` TINYINT(1) DEFAULT 1,
  `meta_title` VARCHAR(255),
  `meta_description` TEXT,
  `sort_order` INT DEFAULT 0,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT `fk_courses_level` FOREIGN KEY (`level_id`) REFERENCES `levels`(`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_courses_instructor` FOREIGN KEY (`instructor_id`) REFERENCES `instructors`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- Posts / Articles (Thư viện)
-- ---------------------------------------------------------------------
CREATE TABLE `posts` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `slug` VARCHAR(255) UNIQUE,
  `title_vi` VARCHAR(255),
  `title_en` VARCHAR(255),
  `excerpt_vi` TEXT,
  `excerpt_en` TEXT,
  `content_vi` LONGTEXT,
  `content_en` LONGTEXT,
  `thumbnail` VARCHAR(255),
  `author_id` INT DEFAULT NULL,
  `is_published` TINYINT(1) DEFAULT 1,
  `meta_title` VARCHAR(255),
  `meta_description` TEXT,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT `fk_posts_author` FOREIGN KEY (`author_id`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- Features (homepage icon row)
-- ---------------------------------------------------------------------
CREATE TABLE `features` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `icon` VARCHAR(100),
  `title_vi` VARCHAR(255),
  `title_en` VARCHAR(255),
  `description_vi` TEXT,
  `description_en` TEXT,
  `sort_order` INT DEFAULT 0,
  `is_active` TINYINT(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- Site settings
-- ---------------------------------------------------------------------
CREATE TABLE `site_settings` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `setting_key` VARCHAR(100) UNIQUE,
  `setting_value` TEXT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- Contact messages (Liên hệ form)
-- ---------------------------------------------------------------------
CREATE TABLE `contact_messages` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(255),
  `email` VARCHAR(255),
  `phone` VARCHAR(50),
  `subject` VARCHAR(255),
  `message` TEXT,
  `is_read` TINYINT(1) DEFAULT 0,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================================
-- SEED DATA
-- =====================================================================

-- Users -- admin password: Admin@123456 | user password: User@123456
INSERT INTO `users` (`name`, `email`, `password`, `is_admin`, `is_active`) VALUES
('Administrator', 'admin@musicofeveryone.com', '$2y$10$aKSNBeVyurt9jMSw9caC6.wiTvpU2SfAfDFZ1eS3TlYcxqyf5U5EG', 1, 1),
('Test User', 'user@example.com', '$2y$10$gP1LaTIsaOBKyVXNNmpLDugVfdDRvcKdpQzdRjTzRsB0Udl7LBpo.', 0, 1);

-- Levels
INSERT INTO `levels` (`name_vi`, `name_en`, `age_range`, `description_vi`, `description_en`, `color`, `sort_order`) VALUES
('Cấp 1', 'Level 1', '6-10 tuổi', 'Làm quen với âm nhạc qua trò chơi, tiết tấu và những bài hát vui nhộn. Bé phát triển cảm thụ âm nhạc tự nhiên.', 'Getting to know music through games, rhythm and fun songs. Children develop natural musicality.', 'green', 1),
('Cấp 2', 'Level 2', '11-15 tuổi', 'Học nhạc lý cơ bản, luyện tập nhạc cụ chuyên sâu và biểu diễn nhóm.', 'Learn basic music theory, focused instrument practice and group performance.', 'blue', 2),
('Cấp 3', 'Level 3', '16+ tuổi', 'Nâng cao kỹ thuật, sáng tác, thu âm và sản xuất âm nhạc chuyên nghiệp.', 'Advanced technique, composition, recording and professional music production.', 'purple', 3);

-- Instructors
INSERT INTO `instructors` (`slug`, `name`, `title_vi`, `title_en`, `bio_vi`, `bio_en`, `photo`, `email`, `is_active`, `sort_order`) VALUES
('nguyen-minh-anh', 'Nguyễn Minh Anh', 'Giảng viên Thanh nhạc', 'Vocal Instructor', 'Tốt nghiệp Học viện Âm nhạc Quốc gia Việt Nam chuyên ngành Thanh nhạc, cô Minh Anh có hơn 10 năm kinh nghiệm giảng dạy cho học viên từ 6 đến 40 tuổi. Cô theo đuổi phương pháp luyện thanh tự nhiên, giúp học viên tìm được chất giọng riêng.', 'A graduate of the Vietnam National Academy of Music majoring in Vocal Performance, Ms. Minh Anh has over 10 years of teaching experience with students aged 6 to 40. She follows a natural voice-training method that helps every student find their own tone.', 'assets/images/course-singing.svg', 'minhanh@musicofeveryone.com', 1, 1),
('tran-quoc-bao', 'Trần Quốc Bảo', 'Giảng viên Piano & Keyboard', 'Piano & Keyboard Instructor', 'Thầy Quốc Bảo là nghệ sĩ piano biểu diễn, từng đạt giải tại nhiều cuộc thi piano trong nước. Thầy chuyên đào tạo piano cổ điển và pop, đồng thời hướng dẫn học viên đệm hát và ứng tấu.', 'Mr. Quoc Bao is a performing pianist and a prize winner at several national piano competitions. He specialises in classical and pop piano, and also coaches accompaniment and improvisation.', 'assets/images/course-piano.svg', 'quocbao@musicofeveryone.com', 1, 2),
('le-thuy-linh', 'Lê Thùy Linh', 'Giảng viên Violin', 'Violin Instructor', 'Cô Thùy Linh có 8 năm kinh nghiệm giảng dạy violin theo phương pháp Suzuki, giúp học viên nhỏ tuổi làm quen với đàn dây một cách nhẹ nhàng và giàu cảm hứng.', 'Ms. Thuy Linh has 8 years of experience teaching violin with the Suzuki method, gently and inspiringly introducing young learners to string instruments.', 'assets/images/course-violin.svg', 'thuylinh@musicofeveryone.com', 1, 3);

-- Courses
INSERT INTO `courses` (`slug`, `title_vi`, `title_en`, `subtitle_vi`, `subtitle_en`, `description_vi`, `description_en`, `level_id`, `instructor_id`, `thumbnail`, `price`, `duration`, `is_featured`, `is_active`, `meta_title`, `meta_description`, `sort_order`) VALUES
('guitar-co-ban', 'Guitar cơ bản', 'Basic Guitar', 'Khởi đầu cùng cây đàn guitar', 'Start your guitar journey', 'Khóa học Guitar cơ bản giúp bạn làm quen với cây đàn từ những hợp âm đầu tiên. Học viên được hướng dẫn tư thế cầm đàn, kỹ thuật quạt chả, móc dây và đệm hát các ca khúc quen thuộc. Sau khóa học, bạn có thể tự tin đệm hát trước bạn bè.', 'The Basic Guitar course introduces you to the instrument starting from your very first chords. Students learn posture, strumming, fingerpicking and how to accompany familiar songs. By the end you can confidently play for friends.', 1, 2, 'assets/images/course-guitar.svg', 1200000.00, '12 buổi', 1, 1, 'Khóa học Guitar cơ bản | MusicOfEveryone', 'Học guitar cơ bản từ con số 0 với giáo viên chuyên nghiệp tại MusicOfEveryone.', 1),
('thanh-nhac-can-ban', 'Thanh nhạc căn bản', 'Vocal Foundation', 'Tìm giọng hát của riêng bạn', 'Find your own voice', 'Khóa Thanh nhạc căn bản tập trung vào hơi thở, khẩu hình và kỹ thuật phát âm. Học viên luyện tập các bài khởi động giọng, mở rộng quãng và xử lý ca khúc theo phong cách riêng.', 'The Vocal Foundation course focuses on breathing, mouth shape and articulation. Students practise warm-ups, extend their range and learn to interpret songs in their own style.', 1, 1, 'assets/images/course-singing.svg', 1500000.00, '12 buổi', 1, 1, 'Khóa học Thanh nhạc căn bản | MusicOfEveryone', 'Luyện thanh, mở rộng quãng giọng và xử lý ca khúc cùng giảng viên thanh nhạc chuyên nghiệp.', 2),
('piano-cho-nguoi-moi', 'Piano cho người mới', 'Piano for Beginners', 'Những phím đàn đầu tiên', 'Your first keys', 'Khóa Piano cho người mới hướng dẫn cách đọc bản nhạc, đặt tay đúng và chơi những giai điệu đầu tiên. Chương trình kết hợp nhạc cổ điển và các bản nhạc hiện đại quen thuộc.', 'Piano for Beginners teaches score reading, correct hand position and your first melodies. The programme blends classical repertoire with familiar modern pieces.', 1, 2, 'assets/images/course-piano.svg', 1800000.00, '16 buổi', 1, 1, 'Khóa học Piano cho người mới | MusicOfEveryone', 'Học piano từ cơ bản: đọc nốt, đặt tay, chơi bản nhạc đầu tiên.', 3),
('violin-nhap-mon', 'Violin nhập môn', 'Violin Introduction', 'Tiếng đàn dây đầu tiên', 'Your first strings', 'Khóa Violin nhập môn theo phương pháp Suzuki, phù hợp cho học viên nhỏ tuổi và người lớn mới bắt đầu. Học viên học cách cầm vĩ, lấy âm chuẩn và chơi các giai điệu ngắn.', 'The Violin Introduction course follows the Suzuki method and suits both young learners and adult beginners. Students learn bow hold, intonation and short melodies.', 2, 3, 'assets/images/course-violin.svg', 2000000.00, '16 buổi', 1, 1, 'Khóa học Violin nhập môn | MusicOfEveryone', 'Violin nhập môn theo phương pháp Suzuki cho trẻ em và người lớn.', 4),
('sao-recorder-cho-be', 'Sáo recorder cho bé', 'Recorder for Kids', 'Nhạc cụ đầu đời của bé', 'A child''s first instrument', 'Sáo recorder là nhạc cụ lý tưởng để bé làm quen với âm nhạc. Khóa học kết hợp trò chơi tiết tấu, tập thổi và biểu diễn nhóm giúp bé tự tin hơn mỗi ngày.', 'The recorder is an ideal first instrument. This course combines rhythm games, breathing practice and group performance so children gain confidence every day.', 1, 3, 'assets/images/course-recorder.svg', 900000.00, '10 buổi', 1, 1, 'Khóa học Sáo recorder cho bé | MusicOfEveryone', 'Khóa sáo recorder vui nhộn dành cho trẻ 6-10 tuổi.', 5),
('san-xuat-am-nhac', 'Sản xuất âm nhạc', 'Music Production', 'Từ ý tưởng đến bản thu', 'From idea to master', 'Khóa Sản xuất âm nhạc hướng dẫn sử dụng keyboard MIDI và phần mềm DAW để sáng tác, phối khí, thu âm và mix bản nhạc hoàn chỉnh của riêng bạn.', 'The Music Production course covers MIDI keyboards and DAW software for composing, arranging, recording and mixing your own finished track.', 3, 2, 'assets/images/course-keyboard.svg', 3500000.00, '20 buổi', 1, 1, 'Khóa học Sản xuất âm nhạc | MusicOfEveryone', 'Học sáng tác, phối khí, thu âm và mix nhạc với DAW chuyên nghiệp.', 6);

-- Posts
INSERT INTO `posts` (`slug`, `title_vi`, `title_en`, `excerpt_vi`, `excerpt_en`, `content_vi`, `content_en`, `thumbnail`, `author_id`, `is_published`, `meta_title`, `meta_description`) VALUES
('5-loi-ich-khi-tre-hoc-nhac-som', '5 lợi ích khi trẻ học nhạc sớm', '5 benefits of learning music early', 'Âm nhạc không chỉ là năng khiếu — đó còn là công cụ phát triển trí não, cảm xúc và kỹ năng xã hội cho trẻ.', 'Music is more than a talent — it is a tool that develops a child''s brain, emotions and social skills.', '<p>Nhiều nghiên cứu cho thấy trẻ được tiếp xúc với âm nhạc từ sớm có khả năng ghi nhớ và tập trung tốt hơn bạn cùng lứa.</p><h3>1. Phát triển trí não</h3><p>Việc đọc bản nhạc và phối hợp hai tay kích thích cả hai bán cầu não cùng lúc.</p><h3>2. Rèn tính kỷ luật</h3><p>Luyện tập mỗi ngày dạy trẻ sự kiên trì và thói quen làm việc đều đặn.</p><h3>3. Tăng sự tự tin</h3><p>Mỗi lần biểu diễn là một lần trẻ vượt qua giới hạn của chính mình.</p><h3>4. Phát triển cảm xúc</h3><p>Âm nhạc giúp trẻ gọi tên và biểu đạt cảm xúc một cách lành mạnh.</p><h3>5. Kỹ năng xã hội</h3><p>Chơi nhạc nhóm dạy trẻ cách lắng nghe và phối hợp với người khác.</p>', '<p>Research shows that children exposed to music early have better memory and concentration than their peers.</p><h3>1. Brain development</h3><p>Reading scores and coordinating both hands stimulates both hemispheres at once.</p><h3>2. Discipline</h3><p>Daily practice teaches persistence and steady working habits.</p><h3>3. Confidence</h3><p>Every performance is a chance to go beyond your own limits.</p><h3>4. Emotional growth</h3><p>Music helps children name and express feelings in a healthy way.</p><h3>5. Social skills</h3><p>Ensemble playing teaches listening and cooperation.</p>', 'assets/images/course-recorder.svg', 1, 1, '5 lợi ích khi trẻ học nhạc sớm | MusicOfEveryone', 'Khám phá 5 lợi ích khoa học của việc cho trẻ học nhạc từ sớm.'),
('chon-nhac-cu-dau-tien', 'Cách chọn nhạc cụ đầu tiên phù hợp', 'How to choose your first instrument', 'Guitar, piano hay violin? Bài viết giúp bạn chọn nhạc cụ phù hợp với độ tuổi, thể chất và sở thích.', 'Guitar, piano or violin? This article helps you pick the instrument that matches your age, physique and taste.', '<p>Chọn đúng nhạc cụ đầu tiên quyết định rất lớn đến việc bạn có gắn bó lâu dài với âm nhạc hay không.</p><h3>Xét theo độ tuổi</h3><p>Trẻ 6-10 tuổi phù hợp với sáo recorder, keyboard hoặc violin cỡ nhỏ. Từ 11 tuổi trở lên có thể bắt đầu guitar hoặc piano.</p><h3>Xét theo sở thích âm nhạc</h3><p>Nếu bạn yêu nhạc pop và muốn đệm hát, guitar là lựa chọn nhanh cho kết quả. Nếu thích nhạc cổ điển, piano và violin là nền tảng vững chắc.</p><h3>Xét theo điều kiện luyện tập</h3><p>Piano cần không gian, guitar và sáo dễ mang theo. Hãy chọn nhạc cụ bạn có thể chạm vào mỗi ngày.</p>', '<p>Choosing the right first instrument largely determines whether you stay with music for the long term.</p><h3>By age</h3><p>Children aged 6-10 suit the recorder, keyboard or a small-size violin. From age 11, guitar or piano works well.</p><h3>By musical taste</h3><p>If you love pop and want to accompany singing, the guitar delivers fast results. If you prefer classical, piano and violin are solid foundations.</p><h3>By practice conditions</h3><p>Piano needs space; guitar and recorder are portable. Pick the instrument you can touch every day.</p>', 'assets/images/course-guitar.svg', 1, 1, 'Cách chọn nhạc cụ đầu tiên | MusicOfEveryone', 'Hướng dẫn chọn nhạc cụ đầu tiên phù hợp với độ tuổi và sở thích.'),
('luyen-tap-hieu-qua-30-phut', 'Luyện tập hiệu quả chỉ với 30 phút mỗi ngày', 'Effective practice in just 30 minutes a day', 'Không cần tập hàng giờ — điều quan trọng là tập đúng cách. Đây là khung luyện tập 30 phút cho người bận rộn.', 'You don''t need hours — you need the right method. Here is a 30-minute routine for busy learners.', '<p>Chất lượng luyện tập quan trọng hơn thời lượng. Dưới đây là khung 30 phút được nhiều giảng viên áp dụng.</p><h3>5 phút khởi động</h3><p>Chạy gam, luyện ngón hoặc khởi động giọng để làm nóng cơ.</p><h3>15 phút xử lý đoạn khó</h3><p>Chọn 1-2 ô nhịp khó nhất, tập chậm với máy đếm nhịp rồi tăng tốc dần.</p><h3>7 phút ghép bài</h3><p>Chơi trọn vẹn tác phẩm để giữ mạch cảm xúc.</p><h3>3 phút ghi chú</h3><p>Ghi lại điều bạn làm được và điều cần sửa cho buổi sau.</p>', '<p>Practice quality matters more than duration. Here is a 30-minute framework used by many teachers.</p><h3>5 minutes warm-up</h3><p>Scales, finger exercises or vocal warm-ups to loosen the muscles.</p><h3>15 minutes on hard passages</h3><p>Pick the one or two hardest bars, play slowly with a metronome, then speed up gradually.</p><h3>7 minutes full run</h3><p>Play the whole piece to keep the musical flow.</p><h3>3 minutes journaling</h3><p>Write down what worked and what to fix next session.</p>', 'assets/images/course-piano.svg', 1, 1, 'Luyện tập hiệu quả 30 phút mỗi ngày | MusicOfEveryone', 'Khung luyện tập âm nhạc 30 phút mỗi ngày dành cho người bận rộn.');

-- Features
INSERT INTO `features` (`icon`, `title_vi`, `title_en`, `description_vi`, `description_en`, `sort_order`, `is_active`) VALUES
('route', 'Lộ trình cá nhân hóa', 'Personalised path', 'Chương trình học được thiết kế riêng theo độ tuổi và mục tiêu của từng học viên.', 'A curriculum designed around each student''s age and goals.', 1, 1),
('laptop', 'Học online linh hoạt', 'Flexible online learning', 'Học mọi lúc mọi nơi với lớp trực tuyến và kho bài giảng video.', 'Learn anytime, anywhere with live online classes and a video library.', 2, 1),
('teacher', 'Giáo viên chất lượng', 'Qualified teachers', 'Đội ngũ giảng viên tốt nghiệp nhạc viện, giàu kinh nghiệm sư phạm.', 'Conservatory-trained instructors with strong teaching experience.', 3, 1),
('library', 'Nội dung đa dạng', 'Diverse content', 'Từ thanh nhạc, nhạc cụ đến sản xuất âm nhạc hiện đại.', 'From vocals and instruments to modern music production.', 4, 1),
('chart', 'Theo dõi tiến độ', 'Progress tracking', 'Báo cáo tiến bộ chi tiết sau mỗi chặng học tập.', 'Detailed progress reports after every learning milestone.', 5, 1);

-- Site settings
INSERT INTO `site_settings` (`setting_key`, `setting_value`) VALUES
('site_name', 'MusicOfEveryone'),
('site_tagline_vi', 'Âm nhạc cho mọi người'),
('site_tagline_en', 'Music for everyone'),
('contact_email', 'lienhe@musicofeveryone.com'),
('phone', '0900 123 456'),
('address', '123 Đường Nguyễn Huệ, Quận 1, TP. Hồ Chí Minh'),
('google_map_embed_url', 'https://www.google.com/maps?q=Nguyen+Hue+District+1+Ho+Chi+Minh&output=embed'),
('facebook_url', 'https://facebook.com/'),
('youtube_url', 'https://youtube.com/'),
('zalo_url', 'https://zalo.me/'),
('working_hours', '08:00 - 21:00 (T2 - CN)');
