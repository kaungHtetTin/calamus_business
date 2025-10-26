<?php
/**
 * Payout Logs Page
 * View and manage partner payouts
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

// Get payout logs with filters
$logsData = $adminAuth->getPayoutLogs($page, $limit, $status, $startDate, $endDate);

// Get statistics
$stats = $adminAuth->getPayoutLogsStatistics($status, $startDate, $endDate);

$pageTitle = 'Payout Logs';
$currentPage = 'payout_logs';
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
        .filter-card {
            background: white;
            border: 1px solid #e8eaed;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 24px;
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
        
        .logs-table {
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
        
        .status-badge {
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 13px;
            font-weight: 500;
        }
        
        .status-pending {
            background: #fef7e0;
            color: #ea8600;
        }
        
        .status-paid {
            background: #e6f4ea;
            color: #137333;
        }
    </style>
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
                <div class="stat-label">Total Partners</div>
                <div class="stat-value"><?php echo number_format($stats['total_partners']); ?></div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Pending Payout</div>
                <div class="stat-value"><?php echo number_format($stats['pending_amount'], 2); ?> MMK</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Paid Amount</div>
                <div class="stat-value"><?php echo number_format($stats['paid_amount'], 2); ?> MMK</div>
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
                        <option value="paid" <?php echo $status == 'paid' ? 'selected' : ''; ?>>Paid</option>
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
                <a href="payout_logs.php" class="btn btn-sm btn-outline-secondary">
                    <i class="fas fa-times me-2"></i>Clear Filters
                </a>
            </div>
            <?php endif; ?>
        </div>
        
        <!-- Payout Table -->
        <div class="logs-table">
            <div class="table-header">
                <h2 class="mb-0" style="font-size: 18px; font-weight: 400; color: #202124;">
                    All Payout Logs (<?php echo number_format($logsData['total']); ?>)
                </h2>
            </div>
            
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Partner ID</th>
                            <th>Contact Name</th>
                            <th>Company</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Total Amount</th>
                            <th>Transactions</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($logsData['logs']) > 0): ?>
                        <?php foreach ($logsData['logs'] as $log): ?>
                        <tr>
                            <td>#<?php echo htmlspecialchars($log['partner_id']); ?></td>
                            <td><?php echo htmlspecialchars($log['contact_name'] ?? 'N/A'); ?></td>
                            <td><?php echo htmlspecialchars($log['company_name'] ?? 'N/A'); ?></td>
                            <td><?php echo htmlspecialchars($log['email'] ?? 'N/A'); ?></td>
                            <td><?php echo htmlspecialchars($log['phone'] ?? 'N/A'); ?></td>
                            <td style="font-weight: 600; color: #202124;"><?php echo number_format($log['total_amount'] ?? 0, 2); ?> MMK</td>
                            <td><?php echo number_format($log['transaction_count'] ?? 0); ?></td>
                            <td>
                                <?php
                                $logStatus = strtolower($log['status'] ?? 'pending');
                                $statusClass = $logStatus === 'paid' ? 'status-paid' : 'status-pending';
                                ?>
                                <span class="status-badge <?php echo $statusClass; ?>"><?php echo ucfirst($logStatus); ?></span>
                            </td>
                            <td>
                                <?php if (strtolower($log['status']) == 'pending'): ?>
                                    <form method="POST" action="process_payout.php" style="display: inline;" onsubmit="return confirm('Are you sure you want to process payout for <?php echo htmlspecialchars($log['contact_name']); ?>? Amount: <?php echo number_format($log['total_amount'], 2); ?> MMK');">
                                        <input type="hidden" name="partner_id" value="<?php echo htmlspecialchars($log['partner_id']); ?>">
                                        <input type="hidden" name="amount" value="<?php echo $log['total_amount']; ?>">
                                        <button type="submit" class="btn btn-sm btn-success">
                                            <i class="fas fa-check-circle me-1"></i>Process Payout
                                        </button>
                                    </form>
                                <?php else: ?>
                                    <span class="text-muted">Already Paid</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php else: ?>
                        <tr>
                            <td colspan="9" class="text-center py-5">
                                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                <p class="text-muted">No payout logs found</p>
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <?php if ($logsData['total_pages'] > 1): ?>
            <div class="d-flex justify-content-between align-items-center p-3 border-top">
                <div class="text-muted">
                    Showing <?php echo (($page - 1) * $limit) + 1; ?> to <?php echo min($page * $limit, $logsData['total']); ?> of <?php echo number_format($logsData['total']); ?> logs
                </div>
                <nav>
                    <ul class="pagination mb-0">
                        <!-- Previous Button -->
                        <li class="page-item <?php echo $page <= 1 ? 'disabled' : ''; ?>">
                            <a class="page-link" href="payout_logs.php?page=<?php echo $page - 1; ?><?php echo $status ? '&status=' . $status : ''; ?><?php echo $startDate ? '&start_date=' . $startDate : ''; ?><?php echo $endDate ? '&end_date=' . $endDate : ''; ?>">Previous</a>
                        </li>
                        
                        <?php
                        $totalPages = $logsData['total_pages'];
                        $startPage = max(1, $page - 2);
                        $endPage = min($totalPages, $page + 2);
                        
                        // Show first page if not in range
                        if ($startPage > 1): ?>
                            <li class="page-item">
                                <a class="page-link" href="payout_logs.php?page=1<?php echo $status ? '&status=' . $status : ''; ?><?php echo $startDate ? '&start_date=' . $startDate : ''; ?><?php echo $endDate ? '&end_date=' . $endDate : ''; ?>">1</a>
                            </li>
                            <?php if ($startPage > 2): ?>
                                <li class="page-item disabled">
                                    <span class="page-link">...</span>
                                </li>
                            <?php endif; ?>
                        <?php endif; ?>
                        
                        <?php for ($i = $startPage; $i <= $endPage; $i++): ?>
                            <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                                <a class="page-link" href="payout_logs.php?page=<?php echo $i; ?><?php echo $status ? '&status=' . $status : ''; ?><?php echo $startDate ? '&start_date=' . $startDate : ''; ?><?php echo $endDate ? '&end_date=' . $endDate : ''; ?>"><?php echo $i; ?></a>
                            </li>
                        <?php endfor; ?>
                        
                        <?php if ($endPage < $totalPages): ?>
                            <?php if ($endPage < $totalPages - 1): ?>
                                <li class="page-item disabled">
                                    <span class="page-link">...</span>
                                </li>
                            <?php endif; ?>
                            <li class="page-item">
                                <a class="page-link" href="payout_logs.php?page=<?php echo $totalPages; ?><?php echo $status ? '&status=' . $status : ''; ?><?php echo $startDate ? '&start_date=' . $startDate : ''; ?><?php echo $endDate ? '&end_date=' . $endDate : ''; ?>"><?php echo $totalPages; ?></a>
                            </li>
                        <?php endif; ?>
                        
                        <!-- Next Button -->
                        <li class="page-item <?php echo $page >= $totalPages ? 'disabled' : ''; ?>">
                            <a class="page-link" href="payout_logs.php?page=<?php echo $page + 1; ?><?php echo $status ? '&status=' . $status : ''; ?><?php echo $startDate ? '&start_date=' . $startDate : ''; ?><?php echo $endDate ? '&end_date=' . $endDate : ''; ?>">Next</a>
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

