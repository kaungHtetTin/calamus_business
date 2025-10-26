<?php
/**
 * Admin Get Partners API
 */

header('Content-Type: application/json');

// Check if admin is logged in
require_once '../classes/admin_auth.php';
$adminAuth = new AdminAuth();

if (!$adminAuth->isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$page = $_GET['page'] ?? 1;
$limit = $_GET['limit'] ?? 20;

$result = $adminAuth->getAllPartners($page, $limit);
echo json_encode($result);
