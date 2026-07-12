<?php
require_once 'classes/autoload.php';

$pageTitle = 'Payment History';
include 'layout/header.php';

// Get payment statistics only (no payment histories)
$paymentHistoriesManager = new PartnerPaymentHistoriesManager();
$paymentStats = $paymentHistoriesManager->getPartnerPaymentStats($currentPartner['id']);
?>

<div class="content-section">
    <!-- Page Header -->
    <div class="admin-page-heading">
        <div>
            <p class="eyebrow">Payouts</p>
            <h1>Payment History</h1>
            <p class="text-muted mb-0">Track your payment disbursements and transaction status</p>
        </div>
        <div class="heading-summary text-end">
            <div class="h3 mb-0"><?php echo number_format($paymentStats['total_received'], 2); ?> MMK</div>
            <small class="text-muted">Total Received</small>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="filter-card glass">
                    <h6 class="card-title mb-3">
                        <i class="fas fa-filter me-2"></i>Filter Payments
                    </h6>
                    <div class="filter-toolbar partner-filter-toolbar">
                        <!-- Status Filter -->
                        <div>
                            <label class="form-label">Status</label>
                            <select class="form-select" id="statusFilter">
                                <option value="">All Status</option>
                                <option value="received">Received</option>
                                <option value="pending">Pending</option>
                                <option value="rejected">Rejected</option>
                            </select>
                        </div>
                        
                        <!-- Period Filter -->
                        <div>
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
                        <div id="customDateRange" style="display: none;">
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
                        <div>
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

    <!-- Statistics Cards -->
    <div class="metrics-grid">
        <div class="stat-card">
            <span class="metric-icon"><i class="fas fa-check-circle"></i></span>
            <span class="stat-label">Total Received</span>
            <div class="stat-number" id="totalReceived"><?php echo number_format($paymentStats['total_received'], 2); ?> MMK</div>
        </div>
        <div class="stat-card">
            <span class="metric-icon"><i class="fas fa-clock"></i></span>
            <span class="stat-label">Pending</span>
            <div class="stat-number" id="totalPending"><?php echo number_format($paymentStats['total_pending'], 2); ?> MMK</div>
        </div>
        <div class="stat-card">
            <span class="metric-icon"><i class="fas fa-times-circle"></i></span>
            <span class="stat-label">Rejected</span>
            <div class="stat-number" id="totalRejected"><?php echo number_format($paymentStats['total_rejected'], 2); ?> MMK</div>
        </div>
        <div class="stat-card">
            <span class="metric-icon"><i class="fas fa-receipt"></i></span>
            <span class="stat-label">Total Payments</span>
            <div class="stat-number" id="totalPayments"><?php echo number_format($paymentStats['total_payments']); ?></div>
        </div>
    </div>

    <!-- Payment History Table -->
    <div class="panel glass p-0">
        <div class="panel-heading">
            <h5 class="card-title mb-0">
                <i class="fas fa-history me-2"></i>Payment History
            </h5>
        </div>
        <div class="panel-body">
            <div id="paymentHistoriesContainer">
                <!-- Loading state -->
                <div id="loadingState" class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-3 text-muted">Loading payment history...</p>
                </div>
                
                <!-- Empty state (hidden initially) -->
                <div id="emptyState" class="empty-state" style="display: none;">
                    <i class="fas fa-credit-card fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No Payment History</h5>
                    <p class="text-muted">Your payment disbursements will appear here once processed.</p>
                    <button class="btn btn-primary" onclick="window.location.href='dashboard.php'">
                        <i class="fas fa-tachometer-alt me-2"></i>Go to Dashboard
                    </button>
                </div>
                
                <!-- Table container (hidden initially) -->
                <div id="tableContainer" style="display: none;">
                    <div class="table-wrap">
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
                                <!-- Payment rows will be populated by JavaScript -->
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Load More Button -->
                    <div class="text-center mt-3">
                        <button class="btn btn-outline-primary" id="loadMoreBtn">
                            <i class="fas fa-plus me-2"></i>Load More Payments
                        </button>
                    </div>
                </div>
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

<script src="js/partner_payment_histories.js"></script>
<?php include 'layout/footer.php'; ?>
