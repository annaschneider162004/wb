<?php
/**
 * MusicOfEveryone - Local / default configuration
 *
 * This is a working copy of config.sample.php with default localhost
 * settings. Update the credentials for your own server.
 */

// ---------------------------------------------------------------------
// Database Configuration
// ---------------------------------------------------------------------
define('DB_HOST', 'localhost');
define('DB_NAME', 'musicofeveryone');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

// ---------------------------------------------------------------------
// Site Configuration
// ---------------------------------------------------------------------
define('SITE_URL', 'http://localhost');
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
define('DEBUG_MODE', true);
