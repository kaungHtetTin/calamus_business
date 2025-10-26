<?php
/**
 * Admin Delete Partner API
 */

header('Content-Type: application/json');

// Check if admin is logged in
require_once '../classes/admin_auth.php';
$adminAuth = new AdminAuth();

if (!$adminAuth->isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$partnerId = $data['partner_id'] ?? '';

if (empty($partnerId)) {
    echo json_encode(['success' => false, 'message' => 'Partner ID required']);
    exit;
}

$result = $adminAuth->deletePartner($partnerId);
echo json_encode($result);
