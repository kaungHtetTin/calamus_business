<?php
require_once 'classes/autoload.php';

$pageTitle = 'Payment History';
include 'layout/header.php';

// Get payment history data
$paymentHistoriesManager = new PartnerPaymentHistoriesManager();
$paymentHistories = $paymentHistoriesManager->getPartnerPaymentHistories($currentPartner['id'], null, 20, 0);
$paymentStats = $paymentHistoriesManager->getPartnerPaymentStats($currentPartner['id']);
?>

<div class="content-section">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">
                <i class="fas fa-credit-card me-2"></i>Payment History
            </h4>
            <p class="text-muted mb-0">Track your payment disbursements and transaction status</p>
        </div>
        <div class="text-end">
            <div class="h3 mb-0 text-success">$<?php echo number_format($paymentStats['total_received'], 2); ?></div>
            <small class="text-muted">Total Received</small>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card bg-light">
                <div class="card-body">
                    <h6 class="card-title mb-3">
                        <i class="fas fa-filter me-2"></i>Filter Payments
                    </h6>
                    <div class="row">
                        <!-- Status Filter -->
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Status</label>
                            <select class="form-select" id="statusFilter">
                                <option value="">All Status</option>
                                <option value="received">Received</option>
                                <option value="pending">Pending</option>
                                <option value="rejected">Rejected</option>
                            </select>
                        </div>
                        
                        <!-- Period Filter -->
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Period</label>
                            <select class="form-select" id="periodFilter">
                                <option value="">All Time</option>
                                <option value="today">Today</option>
                                <option value="week">This Week</option>
                                <option value="month">This Month</option>
                                <option value="quarter">This Quarter</option>
                                <option value="year">This Year</option>
                                <option value="custom">Custom Range</option>
                            </select>
                        </div>
                        
                        <!-- Custom Date Range -->
                        <div class="col-md-4 mb-3" id="customDateRange" style="display: none;">
                            <label class="form-label">Date Range</label>
                            <div class="row">
                                <div class="col-6">
                                    <input type="date" class="form-control" id="startDate" placeholder="Start Date">
                                </div>
                                <div class="col-6">
                                    <input type="date" class="form-control" id="endDate" placeholder="End Date">
                                </div>
                            </div>
                        </div>
                        
                        <!-- Filter Actions -->
                        <div class="col-md-2 mb-3">
                            <label class="form-label">&nbsp;</label>
                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-primary btn-sm" id="applyFilters">
                                    <i class="fas fa-search me-1"></i>Filter
                                </button>
                                <button type="button" class="btn btn-outline-secondary btn-sm" id="clearFilters">
                                    <i class="fas fa-times me-1"></i>Clear
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3 mb-4">
            <div class="card stat-card">
                <div class="card-body text-center">
                    <i class="fas fa-check-circle fa-2x mb-2 text-success"></i>
                    <div class="stat-number text-success" id="totalReceived">$<?php echo number_format($paymentStats['total_received'], 2); ?></div>
                    <div>Total Received</div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-4">
            <div class="card stat-card">
                <div class="card-body text-center">
                    <i class="fas fa-clock fa-2x mb-2 text-warning"></i>
                    <div class="stat-number text-warning" id="totalPending">$<?php echo number_format($paymentStats['total_pending'], 2); ?></div>
                    <div>Pending</div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-4">
            <div class="card stat-card">
                <div class="card-body text-center">
                    <i class="fas fa-times-circle fa-2x mb-2 text-danger"></i>
                    <div class="stat-number text-danger" id="totalRejected">$<?php echo number_format($paymentStats['total_rejected'], 2); ?></div>
                    <div>Rejected</div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-4">
            <div class="card stat-card">
                <div class="card-body text-center">
                    <i class="fas fa-receipt fa-2x mb-2 text-info"></i>
                    <div class="stat-number text-info" id="totalPayments"><?php echo number_format($paymentStats['total_payments']); ?></div>
                    <div>Total Payments</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Payment History Table -->
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">
                <i class="fas fa-history me-2"></i>Payment History
            </h5>
        </div>
        <div class="card-body">
            <div id="paymentHistoriesContainer">
                <?php if (empty($paymentHistories)): ?>
                    <div class="text-center py-5">
                        <i class="fas fa-credit-card fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">No Payment History</h5>
                        <p class="text-muted">Your payment disbursements will appear here once processed.</p>
                        <button class="btn btn-primary" onclick="window.location.href='dashboard.php'">
                            <i class="fas fa-tachometer-alt me-2"></i>Go to Dashboard
                        </button>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Payment Details</th>
                                    <th>Account Information</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="paymentHistoriesTableBody">
                                <?php foreach ($paymentHistories as $payment): ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="me-2">
                                                    <i class="fas fa-<?php echo $payment['payment_method'] === 'Bank Transfer' ? 'university' : 'credit-card'; ?> text-primary"></i>
                                                </div>
                                                <div>
                                                    <strong><?php echo htmlspecialchars($payment['payment_method']); ?></strong>
                                                    <br>
                                                    <small class="text-muted">
                                                        ID: #<?php echo $payment['id']; ?>
                                                    </small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div>
                                                <strong><?php echo htmlspecialchars($payment['account_name']); ?></strong>
                                                <br>
                                                <small class="text-muted">
                                                    <?php echo htmlspecialchars($payment['account_number']); ?>
                                                </small>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="text-success fw-bold">
                                                $<?php echo number_format($payment['amount'], 2); ?>
                                            </div>
                                        </td>
                                        <td>
                                            <?php 
                                            $statusClass = '';
                                            $statusIcon = '';
                                            $statusText = '';
                                            
                                            switch($payment['status']) {
                                                case 'received':
                                                    $statusClass = 'bg-success';
                                                    $statusIcon = 'fas fa-check-circle';
                                                    $statusText = 'Received';
                                                    break;
                                                case 'pending':
                                                    $statusClass = 'bg-warning';
                                                    $statusIcon = 'fas fa-clock';
                                                    $statusText = 'Pending';
                                                    break;
                                                case 'rejected':
                                                    $statusClass = 'bg-danger';
                                                    $statusIcon = 'fas fa-times-circle';
                                                    $statusText = 'Rejected';
                                                    break;
                                            }
                                            ?>
                                            <span class="badge <?php echo $statusClass; ?>">
                                                <i class="<?php echo $statusIcon; ?> me-1"></i>
                                                <?php echo $statusText; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div>
                                                <?php echo date('M j, Y', strtotime($payment['created_at'])); ?>
                                                <br>
                                                <small class="text-muted">
                                                    <?php echo date('H:i', strtotime($payment['created_at'])); ?>
                                                </small>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <button type="button" class="btn btn-sm btn-outline-primary" onclick="viewPaymentDetails(<?php echo $payment['id']; ?>)">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <?php if ($payment['status'] === 'pending'): ?>
                                                    <button type="button" class="btn btn-sm btn-success" onclick="updatePaymentStatus(<?php echo $payment['id']; ?>, 'received')">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-sm btn-danger" onclick="updatePaymentStatus(<?php echo $payment['id']; ?>, 'rejected')">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Load More Button -->
                    <div class="text-center mt-3">
                        <button class="btn btn-outline-primary" id="loadMoreBtn">
                            <i class="fas fa-plus me-2"></i>Load More Payments
                        </button>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Payment Details Modal -->
