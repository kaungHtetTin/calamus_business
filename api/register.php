<?php
require_once __DIR__ . '/../classes/autoload.php';

header('Content-Type: application/json');

// Get request method and data
$method = $_SERVER['REQUEST_METHOD'];
$input = json_decode(file_get_contents('php://input'), true);

// Initialize authentication class
$auth = new PartnerAuth();

// Route requests
$endpoint = $_GET['endpoint'] ?? '';

switch ($endpoint) {
    case 'register':
        if ($method === 'POST') {
            // Validate required fields
            $requiredFields = ['company_name', 'contact_name', 'email', 'phone', 'password', 'address', 'city', 'state', 'national_id_card_number'];
            $missingFields = [];
            
            foreach ($requiredFields as $field) {
                if (empty($input[$field])) {
                    $missingFields[] = $field;
                }
            }
            
            if (!empty($missingFields)) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Missing required fields: ' . implode(', ', $missingFields)
                ]);
                break;
            }
            
            // Validate email format
            if (!filter_var($input['email'], FILTER_VALIDATE_EMAIL)) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Invalid email format'
                ]);
                break;
            }
            
            // Validate password strength
            if (strlen($input['password']) < 8) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Password must be at least 8 characters long'
                ]);
                break;
            }
            
            // Check if email already exists
            $existingPartner = $auth->getPartnerByEmail($input['email']);
            if ($existingPartner) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Email already registered'
                ]);
                break;
            }
            
            // Prepare partner data
            $partnerData = [
                'company_name' => trim($input['company_name']),
                'contact_name' => trim($input['contact_name']),
                'email' => trim($input['email']),
                'phone' => trim($input['phone']),
                'password' => $input['password'],
                'website' => isset($input['website']) ? trim($input['website']) : '',
                'description' => isset($input['description']) ? trim($input['description']) : '',
                'commission_rate' => isset($input['commission_rate']) ? floatval($input['commission_rate']) : 10.0,
                'status' => 'pending', // New partners start as pending
                'address' => trim($input['address']),
                'city' => trim($input['city']),
                'state' => trim($input['state']),
                'national_id_card_number' => trim($input['national_id_card_number'])
            ];
            
            // Register the partner (this will send welcome email automatically)
            $result = $auth->registerPartner($partnerData);
            
            echo json_encode($result);
        } else {
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
        }
        break;
        
    case 'verify_email':
        if ($method === 'POST') {
            $email = $input['email'] ?? '';
            $verificationCode = $input['verification_code'] ?? '';
            
            if (empty($email) || empty($verificationCode)) {
                echo json_encode(['success' => false, 'message' => 'Email and verification code are required']);
                break;
            }
            
            $result = $auth->verifyEmail($email, $verificationCode);
            echo json_encode($result);
        } else {
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
        }
        break;
        
    case 'resend_verification':
        if ($method === 'POST') {
            $email = $input['email'] ?? '';
            
            if (empty($email)) {
                echo json_encode(['success' => false, 'message' => 'Email is required']);
                break;
            }
            
            $result = $auth->resendVerificationEmail($email);
            echo json_encode($result);
        } else {
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
        }
        break;
        
    case 'check_email':
        if ($method === 'POST') {
            $email = $input['email'] ?? '';
            
            if (empty($email)) {
                echo json_encode(['success' => false, 'message' => 'Email is required']);
                break;
            }
            
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                echo json_encode(['success' => false, 'message' => 'Invalid email format']);
                break;
            }
            
            $existingPartner = $auth->getPartnerByEmail($email);
            
            echo json_encode([
                'success' => true,
                'available' => !$existingPartner,
                'message' => $existingPartner ? 'Email already registered' : 'Email is available'
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
        }
        break;
        
        
    case 'get_partner_info':
        if ($method === 'POST') {
            $partnerId = $input['partner_id'] ?? '';
            
            if (empty($partnerId)) {
                echo json_encode(['success' => false, 'message' => 'Partner ID is required']);
                break;
            }
            
            $partner = $auth->getPartnerById($partnerId);
            
            if ($partner) {
                // Remove sensitive information
                unset($partner['password']);
                unset($partner['verification_code']);
                
                echo json_encode([
                    'success' => true,
                    'partner' => $partner
                ]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Partner not found']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
        }
        break;
        
    case 'update_partner':
        if ($method === 'POST') {
            $partnerId = $input['partner_id'] ?? '';
            $updateData = $input['update_data'] ?? [];
            
            if (empty($partnerId) || empty($updateData)) {
                echo json_encode(['success' => false, 'message' => 'Partner ID and update data are required']);
                break;
            }
            
            // Validate partner exists
            $partner = $auth->getPartnerById($partnerId);
            if (!$partner) {
                echo json_encode(['success' => false, 'message' => 'Partner not found']);
                break;
            }
            
            // Allowed fields for update
            $allowedFields = [
                'company_name', 'contact_name', 'phone', 'website', 'description'
            ];
            
            $filteredData = [];
            foreach ($allowedFields as $field) {
                if (isset($updateData[$field])) {
                    $filteredData[$field] = trim($updateData[$field]);
                }
            }
            
            if (empty($filteredData)) {
                echo json_encode(['success' => false, 'message' => 'No valid fields to update']);
                break;
            }
            
            $result = $auth->updatePartner($partnerId, $filteredData);
            echo json_encode($result);
        } else {
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
        }
        break;
        
    case 'change_password':
        if ($method === 'POST') {
            $partnerId = $input['partner_id'] ?? '';
            $currentPassword = $input['current_password'] ?? '';
            $newPassword = $input['new_password'] ?? '';
            
            if (empty($partnerId) || empty($currentPassword) || empty($newPassword)) {
                echo json_encode(['success' => false, 'message' => 'All password fields are required']);
                break;
            }
            
            if (strlen($newPassword) < 8) {
                echo json_encode(['success' => false, 'message' => 'New password must be at least 8 characters long']);
                break;
            }
            
            $result = $auth->changePassword($partnerId, $currentPassword, $newPassword);
            echo json_encode($result);
        } else {
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
        }
        break;
        
    default:
        echo json_encode(['success' => false, 'message' => 'Invalid endpoint']);
        break;
}
?>
