<?php
/**
 * Admin Login API
 * Handles admin authentication
 */

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

require_once '../classes/admin_auth.php';

$data = json_decode(file_get_contents('php://input'), true);
$username = $data['username'] ?? '';
$password = $data['password'] ?? '';

$adminAuth = new AdminAuth();
$result = $adminAuth->loginAdmin($username, $password);

echo json_encode($result);