<div class="modal fade" id="paymentDetailsModal" tabindex="-1" aria-labelledby="paymentDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="paymentDetailsModalLabel">
                    <i class="fas fa-info-circle me-2"></i>Payment Details
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="paymentDetailsContent">
                <!-- Content will be loaded dynamically -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Update Status Confirmation Modal -->
<div class="modal fade" id="updateStatusModal" tabindex="-1" aria-labelledby="updateStatusModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="updateStatusModalLabel">
                    <i class="fas fa-exclamation-triangle me-2"></i>Confirm Status Update
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to mark this payment as <span id="newStatusText" class="fw-bold"></span>?</p>
                <div class="alert alert-warning">
                    <i class="fas fa-info-circle me-2"></i>
                    This action cannot be undone. Please verify the payment details before confirming.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="confirmStatusUpdate">Confirm</button>
            </div>
        </div>
    </div>
</div>

<style>
/* Custom styles for payment history page */
.stat-card {
    background: linear-gradient(135deg, #4a5568 0%, #718096 100%);
    color: white;
    border: none;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.stat-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 15px rgba(0, 0, 0, 0.2);
}

.stat-number {
    font-size: 1.5rem;
    font-weight: bold;
    margin: 0.5rem 0;
}

.table th {
    background-color: #f8f9fa;
    border-top: none;
    font-weight: 600;
    color: #495057;
}

.table td {
    vertical-align: middle;
    border-top: 1px solid #dee2e6;
}

.table tbody tr:hover {
    background-color: #f8f9fa;
}

.badge {
    font-size: 0.75rem;
    padding: 0.375rem 0.75rem;
}

.text-success {
    color: #28a745 !important;
}

.btn-outline-primary {
    border-color: #4a5568;
    color: #4a5568;
}

.btn-outline-primary:hover {
    background-color: #4a5568;
    border-color: #4a5568;
    color: white;
}

.card {
    border: none;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.card-header {
    background-color: #f8f9fa;
    border-bottom: 1px solid #dee2e6;
}

.btn-group .btn {
    margin-right: 2px;
}

.btn-group .btn:last-child {
    margin-right: 0;
}
</style>

<script src="js/partner_payment_histories.js"></script>
<?php include 'layout/footer.php'; ?>
