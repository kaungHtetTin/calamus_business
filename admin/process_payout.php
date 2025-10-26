<?php
/**
 * Process Payout Handler
 * Handles payout processing for partners
 */

require_once '../classes/admin_auth.php';

$adminAuth = new AdminAuth();

// Check if admin is logged in
if (!$adminAuth->isLoggedIn()) {
    header('Location: login.php');
    exit();
}

// Check if partner ID and amount are provided
if (!isset($_POST['partner_id']) || empty($_POST['partner_id']) || !isset($_POST['amount'])) {
    header('Location: payout_logs.php?error=Invalid request');
    exit();
}

$partnerId = $_POST['partner_id'];
$amount = $_POST['amount'];

// Process the payout
$result = $adminAuth->processPayout($partnerId);

if ($result['success']) {
    header('Location: payout_logs.php?success=' . urlencode('Payout processed successfully for ' . number_format($amount, 2) . ' MMK'));
} else {
    header('Location: payout_logs.php?error=' . urlencode($result['message']));
}
exit();

