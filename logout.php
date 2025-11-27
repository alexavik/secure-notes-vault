<?php
/**
 * Secure Notes Vault - Logout
 * Developed by Avik - https://aviksec.xo.je
 */

require_once 'config.php';
require_once 'functions.php';

start_secure_session();

// Clear all session data
$_SESSION = [];

// Delete session cookie
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time() - 3600, '/');
}

// Delete remember me cookie
if (isset($_COOKIE['remember_token'])) {
    setcookie('remember_token', '', time() - 3600, '/', '', false, true);
}

// Destroy session
session_destroy();

// Redirect to login
header("Location: login.php");
exit();
?>