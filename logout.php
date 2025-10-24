<?php
session_start();
require_once 'classes/autoload.php';

// Initialize authentication
$auth = new PartnerAuth();

// Get session token
$sessionToken = $_SESSION['partner_session_token'] ?? '';

// Logout from database
if (!empty($sessionToken)) {
    $auth->logout($sessionToken);
}

// Clear all session data
session_destroy();

// Redirect to login page
header('Location: partner_login.php');
exit;
?>
