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



<?php
// Helper functions
function getStatusColor($status) {
    $colors = [
        'pending' => 'warning',
        'approved' => 'success',
        'paid' => 'info',
        'cancelled' => 'danger'
    ];
    return $colors[$status] ?? 'secondary';
}

function getPaymentStatusColor($status) {
    $colors = [
        'pending' => 'warning',
        'processing' => 'info',
        'completed' => 'success',
        'failed' => 'danger'
    ];
    return $colors[$status] ?? 'secondary';
}

function getCodeStatusColor($status) {
    $colors = [
        'active' => 'success',
        'used' => 'info',
        'expired' => 'warning',
        'cancelled' => 'danger',
        'pending' => 'warning'
    ];
    return $colors[$status] ?? 'secondary';
}
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
    <!-- Top Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary sticky-top">
        <div class="container-fluid">
            <!-- Menu Button - Always visible on leftmost side -->
            <button class="navbar-toggler me-3" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileSidebar" aria-controls="mobileSidebar">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <!-- Brand/Logo -->
            <a class="navbar-brand fw-bold" href="dashboard.php">
                Calamus
            </a>
            
            <!-- Quick Stats & User Info -->
            <div class="navbar-nav ms-auto d-none d-md-flex align-items-center">
                <!-- Quick Stats Display -->
                <div class="nav-item me-3">
                    <span class="navbar-text text-white">
                        <i class="fas fa-ticket-alt me-1"></i>
                        <strong><?php echo number_format($codeStats['total_generated'] ?? 0); ?></strong>
                        <small class="text-white-50">codes</small>
                    </span>
                </div>
                
                <!-- User Dropdown -->
                <div class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle text-white d-flex align-items-center" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <?php if (!empty($currentPartner['profile_image']) && file_exists($currentPartner['profile_image'])): ?>
                            <img src="<?php echo htmlspecialchars($currentPartner['profile_image']); ?>" 
                                 alt="Profile" 
                                 class="navbar-profile-image me-2">
                        <?php else: ?>
                            <i class="fas fa-user-circle me-1"></i>
                        <?php endif; ?>
                        <?php echo htmlspecialchars($currentPartner['contact_name']); ?>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="profile.php"><i class="fas fa-user me-2"></i>Profile</a></li>
                        <li><a class="dropdown-item" href="dashboard.php"><i class="fas fa-tachometer-alt me-2"></i>Dashboard</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-danger" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
                    </ul>
                </div>
            </div>
            
            <!-- Mobile User Info -->
            <div class="d-md-none">
                <span class="navbar-text text-white">
                    <i class="fas fa-user-circle me-1"></i>
                    <?php echo htmlspecialchars($currentPartner['contact_name']); ?>
                </span>
            </div>
        </div>
    </nav>

    <!-- Bootstrap Offcanvas for Navigation (Mobile & Desktop) -->
    <div class="offcanvas offcanvas-start" tabindex="-1" id="mobileSidebar" aria-labelledby="mobileSidebarLabel">
        <div class="offcanvas-header">
            <strong class="offcanvas-title text-white" id="mobileSidebarLabel">
                <i class="fas fa-handshake me-2"></i>Partner Portal
            </strong>
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
                <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'codes.php' ? 'active' : ''; ?>" href="codes.php">
                    <i class="fas fa-ticket-alt me-2"></i>Promotion Codes
                </a>
                <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'partner_payment_methods.php' ? 'active' : ''; ?>" href="partner_payment_methods.php">
                    <i class="fas fa-credit-card me-2"></i>Payment Methods
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
        <div class="row">
            <!-- Desktop Sidebar -->
            <div class="col-md-3 col-lg-2 sidebar p-0 d-none d-md-block" id="sidebar">
                <div class="p-3">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="text-white mb-0">
                            <i class="fas fa-handshake me-2"></i>Partner Portal
                        </h5>
                    </div>
                    <div class="text-white-50 mb-3">
                        <small>Welcome, <?php echo htmlspecialchars($currentPartner['contact_name']); ?></small>
                    </div>
                    <nav class="nav flex-column">
                        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>" href="dashboard.php">
                            <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                        </a>
                        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'codes.php' ? 'active' : ''; ?>" href="codes.php">
                            <i class="fas fa-ticket-alt me-2"></i>Promotion Codes
                        </a>
                        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'partner_payment_methods.php' ? 'active' : ''; ?>" href="partner_payment_methods.php">
                            <i class="fas fa-credit-card me-2"></i>Payment Methods
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
                <!-- Breadcrumb Navigation -->
                <nav aria-label="breadcrumb" class="mb-3">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="dashboard.php" class="text-decoration-none">
                                <i class="fas fa-home me-1"></i>Dashboard
                            </a>
                        </li>
                        <?php
                        $currentPage = basename($_SERVER['PHP_SELF']);
                           $pageNames = [
                               'dashboard.php' => 'Dashboard',
                               'codes.php' => 'Promotion Codes',
                               'partner_payment_methods.php' => 'Payment Methods',
                               'profile.php' => 'Profile'
                           ];
                        
                        if ($currentPage !== 'dashboard.php' && isset($pageNames[$currentPage])) {
                            echo '<li class="breadcrumb-item active" aria-current="page">' . $pageNames[$currentPage] . '</li>';
                        }
                        ?>
                    </ol>
                </nav>
                
                <!-- Welcome Banner -->
                <div class="welcome-banner">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-1">Welcome back, <?php echo htmlspecialchars($currentPartner['contact_name']); ?>!</h5>
                            <small>Manage your promotion codes and profile settings</small>
                        </div>
                        <div class="text-end">
                            <div class="h4 mb-0"><?php echo number_format($codeStats['total_generated'] ?? 0); ?></div>
                            <small>Total Codes Generated</small>
                        </div>
                    </div>
                </div>
