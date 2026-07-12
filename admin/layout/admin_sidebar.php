<?php
$adminNavGroups = [
    'Overview' => [
        ['index', 'index.php', 'fa-gauge-high', 'Dashboard'],
    ],
    'Partner Management' => [
        ['partners', 'partners.php', 'fa-users', 'Partners'],
    ],
    'Finance' => [
        ['earning_logs', 'earning_logs.php', 'fa-money-bill-trend-up', 'Earning Logs'],
        ['payout_logs', 'payout_logs.php', 'fa-credit-card', 'Payout Logs'],
        ['payout_history', 'payout_history.php', 'fa-clock-rotate-left', 'Payout History'],
    ],
    'Communication' => [
        ['compose_email', 'compose_email.php', 'fa-envelope', 'Compose Email'],
    ],
];
?>
<aside class="admin-sidebar glass" aria-label="Admin navigation">
    <a class="portal-brand" href="index.php">
        <img src="../assets/app_logo.png" alt="Calamus Education">
        <span>Calamus Admin</span>
    </a>
    <nav class="portal-nav">
        <?php foreach ($adminNavGroups as $groupLabel => $items): ?>
            <div class="portal-nav-group">
                <span class="portal-nav-label"><?php echo htmlspecialchars($groupLabel); ?></span>
                <?php foreach ($items as [$key, $href, $icon, $label]): ?>
                    <a class="nav-link <?php echo ($currentPage ?? '') === $key ? 'active' : ''; ?>" href="<?php echo $href; ?>" title="<?php echo $label; ?>">
                        <i class="fas <?php echo $icon; ?>"></i><span><?php echo $label; ?></span>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endforeach; ?>
        <a class="nav-link logout-link" href="logout.php" title="Logout"><i class="fas fa-sign-out-alt"></i><span>Logout</span></a>
    </nav>
    <div class="sidebar-profile">
        <div class="sidebar-avatar"><i class="fas fa-user-shield"></i></div>
        <div><strong><?php echo htmlspecialchars($adminAuth->getAdminUsername()); ?></strong><small>Administrator</small></div>
    </div>
</aside>

<div class="offcanvas offcanvas-start" tabindex="-1" id="adminSidebar" aria-labelledby="adminSidebarLabel">
    <div class="offcanvas-header drawer-header">
        <a class="drawer-brand" href="index.php">
            <img src="../assets/app_logo.png" alt="Calamus Education">
            <span>
                <strong id="adminSidebarLabel">Calamus Admin</strong>
                <small>Administration workspace</small>
            </span>
        </a>
        <button type="button" class="btn icon-btn small drawer-close" data-bs-dismiss="offcanvas" aria-label="Close navigation">
            <i class="fas fa-xmark"></i>
        </button>
    </div>
    <div class="offcanvas-body">
        <nav class="portal-nav">
            <?php foreach ($adminNavGroups as $groupLabel => $items): ?>
                <div class="portal-nav-group">
                    <span class="portal-nav-label"><?php echo htmlspecialchars($groupLabel); ?></span>
                    <?php foreach ($items as [$key, $href, $icon, $label]): ?>
                        <a class="nav-link <?php echo ($currentPage ?? '') === $key ? 'active' : ''; ?>" href="<?php echo $href; ?>">
                            <i class="fas <?php echo $icon; ?>"></i><span><?php echo $label; ?></span>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php endforeach; ?>
        </nav>
    </div>
    <div class="drawer-actions">
        <div class="drawer-profile">
            <span class="sidebar-avatar"><i class="fas fa-user-shield"></i></span>
            <div>
                <strong><?php echo htmlspecialchars($adminAuth->getAdminUsername()); ?></strong>
                <small>Administrator</small>
            </div>
        </div>
        <a class="btn secondary drawer-logout" href="logout.php">
            <i class="fas fa-sign-out-alt"></i><span>Logout</span>
        </a>
    </div>
</div>

<main class="admin-main">
    <header class="admin-topbar glass">
        <div class="d-flex align-items-center gap-2 min-w-0">
            <button class="btn icon-btn mobile-nav-toggle" type="button" data-bs-toggle="offcanvas" data-bs-target="#adminSidebar" aria-controls="adminSidebar" aria-label="Open navigation">
                <i class="fas fa-bars"></i>
            </button>
            <div class="topbar-title">
                <strong><?php echo htmlspecialchars($pageTitle ?? 'Admin Dashboard'); ?></strong>
                <small>Administration workspace</small>
            </div>
        </div>
        <div class="topbar-actions">
            <span class="topbar-chip">Welcome, <?php echo htmlspecialchars($adminAuth->getAdminUsername()); ?></span>
            <button class="btn icon-btn theme-toggle" type="button" data-theme-toggle aria-label="Toggle color theme">
                <i class="fas fa-moon theme-light-icon"></i>
                <i class="fas fa-sun theme-dark-icon"></i>
            </button>
            <a class="btn icon-btn" href="logout.php" aria-label="Logout"><i class="fas fa-sign-out-alt"></i></a>
        </div>
    </header>
    <div class="admin-content">
