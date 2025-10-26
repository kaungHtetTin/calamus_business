<?php
session_start();
require_once 'classes/autoload.php';

// Initialize authentication
$auth = new PartnerAuth();
$dashboard = new PartnerDashboard();

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

// If not authenticated, check localStorage via JavaScript before redirecting
if ($needsAuth) {
    // Add JavaScript to check localStorage before redirecting
    echo '<script>
    if (localStorage.getItem("partner_session_token")) {
        // Token exists in localStorage, set it in PHP session
        fetch("api/validate_session.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
            },
            body: JSON.stringify({
                session_token: localStorage.getItem("partner_session_token")
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Session is valid, reload page
                window.location.reload();
            } else {
                // Session is invalid, redirect to login
                localStorage.removeItem("partner_session_token");
                window.location.href = "partner_login.php";
            }
        })
        .catch(error => {
            console.error("Session validation error:", error);
            window.location.href = "partner_login.php";
        });
    } else {
        // No token in localStorage, redirect to login
        window.location.href = "partner_login.php";
    }
    </script>';
    exit;
}

// Get dashboard data
$dashboardData = $dashboard->getDashboardData($currentPartner['id']);

// Get pending payment histories count
$paymentHistoriesManager = new PartnerPaymentHistoriesManager();
$pendingPaymentsCount = $paymentHistoriesManager->getPartnerPaymentHistoriesCount($currentPartner['id'], 'pending');

