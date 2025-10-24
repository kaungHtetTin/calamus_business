<?php
/**
 * Promotion Code Management API
 * 
 * Handles promotion code generation, approval, rejection, and deletion
 */

require_once __DIR__ . '/../classes/autoload.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

$codeManager = new PromotionCodeManager();

// Get endpoint from URL parameter
$endpoint = $_GET['endpoint'] ?? '';

try {
    switch ($endpoint) {
        case 'generate_code':
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('Method not allowed');
            }
            
            $input = json_decode(file_get_contents('php://input'), true);
            
            $requiredFields = ['session_token', 'code_type', 'category_id', 'price'];
            foreach ($requiredFields as $field) {
                if (!isset($input[$field])) {
                    throw new Exception("Missing required field: $field");
                }
            }
            
            // Validate session
            $auth = new PartnerAuth();
            $partner = $auth->validateSession($input['session_token']);
             
            if (!$partner) {
                throw new Exception('Invalid session');
            }else{
                $result = $codeManager->generatePromotionCode(
                    $partner['partner']['id'],
                    $input['code_type'],
                    $input['category_id'],
                    $input['target_course_id'] ?? null,
                    $input['target_package_id'] ?? null,
                    $input['price'],
                    $input['commission_rate'] ?? null, // Use partner's rate if not provided
                    $input['expired_at'] ?? null
                ); 
            }
                  
            echo json_encode($result);
            break;
            
        case 'get_codes':
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('Method not allowed');
            }
            
            $input = json_decode(file_get_contents('php://input'), true);
            
            if (!isset($input['session_token'])) {
                throw new Exception('Missing session token');
            }
            
            // Validate session
            $auth = new PartnerAuth();
            $partner = $auth->validateSession($input['session_token']);
            if (!$partner) {
                throw new Exception('Invalid session');
            }
            
            $status = $input['status'] ?? null;
            $limit = $input['limit'] ?? 20;
            
            $codes = $codeManager->getPartnerPromotionCodes($partner['id'], $status, $limit);
            
            echo json_encode([
                'success' => true,
                'codes' => $codes
            ]);
            break;
            
        case 'get_code_management':
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('Method not allowed');
            }
            
            $input = json_decode(file_get_contents('php://input'), true);
            
            if (!isset($input['session_token'])) {
                throw new Exception('Missing session token');
            }
            
            // Validate session
            $auth = new PartnerAuth();
            $partner = $auth->validateSession($input['session_token']);
            if (!$partner) {
                throw new Exception('Invalid session');
            }
            
            $status = $input['status'] ?? null;
            $limit = $input['limit'] ?? 20;
            
            $codes = $codeManager->getPartnerCodeManagement($partner['id'], $status, $limit);
            
            echo json_encode([
                'success' => true,
                'codes' => $codes
            ]);
            break;
            
        case 'approve_code':
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('Method not allowed');
            }
            
            $input = json_decode(file_get_contents('php://input'), true);
            
            $requiredFields = ['session_token', 'code_id', 'learner_phone'];
            foreach ($requiredFields as $field) {
                if (!isset($input[$field])) {
                    throw new Exception("Missing required field: $field");
                }
            }
            
            // Validate session
            $auth = new PartnerAuth();
            $partner = $auth->validateSession($input['session_token']);
            if (!$partner) {
                throw new Exception('Invalid session');
            }
            
            $result = $codeManager->approvePromotionCode($input['code_id'], $input['learner_phone']);
            echo json_encode($result);
            break;
            
        case 'reject_code':
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('Method not allowed');
            }
            
            $input = json_decode(file_get_contents('php://input'), true);
            
            $requiredFields = ['session_token', 'code_id'];
            foreach ($requiredFields as $field) {
                if (!isset($input[$field])) {
                    throw new Exception("Missing required field: $field");
                }
            }
            
            // Validate session
            $auth = new PartnerAuth();
            $partner = $auth->validateSession($input['session_token']);
            if (!$partner) {
                throw new Exception('Invalid session');
            }
            
            $result = $codeManager->rejectPromotionCode($input['code_id']);
            echo json_encode($result);
            break;
            
        case 'delete_code':
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('Method not allowed');
            }
            
            $input = json_decode(file_get_contents('php://input'), true);
            
            $requiredFields = ['session_token', 'code_id'];
            foreach ($requiredFields as $field) {
                if (!isset($input[$field])) {
                    throw new Exception("Missing required field: $field");
                }
            }
            
            // Validate session
            $auth = new PartnerAuth();
            $partner = $auth->validateSession($input['session_token']);
            if (!$partner) {
                throw new Exception('Invalid session');
            }
            
            $result = $codeManager->deletePromotionCode($input['code_id']);
            echo json_encode($result);
            break;
            
        case 'get_stats':
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('Method not allowed');
            }
            
            $input = json_decode(file_get_contents('php://input'), true);
            
            if (!isset($input['session_token'])) {
                throw new Exception('Missing session token');
            }
            
            // Validate session
            $auth = new PartnerAuth();
            $partner = $auth->validateSession($input['session_token']);
            if (!$partner) {
                throw new Exception('Invalid session');
            }
            
            $stats = $codeManager->getPartnerCodeStats($partner['id']);
            
            echo json_encode([
                'success' => true,
                'stats' => $stats
            ]);
            break;
            
        case 'validate_code':
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('Method not allowed');
            }
            
            $input = json_decode(file_get_contents('php://input'), true);
            
            if (!isset($input['code'])) {
                throw new Exception('Missing code');
            }
            
            $result = $codeManager->validatePromotionCode($input['code']);
            echo json_encode($result);
            break;
            
        case 'decode_code':
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('Method not allowed');
            }
            
            $input = json_decode(file_get_contents('php://input'), true);
            
            if (!isset($input['code'])) {
                throw new Exception('Missing required field: code');
            }
            
            $result = $codeManager->decodePromotionCode($input['code']);
            echo json_encode($result);
            break;
            
        default:
            throw new Exception('Invalid endpoint');
    }
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>