<?php
/**
 * Admin Sidebar Layout Component
 * Reusable sidebar navigation for admin pages
 */
?>

<div class="offcanvas offcanvas-start" tabindex="-1" id="adminSidebar" style="width: 280px; background: #202124;">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title text-white">
                <i class="fas fa-shield-alt me-2"></i>Admin Portal
            </h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <div class="text-white-50 mb-3">
                <small>Welcome, <?php echo htmlspecialchars($adminAuth->getAdminUsername()); ?></small>
            </div>
            <nav class="nav flex-column">
                <a class="nav-link" href="index.php" style="color: rgba(255, 255, 255, 0.87); padding: 12px 16px; border-radius: 6px; margin-bottom: 4px; transition: all 0.2s;">
                    <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                </a>
                <a class="nav-link active" href="partners.php" style="color: #e8f0fe; background: rgba(232, 240, 254, 0.1); padding: 12px 16px; border-radius: 6px; margin-bottom: 4px;">
                    <i class="fas fa-users me-2"></i>Partners
                </a>
                <a class="nav-link" href="#" style="color: rgba(255, 255, 255, 0.87); padding: 12px 16px; border-radius: 6px; margin-bottom: 4px; transition: all 0.2s;">
                    <i class="fas fa-user-check me-2"></i>Verifications
                </a>
                <a class="nav-link" href="#" style="color: rgba(255, 255, 255, 0.87); padding: 12px 16px; border-radius: 6px; margin-bottom: 4px; transition: all 0.2s;">
                    <i class="fas fa-chart-line me-2"></i>Earnings
                </a>
                <a class="nav-link" href="#" style="color: rgba(255, 255, 255, 0.87); padding: 12px 16px; border-radius: 6px; margin-bottom: 4px; transition: all 0.2s;">
                    <i class="fas fa-cog me-2"></i>Settings
                </a>
                <hr style="border-color: rgba(255, 255, 255, 0.1); margin: 16px 0;">
                <a class="nav-link" href="logout.php" style="color: #d93025; padding: 12px 16px; border-radius: 6px; transition: all 0.2s;">
                    <i class="fas fa-sign-out-alt me-2"></i>Logout
                </a>
            </nav>
        </div>
    </div>
