<?php
/**
 * Delete Partner Handler
 * Handles partner deletion via form submission
 */

require_once '../classes/admin_auth.php';

$adminAuth = new AdminAuth();

// Check if admin is logged in
if (!$adminAuth->isLoggedIn()) {
    header('Location: login.php');
    exit();
}

// Check if partner ID is provided
if (!isset($_POST['partner_id']) || empty($_POST['partner_id'])) {
    header('Location: index.php?error=No partner ID provided');
    exit();
}

$partnerId = $_POST['partner_id'];

// Delete the partner
$result = $adminAuth->deletePartner($partnerId);

if ($result['success']) {
    header('Location: index.php?success=Partner deleted successfully');
} else {
    header('Location: index.php?error=' . urlencode($result['message']));
}
exit();
