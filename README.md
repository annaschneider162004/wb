# MusicOfEveryone — Music Club

A complete, production-ready **PHP + MySQL** website for an online music club.
Bilingual (Vietnamese / English), fully responsive, with a public front-end,
member accounts and a full admin panel.

![PHP](https://img.shields.io/badge/PHP-7.4%2B-777bb4)
![MySQL](https://img.shields.io/badge/MySQL-5.7%2B-4479a1)

---

## ✨ Features

**Public site**
- Homepage with hero, level badges, feature row, featured courses, levels, instructors, blog and CTA
- Courses listing with level filter, keyword search and pagination + course detail pages
- Instructors listing and detail pages (bio + their courses)
- Library / blog listing with search + article detail pages with related posts
- Community, About us and Contact pages (contact form stores messages in the DB + Google Maps embed)
- Bilingual VI/EN switching stored in the session
- SEO: per-page meta title/description, Open Graph tags, `sitemap.php`, `robots.txt`, clean URLs

**Members**
- Registration, login, logout
- Profile page: update name / email / phone / avatar and change password

**Admin panel** (`/admin`)
- Dashboard with counters and latest courses, posts, members and contact messages
- CRUD for courses, posts, instructors (with image upload, auto slugs, SEO fields)
- Inline CRUD for levels and homepage features
- Member management: activate/deactivate, promote/demote to admin, reset password, delete
- Site settings (name, tagline, contact info, map embed, social links) + contact message inbox

**Security**
- PDO prepared statements everywhere
- `htmlspecialchars` output escaping via `e()`
- CSRF tokens on every POST form
- `password_hash()` / `password_verify()`
- Upload validation (MIME type + size), PHP execution blocked in `/uploads`
- Admin guard on every admin page

---

## 📋 Requirements

- PHP **7.4+** (8.x recommended) with `pdo_mysql`, `fileinfo`, `mbstring`
- MySQL **5.7+** or MariaDB **10.2+**
- Apache with `mod_rewrite` (a `.htaccess` is provided) — works on any standard cPanel host

---

## 🚀 Installation

### 1. Upload the files
Upload the whole project into your `public_html` (or a sub-folder) via cPanel File Manager / FTP / git.

### 2. Create the database
In cPanel → *MySQL® Databases*, create a database and a user, and grant the user all privileges.

### 3. Import the schema
In cPanel → *phpMyAdmin*, select your database and import `database.sql`.
From the CLI you can also run:

```bash
mysql -u YOUR_USER -p YOUR_DATABASE < database.sql
```

### 4. Configure
Copy the sample config and fill in your credentials:

```bash
cp config.sample.php config.php
```

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'cpaneluser_musicofeveryone');
define('DB_USER', 'cpaneluser_music');
define('DB_PASS', 'your-strong-password');
define('SITE_URL', 'https://yourdomain.com');
```

Set `DEBUG_MODE` to `false` on production.

### 5. Permissions
Make the uploads folder writable:

```bash
chmod 755 uploads
```

### 6. (Optional) regenerate the demo passwords

```bash
php setup-passwords.php   # then delete the file
```

---

## 🔑 Default accounts

| Role  | Email                       | Password       |
|-------|-----------------------------|----------------|
| Admin | admin@musicofeveryone.com   | `Admin@123456` |
| User  | user@example.com            | `User@123456`  |

> **Change these immediately after installation.**

Admin panel: `https://yourdomain.com/admin`

---

## 📁 Project structure

```
.
├── index.php                    Homepage
├── khoa-hoc.php                 Courses listing
├── khoa-hoc-chi-tiet.php        Course detail
├── giang-vien.php               Instructors listing
├── giang-vien-chi-tiet.php      Instructor detail
├── thu-vien.php                 Library / blog listing
├── bai-viet-chi-tiet.php        Article detail
├── cong-dong.php                Community
├── ve-chung-toi.php             About us
├── lien-he.php                  Contact (form + map)
├── register.php / login.php / logout.php / profile.php
├── sitemap.php  robots.txt  .htaccess
├── config.php / config.sample.php
├── database.sql                 Schema + seed data
├── setup-passwords.php          Optional password regenerator
├── lang/                        vi.php, en.php
├── includes/                    db.php, functions.php, header.php, footer.php
├── assets/
│   ├── css/style.css
│   ├── js/main.js
│   └── images/                  SVG logo + character illustrations
├── uploads/                     User/admin uploaded images
└── admin/
    ├── index.php                Dashboard
    ├── login.php / logout.php
    ├── courses.php / course-edit.php
    ├── posts.php / post-edit.php
    ├── instructors.php / instructor-edit.php
    ├── levels.php / features.php / members.php / settings.php
    └── includes/                admin-header.php, admin-footer.php, auth-check.php
```

---

## 🗄️ Database schema

| Table              | Purpose                                             |
|--------------------|-----------------------------------------------------|
| `users`            | Members and administrators                          |
| `levels`           | Learning levels (Cấp 1 / 2 / 3) with age ranges      |
| `instructors`      | Teaching team                                       |
| `courses`          | Courses (bilingual, level + instructor relations)   |
| `posts`            | Library / blog articles                             |
| `features`         | Homepage feature row                                |
| `site_settings`    | Key/value site configuration                        |
| `contact_messages` | Submissions from the contact form                   |

---

## 🌐 Clean URLs

`.htaccess` maps friendly URLs to the PHP files:

| Pretty URL             | File                                  |
|------------------------|---------------------------------------|
| `/khoa-hoc`            | `khoa-hoc.php`                        |
| `/khoa-hoc/{slug}`     | `khoa-hoc-chi-tiet.php?slug={slug}`   |
| `/giang-vien`          | `giang-vien.php`                      |
| `/giang-vien/{slug}`   | `giang-vien-chi-tiet.php?slug={slug}` |
| `/thu-vien`            | `thu-vien.php`                        |
| `/bai-viet/{slug}`     | `bai-viet-chi-tiet.php?slug={slug}`   |
| `/cong-dong`           | `cong-dong.php`                       |
| `/ve-chung-toi`        | `ve-chung-toi.php`                    |
| `/lien-he`             | `lien-he.php`                         |
| `/dang-ky`             | `register.php`                        |
| `/dang-nhap`           | `login.php`                           |
| `/ho-so`               | `profile.php`                         |
| `/sitemap.xml`         | `sitemap.php`                         |

All internal links also work with the plain `.php` URLs, so the site runs fine
even without `mod_rewrite`.

---

## 🎨 Design system

| Token              | Value     |
|--------------------|-----------|
| Primary green      | `#1e7d4f` |
| Deep green         | `#2d5a3d` |
| Accent green       | `#4CAF50` |
| Level blue         | `#1e5c8b` |
| Level purple       | `#7b3f9e` |
| Background         | `#f0faf4` |
| Text               | `#1a1a2e` |
| Muted text         | `#666`    |

Fonts: **Poppins** (headings) and **Nunito** (body) from Google Fonts.
Character illustrations are hand-written SVGs in `assets/images/` — replace them
with your own artwork by keeping the same file names.

---

## 🌍 Translations

Strings live in `lang/vi.php` and `lang/en.php` as a flat key => value array.
Add a key to **both** files and use it in a template:

```php
<?= e(t('my_new_key')) ?>
```

Database content is bilingual through paired `*_vi` / `*_en` columns, resolved by
`localized($row, 'title')`, which falls back to the other language when a
translation is missing.

---

## 🔧 Local development

```bash
mysql -u root -p -e "CREATE DATABASE musicofeveryone CHARACTER SET utf8mb4"
mysql -u root -p musicofeveryone < database.sql
php -S localhost:8000
```

Then open <http://localhost:8000>.

---

## 📄 License

Released for the MusicOfEveryone project. Replace the demo content, imagery and
credentials before going live.
