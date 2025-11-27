<?php
/**
 * Secure Notes Vault - Dashboard
 * Developed by Avik - https://aviksec.xo.je
 */

require_once 'config.php';
require_once 'db.php';
require_once 'functions.php';

start_secure_session();
require_login();

$search = isset($_GET['search']) ? sanitize_input($_GET['search']) : '';
$success = isset($_GET['success']) ? sanitize_input($_GET['success']) : '';
$error = isset($_GET['error']) ? sanitize_input($_GET['error']) : '';

// Fetch user notes
try {
    if (!empty($search)) {
        $stmt = $conn->prepare(
            "SELECT id, title, encrypted_content, created_at, updated_at 
             FROM notes 
             WHERE user_id = ? AND title LIKE ?
             ORDER BY updated_at DESC"
        );
        $stmt->execute([$_SESSION['user_id'], '%' . $search . '%']);
    } else {
        $stmt = $conn->prepare(
            "SELECT id, title, encrypted_content, created_at, updated_at 
             FROM notes 
             WHERE user_id = ? 
             ORDER BY updated_at DESC"
        );
        $stmt->execute([$_SESSION['user_id']]);
    }
    $notes = $stmt->fetchAll();
    $total_notes = count($notes);
} catch (PDOException $e) {
    error_log("Dashboard Error: " . $e->getMessage());
    $notes = [];
    $total_notes = 0;
    $error = 'Error loading notes.';
}

$csrf_token = generate_csrf_token();
$theme = get_user_theme();
?>
<!DOCTYPE html>
<html lang="en" data-theme="<?php echo $theme; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <nav class="navbar">
        <div class="navbar-container">
            <div class="navbar-brand">
                <h1>🔐 <?php echo SITE_NAME; ?></h1>
            </div>
            <div class="navbar-menu">
                <span class="navbar-user">👤 <?php echo escape_html($_SESSION['username']); ?></span>
                <button id="themeToggle" class="btn btn-icon" title="Toggle theme">
                    <span id="themeIcon"><?php echo $theme === 'dark' ? '☀️' : '🌙'; ?></span>
                </button>
                <a href="logout.php" class="btn btn-secondary">Logout</a>
            </div>
        </div>
    </nav>
    
    <div class="container">
        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo escape_html($success); ?></div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo escape_html($error); ?></div>
        <?php endif; ?>
        
        <div class="dashboard-header">
            <div class="dashboard-stats">
                <div class="stat-card">
                    <span class="stat-number"><?php echo $total_notes; ?></span>
                    <span class="stat-label">Total Notes</span>
                </div>
            </div>
            
            <div class="dashboard-actions">
                <form method="GET" action="dashboard.php" class="search-form">
                    <input type="text" name="search" placeholder="🔍 Search notes..." 
                           value="<?php echo escape_html($search); ?>">
                    <button type="submit" class="btn btn-secondary">Search</button>
                    <?php if ($search): ?>
                        <a href="dashboard.php" class="btn btn-secondary">Clear</a>
                    <?php endif; ?>
                </form>
                <a href="add_note.php" class="btn btn-primary">➕ Add New Note</a>
            </div>
        </div>
        
        <div class="notes-grid">
            <?php if (empty($notes)): ?>
                <div class="empty-state">
                    <p>📄 <?php echo $search ? 'No notes found matching your search.' : 'No notes yet. Create your first encrypted note!'; ?></p>
                    <?php if (!$search): ?>
                        <a href="add_note.php" class="btn btn-primary">Create Note</a>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <?php foreach ($notes as $note): ?>
                    <div class="note-card" data-note-id="<?php echo $note['id']; ?>" 
                         data-encrypted="<?php echo escape_html($note['encrypted_content']); ?>">
                        <h3 class="note-title"><?php echo escape_html($note['title']); ?></h3>
                        <div class="note-content-preview" id="preview-<?php echo $note['id']; ?>">
                            <p class="loading">Click to decrypt...</p>
                        </div>
                        <div class="note-meta">
                            <span>📅 <?php echo date('M d, Y', strtotime($note['updated_at'])); ?></span>
                        </div>
                        <div class="note-actions">
                            <a href="edit_note.php?id=<?php echo $note['id']; ?>" class="btn btn-sm btn-secondary">✏️ Edit</a>
                            <button onclick="deleteNote(<?php echo $note['id']; ?>)" class="btn btn-sm btn-danger">🗑️ Delete</button>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        
        <footer class="footer">
            <p>Developed by <a href="https://aviksec.xo.je" target="_blank">Avik</a></p>
        </footer>
    </div>
    
    <script src="assets/js/aes.js"></script>
    <script src="assets/js/app.js"></script>
    <script>
        const CSRF_TOKEN = '<?php echo $csrf_token; ?>';
    </script>
</body>
</html>