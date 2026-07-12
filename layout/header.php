<?php
session_start();
require_once 'classes/autoload.php';

// Initialize authentication
$auth = new PartnerAuth();

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

// Load one aggregate earnings query for the topbar and reuse it on pages.
$currentFile = basename($_SERVER['PHP_SELF']);
$earningsManager = new PartnerEarningsManager();
$earningStats = $earningsManager->getPartnerEarningStats($currentPartner['id']);
$dashboardData = $earningStats + [
    'recent_earnings' => [],
    'monthly_earnings' => []
];

if ($currentFile === 'dashboard.php') {
    $dashboard = new PartnerDashboard();
    $dashboardData = $dashboard->getDashboardData($currentPartner['id'], $earningStats);
}

// Get pending payment histories count
$paymentHistoriesManager = new PartnerPaymentHistoriesManager();
$pendingPaymentsCount = $paymentHistoriesManager->getPartnerPaymentHistoriesCount($currentPartner['id'], 'pending');

$totalPendingEarnings = $earningStats['pending_earnings'];
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


<?php
$partnerNavGroups = [
    'Overview' => [
        ['dashboard.php', 'fa-gauge-high', 'Dashboard'],
    ],
    'Earnings & Payments' => [
        ['earning_history.php', 'fa-chart-line', 'Earning History'],
        ['partner_payment_histories.php', 'fa-credit-card', 'Payment History'],
        ['partner_payment_methods.php', 'fa-mobile-screen', 'Mobile Money'],
    ],
    'Growth Tools' => [
        ['marketing_assets.php', 'fa-bullhorn', 'Marketing Assets'],
        ['program_rules.php', 'fa-scale-balanced', 'Program Rules'],
    ],
    'Account' => [
        ['account_status.php', 'fa-user-shield', 'Account Status'],
        ['profile.php', 'fa-user', 'Profile'],
    ],
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle ?? 'Partner Dashboard'); ?> - Calamus</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/portal.css?v=5">
    <link rel="icon" href="assets/favicon.png" type="image/png">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
