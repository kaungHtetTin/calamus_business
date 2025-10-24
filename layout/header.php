<?php
session_start();
require_once 'classes/autoload.php';

// Initialize authentication
$auth = new PartnerAuth();
$dashboard = new PartnerDashboard();
$codeManager = new PromotionCodeManager();

// Check if user is logged in
$sessionToken = $_SESSION['partner_session_token'] ?? '';

if (empty($sessionToken)) {
    // Check localStorage token via JavaScript
    $needsAuth = true;
} else {
    // Validate session
    $session = $auth->validateSession($sessionToken);
    if (!$session['success']) {
        $needsAuth = true;
        unset($_SESSION['partner_session_token']);
    } else {
        $currentPartner = $session['partner'];
        $needsAuth = false;
    }
}

// If not authenticated, redirect to login
if ($needsAuth) {
    header('Location: partner_login.php');
    exit;
}

// Get dashboard data
$dashboardData = $dashboard->getDashboardData($currentPartner['id']);

// Get promotion code data
$codeStats = $codeManager->getPartnerCodeStats($currentPartner['id']);
$recentCodes = $codeManager->getPartnerPromotionCodes($currentPartner['id'], null, 10);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? $pageTitle : 'Partner Dashboard'; ?> - <?php echo htmlspecialchars($currentPartner['contact_name']); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="css/app.css">
</head>
<body>
    <!-- Bootstrap Offcanvas for Mobile Navigation -->
    <div class="offcanvas offcanvas-start" tabindex="-1" id="mobileSidebar" aria-labelledby="mobileSidebarLabel">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title text-white" id="mobileSidebarLabel">
                <i class="fas fa-handshake me-2"></i>Partner Portal
            </h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <div class="text-white-50 mb-3">
                <small>Welcome, <?php echo htmlspecialchars($currentPartner['contact_name']); ?></small>
            </div>
            <nav class="nav flex-column">
                <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>" href="dashboard.php">
                    <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                </a>
                <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'links.php' ? 'active' : ''; ?>" href="links.php">
                    <i class="fas fa-link me-2"></i>Affiliate Links
                </a>
                <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'codes.php' ? 'active' : ''; ?>" href="codes.php">
                    <i class="fas fa-ticket-alt me-2"></i>Promotion Codes
                </a>
                <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'conversions.php' ? 'active' : ''; ?>" href="conversions.php">
                    <i class="fas fa-chart-line me-2"></i>Conversions
                </a>
                <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'payments.php' ? 'active' : ''; ?>" href="payments.php">
                    <i class="fas fa-money-bill-wave me-2"></i>Payments
                </a>
                <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'profile.php' ? 'active' : ''; ?>" href="profile.php">
                    <i class="fas fa-user me-2"></i>Profile
                </a>
                <a class="nav-link" href="logout.php">
                    <i class="fas fa-sign-out-alt me-2"></i>Logout
                </a>
            </nav>
        </div>
    </div>
    
    <div class="container-fluid">
        <!-- Mobile Navigation Toggle -->
        <button class="btn btn-primary d-md-none mb-3" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileSidebar" aria-controls="mobileSidebar">
            <i class="fas fa-bars me-2"></i>Menu
        </button>
        
        <div class="row">
            <!-- Desktop Sidebar -->
            <div class="col-md-3 col-lg-2 sidebar p-0 d-none d-md-block" id="sidebar">
                <div class="p-3">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h4 class="text-white mb-0">
                            <i class="fas fa-handshake me-2"></i>Partner Portal
                        </h4>
                    </div>
                    <div class="text-white-50 mb-3">
                        <small>Welcome, <?php echo htmlspecialchars($currentPartner['contact_name']); ?></small>
                    </div>
                    <nav class="nav flex-column">
                        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>" href="dashboard.php">
                            <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                        </a>
                        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'links.php' ? 'active' : ''; ?>" href="links.php">
                            <i class="fas fa-link me-2"></i>Affiliate Links
                        </a>
                        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'codes.php' ? 'active' : ''; ?>" href="codes.php">
                            <i class="fas fa-ticket-alt me-2"></i>Promotion Codes
                        </a>
                        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'conversions.php' ? 'active' : ''; ?>" href="conversions.php">
                            <i class="fas fa-chart-line me-2"></i>Conversions
                        </a>
                        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'payments.php' ? 'active' : ''; ?>" href="payments.php">
                            <i class="fas fa-money-bill-wave me-2"></i>Payments
                        </a>
                        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'profile.php' ? 'active' : ''; ?>" href="profile.php">
                            <i class="fas fa-user me-2"></i>Profile
                        </a>
                        <a class="nav-link" href="logout.php">
                            <i class="fas fa-sign-out-alt me-2"></i>Logout
                        </a>
                    </nav>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 p-4 main-content">
                <!-- Welcome Banner -->
                <div class="welcome-banner">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-1">Welcome back, <?php echo htmlspecialchars($currentPartner['contact_name']); ?>!</h5>
                            <small>Track your affiliate performance and earnings</small>
                        </div>
                        <div class="text-end">
                            <div class="h4 mb-0">$<?php echo number_format($dashboardData['stats']['total_earnings'], 2); ?></div>
                            <small>Total Earnings</small>
                        </div>
                    </div>
                </div>
