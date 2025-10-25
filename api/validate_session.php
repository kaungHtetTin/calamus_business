<?php
require_once __DIR__ . '/../classes/autoload.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

// Get input data
$input = json_decode(file_get_contents('php://input'), true);
$sessionToken = $input['session_token'] ?? '';

if (empty($sessionToken)) {
    echo json_encode(['success' => false, 'message' => 'Session token is required']);
    exit;
}

// Validate session
$auth = new PartnerAuth();
$sessionResult = $auth->validateSession($sessionToken);

if ($sessionResult['success']) {
    // Set session in PHP session
    session_start();
    $_SESSION['partner_session_token'] = $sessionToken;
    
    echo json_encode([
        'success' => true,
        'message' => 'Session validated successfully',
        'partner' => $sessionResult['partner']
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid or expired session'
    ]);
}
?>