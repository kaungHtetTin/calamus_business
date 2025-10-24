<?php
require_once __DIR__ . '/../classes/autoload.php';

header('Content-Type: application/json');

// Get request method and data
$method = $_SERVER['REQUEST_METHOD'];
$input = json_decode(file_get_contents('php://input'), true);

// Initialize classes
$codeManager = new PromotionCodeManager();
$commissionManager = new CommissionManager();

// Route requests
$endpoint = $_GET['endpoint'] ?? '';

switch ($endpoint) {
    case 'validate_code':
        if ($method === 'POST') {
            $code = $input['code'] ?? '';
            
            if (empty($code)) {
                echo json_encode(['valid' => false, 'message' => 'Code is required']);
                break;
            }
            
            $result = $codeManager->validatePromotionCode($code);
            echo json_encode($result);
        }
        break;
        
    case 'use_code':
        if ($method === 'POST') {
            $code = $input['code'] ?? '';
            $learnerPhone = $input['learner_phone'] ?? '';
            
            if (empty($code) || empty($learnerPhone)) {
                echo json_encode(['success' => false, 'message' => 'Code and learner phone are required']);
                break;
            }
            
            $result = $codeManager->usePromotionCode($code, $learnerPhone);
            echo json_encode($result);
        }
        break;
        
    case 'process_vip_with_code':
        if ($method === 'POST') {
            $code = $input['code'] ?? '';
            $learnerPhone = $input['learner_phone'] ?? '';
            $courseId = $input['course_id'] ?? '';
            $amount = $input['amount'] ?? 0;
            
            if (empty($code) || empty($learnerPhone) || empty($courseId)) {
                echo json_encode(['success' => false, 'message' => 'Code, learner phone, and course ID are required']);
                break;
            }
            
            // First validate the code
            $validation = $codeManager->validatePromotionCode($code);
            
            if (!$validation['valid']) {
                echo json_encode($validation);
                break;
            }
            
            $codeData = $validation['code_data'];
            
            // Process VIP subscription with commission tracking
            $result = $commissionManager->processVipSubscriptionWithCode($learnerPhone, $courseId, $amount, $codeData);
            echo json_encode($result);
        }
        break;
        
    case 'process_package_with_code':
        if ($method === 'POST') {
            $code = $input['code'] ?? '';
            $learnerPhone = $input['learner_phone'] ?? '';
            $packageId = $input['package_id'] ?? '';
            $amount = $input['amount'] ?? 0;
            
            if (empty($code) || empty($learnerPhone) || empty($packageId)) {
                echo json_encode(['success' => false, 'message' => 'Code, learner phone, and package ID are required']);
                break;
            }
            
            // First validate the code
            $validation = $codeManager->validatePromotionCode($code);
            
            if (!$validation['valid']) {
                echo json_encode($validation);
                break;
            }
            
            $codeData = $validation['code_data'];
            
            // Process package purchase with commission tracking
            $result = $commissionManager->processPackagePurchaseWithCode($learnerPhone, $packageId, $amount, $codeData);
            echo json_encode($result);
        }
        break;
        
    case 'get_code_details':
        if ($method === 'POST') {
            $code = $input['code'] ?? '';
            
            if (empty($code)) {
                echo json_encode(['success' => false, 'message' => 'Code is required']);
                break;
            }
            
            $validation = $codeManager->validatePromotionCode($code);
            
            if ($validation['valid']) {
                $codeData = $validation['code_data'];
                
                // Get additional details
                $details = [
                    'code' => $codeData['code'],
                    'partner_name' => $codeData['contact_name'],
                    'partner_company' => $codeData['company_name'],
                    'code_type' => $codeData['code_type'],
                    'target_course_id' => $codeData['target_course_id'],
                    'target_major' => $codeData['target_major'],
                    'commission_rate' => $codeData['commission_rate'],
                    'generated_for' => $codeData['generated_for'],
                    'expired_at' => $codeData['expired_at'],
                    'created_at' => $codeData['created_at']
                ];
                
                echo json_encode(['success' => true, 'details' => $details]);
            } else {
                echo json_encode($validation);
            }
        }
        break;
        
    case 'get_all_codes':
        if ($method === 'POST') {
            // This endpoint is for admin/customer service to view all codes
            $status = $input['status'] ?? null;
            $partnerId = $input['partner_id'] ?? null;
            $limit = $input['limit'] ?? 100;
            
            $db = new Database();
            
            $whereClause = "WHERE 1=1";
            if ($status) {
                $whereClause .= " AND pc.status = '$status'";
            }
            if ($partnerId) {
                $whereClause .= " AND pc.partner_id = '$partnerId'";
            }
            
            $query = "SELECT pc.*, p.contact_name, p.company_name, c.title as course_title 
                     FROM promotion_codes pc 
                     JOIN partners p ON pc.partner_id = p.id 
                     LEFT JOIN courses c ON pc.target_course_id = c.course_id 
                     $whereClause 
                     ORDER BY pc.created_at DESC 
                     LIMIT $limit";
            
            $codes = $db->read($query);
            echo json_encode(['success' => true, 'codes' => $codes]);
        }
        break;
        
    default:
        echo json_encode(['success' => false, 'message' => 'Invalid endpoint']);
        break;
}
?>