// Get total pending earnings amount
$earningsManager = new PartnerEarningsManager();
$pendingEarningsStats = $earningsManager->getPartnerEarningStats($currentPartner['id']);
$totalPendingEarnings = $pendingEarningsStats['pending_earnings'];
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
                <!-- Pending Payments Count -->
                <div class="nav-item me-3">
                    <span class="navbar-text text-white">
                        <i class="fas fa-clock me-1"></i>
                        <strong><?php echo number_format($pendingPaymentsCount); ?></strong>
                        <small class="text-white-50">pending payments</small>
                    </span>
                </div>
                
                <!-- Total Pending Earnings -->
                <div class="nav-item me-3">
                    <span class="navbar-text text-white">
                        <i class="fas fa-money-bill-wave me-1"></i>
                        <strong><?php echo number_format($totalPendingEarnings, 2); ?> MMK</strong>
                        <small class="text-white-50">to receive</small>
                    </span>
                </div>
                
                <!-- User Dropdown -->
                <div class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle text-white d-flex align-items-center user-profile-link" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <div class="user-profile-container me-2">
                            <?php if (!empty($currentPartner['profile_image']) && file_exists($currentPartner['profile_image'])): ?>
                                <img src="<?php echo htmlspecialchars($currentPartner['profile_image']); ?>" 
                                     alt="Profile" 
                                     class="navbar-profile-image">
                            <?php else: ?>
                                <div class="navbar-profile-placeholder">
                                    <i class="fas fa-user"></i>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="user-info d-none d-lg-block">
                            <div class="user-name"><?php echo htmlspecialchars($currentPartner['contact_name']); ?></div>
                            <div class="user-role">Partner</div>
                        </div>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end user-dropdown-menu">
                        <li class="dropdown-header">
                            <div class="d-flex align-items-center">
                                <div class="me-3">
                                    <?php if (!empty($currentPartner['profile_image']) && file_exists($currentPartner['profile_image'])): ?>
                                        <img src="<?php echo htmlspecialchars($currentPartner['profile_image']); ?>" 
                                             alt="Profile" 
                                             class="dropdown-profile-image">
                                    <?php else: ?>
                                        <div class="dropdown-profile-placeholder">
                                            <i class="fas fa-user"></i>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div>
                                    <div class="fw-bold"><?php echo htmlspecialchars($currentPartner['contact_name']); ?></div>
                                    <small class="text-muted"><?php echo htmlspecialchars($currentPartner['email']); ?></small>
                                </div>
                            </div>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="profile.php"><i class="fas fa-user me-2"></i>My Profile</a></li>
                        <li><a class="dropdown-item" href="dashboard.php"><i class="fas fa-tachometer-alt me-2"></i>Dashboard</a></li>
                        <li><a class="dropdown-item" href="earning_history.php"><i class="fas fa-chart-line me-2"></i>Earnings</a></li>
                        <li><a class="dropdown-item" href="partner_payment_histories.php"><i class="fas fa-credit-card me-2"></i>Payments</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="profile.php#password-change"><i class="fas fa-key me-2"></i>Change Password</a></li>
                        <li><a class="dropdown-item" href="settings.php"><i class="fas fa-cog me-2"></i>Settings</a></li>
                        <li><a class="dropdown-item" href="help.php"><i class="fas fa-question-circle me-2"></i>Help & Support</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-danger" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
                    </ul>
                </div>
            </div>
            
            <!-- Mobile User Info -->
            <div class="d-md-none">
                <div class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle text-white d-flex align-items-center" href="#" id="mobileUserDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <div class="user-profile-container me-2">
                            <?php if (!empty($currentPartner['profile_image']) && file_exists($currentPartner['profile_image'])): ?>
                                <img src="<?php echo htmlspecialchars($currentPartner['profile_image']); ?>" 
                                     alt="Profile" 
                                     class="navbar-profile-image">
                            <?php else: ?>
                                <div class="navbar-profile-placeholder">
                                    <i class="fas fa-user"></i>
                                </div>
                            <?php endif; ?>
                        </div>
                        <span class="navbar-text text-white">
                            <?php echo htmlspecialchars($currentPartner['contact_name']); ?>
                        </span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end user-dropdown-menu">
                        <li class="dropdown-header">
                            <div class="d-flex align-items-center">
                                <div class="me-3">
                                    <?php if (!empty($currentPartner['profile_image']) && file_exists($currentPartner['profile_image'])): ?>
                                        <img src="<?php echo htmlspecialchars($currentPartner['profile_image']); ?>" 
                                             alt="Profile" 
                                             class="dropdown-profile-image">
                                    <?php else: ?>
                                        <div class="dropdown-profile-placeholder">
                                            <i class="fas fa-user"></i>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div>
                                    <div class="fw-bold"><?php echo htmlspecialchars($currentPartner['contact_name']); ?></div>
                                    <small class="text-muted"><?php echo htmlspecialchars($currentPartner['email']); ?></small>
                                </div>
                            </div>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="profile.php"><i class="fas fa-user me-2"></i>My Profile</a></li>
                        <li><a class="dropdown-item" href="dashboard.php"><i class="fas fa-tachometer-alt me-2"></i>Dashboard</a></li>
                        <li><a class="dropdown-item" href="earning_history.php"><i class="fas fa-chart-line me-2"></i>Earnings</a></li>
                        <li><a class="dropdown-item" href="partner_payment_histories.php"><i class="fas fa-credit-card me-2"></i>Payments</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="profile.php#password-change"><i class="fas fa-key me-2"></i>Change Password</a></li>
                        <li><a class="dropdown-item" href="settings.php"><i class="fas fa-cog me-2"></i>Settings</a></li>
                        <li><a class="dropdown-item" href="help.php"><i class="fas fa-question-circle me-2"></i>Help & Support</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-danger" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
                    </ul>
                </div>
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
                <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'partner_payment_methods.php' ? 'active' : ''; ?>" href="partner_payment_methods.php">
                    <i class="fas fa-mobile-alt me-2"></i>Mobile Money
                </a>
                <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'earning_history.php' ? 'active' : ''; ?>" href="earning_history.php">
                    <i class="fas fa-chart-line me-2"></i>Earning History
                </a>
                <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'partner_payment_histories.php' ? 'active' : ''; ?>" href="partner_payment_histories.php">
                    <i class="fas fa-credit-card me-2"></i>Payment History
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
                        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'partner_payment_methods.php' ? 'active' : ''; ?>" href="partner_payment_methods.php">
                            <i class="fas fa-mobile-alt me-2"></i>Mobile Money
                        </a>
                        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'earning_history.php' ? 'active' : ''; ?>" href="earning_history.php">
                            <i class="fas fa-chart-line me-2"></i>Earning History
                        </a>
                        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'partner_payment_histories.php' ? 'active' : ''; ?>" href="partner_payment_histories.php">
                            <i class="fas fa-credit-card me-2"></i>Payment History
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
                               'partner_payment_methods.php' => 'Mobile Money',
                               'earning_history.php' => 'Earning History',
                               'partner_payment_histories.php' => 'Payment History',
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
                            <small>Manage your earnings and profile settings</small>
                        </div>
                        <div class="text-end">
                            <div class="h4 mb-0"><?php echo number_format($dashboardData['total_earnings'] ?? 0, 2); ?> MMK</div>
                            <small>Total Earnings</small>
                        </div>
                    </div>
                </div>