<div class="app-root portal-partner" data-theme="light">
    <div class="admin-app">
        <aside class="admin-sidebar glass" aria-label="Partner navigation">
            <a class="portal-brand" href="dashboard.php">
                <img src="assets/app_logo.png" alt="Calamus Education">
                <span>Calamus Partner</span>
            </a>
            <nav class="portal-nav">
                <?php foreach ($partnerNavGroups as $groupLabel => $items): ?>
                    <div class="portal-nav-group">
                        <span class="portal-nav-label"><?php echo htmlspecialchars($groupLabel); ?></span>
                        <?php foreach ($items as [$href, $icon, $label]): ?>
                            <a class="nav-link <?php echo $currentFile === $href ? 'active' : ''; ?>" href="<?php echo $href; ?>" title="<?php echo $label; ?>">
                                <i class="fas <?php echo $icon; ?>"></i><span><?php echo $label; ?></span>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php endforeach; ?>
                <a class="nav-link logout-link" href="logout.php" title="Logout">
                    <i class="fas fa-sign-out-alt"></i><span>Logout</span>
                </a>
            </nav>
            <div class="sidebar-profile">
                <?php if (!empty($currentPartner['profile_image']) && file_exists($currentPartner['profile_image'])): ?>
                    <img class="navbar-profile-image" src="<?php echo htmlspecialchars($currentPartner['profile_image']); ?>" alt="">
                <?php else: ?>
                    <div class="sidebar-avatar"><i class="fas fa-user"></i></div>
                <?php endif; ?>
                <div>
                    <strong><?php echo htmlspecialchars($currentPartner['contact_name']); ?></strong>
                    <small>Partner</small>
                </div>
            </div>
        </aside>

        <div class="offcanvas offcanvas-start" tabindex="-1" id="mobileSidebar" aria-labelledby="mobileSidebarLabel">
            <div class="offcanvas-header">
                <a class="mobile-drawer-brand" href="dashboard.php">
                    <img src="assets/app_logo.png" alt="Calamus Education">
                    <span id="mobileSidebarLabel">Partner Portal</span>
                </a>
                <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body">
                <nav class="portal-nav">
                    <?php foreach ($partnerNavGroups as $groupLabel => $items): ?>
                        <div class="portal-nav-group">
                            <span class="portal-nav-label"><?php echo htmlspecialchars($groupLabel); ?></span>
                            <?php foreach ($items as [$href, $icon, $label]): ?>
                                <a class="nav-link <?php echo $currentFile === $href ? 'active' : ''; ?>" href="<?php echo $href; ?>">
                                    <i class="fas <?php echo $icon; ?>"></i><span><?php echo $label; ?></span>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    <?php endforeach; ?>
                    <a class="nav-link logout-link" href="logout.php"><i class="fas fa-sign-out-alt"></i><span>Logout</span></a>
                </nav>
            </div>
        </div>

        <nav class="mobile-bottom-nav glass" aria-label="Mobile partner navigation">
            <a class="<?php echo $currentFile === 'dashboard.php' ? 'active' : ''; ?>" href="dashboard.php">
                <i class="fas fa-house"></i><span>Home</span>
            </a>
            <a class="<?php echo $currentFile === 'earning_history.php' ? 'active' : ''; ?>" href="earning_history.php">
                <i class="fas fa-chart-line"></i><span>Earnings</span>
            </a>
            <a class="<?php echo $currentFile === 'partner_payment_histories.php' ? 'active' : ''; ?>" href="partner_payment_histories.php">
                <i class="fas fa-credit-card"></i><span>Payments</span>
            </a>
            <a class="<?php echo $currentFile === 'partner_payment_methods.php' ? 'active' : ''; ?>" href="partner_payment_methods.php">
                <i class="fas fa-mobile-screen"></i><span>Money</span>
            </a>
            <button class="<?php echo in_array($currentFile, ['account_status.php', 'marketing_assets.php', 'program_rules.php', 'profile.php']) ? 'active' : ''; ?>" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileSidebar" aria-controls="mobileSidebar">
                <i class="fas fa-bars"></i><span>More</span>
            </button>
        </nav>

        <main class="admin-main">
            <header class="admin-topbar glass">
                <div class="d-flex align-items-center gap-2 min-w-0">
                    <a class="topbar-app-logo" href="dashboard.php" aria-label="Partner dashboard">
                        <img src="assets/app_logo.png" alt="">
                    </a>
                    <button class="btn icon-btn mobile-nav-toggle" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileSidebar" aria-controls="mobileSidebar" aria-label="Open navigation">
                        <i class="fas fa-bars"></i>
                    </button>
                    <div class="topbar-title">
                        <strong><?php echo htmlspecialchars($pageTitle ?? 'Partner Dashboard'); ?></strong>
                        <small>Partner workspace</small>
                    </div>
                </div>
                <div class="topbar-actions">
                    <span class="topbar-chip"><i class="fas fa-clock me-1"></i><?php echo number_format($pendingPaymentsCount); ?> pending</span>
                    <span class="topbar-chip"><i class="fas fa-wallet me-1"></i><?php echo number_format($totalPendingEarnings, 2); ?> MMK</span>
                    <button class="btn icon-btn theme-toggle" type="button" data-theme-toggle aria-label="Toggle color theme">
                        <i class="fas fa-moon theme-light-icon"></i>
                        <i class="fas fa-sun theme-dark-icon"></i>
                    </button>
                    <div class="dropdown partner-profile-menu">
                        <button class="btn profile-menu-toggle dropdown-toggle" type="button" id="partnerProfileMenu" data-bs-toggle="dropdown" aria-expanded="false">
                            <?php if (!empty($currentPartner['profile_image']) && file_exists($currentPartner['profile_image'])): ?>
                                <img src="<?php echo htmlspecialchars($currentPartner['profile_image']); ?>" alt="">
                            <?php else: ?>
                                <span class="profile-menu-avatar"><i class="fas fa-user"></i></span>
                            <?php endif; ?>
                            <span class="profile-menu-name"><?php echo htmlspecialchars($currentPartner['contact_name']); ?></span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="partnerProfileMenu">
                            <li class="profile-menu-summary">
                                <strong><?php echo htmlspecialchars($currentPartner['contact_name']); ?></strong>
                                <small><?php echo htmlspecialchars($currentPartner['email']); ?></small>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="profile.php"><i class="fas fa-user me-2"></i>My Profile</a></li>
                            <li><a class="dropdown-item" href="program_rules.php"><i class="fas fa-scale-balanced me-2"></i>Program Rules</a></li>
                            <li><a class="dropdown-item" href="profile.php#password-change"><i class="fas fa-key me-2"></i>Change Password</a></li>
                            <li><a class="dropdown-item" href="help.php"><i class="fas fa-circle-question me-2"></i>Help & Support</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
                        </ul>
                    </div>
                </div>
            </header>
            <div class="admin-content">
