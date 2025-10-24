<?php
require_once 'partner_auth.php';
require_once 'promotion_code_manager.php';

header('Content-Type: application/json');

// Get request method and data
$method = $_SERVER['REQUEST_METHOD'];
$input = json_decode(file_get_contents('php://input'), true);

// Initialize classes
$auth = new PartnerAuth();
$codeManager = new PromotionCodeManager();

// Route requests
$endpoint = $_GET['endpoint'] ?? '';

switch ($endpoint) {
    case 'generate_promotion_code':
        if ($method === 'POST') {
            $sessionToken = $input['session_token'] ?? '';
            $session = $auth->validateSession($sessionToken);
            
            if ($session['success']) {
                $partnerId = $session['partner']['id'];
                $result = $codeManager->generatePromotionCode(
                    $partnerId,
                    $input['code_type'],
                    $input['target_course_id'] ?? null,
                    $input['target_major'] ?? null,
                    $input['client_name'] ?? '',
                    $input['expires_at'] ?? null
                );
                echo json_encode($result);
            } else {
                echo json_encode(['success' => false, 'message' => 'Invalid session']);
            }
        }
        break;
        
    case 'get_promotion_codes':
        if ($method === 'POST') {
            $sessionToken = $input['session_token'] ?? '';
            $session = $auth->validateSession($sessionToken);
            
            if ($session['success']) {
                $status = $input['status'] ?? null;
                $limit = $input['limit'] ?? 50;
                $codes = $codeManager->getPartnerPromotionCodes($session['partner']['id'], $status, $limit);
                echo json_encode(['success' => true, 'codes' => $codes]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Invalid session']);
            }
        }
        break;
        
    case 'get_code_stats':
        if ($method === 'POST') {
            $sessionToken = $input['session_token'] ?? '';
            $session = $auth->validateSession($sessionToken);
            
            if ($session['success']) {
                $stats = $codeManager->getPartnerCodeStats($session['partner']['id']);
                echo json_encode(['success' => true, 'stats' => $stats]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Invalid session']);
            }
        }
        break;
        
    case 'cancel_promotion_code':
        if ($method === 'POST') {
            $sessionToken = $input['session_token'] ?? '';
            $session = $auth->validateSession($sessionToken);
            
            if ($session['success']) {
                $result = $codeManager->cancelPromotionCode($input['code_id'], $session['partner']['id']);
                echo json_encode($result);
            } else {
                echo json_encode(['success' => false, 'message' => 'Invalid session']);
            }
        }
        break;
        
    case 'get_code_usage_history':
        if ($method === 'POST') {
            $sessionToken = $input['session_token'] ?? '';
            $session = $auth->validateSession($sessionToken);
            
            if ($session['success']) {
                $limit = $input['limit'] ?? 20;
                $history = $codeManager->getCodeUsageHistory($session['partner']['id'], $limit);
                echo json_encode(['success' => true, 'history' => $history]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Invalid session']);
            }
        }
        break;
        
    case 'update_code_prefix':
        if ($method === 'POST') {
            $sessionToken = $input['session_token'] ?? '';
            $session = $auth->validateSession($sessionToken);
            
            if ($session['success']) {
                $result = $codeManager->updatePartnerCodePrefix($session['partner']['id'], $input['new_prefix']);
                echo json_encode($result);
            } else {
                echo json_encode(['success' => false, 'message' => 'Invalid session']);
            }
        }
        break;
        
    default:
        echo json_encode(['success' => false, 'message' => 'Invalid endpoint']);
        break;
}
?>
