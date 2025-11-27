<?php
/**
 * Secure Notes Vault - Edit Note
 * Developed by Avik - https://aviksec.xo.je
 */

require_once 'config.php';
require_once 'db.php';
require_once 'functions.php';

start_secure_session();
require_login();

$error = '';
$note = null;
$note_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($note_id <= 0) {
    header("Location: dashboard.php?error=Invalid note ID");
    exit();
}

// Fetch note
try {
    $stmt = $conn->prepare("SELECT * FROM notes WHERE id = ? AND user_id = ?");
    $stmt->execute([$note_id, $_SESSION['user_id']]);
    $note = $stmt->fetch();
    
    if (!$note) {
        header("Location: dashboard.php?error=Note not found");
        exit();
    }
} catch (PDOException $e) {
    error_log("Edit Note Fetch Error: " . $e->getMessage());
    header("Location: dashboard.php?error=Error loading note");
    exit();
}

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
                    "UPDATE notes SET title = ?, encrypted_content = ?, updated_at = NOW() WHERE id = ? AND user_id = ?"
                );
                
                if ($stmt->execute([$title, $encrypted_content, $note_id, $_SESSION['user_id']])) {
                    header("Location: dashboard.php?success=Note updated successfully!");
                    exit();
                } else {
                    $error = 'Failed to update note. Please try again.';
                }
            } catch (PDOException $e) {
                error_log("Edit Note Error: " . $e->getMessage());
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
    <title>Edit Note - <?php echo SITE_NAME; ?></title>
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
            <h2>✏️ Edit Note</h2>
            
            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo escape_html($error); ?></div>
            <?php endif; ?>
            
            <form method="POST" action="edit_note.php?id=<?php echo $note_id; ?>" id="noteForm">
                <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                <input type="hidden" name="encrypted_content" id="encryptedContent">
                <input type="hidden" id="originalEncrypted" value="<?php echo escape_html($note['encrypted_content']); ?>">
                
                <div class="form-group">
                    <label for="title">Note Title</label>
                    <input type="text" id="title" name="title" required maxlength="255" 
                           value="<?php echo escape_html($note['title']); ?>">
                </div>
                
                <div class="form-group">
                    <label for="content">Note Content</label>
                    <textarea id="content" name="content" rows="12" required 
                              placeholder="Loading encrypted content..."></textarea>
                    <small class="help-text">🔒 Your note will be encrypted on your device before being sent to the server.</small>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">💾 Update Note</button>
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
    <script>
        // Decrypt and load existing content
        document.addEventListener('DOMContentLoaded', function() {
            const encryptedData = document.getElementById('originalEncrypted').value;
            const contentTextarea = document.getElementById('content');
            
            try {
                const decrypted = decryptData(encryptedData);
                contentTextarea.value = decrypted;
            } catch (error) {
                console.error('Decryption error:', error);
                contentTextarea.value = '';
                alert('Error decrypting note content. Please try again.');
            }
        });
    </script>
</body>
</html>