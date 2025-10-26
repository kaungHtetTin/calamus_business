<?php
/**
 * Update Partner Status Handler
 * Handles partner status updates via form submission
 */

require_once '../classes/admin_auth.php';

$adminAuth = new AdminAuth();

// Check if admin is logged in
if (!$adminAuth->isLoggedIn()) {
    header('Location: login.php');
    exit();
}

// Check if partner ID and status are provided
if (!isset($_POST['partner_id']) || empty($_POST['partner_id']) || !isset($_POST['status'])) {
    header('Location: index.php?error=Invalid request');
    exit();
}

$partnerId = $_POST['partner_id'];
$status = $_POST['status'];

// Update partner status
$result = $adminAuth->updatePartnerStatus($partnerId, $status);

if ($result['success']) {
    header('Location: index.php?success=Partner status updated successfully');
} else {
    header('Location: index.php?error=' . urlencode($result['message']));
}
exit();
