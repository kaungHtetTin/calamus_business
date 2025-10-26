<?php
/**
 * Payout History Page
 * View all payout history records
 */

require_once '../classes/admin_auth.php';

$adminAuth = new AdminAuth();

// Check if admin is logged in
if (!$adminAuth->isLoggedIn()) {
    header('Location: login.php');
    exit();
}

// Get filter parameters
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 20;
$status = isset($_GET['status']) && $_GET['status'] != '' ? $_GET['status'] : null;
$startDate = isset($_GET['start_date']) && $_GET['start_date'] != '' ? $_GET['start_date'] : null;
$endDate = isset($_GET['end_date']) && $_GET['end_date'] != '' ? $_GET['end_date'] : null;

// Get payout histories with filters
$historiesData = $adminAuth->getPayoutHistories($page, $limit, $status, $startDate, $endDate);

// Get statistics
$stats = $adminAuth->getPayoutHistoryStatistics($status, $startDate, $endDate);

$pageTitle = 'Payout History';
$currentPage = 'payout_history';
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
</head>
<body>
    <!-- Admin Header -->
    <?php include 'layout/admin_header.php'; ?>
    
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
                <div class="stat-label">Total Payout</div>
                <div class="stat-value"><?php echo number_format($stats['total_amount'], 2); ?> MMK</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Total Transactions</div>
                <div class="stat-value"><?php echo number_format($stats['total_transactions']); ?></div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Pending Amount</div>
                <div class="stat-value"><?php echo number_format($stats['pending_amount'], 2); ?> MMK</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Received Amount</div>
                <div class="stat-value"><?php echo number_format($stats['received_amount'], 2); ?> MMK</div>
            </div>
        </div>
        
        <!-- Filters -->
        <div class="filter-card">
            <h5 class="mb-3" style="color: #202124;">
                <i class="fas fa-filter me-2"></i>Filters
            </h5>
            <form method="GET" class="row g-3">
                <div class="col-md-3">
                    <label for="status" class="form-label">Status</label>
                    <select class="form-select" id="status" name="status">
                        <option value="">All Status</option>
                        <option value="pending" <?php echo $status == 'pending' ? 'selected' : ''; ?>>Pending</option>
                        <option value="received" <?php echo $status == 'received' ? 'selected' : ''; ?>>Received</option>
                        <option value="rejected" <?php echo $status == 'rejected' ? 'selected' : ''; ?>>Rejected</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="start_date" class="form-label">Start Date</label>
                    <input type="date" class="form-control" id="start_date" name="start_date" value="<?php echo $startDate; ?>">
                </div>
                <div class="col-md-3">
                    <label for="end_date" class="form-label">End Date</label>
                    <input type="date" class="form-control" id="end_date" name="end_date" value="<?php echo $endDate; ?>">
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search me-2"></i>Apply Filters
                    </button>
                </div>
            </form>
            <?php if ($status || $startDate || $endDate): ?>
            <div class="mt-3">
                <a href="payout_history.php" class="btn btn-sm btn-outline-secondary">
                    <i class="fas fa-times me-2"></i>Clear Filters
                </a>
            </div>
            <?php endif; ?>
        </div>
        
        <!-- Payout History Table -->
        <div class="logs-table">
            <div class="table-header">
                <h5 class="table-title">Payout History (<?php echo number_format($historiesData['total']); ?>)</h5>
                <a href="payout_history.php" class="btn btn-sm btn-primary">
                    <i class="fas fa-sync-alt me-2"></i>Refresh
                </a>
            </div>
            
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Partner Name</th>
                            <th>Payment Method</th>
                            <th>Payment Account</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($historiesData['histories']) > 0): ?>
                        <?php foreach ($historiesData['histories'] as $history): ?>
                        <tr>
                            <td>#<?php echo htmlspecialchars($history['id']); ?></td>
                            <td>
                                <div><?php echo htmlspecialchars($history['contact_name'] ?? 'N/A'); ?></div>
                                <small class="text-muted"><?php echo htmlspecialchars($history['company_name'] ?? 'N/A'); ?></small>
                            </td>
                            <td><?php echo htmlspecialchars($history['payment_method']); ?></td>
                            <td>
                                <div><strong><?php echo htmlspecialchars($history['account_name']); ?></strong></div>
                                <small class="text-muted"><?php echo htmlspecialchars($history['account_number']); ?></small>
                            </td>
                            <td><?php echo number_format($history['amount'], 2); ?> MMK</td>
                            <td>
                                <?php
                                $status = strtolower($history['status'] ?? 'pending');
                                $statusClass = $status === 'received' ? 'status-paid' : ($status === 'rejected' ? 'status-pending' : 'status-pending');
                                ?>
                                <span class="status-badge <?php echo $statusClass; ?>"><?php echo ucfirst($status); ?></span>
                            </td>
                            <td><?php echo date('M d, Y', strtotime($history['created_at'])); ?></td>
                            <td>
                                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="viewPayoutDetails(<?php echo $history['id']; ?>)">
                                    <i class="fas fa-eye"></i> View
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php else: ?>
                        <tr>
                            <td colspan="8" class="text-center py-5">
                                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                <p class="text-muted">No payout history found</p>
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <?php if ($historiesData['total_pages'] > 1): ?>
            <div class="d-flex justify-content-between align-items-center p-3 border-top">
                <div class="text-muted">
                    Showing <?php echo (($page - 1) * $limit) + 1; ?> to <?php echo min($page * $limit, $historiesData['total']); ?> of <?php echo number_format($historiesData['total']); ?> records
                </div>
                <nav>
                    <ul class="pagination mb-0">
                        <?php
                        $searchParam = '';
                        if ($status) $searchParam .= '&status=' . urlencode($status);
                        if ($startDate) $searchParam .= '&start_date=' . urlencode($startDate);
                        if ($endDate) $searchParam .= '&end_date=' . urlencode($endDate);
                        ?>
                        <!-- Previous Button -->
                        <li class="page-item <?php echo $page <= 1 ? 'disabled' : ''; ?>">
                            <a class="page-link" href="payout_history.php?page=<?php echo $page - 1; ?><?php echo $searchParam; ?>">Previous</a>
                        </li>
                        
                        <?php
                        $totalPages = $historiesData['total_pages'];
                        $startPage = max(1, $page - 2);
                        $endPage = min($totalPages, $page + 2);
                        
                        // Show first page if not in range
                        if ($startPage > 1): ?>
                            <li class="page-item">
                                <a class="page-link" href="payout_history.php?page=1<?php echo $searchParam; ?>">1</a>
                            </li>
                            <?php if ($startPage > 2): ?>
                                <li class="page-item disabled">
                                    <span class="page-link">...</span>
                                </li>
                            <?php endif; ?>
                        <?php endif; ?>
                        
                        <?php for ($i = $startPage; $i <= $endPage; $i++): ?>
                            <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                                <a class="page-link" href="payout_history.php?page=<?php echo $i; ?><?php echo $searchParam; ?>"><?php echo $i; ?></a>
                            </li>
                        <?php endfor; ?>
                        
                        <?php if ($endPage < $totalPages): ?>
                            <?php if ($endPage < $totalPages - 1): ?>
                                <li class="page-item disabled">
                                    <span class="page-link">...</span>
                                </li>
                            <?php endif; ?>
                            <li class="page-item">
                                <a class="page-link" href="payout_history.php?page=<?php echo $totalPages; ?><?php echo $searchParam; ?>"><?php echo $totalPages; ?></a>
                            </li>
                        <?php endif; ?>
                        
                        <!-- Next Button -->
                        <li class="page-item <?php echo $page >= $totalPages ? 'disabled' : ''; ?>">
                            <a class="page-link" href="payout_history.php?page=<?php echo $page + 1; ?><?php echo $searchParam; ?>">Next</a>
                        </li>
                    </ul>
                </nav>
            </div>
            <?php endif; ?>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function viewPayoutDetails(id) {
            // Implement view payout details logic
            alert('View payout details for ID: ' + id);
            // You can redirect to a details page or open a modal here
        }
    </script>
</body>
</html>
