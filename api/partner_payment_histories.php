<?php
require_once '../classes/autoload.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

$method = $_SERVER['REQUEST_METHOD'];
$input = json_decode(file_get_contents('php://input'), true);

// Get session token from headers or input
$sessionToken = $input['session_token'] ?? $_SERVER['HTTP_AUTHORIZATION'] ?? '';

// Validate session
$auth = new PartnerAuth();
$sessionValidation = $auth->validateSession($sessionToken);

if (!$sessionValidation['success']) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$currentPartner = $sessionValidation['partner'];
$paymentHistoriesManager = new PartnerPaymentHistoriesManager();

$action = $input['action'] ?? '';

switch ($action) {
    case 'get_payment_histories':
        if ($method === 'POST') {
            $status = $input['status'] ?? null;
            $limit = (int)($input['limit'] ?? 20);
            $offset = (int)($input['offset'] ?? 0);
            $startDate = $input['start_date'] ?? null;
            $endDate = $input['end_date'] ?? null;
            
            $paymentHistories = $paymentHistoriesManager->getPartnerPaymentHistories(
                $currentPartner['id'], 
                $status, 
                $limit, 
                $offset, 
                $startDate, 
                $endDate
            );
            
            $totalCount = $paymentHistoriesManager->getPartnerPaymentHistoriesCount(
                $currentPartner['id'], 
                $status, 
                $startDate, 
                $endDate
            );
            
            echo json_encode([
                'success' => true,
                'data' => $paymentHistories,
                'total_count' => $totalCount,
                'has_more' => ($offset + $limit) < $totalCount
            ]);
        }
        break;
        
    case 'get_payment_stats':
        if ($method === 'POST') {
            $status = $input['status'] ?? null;
            $startDate = $input['start_date'] ?? null;
            $endDate = $input['end_date'] ?? null;
            
            $stats = $paymentHistoriesManager->getPartnerPaymentStats(
                $currentPartner['id'], 
                $status, 
                $startDate, 
                $endDate
            );
            
            echo json_encode([
                'success' => true,
                'stats' => $stats
            ]);
        }
        break;
        
    case 'update_payment_status':
        if ($method === 'POST') {
            $paymentId = $input['payment_id'] ?? '';
            $status = $input['status'] ?? '';
            
            if (empty($paymentId) || empty($status)) {
                echo json_encode(['success' => false, 'message' => 'Payment ID and status are required']);
                break;
            }
            
            if (!in_array($status, ['received', 'rejected'])) {
                echo json_encode(['success' => false, 'message' => 'Invalid status']);
                break;
            }
            
            $result = $paymentHistoriesManager->updatePaymentStatus($paymentId, $status, $currentPartner['id']);
            echo json_encode($result);
        }
        break;
        
    case 'get_payment_details':
        if ($method === 'POST') {
            $paymentId = $input['payment_id'] ?? '';
            
            if (empty($paymentId)) {
                echo json_encode(['success' => false, 'message' => 'Payment ID is required']);
                break;
            }
            
            $payment = $paymentHistoriesManager->getPaymentHistoryById($paymentId, $currentPartner['id']);
            
            if ($payment) {
                echo json_encode([
                    'success' => true,
                    'payment' => $payment
                ]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Payment not found']);
            }
        }
        break;
        
    default:
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
        break;
}
?>
