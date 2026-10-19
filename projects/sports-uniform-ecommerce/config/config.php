<?php
/**
 * Central configuration.
 * Edit the database credentials below to match your environment
 * (the defaults work out-of-the-box with XAMPP / Laragon / MAMP).
 */

// ---- Database ----
define('DB_HOST', getenv('DB_HOST') ?: '127.0.0.1');
define('DB_NAME', getenv('DB_NAME') ?: 'vantage_shop');
define('DB_USER', getenv('DB_USER') ?: 'root');
define('DB_PASS', getenv('DB_PASS') ?: '');
define('DB_CHARSET', 'utf8mb4');

// ---- Paths / URL ----
// BASE_URL is auto-detected but you can hard-code it if needed, e.g.
// define('BASE_URL', 'http://localhost/sports-uniform-ecommerce');
if (!defined('BASE_URL')) {
    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host   = $_SERVER['HTTP_HOST'] ?? 'localhost';
    // Directory the app lives in (works in sub-folders too)
    $script = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? ''));
    // If we're inside /admin, step back up one level for the public base
    $script = preg_replace('#/admin$#', '', $script);
    $script = rtrim($script, '/');
    define('BASE_URL', $scheme . '://' . $host . $script);
}

define('APP_ROOT', dirname(__DIR__));
define('UPLOAD_DIR', APP_ROOT . '/assets/uploads');
define('UPLOAD_URL', BASE_URL . '/assets/uploads');

// ---- Session & errors ----
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Flip to 0 in production
define('APP_DEBUG', true);
if (APP_DEBUG) {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
} else {
    error_reporting(0);
    ini_set('display_errors', '0');
}

date_default_timezone_set('UTC');
