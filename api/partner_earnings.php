<?php
require_once __DIR__ . '/../classes/autoload.php';

header('Content-Type: application/json');

try {
    $endpoint = $_GET['endpoint'] ?? '';
    $method = $_SERVER['REQUEST_METHOD'];
    
    $earningsManager = new PartnerEarningsManager();
    
    switch ($endpoint) {
        case 'get_earning_history':
            if ($method !== 'GET') {
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
            
            $limit = (int)($_GET['limit'] ?? 20);
            $page = (int)($_GET['page'] ?? 1);
            $offset = ($page - 1) * $limit;
            
            $earnings = $earningsManager->getPartnerEarnings($partner['id'], null, $limit, $offset);
            $totalCount = $earningsManager->getPartnerEarningsCount($partner['id'], null);
            
            echo json_encode([
                'success' => true,
                'data' => $earnings,
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
            
        case 'get_earning_stats':
            if ($method !== 'GET') {
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
            
            $stats = $earningsManager->getPartnerEarningStats($partner['id']);
            
            echo json_encode([
                'success' => true,
                'data' => $stats
            ]);
            break;
            
        default:
            throw new Exception('Invalid endpoint');
    }
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>
