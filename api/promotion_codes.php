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
            if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
                throw new Exception('Method not allowed');
            }
            
            $sessionToken = $_GET['session_token'] ?? '';
            if (empty($sessionToken)) {
                throw new Exception('Session token is required');
            }
            
            // Validate session
            $auth = new PartnerAuth();
            $sessionResult = $auth->validateSession($sessionToken);
            if (!$sessionResult['success']) {
                throw new Exception('Invalid session');
            }
            $partner = $sessionResult['partner'];
            
            $status = $_GET['status'] ?? null;
            $limit = (int)($_GET['limit'] ?? 20);
            $page = (int)($_GET['page'] ?? 1);
            $offset = ($page - 1) * $limit;
            
            $codes = $codeManager->getPartnerPromotionCodes($partner['id'], $status, $limit, $offset);
            $totalCount = $codeManager->getPartnerPromotionCodesCount($partner['id'], $status);
            
            echo json_encode([
                'success' => true,
                'data' => $codes,
                'pagination' => [
                    'current_page' => $page,
                    'per_page' => $limit,
                    'total_count' => $totalCount,
                    'total_pages' => ceil($totalCount / $limit),
                    'has_next' => $page < ceil($totalCount / $limit),
                    'has_prev' => $page > 1
                ]
            ]);
            break;
            
        case 'get_code_management':
            if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
                throw new Exception('Method not allowed');
            }
            
            $sessionToken = $_GET['session_token'] ?? '';
            if (empty($sessionToken)) {
                throw new Exception('Session token is required');
            }
            
            // Validate session
            $auth = new PartnerAuth();
            $sessionResult = $auth->validateSession($sessionToken);
            if (!$sessionResult['success']) {
                throw new Exception('Invalid session');
            }
            $partner = $sessionResult['partner'];
            
            $status = $_GET['status'] ?? null;
            $limit = (int)($_GET['limit'] ?? 20);
            $page = (int)($_GET['page'] ?? 1);
            $offset = ($page - 1) * $limit;
            
            $codes = $codeManager->getPartnerCodeManagement($partner['id'], $status, $limit, $offset);
            $totalCount = $codeManager->getPartnerCodeManagementCount($partner['id'], $status);
            
            echo json_encode([
                'success' => true,
                'data' => $codes,
                'pagination' => [
                    'current_page' => $page,
                    'per_page' => $limit,
                    'total_count' => $totalCount,
                    'total_pages' => ceil($totalCount / $limit),
                    'has_next' => $page < ceil($totalCount / $limit),
                    'has_prev' => $page > 1
                ]
            ]);
            break;
            
        case 'get_earning_history':
            if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
                throw new Exception('Method not allowed');
            }
            
            $sessionToken = $_GET['session_token'] ?? '';
            if (empty($sessionToken)) {
                throw new Exception('Session token is required');
            }
            
            // Validate session
            $auth = new PartnerAuth();
            $sessionResult = $auth->validateSession($sessionToken);
            if (!$sessionResult['success']) {
                throw new Exception('Invalid session');
            }
            $partner = $sessionResult['partner'];
            
            $limit = $_GET['limit'] ?? 50;
            $earningHistory = $codeManager->getPartnerEarningHistory($partner['id'], $limit);
            
            echo json_encode([
                'success' => true,
                'data' => $earningHistory
            ]);
            break;
            
        case 'get_earning_stats':
            if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
                throw new Exception('Method not allowed');
            }
            
            $sessionToken = $_GET['session_token'] ?? '';
            if (empty($sessionToken)) {
                throw new Exception('Session token is required');
            }
            
            // Validate session
            $auth = new PartnerAuth();
            $sessionResult = $auth->validateSession($sessionToken);
            if (!$sessionResult['success']) {
                throw new Exception('Invalid session');
            }
            $partner = $sessionResult['partner'];
            
            $earningStats = $codeManager->getPartnerEarningStats($partner['id']);
            
            echo json_encode([
                'success' => true,
                'data' => $earningStats
            ]);
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