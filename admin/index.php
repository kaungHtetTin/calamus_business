<?php
/**
 * Admin Dashboard
 * Main page for managing partners
 */

require_once '../classes/admin_auth.php';

$adminAuth = new AdminAuth();

// Check if admin is logged in
if (!$adminAuth->isLoggedIn()) {
    header('Location: login.php');
    exit();
}

// Get statistics
$stats = $adminAuth->getPartnerStatistics();

// Get current page from URL
$currentPageNum = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 20; // Partners per page

// Get partners with pagination
$partnersData = $adminAuth->getAllPartners($currentPageNum, $limit);

$pageTitle = 'Admin Dashboard';
$isAdmin = true;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?> - Calamus Education Partner Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../css/app.css">
    <style>
        .admin-header {
            background: white;
            border-bottom: 1px solid #e8eaed;
            padding: 12px 24px;
        }
        
        .admin-header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .admin-title {
            font-size: 22px;
            font-weight: 400;
            color: #202124;
        }
        
        .admin-actions button {
            margin-left: 8px;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background: white;
            border: 1px solid #e8eaed;
            border-radius: 8px;
            padding: 20px;
            transition: box-shadow 0.2s;
        }
        
        .stat-card:hover {
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        
        .stat-label {
            font-size: 14px;
            color: #5f6368;
            margin-bottom: 8px;
        }
        
        .stat-value {
            font-size: 32px;
            font-weight: 400;
            color: #202124;
        }
        
        .partners-table {
            background: white;
            border: 1px solid #e8eaed;
            border-radius: 8px;
            overflow: hidden;
        }
        
        .table-header {
            padding: 16px 24px;
            border-bottom: 1px solid #e8eaed;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .table-title {
            font-size: 18px;
            font-weight: 400;
            color: #202124;
        }
        
        .btn-icon {
            background: none;
            border: 1px solid #dadce0;
            color: #5f6368;
            padding: 8px 12px;
            border-radius: 4px;
            cursor: pointer;
            transition: all 0.2s;
        }
        
        .btn-icon:hover {
            background: #f8f9fa;
            color: #202124;
        }
        
        .btn-success {
            background: #137333;
            color: white;
            border: none;
        }
        
        .btn-danger {
            background: #d93025;
            color: white;
            border: none;
        }
        
        .btn-warning {
            background: #fbbc04;
            color: white;
            border: none;
        }
        
        .status-badge {
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 13px;
            font-weight: 500;
        }
        
        .status-active {
            background: #e6f4ea;
            color: #137333;
        }
        
        .status-inactive {
            background: #fce8e6;
            color: #d93025;
        }
        
        .status-suspended {
            background: #fef7e0;
            color: #ea8600;
        }
        
        .verified-icon {
            color: #137333;
        }
        
        .unverified-icon {
            color: #d93025;
        }
        
        .actions-dropdown {
            position: absolute;
            right: 0;
            top: 100%;
            background: white;
            border: 1px solid #dadce0;
            border-radius: 4px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            min-width: 180px;
            z-index: 1000;
        }
        
        .dropdown-item {
            padding: 12px 16px;
            cursor: pointer;
            transition: background 0.2s;
        }
        
        .dropdown-item:hover {
            background: #f8f9fa;
        }
        
        .dropdown-item-danger {
            color: #d93025;
        }
        
        /* Navigation Links Hover Effect */
        .offcanvas-body .nav-link {
            transition: all 0.2s ease;
        }
        
        .offcanvas-body .nav-link:hover {
            background: rgba(255, 255, 255, 0.1) !important;
            color: white !important;
            transform: translateX(4px);
        }
        
        .offcanvas-body .nav-link.active {
            color: #e8f0fe !important;
            background: rgba(232, 240, 254, 0.1) !important;
        }
    </style>
</head>
<body>
    <!-- Admin Header -->
    <nav class="navbar navbar-expand-lg admin-navbar" style="background: white; border-bottom: 1px solid #e8eaed; padding: 12px 24px;">
        <div class="container-fluid">
            <button class="btn btn-sm me-2" type="button" data-bs-toggle="offcanvas" data-bs-target="#adminSidebar" aria-controls="adminSidebar" style="border: 1px solid #dadce0;">
                <i class="fas fa-bars"></i>
            </button>
            <h1 class="admin-title mb-0" style="font-size: 22px; font-weight: 400; color: #202124;">
                <i class="fas fa-shield-alt me-2"></i>Admin Dashboard
            </h1>
            <div class="ms-auto d-flex align-items-center">
                <span class="text-muted me-3">Welcome, <?php echo htmlspecialchars($adminAuth->getAdminUsername()); ?></span>
                <button class="btn btn-sm btn-outline-secondary" onclick="window.location.href='logout.php'">
                    <i class="fas fa-sign-out-alt me-2"></i>Logout
                </button>
            </div>
        </div>
    </nav>
    
    
    <?php include 'layout/admin_sidebar.php'; ?>

    <div class="container-fluid" style="padding: 24px;">
        <!-- Alert Messages -->
        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php echo htmlspecialchars($_GET['success']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        
        <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php echo htmlspecialchars($_GET['error']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        
        <!-- Statistics -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-label">Total Partners</div>
                <div class="stat-value"><?php echo number_format($stats['total']); ?></div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Active Partners</div>
                <div class="stat-value"><?php echo number_format($stats['active']); ?></div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Verified Partners</div>
                <div class="stat-value"><?php echo number_format($stats['verified']); ?></div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Unverified Partners</div>
                <div class="stat-value"><?php echo number_format($stats['unverified']); ?></div>
            </div>
        </div>
        
        <!-- Partners Table -->
        <div class="partners-table">
            <div class="table-header">
                <h2 class="table-title">All Partners (<?php echo number_format($partnersData['total']); ?>)</h2>
                <a href="partners.php" class="btn btn-sm btn-primary">
                    <i class="fas fa-sync-alt me-2"></i>Refresh
                </a>
            </div>
            
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Partner ID</th>
                            <th>Company Name</th>
                            <th>Contact Name</th>
                            <th>Email</th>
                            <th>Status</th>
                            <th>Verified</th>
                            <th>Joined</th>
                            <th>Last Login</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($partnersData['partners'] as $partner): ?>
                        <tr data-partner-id="<?php echo htmlspecialchars($partner['id']); ?>">
                            <td>#<?php echo htmlspecialchars($partner['id']); ?></td>
                            <td><?php echo htmlspecialchars($partner['company_name'] ?? 'N/A'); ?></td>
                            <td><?php echo htmlspecialchars($partner['contact_name'] ?? 'N/A'); ?></td>
                            <td><?php echo htmlspecialchars($partner['email']); ?></td>
                            <td>
                                <?php
                                $status = $partner['status'] ?? 'active';
                                $statusClass = 'status-active';
                                if ($status === 'inactive') $statusClass = 'status-inactive';
                                if ($status === 'suspended') $statusClass = 'status-suspended';
                                ?>
                                <span class="status-badge <?php echo $statusClass; ?>"><?php echo ucfirst($status); ?></span>
                            </td>
                            <td>
                                <?php if ($partner['email_verified']): ?>
                                    <i class="fas fa-check-circle verified-icon"></i>
                                <?php else: ?>
                                    <i class="fas fa-times-circle unverified-icon"></i>
                                <?php endif; ?>
                            </td>
                            <td><?php echo date('M d, Y', strtotime($partner['created_at'])); ?></td>
                            <td><?php echo $partner['last_login'] ? date('M d, Y', strtotime($partner['last_login'])) : 'Never'; ?></td>
                            <td>
                                <div class="btn-group" role="group">
                                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="window.location.href='view_partner.php?id=<?php echo $partner['id']; ?>'">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <?php if ($partnersData['total_pages'] > 1): ?>
            <div class="d-flex justify-content-between align-items-center p-3 border-top">
                <div class="text-muted">
                    Showing <?php echo (($currentPageNum - 1) * $limit) + 1; ?> to <?php echo min($currentPageNum * $limit, $partnersData['total']); ?> of <?php echo number_format($partnersData['total']); ?> partners
                </div>
                <nav>
                    <ul class="pagination mb-0">
                        <!-- Previous Button -->
                        <li class="page-item <?php echo $currentPageNum <= 1 ? 'disabled' : ''; ?>">
                            <a class="page-link" href="index.php?page=<?php echo $currentPageNum - 1; ?>">Previous</a>
                        </li>
                        
                        <?php
                        $totalPages = $partnersData['total_pages'];
                        $startPage = max(1, $currentPageNum - 2);
                        $endPage = min($totalPages, $currentPageNum + 2);
                        
                        // Show first page if not in range
                        if ($startPage > 1): ?>
                            <li class="page-item">
                                <a class="page-link" href="index.php?page=1">1</a>
                            </li>
                            <?php if ($startPage > 2): ?>
                                <li class="page-item disabled">
                                    <span class="page-link">...</span>
                                </li>
                            <?php endif; ?>
                        <?php endif; ?>
                        
                        <?php for ($i = $startPage; $i <= $endPage; $i++): ?>
                            <li class="page-item <?php echo $i == $currentPageNum ? 'active' : ''; ?>">
                                <a class="page-link" href="index.php?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                            </li>
                        <?php endfor; ?>
                        
                        <?php if ($endPage < $totalPages): ?>
                            <?php if ($endPage < $totalPages - 1): ?>
                                <li class="page-item disabled">
                                    <span class="page-link">...</span>
                                </li>
                            <?php endif; ?>
                            <li class="page-item">
                                <a class="page-link" href="index.php?page=<?php echo $totalPages; ?>"><?php echo $totalPages; ?></a>
                            </li>
                        <?php endif; ?>
                        
                        <!-- Next Button -->
                        <li class="page-item <?php echo $currentPageNum >= $totalPages ? 'disabled' : ''; ?>">
                            <a class="page-link" href="index.php?page=<?php echo $currentPageNum + 1; ?>">Next</a>
                        </li>
                    </ul>
                </nav>
            </div>
            <?php endif; ?>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
