<?php
/**
 * Secure Notes Vault - Helper Functions
 * Developed by Avik - https://aviksec.xo.je
 */

require_once 'config.php';

// Start secure session
function start_secure_session() {
    if (session_status() == PHP_SESSION_NONE) {
        ini_set('session.cookie_httponly', 1);
        ini_set('session.use_only_cookies', 1);
        ini_set('session.cookie_secure', 0); // Set to 1 if using HTTPS
        session_start();
    }
}

// Generate CSRF Token
function generate_csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// Verify CSRF Token
function verify_csrf_token($token) {
    if (!isset($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $token)) {
        return false;
    }
    return true;
}

// Sanitize Input
function sanitize_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

// Check if user is logged in
function is_logged_in() {
    return isset($_SESSION['user_id']) && isset($_SESSION['user_email']);
}

// Check session timeout
function check_session_timeout() {
    if (isset($_SESSION['last_activity'])) {
        $elapsed_time = time() - $_SESSION['last_activity'];
        if ($elapsed_time > SESSION_TIMEOUT) {
            session_unset();
            session_destroy();
            header("Location: login.php?timeout=1");
            exit();
        }
    }
    $_SESSION['last_activity'] = time();
}

// Redirect if not logged in
function require_login() {
    if (!is_logged_in()) {
        header("Location: login.php");
        exit();
    }
    check_session_timeout();
}

// Redirect if already logged in
function redirect_if_logged_in() {
    if (is_logged_in()) {
        header("Location: dashboard.php");
        exit();
    }
}

// Check rate limiting for login attempts
function check_login_attempts($email, $conn) {
    $stmt = $conn->prepare("SELECT attempt_count, last_attempt FROM login_attempts WHERE email = ?");
    $stmt->execute([$email]);
    $result = $stmt->fetch();
    
    if ($result) {
        $elapsed = time() - strtotime($result['last_attempt']);
        
        if ($result['attempt_count'] >= MAX_LOGIN_ATTEMPTS && $elapsed < LOGIN_TIMEOUT) {
            return false;
        } elseif ($elapsed >= LOGIN_TIMEOUT) {
            // Reset attempts after timeout
            $stmt = $conn->prepare("DELETE FROM login_attempts WHERE email = ?");
            $stmt->execute([$email]);
        }
    }
    return true;
}

// Record failed login attempt
function record_login_attempt($email, $conn) {
    $stmt = $conn->prepare("SELECT id, attempt_count FROM login_attempts WHERE email = ?");
    $stmt->execute([$email]);
    $result = $stmt->fetch();
    
    if ($result) {
        $stmt = $conn->prepare("UPDATE login_attempts SET attempt_count = attempt_count + 1, last_attempt = NOW() WHERE email = ?");
        $stmt->execute([$email]);
    } else {
        $stmt = $conn->prepare("INSERT INTO login_attempts (email, attempt_count, last_attempt) VALUES (?, 1, NOW())");
        $stmt->execute([$email]);
    }
}

// Clear login attempts on successful login
function clear_login_attempts($email, $conn) {
    $stmt = $conn->prepare("DELETE FROM login_attempts WHERE email = ?");
    $stmt->execute([$email]);
}

// Get user theme preference
function get_user_theme() {
    return isset($_SESSION['theme']) ? $_SESSION['theme'] : 'light';
}

// Escape output for HTML display
function escape_html($text) {
    return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
}
?>