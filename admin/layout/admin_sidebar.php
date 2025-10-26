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
                <a class="nav-link <?php echo isset($currentPage) && $currentPage == 'index' ? 'active' : ''; ?>" href="index.php" style="color: <?php echo isset($currentPage) && $currentPage == 'index' ? '#e8f0fe' : 'rgba(255, 255, 255, 0.87)'; ?>; background: <?php echo isset($currentPage) && $currentPage == 'index' ? 'rgba(232, 240, 254, 0.1)' : 'transparent'; ?>; padding: 12px 16px; border-radius: 6px; margin-bottom: 4px; transition: all 0.2s;">
                    <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                </a>
                <a class="nav-link <?php echo isset($currentPage) && $currentPage == 'partners' ? 'active' : ''; ?>" href="partners.php" style="color: <?php echo isset($currentPage) && $currentPage == 'partners' ? '#e8f0fe' : 'rgba(255, 255, 255, 0.87)'; ?>; background: <?php echo isset($currentPage) && $currentPage == 'partners' ? 'rgba(232, 240, 254, 0.1)' : 'transparent'; ?>; padding: 12px 16px; border-radius: 6px; margin-bottom: 4px; transition: all 0.2s;">
                    <i class="fas fa-users me-2"></i>Partners
                </a>
                <a class="nav-link <?php echo isset($currentPage) && $currentPage == 'earning_logs' ? 'active' : ''; ?>" href="earning_logs.php" style="color: <?php echo isset($currentPage) && $currentPage == 'earning_logs' ? '#e8f0fe' : 'rgba(255, 255, 255, 0.87)'; ?>; background: <?php echo isset($currentPage) && $currentPage == 'earning_logs' ? 'rgba(232, 240, 254, 0.1)' : 'transparent'; ?>; padding: 12px 16px; border-radius: 6px; margin-bottom: 4px; transition: all 0.2s;">
                    <i class="fas fa-money-bill-wave me-2"></i>Earning Logs
                </a>
                <a class="nav-link <?php echo isset($currentPage) && $currentPage == 'payout_logs' ? 'active' : ''; ?>" href="payout_logs.php" style="color: <?php echo isset($currentPage) && $currentPage == 'payout_logs' ? '#e8f0fe' : 'rgba(255, 255, 255, 0.87)'; ?>; background: <?php echo isset($currentPage) && $currentPage == 'payout_logs' ? 'rgba(232, 240, 254, 0.1)' : 'transparent'; ?>; padding: 12px 16px; border-radius: 6px; margin-bottom: 4px; transition: all 0.2s;">
                    <i class="fas fa-credit-card me-2"></i>Payout Logs
                </a>
                <a class="nav-link <?php echo isset($currentPage) && $currentPage == 'payout_history' ? 'active' : ''; ?>" href="payout_history.php" style="color: <?php echo isset($currentPage) && $currentPage == 'payout_history' ? '#e8f0fe' : 'rgba(255, 255, 255, 0.87)'; ?>; background: <?php echo isset($currentPage) && $currentPage == 'payout_history' ? 'rgba(232, 240, 254, 0.1)' : 'transparent'; ?>; padding: 12px 16px; border-radius: 6px; margin-bottom: 4px; transition: all 0.2s;">
                    <i class="fas fa-history me-2"></i>Payout History
                </a>
                <hr style="border-color: rgba(255, 255, 255, 0.1); margin: 16px 0;">
                <a class="nav-link" href="logout.php" style="color: #d93025; padding: 12px 16px; border-radius: 6px; transition: all 0.2s;">
                    <i class="fas fa-sign-out-alt me-2"></i>Logout
                </a>
            </nav>
        </div>
    </div>
