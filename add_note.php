<?php
/**
 * Secure Notes Vault - Add Note
 * Developed by Avik - https://aviksec.xo.je
 */

require_once 'config.php';
require_once 'db.php';
require_once 'functions.php';

start_secure_session();
require_login();

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $error = 'Invalid security token. Please try again.';
    } else {
        $title = sanitize_input($_POST['title'] ?? '');
        $encrypted_content = $_POST['encrypted_content'] ?? '';
        
        if (empty($title)) {
            $error = 'Title is required.';
        } elseif (empty($encrypted_content)) {
            $error = 'Note content cannot be empty.';
        } elseif (strlen($title) > 255) {
            $error = 'Title is too long (max 255 characters).';
        } else {
            try {
                $stmt = $conn->prepare(
                    "INSERT INTO notes (user_id, title, encrypted_content) VALUES (?, ?, ?)"
                );
                
                if ($stmt->execute([$_SESSION['user_id'], $title, $encrypted_content])) {
                    header("Location: dashboard.php?success=Note created successfully!");
                    exit();
                } else {
                    $error = 'Failed to save note. Please try again.';
                }
            } catch (PDOException $e) {
                error_log("Add Note Error: " . $e->getMessage());
                $error = 'An error occurred. Please try again later.';
            }
        }
    }
}

$csrf_token = generate_csrf_token();
$theme = get_user_theme();
?>
<!DOCTYPE html>
<html lang="en" data-theme="<?php echo $theme; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Note - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <nav class="navbar">
        <div class="navbar-container">
            <div class="navbar-brand">
                <h1>🔐 <?php echo SITE_NAME; ?></h1>
            </div>
            <div class="navbar-menu">
                <a href="dashboard.php" class="btn btn-secondary">← Back to Dashboard</a>
                <a href="logout.php" class="btn btn-secondary">Logout</a>
            </div>
        </div>
    </nav>
    
    <div class="container">
        <div class="note-form-container">
            <h2>➕ Create New Note</h2>
            
            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo escape_html($error); ?></div>
            <?php endif; ?>
            
            <form method="POST" action="add_note.php" id="noteForm">
                <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                <input type="hidden" name="encrypted_content" id="encryptedContent">
                
                <div class="form-group">
                    <label for="title">Note Title</label>
                    <input type="text" id="title" name="title" required maxlength="255" 
                           placeholder="Enter note title..."
                           value="<?php echo isset($_POST['title']) ? escape_html($_POST['title']) : ''; ?>">
                </div>
                
                <div class="form-group">
                    <label for="content">Note Content</label>
                    <textarea id="content" name="content" rows="12" required 
                              placeholder="Write your note here... (will be encrypted before saving)"></textarea>
                    <small class="help-text">🔒 Your note will be encrypted on your device before being sent to the server.</small>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">💾 Save Note</button>
                    <a href="dashboard.php" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
        
        <footer class="footer">
            <p>Developed by <a href="https://aviksec.xo.je" target="_blank">Avik</a></p>
        </footer>
    </div>
    
    <script src="assets/js/aes.js"></script>
    <script src="assets/js/app.js"></script>
</body>
</html>