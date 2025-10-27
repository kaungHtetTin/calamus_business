<?php
session_start();
require_once 'classes/autoload.php';

// Initialize authentication
$auth = new PartnerAuth();

// Check if user is logged in
$sessionToken = $_SESSION['partner_session_token'] ?? '';
$isLoggedIn = false;
$currentPartner = null;

if (!empty($sessionToken)) {
    // Validate session
    $session = $auth->validateSession($sessionToken);
    if ($session['success']) {
        $currentPartner = $session['partner'];
        $isLoggedIn = true;
    } else {
        unset($_SESSION['partner_session_token']);
    }
}

// If not authenticated, check localStorage via JavaScript
if (!$isLoggedIn) {
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
                // Session is invalid, clear localStorage
                localStorage.removeItem("partner_session_token");
            }
        })
        .catch(error => {
            console.error("Session validation error:", error);
            localStorage.removeItem("partner_session_token");
        });
    }
    </script>';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle ?? 'Calamus Education Partner Portal'; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        /* Google-style Navigation */
        .navbar-public {
            background: white;
            border-bottom: 1px solid #e8eaed;
            box-shadow: 0 1px 2px 0 rgba(60,64,67,.3), 0 1px 3px 1px rgba(60,64,67,.15);
            padding: 8px 16px;
        }
        
        .navbar-brand {
            font-weight: 400;
            font-size: 22px;
            color: #5f6368;
            text-decoration: none;
        }
        
        .navbar-brand:hover {
            color: #1a73e8;
        }
        
        .nav-link {
            color: #5f6368;
            font-weight: 400;
            padding: 8px 16px;
            border-radius: 4px;
            transition: background-color 0.2s;
        }
        
        .nav-link:hover {
            background-color: #f1f3f4;
            color: #1a73e8;
        }
        
        .nav-link.active {
            background-color: #e8f0fe;
            color: #1a73e8;
        }
        
        /* User Profile Styles */
        .navbar-profile-image {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #e8eaed;
            transition: all 0.3s ease;
        }
        
        .navbar-profile-image:hover {
            border-color: #1a73e8;
            transform: scale(1.1);
        }
        
        .navbar-profile-placeholder {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background-color: #f1f3f4;
            border: 2px solid #e8eaed;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #5f6368;
            font-size: 14px;
            transition: all 0.3s ease;
        }
        
        .navbar-profile-placeholder:hover {
            background-color: #e8eaed;
            border-color: #1a73e8;
            transform: scale(1.1);
        }
        
        .user-profile-link {
            padding: 8px 12px !important;
            border-radius: 6px !important;
            transition: all 0.3s ease;
        }
        
        .user-profile-link:hover {
            background-color: #f1f3f4 !important;
        }
        
        .user-info {
            text-align: left;
        }
        
        .user-name {
            font-size: 14px;
            font-weight: 500;
            line-height: 1.2;
            color: #3c4043;
        }
        
        .user-role {
            font-size: 12px;
            color: #5f6368;
            line-height: 1;
        }
        
        .user-dropdown-menu {
            min-width: 260px;
            border: none;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            border-radius: 8px;
            padding: 0.5rem 0;
        }
        
        .user-dropdown-menu .dropdown-header {
            padding: 1rem 1rem 0.5rem 1rem;
            background-color: #f8f9fa;
            border-radius: 8px 8px 0 0;
            margin: -0.5rem -0.5rem 0 -0.5rem;
        }
        
        .user-dropdown-menu .dropdown-item {
            padding: 0.75rem 1rem;
            font-size: 14px;
            transition: all 0.2s ease;
            border-radius: 0;
        }
        
        .user-dropdown-menu .dropdown-item:hover {
            background-color: #f1f3f4;
            color: #1a73e8;
            transform: translateX(4px);
        }
        
        .user-dropdown-menu .dropdown-item i {
            width: 18px;
            text-align: center;
        }
        
        .dropdown-profile-image {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #e8eaed;
        }
        
        .dropdown-profile-placeholder {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: #e8eaed;
            border: 2px solid #dadce0;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #5f6368;
            font-size: 16px;
        }
        
        /* Drawer Profile Styles */
        .drawer-user-info {
            background-color: rgba(255, 255, 255, 0.1);
            margin: -8px -8px 8px -8px;
        }
        
        .drawer-profile-image {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid rgba(255, 255, 255, 0.3);
        }
        
        .drawer-profile-placeholder {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background-color: rgba(255, 255, 255, 0.2);
            border: 2px solid rgba(255, 255, 255, 0.3);
            display: flex;
            align-items: center;
            justify-content: center;
            color: rgba(255, 255, 255, 0.8);
            font-size: 20px;
        }
        
        /* Google-style Drawer */
        .drawer-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1040;
            display: none;
        }
        
        .drawer {
            position: fixed;
            top: 0;
            left: -280px;
            width: 280px;
            height: 100%;
            background: white;
            box-shadow: 0 8px 10px 1px rgba(0,0,0,.14), 0 3px 14px 2px rgba(0,0,0,.12), 0 5px 5px -3px rgba(0,0,0,.2);
            z-index: 1050;
            transition: left 0.3s ease;
            overflow-y: auto;
        }
        
        .drawer.open {
            left: 0;
        }
        
        .drawer-header {
            padding: 16px;
            border-bottom: 1px solid #e8eaed;
            background: #f8f9fa;
        }
        
        .drawer-title {
            font-size: 16px;
            font-weight: 500;
            color: #3c4043;
            margin: 0;
        }
        
        .drawer-content {
            padding: 8px 0;
        }
        
        .drawer-item {
            display: flex;
            align-items: center;
            padding: 12px 16px;
            color: #3c4043;
            text-decoration: none;
            transition: background-color 0.2s;
            border: none;
            background: none;
            width: 100%;
            text-align: left;
        }
        
        .drawer-item:hover {
            background-color: #f1f3f4;
            color: #1a73e8;
        }
        
        .drawer-item i {
            width: 24px;
            margin-right: 16px;
            font-size: 20px;
        }
        
        .drawer-divider {
            height: 1px;
            background-color: #e8eaed;
            margin: 8px 0;
        }
        
        /* Content Area */
        .main-content {
            margin-top: 64px;
            min-height: calc(100vh - 64px);
        }
        
        .content-section {
            padding: 24px;
            max-width: 1200px;
            margin: 0 auto;
        }
        
        /* Cards */
        .card {
            border: 1px solid #e8eaed;
            border-radius: 8px;
            box-shadow: 0 1px 2px 0 rgba(60,64,67,.3), 0 1px 3px 1px rgba(60,64,67,.15);
        }
        
        .card-header {
            background: #f8f9fa;
            border-bottom: 1px solid #e8eaed;
            padding: 16px 20px;
        }
        
        .card-body {
            padding: 20px;
        }
        
        /* Footer */
        .footer-public {
            background: white;
            border-top: 1px solid #e8eaed;
            padding: 24px 0;
            margin-top: 48px;
        }
        
        .footer-links {
            display: flex;
            flex-wrap: wrap;
            gap: 24px;
            justify-content: center;
            margin-bottom: 16px;
        }
        
        .footer-links a {
            color: #5f6368;
            text-decoration: none;
            font-size: 14px;
        }
        
        .footer-links a:hover {
            color: #1a73e8;
        }
        
        .footer-copyright {
            text-align: center;
            color: #5f6368;
            font-size: 12px;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .drawer {
                width: 100%;
                left: -100%;
            }
            
            .content-section {
                padding: 16px;
            }
            
            .footer-links {
                flex-direction: column;
                align-items: center;
                gap: 12px;
            }
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-public fixed-top">
        <div class="container-fluid">
            <button class="btn btn-link p-0 me-3" onclick="toggleDrawer()">
                <i class="fas fa-bars" style="color: #5f6368; font-size: 20px;"></i>
            </button>
            <a class="navbar-brand" href="<?php echo $isLoggedIn ? 'dashboard.php' : 'partner_login.php'; ?>">
                <i class="fas fa-graduation-cap me-2"></i>Calamus Education
            </a>
            <div class="navbar-nav ms-auto">
                <?php if ($isLoggedIn): ?>
                    <!-- Logged-in User Profile -->
                    <div class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center user-profile-link" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
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
                            <li><a class="dropdown-item" href="dashboard.php"><i class="fas fa-tachometer-alt me-2"></i>Dashboard</a></li>
                            <li><a class="dropdown-item" href="profile.php"><i class="fas fa-user me-2"></i>My Profile</a></li>
                            <li><a class="dropdown-item" href="earning_history.php"><i class="fas fa-chart-line me-2"></i>Earnings</a></li>
                            <li><a class="dropdown-item" href="partner_payment_histories.php"><i class="fas fa-credit-card me-2"></i>Payments</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="settings.php"><i class="fas fa-cog me-2"></i>Settings</a></li>
                            <li><a class="dropdown-item" href="help.php"><i class="fas fa-question-circle me-2"></i>Help & Support</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
                        </ul>
                    </div>
                <?php else: ?>
                    <!-- Non-logged-in Users -->
                    <a class="nav-link" href="partner_login.php">
                        <i class="fas fa-sign-in-alt me-1"></i>Login
                    </a>
                    <a class="nav-link" href="partner_register.php">
                        <i class="fas fa-user-plus me-1"></i>Register
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <!-- Drawer Overlay -->
    <div class="drawer-overlay" onclick="closeDrawer()"></div>

    <!-- Drawer Navigation -->
    <div class="drawer" id="drawer">
        <div class="drawer-header">
            <h6 class="drawer-title">Navigation</h6>
        </div>
        <div class="drawer-content">
            <?php if ($isLoggedIn): ?>
                <!-- Logged-in User Section -->
                <div class="drawer-user-info">
                    <div class="d-flex align-items-center p-3">
                        <div class="me-3">
                            <?php if (!empty($currentPartner['profile_image']) && file_exists($currentPartner['profile_image'])): ?>
                                <img src="<?php echo htmlspecialchars($currentPartner['profile_image']); ?>" 
                                     alt="Profile" 
                                     class="drawer-profile-image">
                            <?php else: ?>
                                <div class="drawer-profile-placeholder">
                                    <i class="fas fa-user"></i>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div>
                            <div class="fw-bold text-white"><?php echo htmlspecialchars($currentPartner['contact_name']); ?></div>
                            <small class="text-white-50"><?php echo htmlspecialchars($currentPartner['email']); ?></small>
                        </div>
                    </div>
                </div>
                
                <div class="drawer-divider"></div>
                
                <a href="dashboard.php" class="drawer-item">
                    <i class="fas fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>
                <a href="profile.php" class="drawer-item">
                    <i class="fas fa-user"></i>
                    <span>My Profile</span>
                </a>
                <a href="earning_history.php" class="drawer-item">
                    <i class="fas fa-chart-line"></i>
                    <span>Earnings</span>
                </a>
                <a href="partner_payment_histories.php" class="drawer-item">
                    <i class="fas fa-credit-card"></i>
                    <span>Payments</span>
                </a>
                
                <div class="drawer-divider"></div>
                
                <a href="settings.php" class="drawer-item">
                    <i class="fas fa-cog"></i>
                    <span>Settings</span>
                </a>
                <a href="help.php" class="drawer-item">
                    <i class="fas fa-question-circle"></i>
                    <span>Help & Support</span>
                </a>
                
                <div class="drawer-divider"></div>
                
                <a href="logout.php" class="drawer-item text-danger">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Logout</span>
                </a>
            <?php else: ?>
                <!-- Non-logged-in Users -->
                <a href="partner_login.php" class="drawer-item">
                    <i class="fas fa-sign-in-alt"></i>
                    <span>Login</span>
                </a>
                <a href="partner_register.php" class="drawer-item">
                    <i class="fas fa-user-plus"></i>
                    <span>Register</span>
                </a>
            <?php endif; ?>
            
            <div class="drawer-divider"></div>
            
            <a href="terms_conditions.php" class="drawer-item">
                <i class="fas fa-file-contract"></i>
                <span>Terms & Conditions</span>
            </a>
            <a href="privacy_policy.php" class="drawer-item">
                <i class="fas fa-shield-alt"></i>
                <span>Privacy Policy</span>
            </a>
            <a href="cookie_policy.php" class="drawer-item">
                <i class="fas fa-cookie-bite"></i>
                <span>Cookie Policy</span>
            </a>
            <a href="contact_us.php" class="drawer-item">
                <i class="fas fa-envelope"></i>
                <span>Contact Us</span>
            </a>
            
            <div class="drawer-divider"></div>
            
            <a href="https://www.calamuseducation.com/app-portfolio/easy-korean.php" class="drawer-item" target="_blank">
                <i class="fas fa-mobile-alt"></i>
                <span>Easy Korean App</span>
            </a>
            <a href="https://www.calamuseducation.com/app-portfolio/easy-english.php" class="drawer-item" target="_blank">
                <i class="fas fa-mobile-alt"></i>
                <span>Easy English App</span>
            </a>
            <a href="https://play.google.com/store/apps/details?id=com.qanda.learnroom" class="drawer-item" target="_blank">
                <i class="fas fa-play"></i>
                <span>Easy English (Play Store)</span>
            </a>
            <a href="https://play.google.com/store/apps/details?id=com.calamus.easykorean" class="drawer-item" target="_blank">
                <i class="fas fa-play"></i>
                <span>Easy Korean (Play Store)</span>
            </a>
            
            <div class="drawer-divider"></div>
            
            <a href="https://www.calamuseducation.com/calamus/about_us.php" class="drawer-item" target="_blank">
                <i class="fas fa-info-circle"></i>
                <span>About Calamus</span>
            </a>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <?php 
        if (isset($content)) {
            echo $content;
        }
        ?>
    </div>

    <!-- Footer -->
    <footer class="footer-public">
        <div class="container">
            <div class="footer-links">
                <a href="terms_conditions.php">Terms & Conditions</a>
                <a href="privacy_policy.php">Privacy Policy</a>
                <a href="cookie_policy.php">Cookie Policy</a>
                <a href="contact_us.php">Contact Us</a>
            </div>
            <div class="footer-copyright">
                <i class="fas fa-copyright me-1"></i>
                <?php echo date('Y'); ?> Calamus Education. All rights reserved.
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function toggleDrawer() {
            const drawer = document.getElementById('drawer');
            const overlay = document.querySelector('.drawer-overlay');
            
            if (drawer.classList.contains('open')) {
                closeDrawer();
            } else {
                drawer.classList.add('open');
                overlay.style.display = 'block';
                document.body.style.overflow = 'hidden';
            }
        }
        
        function closeDrawer() {
            const drawer = document.getElementById('drawer');
            const overlay = document.querySelector('.drawer-overlay');
            
            drawer.classList.remove('open');
            overlay.style.display = 'none';
            document.body.style.overflow = 'auto';
        }
        
        // Close drawer on escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeDrawer();
            }
        });
        
        // Close drawer when clicking outside
        document.addEventListener('click', function(e) {
            const drawer = document.getElementById('drawer');
            const overlay = document.querySelector('.drawer-overlay');
            
            if (!drawer.contains(e.target) && !e.target.closest('.navbar')) {
                closeDrawer();
            }
        });
    </script>
</body>
</html>
