<?php
/**
 * Secure Notes Vault - Delete Note
 * Developed by Avik - https://aviksec.xo.je
 */

require_once 'config.php';
require_once 'db.php';
require_once 'functions.php';

start_secure_session();
require_login();

header('Content-Type: application/json');

$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!verify_csrf_token($data['csrf_token'] ?? '')) {
        $response['message'] = 'Invalid security token.';
    } else {
        $note_id = isset($data['note_id']) ? intval($data['note_id']) : 0;
        
        if ($note_id <= 0) {
            $response['message'] = 'Invalid note ID.';
        } else {
            try {
                $stmt = $conn->prepare("DELETE FROM notes WHERE id = ? AND user_id = ?");
                
                if ($stmt->execute([$note_id, $_SESSION['user_id']])) {
                    if ($stmt->rowCount() > 0) {
                        $response['success'] = true;
                        $response['message'] = 'Note deleted successfully!';
                    } else {
                        $response['message'] = 'Note not found or already deleted.';
                    }
                } else {
                    $response['message'] = 'Failed to delete note.';
                }
            } catch (PDOException $e) {
                error_log("Delete Note Error: " . $e->getMessage());
                $response['message'] = 'An error occurred while deleting the note.';
            }
        }
    }
} else {
    $response['message'] = 'Invalid request method.';
}

echo json_encode($response);
?>