<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../classes/autoload.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

try {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($input['token']) || empty($input['token'])) {
        echo json_encode(['success' => false, 'message' => 'Reset token is required']);
        exit;
    }
    
    if (!isset($input['newPassword']) || empty($input['newPassword'])) {
        echo json_encode(['success' => false, 'message' => 'New password is required', 'field' => 'newPassword']);
        exit;
    }
    
    $token = trim($input['token']);
    $newPassword = trim($input['newPassword']);
    
    // Validate password strength
    if (strlen($newPassword) < 8) {
        echo json_encode(['success' => false, 'message' => 'Password must be at least 8 characters long', 'field' => 'newPassword']);
        exit;
    }
    
    // Check for common weak passwords
    $weakPasswords = ['password', '12345678', 'qwerty123', 'abc12345', 'password123'];
    if (in_array(strtolower($newPassword), $weakPasswords)) {
        echo json_encode(['success' => false, 'message' => 'Password is too weak. Please choose a stronger password.', 'field' => 'newPassword']);
        exit;
    }
    
    $auth = new PartnerAuth();
    $result = $auth->resetPasswordWithToken($token, $newPassword);
    
    echo json_encode($result);
    
} catch (Exception $e) {
    error_log('Reset password error: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'An error occurred. Please try again.']);
}
?>
