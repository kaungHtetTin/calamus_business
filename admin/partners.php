<?php
/**
 * Admin Partners Page
 * Detailed partner management
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

// Get search query from URL
$searchQuery = isset($_GET['search']) ? trim($_GET['search']) : null;

// Get partners with pagination and search
$partnersData = $adminAuth->getAllPartners($currentPageNum, $limit, $searchQuery);

$pageTitle = 'Manage Partners';
$isAdmin = true;
$currentPage = 'partners';
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
    <link rel="stylesheet" href="css/app.css">
    <style>
        .verified-icon {
            color: #137333;
        }
        
        .unverified-icon {
            color: #d93025;
        }
    </style>
</head>
<body>
    <!-- Admin Header -->
    <?php include 'layout/admin_header.php'; ?>
    
    <?php include 'layout/admin_sidebar.php'; ?>
    
    <!-- Main Content -->
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
        
        <!-- Search Form -->
        <div class="filter-card">
            <h5 class="mb-3" style="color: #202124;">
                <i class="fas fa-search me-2"></i>Search Partners
            </h5>
            <form method="GET" class="row g-3">
                <div class="col-md-10">
                    <input type="text" class="form-control" name="search" placeholder="Search by email, contact name, or company name..." value="<?php echo htmlspecialchars($searchQuery ?? ''); ?>">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search me-2"></i>Search
                    </button>
                </div>
                <?php if ($searchQuery): ?>
                    <div class="col-12">
                        <a href="partners.php" class="btn btn-sm btn-outline-secondary">
                            <i class="fas fa-times me-2"></i>Clear Search
                        </a>
                        <span class="ms-2 text-muted">
                            Showing results for: "<strong><?php echo htmlspecialchars($searchQuery); ?></strong>"
                        </span>
                    </div>
                <?php endif; ?>
            </form>
        </div>
        
        <!-- Account Status Check Table (Eligible Partners) -->
        <?php 
            $eligiblePartners = $adminAuth->getPartnersEligibleForStatusCheck();
            $hasEligible = is_array($eligiblePartners) && count($eligiblePartners) > 0;
        ?>
        <?php if ($hasEligible): ?>
        <div class="partners-table mb-4">
            <div class="table-header">
                <h2 class="table-title">Account Status Check (<?php echo number_format(count($eligiblePartners)); ?>)</h2>
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Partner ID</th>
                            <th>Company Name</th>
                            <th>Contact Name</th>
                            <th>Email</th>
                            <th>Email Verified</th>
                            <th>Payment Method</th>
                            <th>Personal Info</th>
                            <th>Joined</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($eligiblePartners as $partner): ?>
                        <?php
                            $emailVerified = !empty($partner['email_verified']);
                            // minimal flags already ensured by query; compute for display
                            $personalInfoComplete = (!empty($partner['address']) && !empty($partner['city']) && !empty($partner['state']) && !empty($partner['national_id_card_number']));
                            // has payment method check via a quick existence query
                            $pmCountRes = (new Database())->read("SELECT COUNT(*) AS cnt FROM partner_payment_methods WHERE partner_id = '" . $partner['id'] . "'");
                            $hasPaymentMethod = $pmCountRes && isset($pmCountRes[0]['cnt']) && (int)$pmCountRes[0]['cnt'] > 0;
                        ?>
                        <tr>
                            <td>#<?php echo htmlspecialchars($partner['id']); ?></td>
                            <td><?php echo htmlspecialchars($partner['company_name'] ?? 'N/A'); ?></td>
                            <td><?php echo htmlspecialchars($partner['contact_name'] ?? 'N/A'); ?></td>
                            <td><?php echo htmlspecialchars($partner['email']); ?></td>
                            <td>
                                <?php if ($emailVerified): ?>
                                    <span class="badge bg-success">Verified</span>
                                <?php else: ?>
                                    <span class="badge bg-danger">Not Verified</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($hasPaymentMethod): ?>
                                    <span class="badge bg-success">Added</span>
                                <?php else: ?>
                                    <span class="badge bg-danger">Missing</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($personalInfoComplete): ?>
                                    <span class="badge bg-success">Complete</span>
                                <?php else: ?>
                                    <span class="badge bg-danger">Incomplete</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo date('M d, Y', strtotime($partner['created_at'])); ?></td>
                            <td>
                                <div class="btn-group" role="group">
                                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="window.location.href='account_verify.php?id=<?php echo $partner['id']; ?>'">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php endif; ?>

        <!-- Partners Table -->
        <div class="partners-table">
            <div class="table-header">
                <h2 class="table-title">All Partners (<?php echo number_format($partnersData['total']); ?>)</h2>
                <div>
                    <a href="create_partner.php" class="btn btn-sm btn-success me-2">
                        <i class="fas fa-plus me-2"></i>Create New Partner
                    </a>
                    <a href="partners.php<?php echo $searchQuery ? '?search=' . urlencode($searchQuery) : ''; ?>" class="btn btn-sm btn-primary">
                        <i class="fas fa-sync-alt me-2"></i>Refresh
                    </a>
                </div>
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
                            <th>Account Verified</th>
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
                            <td>
                                <?php if ($partner['account_verified']): ?>
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
                        <?php $searchParam = $searchQuery ? '&search=' . urlencode($searchQuery) : ''; ?>
                        <li class="page-item <?php echo $currentPageNum <= 1 ? 'disabled' : ''; ?>">
                            <a class="page-link" href="partners.php?page=<?php echo $currentPageNum - 1; ?><?php echo $searchParam; ?>">Previous</a>
                        </li>
                        
                        <?php
                        $totalPages = $partnersData['total_pages'];
                        $startPage = max(1, $currentPageNum - 2);
                        $endPage = min($totalPages, $currentPageNum + 2);
                        
                        // Show first page if not in range
                        if ($startPage > 1): ?>
                            <li class="page-item">
                                <a class="page-link" href="partners.php?page=1<?php echo $searchParam; ?>">1</a>
                            </li>
                            <?php if ($startPage > 2): ?>
                                <li class="page-item disabled">
                                    <span class="page-link">...</span>
                                </li>
                            <?php endif; ?>
                        <?php endif; ?>
                        
                        <?php for ($i = $startPage; $i <= $endPage; $i++): ?>
                            <li class="page-item <?php echo $i == $currentPageNum ? 'active' : ''; ?>">
                                <a class="page-link" href="partners.php?page=<?php echo $i; ?><?php echo $searchParam; ?>"><?php echo $i; ?></a>
                            </li>
                        <?php endfor; ?>
                        
                        <?php if ($endPage < $totalPages): ?>
                            <?php if ($endPage < $totalPages - 1): ?>
                                <li class="page-item disabled">
                                    <span class="page-link">...</span>
                                </li>
                            <?php endif; ?>
                            <li class="page-item">
                                <a class="page-link" href="partners.php?page=<?php echo $totalPages; ?><?php echo $searchParam; ?>"><?php echo $totalPages; ?></a>
                            </li>
                        <?php endif; ?>
                        
                        <!-- Next Button -->
                        <li class="page-item <?php echo $currentPageNum >= $totalPages ? 'disabled' : ''; ?>">
                            <a class="page-link" href="partners.php?page=<?php echo $currentPageNum + 1; ?><?php echo $searchParam; ?>">Next</a>
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
