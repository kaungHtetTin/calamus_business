<?php
/**
 * Session Validation API Endpoint
 * 
 * This is a dedicated endpoint for session validation.
 * It's a wrapper around the login.php API for convenience.
 */

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

$method = $_SERVER['REQUEST_METHOD'];
$auth = new PartnerAuth();

// Get input data
$input = json_decode(file_get_contents('php://input'), true);

// Handle different request methods
if ($method === 'POST') {
    $sessionToken = $input['session_token'] ?? '';
    
    if (empty($sessionToken)) {
        echo json_encode([
            'success' => false, 
            'message' => 'Session token is required'
        ]);
        exit;
    }
    
    $result = $auth->validateSession($sessionToken);
    
    if ($result['success']) {

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION['partner_session_token'] = $sessionToken;
        $_SESSION['partner_id'] =  $result['partner']['id'];

        echo json_encode([
            'success' => true,
            'partner' => $result['partner']
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => $result['message']
        ]);
    }
    
} elseif ($method === 'GET') {
    // Handle GET request (for testing)
    $sessionToken = $_GET['session_token'] ?? '';
    
    if (empty($sessionToken)) {
        echo json_encode([
            'success' => false, 
            'message' => 'Session token is required',
            'usage' => 'POST /api/validate_session.php with {"session_token": "your_token"}'
        ]);
        exit;
    }
    
    $result = $auth->validateSession($sessionToken);
    
    if ($result['success']) {
        echo json_encode([
            'success' => true,
            'partner' => $result['partner']
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => $result['message']
        ]);
    }
    
} else {
    echo json_encode([
        'success' => false, 
        'message' => 'Method not allowed. Use POST or GET.',
        'usage' => 'POST /api/validate_session.php with {"session_token": "your_token"}'
    ]);
}
?>
