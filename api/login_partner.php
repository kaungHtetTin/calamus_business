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

$method = $_SERVER['REQUEST_METHOD'];
$endpoint = $_GET['endpoint'] ?? '';

$auth = new PartnerAuth();

// Get input data
$input = json_decode(file_get_contents('php://input'), true);

switch ($endpoint) {
    case 'login':
        if ($method === 'POST') {
            $email = $input['email'] ?? '';
            $password = $input['password'] ?? '';
            $remember = $input['remember'] ?? false;
            
            // Validate input
            if (empty($email) || empty($password)) {
                echo json_encode([
                    'success' => false, 
                    'message' => 'Email and password are required'
                ]);
                break;
            }
            
            // Validate email format
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                echo json_encode([
                    'success' => false, 
                    'message' => 'Please enter a valid email address'
                ]);
                break;
            }
            
            // Attempt login
            $result = $auth->loginPartner($email, $password);
            
            if ($result['success']) {
                // Set session if remember is true
                if ($remember) {
                    // Extend session to 30 days
                    $extendedExpiry = date('Y-m-d H:i:s', strtotime('+30 days'));
                    $auth->extendSession($result['session_token'], $extendedExpiry);
                }
                
                // Log successful login
                $auth->logLoginAttempt($email, true, 'Login successful');
                
                echo json_encode([
                    'success' => true,
                    'message' => $result['message'],
                    'session_token' => $result['session_token'],
                    'partner' => $result['partner'],
                    'redirect_url' => '../index.php'
                ]);
            } else {
                // Log failed login
                $auth->logLoginAttempt($email, false, $result['message']);
                
                echo json_encode([
                    'success' => false,
                    'message' => $result['message']
                ]);
            }
        } else {
            echo json_encode([
                'success' => false, 
                'message' => 'Method not allowed'
            ]);
        }
        break;
        
    case 'logout':
        if ($method === 'POST') {
            $sessionToken = $input['session_token'] ?? '';
            
            if (empty($sessionToken)) {
                echo json_encode([
                    'success' => false, 
                    'message' => 'Session token is required'
                ]);
                break;
            }
            
            $result = $auth->logout($sessionToken);
            
            if ($result) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Logged out successfully',
                    'redirect_url' => '../partner_login.php'
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Logout failed'
                ]);
            }
        } else {
            echo json_encode([
                'success' => false, 
                'message' => 'Method not allowed'
            ]);
        }
        break;
        
    case 'validate_session':
        if ($method === 'POST') {
            $sessionToken = $input['session_token'] ?? '';
            
            if (empty($sessionToken)) {
                echo json_encode([
                    'success' => false, 
                    'message' => 'Session token is required'
                ]);
                break;
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
                'message' => 'Method not allowed'
            ]);
        }
        break;
        
    case 'forgot_password':
        if ($method === 'POST') {
            $email = $input['email'] ?? '';
            
            if (empty($email)) {
                echo json_encode([
                    'success' => false, 
                    'message' => 'Email is required'
                ]);
                break;
            }
            
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                echo json_encode([
                    'success' => false, 
                    'message' => 'Please enter a valid email address'
                ]);
                break;
            }
            
            $result = $auth->forgotPassword($email);
            echo json_encode($result);
        } else {
            echo json_encode([
                'success' => false, 
                'message' => 'Method not allowed'
            ]);
        }
        break;
        
    case 'reset_password':
        if ($method === 'POST') {
            $token = $input['token'] ?? '';
            $newPassword = $input['new_password'] ?? '';
            $confirmPassword = $input['confirm_password'] ?? '';
            
            if (empty($token) || empty($newPassword) || empty($confirmPassword)) {
                echo json_encode([
                    'success' => false, 
                    'message' => 'Token, new password, and confirm password are required'
                ]);
                break;
            }
            
            if ($newPassword !== $confirmPassword) {
                echo json_encode([
                    'success' => false, 
                    'message' => 'Passwords do not match'
                ]);
                break;
            }
            
            // Validate password strength
            if (strlen($newPassword) < 8) {
                echo json_encode([
                    'success' => false, 
                    'message' => 'Password must be at least 8 characters long'
                ]);
                break;
            }
            
            $result = $auth->resetPassword($token, $newPassword);
            echo json_encode($result);
        } else {
            echo json_encode([
                'success' => false, 
                'message' => 'Method not allowed'
            ]);
        }
        break;
        
    case 'change_password':
        if ($method === 'POST') {
            $sessionToken = $input['session_token'] ?? '';
            $currentPassword = $input['current_password'] ?? '';
            $newPassword = $input['new_password'] ?? '';
            $confirmPassword = $input['confirm_password'] ?? '';
            
            if (empty($sessionToken) || empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
                echo json_encode([
                    'success' => false, 
                    'message' => 'All fields are required'
                ]);
                break;
            }
            
            // Validate session first
            $sessionResult = $auth->validateSession($sessionToken);
            if (!$sessionResult['success']) {
                echo json_encode([
                    'success' => false, 
                    'message' => 'Invalid session'
                ]);
                break;
            }
            
            if ($newPassword !== $confirmPassword) {
                echo json_encode([
                    'success' => false, 
                    'message' => 'New passwords do not match'
                ]);
                break;
            }
            
            // Validate password strength
            if (strlen($newPassword) < 8) {
                echo json_encode([
                    'success' => false, 
                    'message' => 'New password must be at least 8 characters long'
                ]);
                break;
            }
            
            $partnerId = $sessionResult['partner']['id'];
            $result = $auth->changePassword($partnerId, $currentPassword, $newPassword);
            echo json_encode($result);
        } else {
            echo json_encode([
                'success' => false, 
                'message' => 'Method not allowed'
            ]);
        }
        break;
        
    case 'get_partner_info':
        if ($method === 'POST') {
            $sessionToken = $input['session_token'] ?? '';
            
            if (empty($sessionToken)) {
                echo json_encode([
                    'success' => false, 
                    'message' => 'Session token is required'
                ]);
                break;
            }
            
            $sessionResult = $auth->validateSession($sessionToken);
            if (!$sessionResult['success']) {
                echo json_encode([
                    'success' => false, 
                    'message' => 'Invalid session'
                ]);
                break;
            }
            
            $partner = $sessionResult['partner'];
            
            // Remove sensitive information
            unset($partner['password']);
            unset($partner['reset_token']);
            unset($partner['verification_code']);
            
            echo json_encode([
                'success' => true,
                'partner' => $partner
            ]);
        } else {
            echo json_encode([
                'success' => false, 
                'message' => 'Method not allowed'
            ]);
        }
        break;
        
    case 'update_profile':
        if ($method === 'POST') {
            $sessionToken = $input['session_token'] ?? '';
            $updateData = $input['update_data'] ?? [];
            
            if (empty($sessionToken)) {
                echo json_encode([
                    'success' => false, 
                    'message' => 'Session token is required'
                ]);
                break;
            }
            
            // Validate session first
            $sessionResult = $auth->validateSession($sessionToken);
            if (!$sessionResult['success']) {
                echo json_encode([
                    'success' => false, 
                    'message' => 'Invalid session'
                ]);
                break;
            }
            
            $partnerId = $sessionResult['partner']['id'];
            
            // Remove sensitive fields that shouldn't be updated via this endpoint
            $allowedFields = ['company_name', 'contact_name', 'phone', 'website', 'description', 'payment_method', 'payment_details'];
            $filteredData = array_intersect_key($updateData, array_flip($allowedFields));
            
            if (empty($filteredData)) {
                echo json_encode([
                    'success' => false, 
                    'message' => 'No valid fields to update'
                ]);
                break;
            }
            
            $result = $auth->updatePartner($partnerId, $filteredData);
            echo json_encode($result);
        } else {
            echo json_encode([
                'success' => false, 
                'message' => 'Method not allowed'
            ]);
        }
        break;
        
    default:
        echo json_encode([
            'success' => false, 
            'message' => 'Invalid API endpoint'
        ]);
        break;
}
?>
