<?php
/**
 * Secure Notes Vault - Login Page
 * Developed by Avik - https://aviksec.xo.je
 */

require_once 'config.php';
require_once 'db.php';
require_once 'functions.php';

start_secure_session();
redirect_if_logged_in();

$error = '';
$info = '';

// Check for timeout message
if (isset($_GET['timeout'])) {
    $info = 'Your session has expired. Please login again.';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $error = 'Invalid security token. Please try again.';
    } else {
        $email = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
        $password = $_POST['password'] ?? '';
        $remember = isset($_POST['remember']);
        
        if (empty($email) || empty($password)) {
            $error = 'Email and password are required.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = 'Invalid email address.';
        } else {
            try {
                // Check rate limiting
                if (!check_login_attempts($email, $conn)) {
                    $error = 'Too many failed login attempts. Please try again after 5 minutes.';
                } else {
                    // Verify credentials
                    $stmt = $conn->prepare("SELECT id, username, email, password_hash, theme FROM users WHERE email = ?");
                    $stmt->execute([$email]);
                    $user = $stmt->fetch();
                    
                    if ($user && password_verify($password, $user['password_hash'])) {
                        // Successful login
                        clear_login_attempts($email, $conn);
                        
                        // Regenerate session ID to prevent session fixation
                        session_regenerate_id(true);
                        
                        $_SESSION['user_id'] = $user['id'];
                        $_SESSION['user_email'] = $user['email'];
                        $_SESSION['username'] = $user['username'];
                        $_SESSION['theme'] = $user['theme'];
                        $_SESSION['last_activity'] = time();
                        
                        // Update last login
                        $stmt = $conn->prepare("UPDATE users SET last_login = NOW() WHERE id = ?");
                        $stmt->execute([$user['id']]);
                        
                        // Set remember me cookie (optional)
                        if ($remember) {
                            $token = bin2hex(random_bytes(32));
                            setcookie('remember_token', $token, time() + (86400 * 30), '/', '', false, true);
                        }
                        
                        header("Location: dashboard.php");
                        exit();
                    } else {
                        // Failed login
                        record_login_attempt($email, $conn);
                        $error = 'Invalid email or password.';
                    }
                }
            } catch (PDOException $e) {
                error_log("Login Error: " . $e->getMessage());
                $error = 'An error occurred. Please try again later.';
            }
        }
    }
}

$csrf_token = generate_csrf_token();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="auth-container">
        <div class="auth-box">
            <h1>🔐 <?php echo SITE_NAME; ?></h1>
            <h2>Login to Your Account</h2>
            
            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo escape_html($error); ?></div>
            <?php endif; ?>
            
            <?php if ($info): ?>
                <div class="alert alert-info"><?php echo escape_html($info); ?></div>
            <?php endif; ?>
            
            <form method="POST" action="login.php">
                <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required autocomplete="email"
                           value="<?php echo isset($_POST['email']) ? escape_html($_POST['email']) : ''; ?>">
                </div>
                
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required autocomplete="current-password">
                </div>
                
                <div class="form-group-checkbox">
                    <label>
                        <input type="checkbox" name="remember" value="1">
                        Remember me for 30 days
                    </label>
                </div>
                
                <button type="submit" class="btn btn-primary btn-block">Login</button>
            </form>
            
            <p class="auth-footer">
                Don't have an account? <a href="register.php">Register here</a>
            </p>
            
            <p class="credit">Developed by <a href="https://aviksec.xo.je" target="_blank">Avik</a></p>
        </div>
    </div>
</body>
</html>