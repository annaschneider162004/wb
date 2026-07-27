<?php
/**
 * Shared helper functions for MusicOfEveryone.
 */

require_once __DIR__ . '/db.php';

// ---------------------------------------------------------------------
// Session
// ---------------------------------------------------------------------

/**
 * Start the application session (idempotent).
 */
function startSession(): void
{
    if (session_status() === PHP_SESSION_ACTIVE) {
        return;
    }
    if (defined('SESSION_NAME')) {
        session_name(SESSION_NAME);
    }
    session_set_cookie_params([
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
}

// ---------------------------------------------------------------------
// URLs
// ---------------------------------------------------------------------

/**
 * Base path of the application relative to the document root.
 */
function basePath(): string
{
    static $base = null;
    if ($base !== null) {
        return $base;
    }

    $appRoot  = str_replace('\\', '/', realpath(dirname(__DIR__)) ?: dirname(__DIR__));
    $docRoot  = isset($_SERVER['DOCUMENT_ROOT']) ? realpath($_SERVER['DOCUMENT_ROOT']) : false;
    $docRoot  = $docRoot ? str_replace('\\', '/', $docRoot) : '';

    if ($docRoot !== '' && strpos($appRoot, $docRoot) === 0) {
        $base = rtrim(substr($appRoot, strlen($docRoot)), '/');
    } else {
        $base = '';
    }

    return $base;
}

/**
 * Build an application URL.
 */
function url(string $path = ''): string
{
    return basePath() . '/' . ltrim($path, '/');
}

/**
 * Build an absolute URL (used for sitemap / canonical tags).
 */
function absoluteUrl(string $path = ''): string
{
    $siteUrl = defined('SITE_URL') ? rtrim(SITE_URL, '/') : '';
    if ($siteUrl === '' || $siteUrl === 'http://localhost') {
        $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $host   = $_SERVER['HTTP_HOST'] ?? 'localhost';
        $siteUrl = $scheme . '://' . $host . basePath();
        return rtrim($siteUrl, '/') . '/' . ltrim($path, '/');
    }
    return $siteUrl . '/' . ltrim($path, '/');
}

/**
 * URL of an asset inside /assets.
 */
function asset(string $path): string
{
    return url('assets/' . ltrim($path, '/'));
}

/**
 * URL of an image stored either in /assets/images or /uploads.
 */
function imageUrl(?string $path, string $fallback = 'assets/images/course-guitar.svg'): string
{
    $path = trim((string) $path);
    if ($path === '') {
        return url($fallback);
    }
    if (preg_match('#^https?://#i', $path)) {
        return $path;
    }
    if (strpos($path, 'assets/') === 0 || strpos($path, 'uploads/') === 0) {
        return url($path);
    }
    return url('uploads/' . ltrim($path, '/'));
}

/**
 * Redirect and stop execution.
 */
function redirect(string $path): void
{
    if (!preg_match('#^https?://#i', $path)) {
        $path = url($path);
    }
    header('Location: ' . $path);
    exit;
}

// ---------------------------------------------------------------------
// Output escaping
// ---------------------------------------------------------------------

/**
 * Escape a value for HTML output.
 */
function e($value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/**
 * Allow a small, safe subset of HTML (used for post content from admin).
 */
function safeHtml(?string $html): string
{
    $allowed = '<p><br><b><strong><i><em><u><ul><ol><li><h2><h3><h4><blockquote><a><img><figure><figcaption><hr><table><thead><tbody><tr><th><td><iframe>';
    return strip_tags((string) $html, $allowed);
}

/**
 * Truncate plain text to a given length.
 */
function excerpt(?string $text, int $length = 140): string
{
    $text = trim(preg_replace('/\s+/u', ' ', strip_tags((string) $text)));
    if (function_exists('mb_strlen') && mb_strlen($text, 'UTF-8') > $length) {
        return mb_substr($text, 0, $length, 'UTF-8') . '…';
    }
    if (!function_exists('mb_strlen') && strlen($text) > $length) {
        return substr($text, 0, $length) . '…';
    }
    return $text;
}

// ---------------------------------------------------------------------
// Language
// ---------------------------------------------------------------------

/**
 * Determine and store the active language.
 */
function setLanguage(): string
{
    startSession();

    $available = ['vi', 'en'];

    if (isset($_GET['lang']) && in_array($_GET['lang'], $available, true)) {
        $_SESSION['lang'] = $_GET['lang'];
    }

    if (empty($_SESSION['lang']) || !in_array($_SESSION['lang'], $available, true)) {
        $_SESSION['lang'] = 'vi';
    }

    return $_SESSION['lang'];
}

/**
 * Current language code.
 */
function currentLang(): string
{
    startSession();
    return $_SESSION['lang'] ?? 'vi';
}

/**
 * Translate a key using the active language file.
 */
function t(string $key, ?string $default = null): string
{
    static $strings = [];
    $lang = currentLang();

    if (!isset($strings[$lang])) {
        $file = dirname(__DIR__) . '/lang/' . $lang . '.php';
        $strings[$lang] = file_exists($file) ? (array) require $file : [];
    }

    return $strings[$lang][$key] ?? ($default ?? $key);
}

/**
 * Pick the localised column of a database row (e.g. title_vi / title_en).
 */
function localized(?array $row, string $field, string $fallbackField = ''): string
{
    if (!$row) {
        return '';
    }
    $lang = currentLang();
    $key  = $field . '_' . $lang;

    if (!empty($row[$key])) {
        return (string) $row[$key];
    }
    foreach (['vi', 'en'] as $alt) {
        if (!empty($row[$field . '_' . $alt])) {
            return (string) $row[$field . '_' . $alt];
        }
    }
    if ($fallbackField !== '' && !empty($row[$fallbackField])) {
        return (string) $row[$fallbackField];
    }
    return '';
}

/**
 * Build a URL for switching language, keeping the current query string.
 */
function langSwitchUrl(string $lang): string
{
    $query = $_GET;
    $query['lang'] = $lang;
    $uri = strtok($_SERVER['REQUEST_URI'] ?? '/', '?');
    return $uri . '?' . http_build_query($query);
}

// ---------------------------------------------------------------------
// CSRF
// ---------------------------------------------------------------------

/**
 * Get (or create) the CSRF token for this session.
 */
function csrfToken(): string
{
    startSession();
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Hidden input containing the CSRF token.
 */
function csrfField(): string
{
    return '<input type="hidden" name="csrf_token" value="' . e(csrfToken()) . '">';
}

/**
 * Validate a submitted CSRF token.
 */
function verifyCsrf(?string $token = null): bool
{
    startSession();
    $token = $token ?? ($_POST['csrf_token'] ?? '');
    return !empty($_SESSION['csrf_token']) && is_string($token) && hash_equals($_SESSION['csrf_token'], $token);
}

// ---------------------------------------------------------------------
// Authentication
// ---------------------------------------------------------------------

/**
 * Currently logged-in user row, or null.
 */
function currentUser(): ?array
{
    startSession();
    static $user = null;
    static $loaded = false;

    if ($loaded) {
        return $user;
    }
    $loaded = true;

    if (empty($_SESSION['user_id'])) {
        return null;
    }

    $user = dbOne('SELECT * FROM users WHERE id = ? AND is_active = 1', [(int) $_SESSION['user_id']]);
    if (!$user) {
        unset($_SESSION['user_id']);
    }
    return $user;
}

function isLoggedIn(): bool
{
    return currentUser() !== null;
}

function isAdmin(): bool
{
    $user = currentUser();
    return $user !== null && (int) $user['is_admin'] === 1;
}

/**
 * Log a user in by id.
 */
function loginUser(int $userId): void
{
    startSession();
    session_regenerate_id(true);
    $_SESSION['user_id'] = $userId;
}

/**
 * Log the current user out.
 */
function logoutUser(): void
{
    startSession();
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
    }
    session_destroy();
}

/**
 * Require an authenticated user, otherwise redirect to login.
 */
function requireLogin(): void
{
    if (!isLoggedIn()) {
        redirect('login.php');
    }
}

// ---------------------------------------------------------------------
// Flash messages
// ---------------------------------------------------------------------

function setFlash(string $message, string $type = 'success'): void
{
    startSession();
    $_SESSION['flash'][] = ['message' => $message, 'type' => $type];
}

function getFlashes(): array
{
    startSession();
    $flashes = $_SESSION['flash'] ?? [];
    unset($_SESSION['flash']);
    return $flashes;
}

// ---------------------------------------------------------------------
// Settings
// ---------------------------------------------------------------------

/**
 * Get a site setting value.
 */
function setting(string $key, string $default = ''): string
{
    static $settings = null;

    if ($settings === null) {
        $settings = [];
        try {
            foreach (dbAll('SELECT setting_key, setting_value FROM site_settings') as $row) {
                $settings[$row['setting_key']] = (string) $row['setting_value'];
            }
        } catch (Throwable $e) {
            $settings = [];
        }
    }

    return (isset($settings[$key]) && $settings[$key] !== '') ? $settings[$key] : $default;
}

/**
 * Persist a site setting.
 */
function saveSetting(string $key, string $value): void
{
    dbQuery(
        'INSERT INTO site_settings (setting_key, setting_value) VALUES (?, ?)
         ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)',
        [$key, $value]
    );
}

// ---------------------------------------------------------------------
// Misc helpers
// ---------------------------------------------------------------------

/**
 * Create a URL-friendly slug (handles Vietnamese diacritics).
 */
function slugify(string $text): string
{
    $map = [
        'à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ' => 'a',
        'è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ'             => 'e',
        'ì|í|ị|ỉ|ĩ'                         => 'i',
        'ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ' => 'o',
        'ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ'             => 'u',
        'ỳ|ý|ỵ|ỷ|ỹ'                         => 'y',
        'đ'                                 => 'd',
    ];

    $text = function_exists('mb_strtolower') ? mb_strtolower($text, 'UTF-8') : strtolower($text);
    foreach ($map as $pattern => $replacement) {
        $text = preg_replace('/(' . $pattern . ')/u', $replacement, $text);
    }
    $text = preg_replace('/[^a-z0-9]+/u', '-', $text);
    $text = trim((string) $text, '-');

    return $text !== '' ? $text : 'item-' . time();
}

/**
 * Make sure a slug is unique inside a table.
 */
function uniqueSlug(string $table, string $slug, int $ignoreId = 0): string
{
    $table = preg_replace('/[^a-z_]/', '', $table);
    $base  = $slug;
    $i     = 1;

    while (true) {
        $existing = dbOne("SELECT id FROM `$table` WHERE slug = ? AND id <> ?", [$slug, $ignoreId]);
        if (!$existing) {
            return $slug;
        }
        $slug = $base . '-' . (++$i);
    }
}

/**
 * Format a price in VND.
 */
function formatPrice($price): string
{
    $price = (float) $price;
    if ($price <= 0) {
        return t('free');
    }
    return number_format($price, 0, ',', '.') . ' ₫';
}

/**
 * Format a date for display.
 */
function formatDate(?string $date): string
{
    if (!$date) {
        return '';
    }
    $ts = strtotime($date);
    return $ts ? date('d/m/Y', $ts) : '';
}

/**
 * CSS modifier class for a level colour.
 */
function levelColorClass(?string $color): string
{
    $allowed = ['green', 'blue', 'purple', 'orange'];
    $color = strtolower((string) $color);
    return in_array($color, $allowed, true) ? $color : 'green';
}

/**
 * Handle an uploaded image and return the stored relative path (uploads/xxx.jpg).
 *
 * @throws RuntimeException on validation failure
 */
function handleImageUpload(string $field, ?string $currentPath = null): ?string
{
    if (empty($_FILES[$field]) || ($_FILES[$field]['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
        return $currentPath;
    }

    $file = $_FILES[$field];

    if ($file['error'] !== UPLOAD_ERR_OK) {
        throw new RuntimeException('Upload failed (error code ' . $file['error'] . ').');
    }
    if ($file['size'] > MAX_UPLOAD_SIZE) {
        throw new RuntimeException('File is too large. Maximum size is ' . round(MAX_UPLOAD_SIZE / 1048576) . 'MB.');
    }

    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime  = $finfo->file($file['tmp_name']);
    if (!in_array($mime, ALLOWED_IMAGE_TYPES, true)) {
        throw new RuntimeException('Unsupported file type: ' . $mime);
    }

    $extensions = [
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/gif'  => 'gif',
        'image/webp' => 'webp',
    ];
    $ext = $extensions[$mime] ?? 'jpg';

    if (!is_dir(UPLOAD_DIR)) {
        mkdir(UPLOAD_DIR, 0755, true);
    }

    $name = date('Ymd-His') . '-' . bin2hex(random_bytes(4)) . '.' . $ext;
    $dest = rtrim(UPLOAD_DIR, '/') . '/' . $name;

    if (!move_uploaded_file($file['tmp_name'], $dest)) {
        throw new RuntimeException('Could not save the uploaded file.');
    }

    return 'uploads/' . $name;
}

/**
 * Simple pagination metadata.
 */
function paginate(int $total, int $perPage, int $page): array
{
    $perPage = max(1, $perPage);
    $pages   = max(1, (int) ceil($total / $perPage));
    $page    = min(max(1, $page), $pages);

    return [
        'total'   => $total,
        'perPage' => $perPage,
        'page'    => $page,
        'pages'   => $pages,
        'offset'  => ($page - 1) * $perPage,
    ];
}

/**
 * Render pagination links preserving current query parameters.
 */
function renderPagination(array $p, string $baseUrl = ''): string
{
    if ($p['pages'] <= 1) {
        return '';
    }

    $query = $_GET;
    unset($query['page']);
    $baseUrl = $baseUrl !== '' ? $baseUrl : strtok($_SERVER['REQUEST_URI'] ?? '', '?');

    $link = static function (int $page) use ($query, $baseUrl): string {
        $query['page'] = $page;
        return e($baseUrl . '?' . http_build_query($query));
    };

    $html = '<nav class="pagination">';
    if ($p['page'] > 1) {
        $html .= '<a class="pagination__link" href="' . $link($p['page'] - 1) . '">&laquo; ' . e(t('prev')) . '</a>';
    }
    for ($i = 1; $i <= $p['pages']; $i++) {
        $active = $i === $p['page'] ? ' is-active' : '';
        $html .= '<a class="pagination__link' . $active . '" href="' . $link($i) . '">' . $i . '</a>';
    }
    if ($p['page'] < $p['pages']) {
        $html .= '<a class="pagination__link" href="' . $link($p['page'] + 1) . '">' . e(t('next')) . ' &raquo;</a>';
    }
    $html .= '</nav>';

    return $html;
}

/**
 * Inline SVG icon set used across the site.
 */
function icon(string $name, string $class = 'icon'): string
{
    $paths = [
        'route'    => '<path d="M6 3a3 3 0 0 0-1 5.83V15a3 3 0 0 0 3 3h5.17A3 3 0 1 0 16 13H8a1 1 0 0 1-1-1V8.83A3 3 0 0 0 6 3Z"/>',
        'laptop'   => '<path d="M4 5a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v9H4V5Zm-2 11h20v1a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2v-1Z"/>',
        'teacher'  => '<path d="M12 3 1 8l11 5 9-4.09V15h2V8L12 3ZM5 13.18v3L12 20l7-3.82v-3L12 17l-7-3.82Z"/>',
        'library'  => '<path d="M4 3h5v18H4V3Zm7 0h4v18h-4V3Zm6.2.6 3.9 17.1-2 .5-3.9-17.1 2-.5Z"/>',
        'chart'    => '<path d="M4 20h16v2H2V2h2v18Zm3-3V9h3v8H7Zm5 0V5h3v12h-3Zm5 0v-5h3v5h-3Z"/>',
        'music'    => '<path d="M12 3v10.55A4 4 0 1 0 14 17V7h4V3h-6Z"/>',
        'star'     => '<path d="m12 17.27 6.18 3.73-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21 12 17.27Z"/>',
        'clock'    => '<path d="M12 2a10 10 0 1 0 0 20 10 10 0 0 0 0-20Zm1 10.59 3.7 3.7-1.41 1.42L11 13.41V6h2v6.59Z"/>',
        'user'     => '<path d="M12 12a5 5 0 1 0 0-10 5 5 0 0 0 0 10Zm0 2c-4.42 0-8 2.24-8 5v3h16v-3c0-2.76-3.58-5-8-5Z"/>',
        'mail'     => '<path d="M2 5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2l-10 6L2 5Zm0 2.24V19a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V7.24l-10 6-10-6Z"/>',
        'phone'    => '<path d="M6.6 10.8a15.1 15.1 0 0 0 6.6 6.6l2.2-2.2a1 1 0 0 1 1-.25 11.4 11.4 0 0 0 3.6.58 1 1 0 0 1 1 1V20a1 1 0 0 1-1 1A17 17 0 0 1 3 4a1 1 0 0 1 1-1h3.5a1 1 0 0 1 1 1c0 1.25.2 2.46.57 3.6a1 1 0 0 1-.25 1l-2.22 2.2Z"/>',
        'pin'      => '<path d="M12 2a7 7 0 0 0-7 7c0 5.25 7 13 7 13s7-7.75 7-13a7 7 0 0 0-7-7Zm0 9.5A2.5 2.5 0 1 1 12 6.5a2.5 2.5 0 0 1 0 5Z"/>',
        'facebook' => '<path d="M13 22v-9h3l.5-3.5H13V7.5c0-1 .3-1.7 1.8-1.7H17V2.6A24 24 0 0 0 14.4 2.5C11.9 2.5 10 4 10 6.9V9.5H7V13h3v9h3Z"/>',
        'youtube'  => '<path d="M23 12s0-3.4-.44-5a2.8 2.8 0 0 0-2-2C18.8 4.5 12 4.5 12 4.5s-6.8 0-8.56.5a2.8 2.8 0 0 0-2 2C1 8.6 1 12 1 12s0 3.4.44 5a2.8 2.8 0 0 0 2 2c1.76.5 8.56.5 8.56.5s6.8 0 8.56-.5a2.8 2.8 0 0 0 2-2C23 15.4 23 12 23 12ZM9.8 15.3V8.7l5.7 3.3-5.7 3.3Z"/>',
        'zalo'     => '<path d="M12 2C6.5 2 2 5.9 2 10.7c0 2.7 1.5 5.1 3.8 6.7-.1 1-.6 2.4-1.4 3.4-.2.3 0 .7.4.6 2-.4 3.6-1.3 4.5-1.9.9.2 1.8.3 2.7.3 5.5 0 10-3.9 10-8.7S17.5 2 12 2Z"/>',
        'check'    => '<path d="M9 16.17 4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41L9 16.17Z"/>',
        'globe'    => '<path d="M12 2a10 10 0 1 0 0 20 10 10 0 0 0 0-20Zm6.9 6h-2.6a15.6 15.6 0 0 0-1.4-3.4A8 8 0 0 1 18.9 8ZM12 4.1c.7 1 1.3 2.2 1.7 3.9h-3.4c.4-1.7 1-2.9 1.7-3.9ZM4.3 14a8 8 0 0 1 0-4h3a17 17 0 0 0 0 4h-3Zm.8 2h2.6c.3 1.2.8 2.4 1.4 3.4A8 8 0 0 1 5.1 16Zm2.6-8H5.1a8 8 0 0 1 4-3.4A15.6 15.6 0 0 0 7.7 8ZM12 19.9c-.7-1-1.3-2.2-1.7-3.9h3.4c-.4 1.7-1 2.9-1.7 3.9ZM14.1 14H9.9a14.7 14.7 0 0 1 0-4h4.2a14.7 14.7 0 0 1 0 4Zm.8 5.4c.6-1 1.1-2.2 1.4-3.4h2.6a8 8 0 0 1-4 3.4Zm1.8-5.4a17 17 0 0 0 0-4h3a8 8 0 0 1 0 4h-3Z"/>',
        'arrow'    => '<path d="M13.2 5.6 11.8 7l4 4H4v2h11.8l-4 4 1.4 1.4L19.6 12l-6.4-6.4Z"/>',
    ];

    $path = $paths[$name] ?? $paths['music'];

    return '<svg class="' . e($class) . '" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">' . $path . '</svg>';
}
