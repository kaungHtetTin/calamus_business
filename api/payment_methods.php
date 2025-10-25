<?php
require_once __DIR__ . '/../classes/autoload.php';
header('Content-Type: application/json');

// Get endpoint from URL parameter
$endpoint = $_GET['endpoint'] ?? '';

// Get session token from request
$sessionToken = null;

// Check for session token in different ways
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $sessionToken = $input['session_token'] ?? null;
} else {
    $sessionToken = $_GET['session_token'] ?? null;
}

// Validate session token
if (empty($sessionToken)) {
    echo json_encode(['success' => false, 'message' => 'Session token is required']);
    exit;
}

$auth = new PartnerAuth();
$sessionResult = $auth->validateSession($sessionToken);

if (!$sessionResult['success']) {
    echo json_encode(['success' => false, 'message' => 'Invalid session token']);
    exit;
}

$partner = $sessionResult['partner'];
$paymentMethodsManager = new PaymentMethodsManager();

switch ($endpoint) {
    case 'get_payment_methods':
        $paymentMethods = $paymentMethodsManager->getPartnerPaymentMethods($partner['id']);
        echo json_encode(['success' => true, 'data' => $paymentMethods]);
        break;
        
    case 'get_payment_method':
        $paymentMethodId = $_GET['id'] ?? '';
        if (empty($paymentMethodId)) {
            echo json_encode(['success' => false, 'message' => 'Payment method ID is required']);
            break;
        }
        
        $paymentMethod = $paymentMethodsManager->getPaymentMethod($paymentMethodId, $partner['id']);
        if ($paymentMethod) {
            echo json_encode(['success' => true, 'data' => $paymentMethod]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Payment method not found']);
        }
        break;
        
    case 'add_payment_method':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Only POST method allowed']);
            break;
        }
        
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!$input) {
            echo json_encode(['success' => false, 'message' => 'Invalid JSON input']);
            break;
        }
        
        $paymentMethod = $input['payment_method'] ?? '';
        $accountNumber = $input['account_number'] ?? '';
        $accountName = $input['account_name'] ?? '';
        
        $result = $paymentMethodsManager->addPaymentMethod($partner['id'], $paymentMethod, $accountNumber, $accountName);
        echo json_encode($result);
        break;
        
    case 'update_payment_method':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Only POST method allowed']);
            break;
        }
        
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!$input) {
            echo json_encode(['success' => false, 'message' => 'Invalid JSON input']);
            break;
        }
        
        $paymentMethodId = $input['id'] ?? '';
        $paymentMethod = $input['payment_method'] ?? '';
        $accountNumber = $input['account_number'] ?? '';
        $accountName = $input['account_name'] ?? '';
        
        if (empty($paymentMethodId)) {
            echo json_encode(['success' => false, 'message' => 'Payment method ID is required']);
            break;
        }
        
        $result = $paymentMethodsManager->updatePaymentMethod($paymentMethodId, $partner['id'], $paymentMethod, $accountNumber, $accountName);
        echo json_encode($result);
        break;
        
    case 'delete_payment_method':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Only POST method allowed']);
            break;
        }
        
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!$input) {
            echo json_encode(['success' => false, 'message' => 'Invalid JSON input']);
            break;
        }
        
        $paymentMethodId = $input['id'] ?? '';
        
        if (empty($paymentMethodId)) {
            echo json_encode(['success' => false, 'message' => 'Payment method ID is required']);
            break;
        }
        
        $result = $paymentMethodsManager->deletePaymentMethod($paymentMethodId, $partner['id']);
        echo json_encode($result);
        break;
        
    case 'get_stats':
        $stats = $paymentMethodsManager->getPaymentMethodStats($partner['id']);
        echo json_encode(['success' => true, 'data' => $stats]);
        break;
        
    default:
        echo json_encode(['success' => false, 'message' => 'Invalid endpoint']);
        break;
}
