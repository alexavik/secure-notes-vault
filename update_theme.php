<?php
/**
 * Secure Notes Vault - Update Theme Preference
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
    $theme = isset($data['theme']) ? $data['theme'] : '';
    
    if ($theme === 'light' || $theme === 'dark') {
        try {
            $stmt = $conn->prepare("UPDATE users SET theme = ? WHERE id = ?");
            
            if ($stmt->execute([$theme, $_SESSION['user_id']])) {
                $_SESSION['theme'] = $theme;
                $response['success'] = true;
                $response['message'] = 'Theme updated successfully.';
            } else {
                $response['message'] = 'Failed to update theme.';
            }
        } catch (PDOException $e) {
            error_log("Theme Update Error: " . $e->getMessage());
            $response['message'] = 'An error occurred.';
        }
    } else {
        $response['message'] = 'Invalid theme value.';
    }
} else {
    $response['message'] = 'Invalid request method.';
}

echo json_encode($response);
?>