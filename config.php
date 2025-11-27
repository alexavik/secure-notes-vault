<?php
/**
 * Secure Notes Vault - Configuration File
 * Developed by Avik - https://aviksec.xo.je
 */

// Database Configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'your_db_username');
define('DB_PASS', 'your_db_password');
define('DB_NAME', 'secure_notes_vault');

// Security Configuration
define('ENCRYPTION_KEY', 'change-this-to-random-32-chars!');
define('SITE_KEY', hash('sha256', 'your-site-secret-key'));

// Session Configuration
define('SESSION_TIMEOUT', 1800); // 30 minutes in seconds
define('MAX_LOGIN_ATTEMPTS', 5);
define('LOGIN_TIMEOUT', 300); // 5 minutes lockout

// Site Configuration
define('SITE_NAME', 'Secure Notes Vault');
define('SITE_URL', 'http://localhost');

// Error Reporting (Set to 0 in production)
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Timezone
date_default_timezone_set('Asia/Kolkata');
?>