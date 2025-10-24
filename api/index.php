<?php
require_once 'partner_auth.php';
require_once 'partner_dashboard.php';
require_once 'affiliate_tracker.php';

header('Content-Type: application/json');

// Get request method and data
$method = $_SERVER['REQUEST_METHOD'];
$input = json_decode(file_get_contents('php://input'), true);

// Initialize classes
$auth = new PartnerAuth();
$dashboard = new PartnerDashboard();
$tracker = new AffiliateTracker();

// Route requests
$endpoint = $_GET['endpoint'] ?? '';

switch ($endpoint) {
    case 'validate_session':
        if ($method === 'POST') {
            $sessionToken = $input['session_token'] ?? '';
            $result = $auth->validateSession($sessionToken);
            echo json_encode($result);
        }
        break;
        
    case 'dashboard_data':
        if ($method === 'POST') {
            $sessionToken = $input['session_token'] ?? '';
            $session = $auth->validateSession($sessionToken);
            
            if ($session['success']) {
                $data = $dashboard->getDashboardData($session['partner']['id']);
                echo json_encode(['success' => true, 'data' => $data]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Invalid session']);
            }
        }
        break;
        
    case 'create_affiliate_link':
        if ($method === 'POST') {
            $sessionToken = $input['session_token'] ?? '';
            $session = $auth->validateSession($sessionToken);
            
            if ($session['success']) {
                $partnerId = $session['partner']['id'];
                $result = $dashboard->createAffiliateLink(
                    $partnerId,
                    $input['campaign_name'],
                    $input['target_course_id'] ?? null,
                    $input['target_major'] ?? null,
                    $input['custom_url'] ?? ''
                );
                echo json_encode($result);
            } else {
                echo json_encode(['success' => false, 'message' => 'Invalid session']);
            }
        }
        break;
        
    case 'get_affiliate_links':
        if ($method === 'POST') {
            $sessionToken = $input['session_token'] ?? '';
            $session = $auth->validateSession($sessionToken);
            
            if ($session['success']) {
                $links = $dashboard->getAffiliateLinks($session['partner']['id']);
                echo json_encode(['success' => true, 'links' => $links]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Invalid session']);
            }
        }
        break;
        
    case 'get_conversions':
        if ($method === 'POST') {
            $sessionToken = $input['session_token'] ?? '';
            $session = $auth->validateSession($sessionToken);
            
            if ($session['success']) {
                $page = $input['page'] ?? 1;
                $conversions = $dashboard->getConversionHistory($session['partner']['id'], $page);
                echo json_encode(['success' => true, 'conversions' => $conversions]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Invalid session']);
            }
        }
        break;
        
    case 'get_payments':
        if ($method === 'POST') {
            $sessionToken = $input['session_token'] ?? '';
            $session = $auth->validateSession($sessionToken);
            
            if ($session['success']) {
                $payments = $dashboard->getPaymentHistory($session['partner']['id']);
                echo json_encode(['success' => true, 'payments' => $payments]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Invalid session']);
            }
        }
        break;
        
    case 'update_profile':
        if ($method === 'POST') {
            $sessionToken = $input['session_token'] ?? '';
            $session = $auth->validateSession($sessionToken);
            
            if ($session['success']) {
                $profileData = $input;
                unset($profileData['session_token']);
                $result = $dashboard->updateProfile($session['partner']['id'], $profileData);
                echo json_encode($result);
            } else {
                echo json_encode(['success' => false, 'message' => 'Invalid session']);
            }
        }
        break;
        
    case 'track_conversion':
        if ($method === 'POST') {
            $userId = $input['user_id'] ?? '';
            $conversionType = $input['conversion_type'] ?? 'vip_subscription';
            $conversionValue = $input['conversion_value'] ?? 0;
            
            $result = $tracker->trackConversion($userId, $conversionType, $conversionValue);
            echo json_encode($result);
        }
        break;
        
    case 'get_partner_stats':
        if ($method === 'POST') {
            $sessionToken = $input['session_token'] ?? '';
            $session = $auth->validateSession($sessionToken);
            
            if ($session['success']) {
                $period = $input['period'] ?? '30';
                $stats = $tracker->getPartnerStats($session['partner']['id'], $period);
                echo json_encode(['success' => true, 'stats' => $stats]);
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
