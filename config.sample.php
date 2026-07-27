<?php
/**
 * MusicOfEveryone - Configuration sample
 *
 * Copy this file to config.php and fill in your cPanel database details.
 *   cp config.sample.php config.php
 */

// ---------------------------------------------------------------------
// Database Configuration
// ---------------------------------------------------------------------
define('DB_HOST', 'localhost');
define('DB_NAME', 'your_database_name');
define('DB_USER', 'your_database_user');
define('DB_PASS', 'your_database_password');
define('DB_CHARSET', 'utf8mb4');

// ---------------------------------------------------------------------
// Site Configuration
// ---------------------------------------------------------------------
define('SITE_URL', 'https://yourdomain.com');
define('SITE_NAME', 'MusicOfEveryone');
define('UPLOAD_DIR', __DIR__ . '/uploads/');
define('UPLOAD_URL', SITE_URL . '/uploads/');
define('MAX_UPLOAD_SIZE', 5 * 1024 * 1024); // 5MB
define('ALLOWED_IMAGE_TYPES', ['image/jpeg', 'image/png', 'image/gif', 'image/webp']);

// ---------------------------------------------------------------------
// Google Maps (optional - iframe embed URL from Google Maps)
// ---------------------------------------------------------------------
define('GOOGLE_MAPS_EMBED_URL', '');

// ---------------------------------------------------------------------
// Session
// ---------------------------------------------------------------------
define('SESSION_NAME', 'musicofeveryone_session');

// ---------------------------------------------------------------------
// Admin
// ---------------------------------------------------------------------
define('ADMIN_EMAIL', 'admin@musicofeveryone.com');

// ---------------------------------------------------------------------
// Debug (set to false in production)
// ---------------------------------------------------------------------
define('DEBUG_MODE', false);
