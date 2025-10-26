<?php
/**
 * Admin Logout
 */

require_once '../classes/admin_auth.php';

$adminAuth = new AdminAuth();
$adminAuth->logout();

header('Location: login.php');
exit();
