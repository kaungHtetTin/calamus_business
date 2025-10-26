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
    
    if (!isset($input['currentPassword']) || empty($input['currentPassword'])) {
        echo json_encode(['success' => false, 'message' => 'Current password is required', 'field' => 'currentPassword']);
        exit;
    }
    
    if (!isset($input['newPassword']) || empty($input['newPassword'])) {
        echo json_encode(['success' => false, 'message' => 'New password is required', 'field' => 'newPassword']);
        exit;
    }
    
    // Validate session token
    $sessionToken = $input['session_token'] ?? '';
    if (empty($sessionToken)) {
        echo json_encode(['success' => false, 'message' => 'Session token is required']);
        exit;
    }
    
    // Validate session
    $auth = new PartnerAuth();
    $sessionValidation = $auth->validateSession($sessionToken);
    
    if (!$sessionValidation['success']) {
        echo json_encode(['success' => false, 'message' => 'Invalid or expired session']);
        exit;
    }
    
    $partnerId = $sessionValidation['partner']['id'];
    $currentPassword = trim($input['currentPassword']);
    $newPassword = trim($input['newPassword']);
    
    // Change password
    $result = $auth->changePassword($partnerId, $currentPassword, $newPassword);
    
    echo json_encode($result);
    
} catch (Exception $e) {
    error_log('Change password error: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'An error occurred. Please try again.']);
}
?>
