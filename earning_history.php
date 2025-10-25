<?php
require_once 'classes/autoload.php';

$pageTitle = 'Earning History';
include 'layout/header.php';

// Get earning statistics only (no earning history)
$earningsManager = new PartnerEarningsManager();
$earningStats = $earningsManager->getPartnerEarningStats($currentPartner['id']);
?>

<div class="content-section">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">
                <i class="fas fa-chart-line me-2"></i>Earning History
            </h4>
            <p class="text-muted mb-0">Track your earnings from completed transactions</p>
        </div>
        <div class="text-end">
            <div class="h3 mb-0 text-success"><?php echo number_format($earningStats['total_earnings'], 2); ?> MMK</div>
            <small class="text-muted">Total Earnings</small>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card bg-light">
                <div class="card-body">
                    <h6 class="card-title mb-3">
                        <i class="fas fa-filter me-2"></i>Filter Earnings
                    </h6>
                    <div class="row">
                        <!-- Status Filter -->
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Status</label>
                            <select class="form-select" id="statusFilter">
                                <option value="">All Status</option>
                                <option value="paid">Paid</option>
                                <option value="pending">Pending</option>
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
        <div class="col-md-4 mb-4">
            <div class="card stat-card">
                <div class="card-body text-center">
                    <i class="fas fa-dollar-sign fa-2x mb-2"></i>
                    <div class="stat-number" id="totalEarnings"><?php echo number_format($earningStats['total_earnings'], 2); ?> MMK</div>
                    <div>Total Earnings</div>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card stat-card">
                <div class="card-body text-center">
                    <i class="fas fa-receipt fa-2x mb-2"></i>
                    <div class="stat-number" id="totalTransactions"><?php echo number_format($earningStats['total_transactions']); ?></div>
                    <div>Total Transactions</div>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card stat-card">
                <div class="card-body text-center">
                    <i class="fas fa-calendar-alt fa-2x mb-2"></i>
                    <div class="stat-number" id="thisMonthEarnings"><?php echo number_format($earningStats['this_month_earnings'], 2); ?> MMK</div>
                    <div>This Month</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Earning History Table -->
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">
                <i class="fas fa-history me-2"></i>Recent Earnings
            </h5>
        </div>
        <div class="card-body">
            <!-- Loading state -->
            <div id="loadingState" class="text-center py-5">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-3 text-muted">Loading earning history...</p>
            </div>
            
            <!-- Empty state (hidden initially) -->
            <div id="emptyState" class="text-center py-5" style="display: none;">
                <i class="fas fa-chart-line fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">No Earnings Yet</h5>
                <p class="text-muted">Your earnings will appear here once transactions are completed and payments are processed.</p>
                <button class="btn btn-primary" onclick="window.location.href='dashboard.php'">
                    <i class="fas fa-tachometer-alt me-2"></i>Go to Dashboard
                </button>
            </div>
            
            <!-- Table container (hidden initially) -->
            <div id="tableContainer" style="display: none;">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Transaction Details</th>
                                <th>User Details</th>
                                <th>Amount Earned</th>
                                <th>Commission Rate</th>
                                <th>Transaction Date</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody id="earningHistoryTableBody">
                            <!-- Earning rows will be populated by JavaScript -->
                        </tbody>
                    </table>
                </div>
                
                <!-- Load More Button -->
                <div class="text-center mt-3">
                    <button class="btn btn-outline-primary" id="loadMoreBtn">
                        <i class="fas fa-plus me-2"></i>Load More Earnings
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Earning Details Modal -->
<div class="modal fade" id="earningDetailsModal" tabindex="-1" aria-labelledby="earningDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="earningDetailsModalLabel">
                    <i class="fas fa-info-circle me-2"></i>Earning Details
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="earningDetailsContent">
                <!-- Content will be loaded dynamically -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<style>
/* Custom styles for earning history page */
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

code {
    background-color: #f8f9fa;
    padding: 0.25rem 0.5rem;
    border-radius: 0.25rem;
    font-size: 0.875rem;
}
</style>

<script src="js/earning_history.js"></script>
<?php include 'layout/footer.php'; ?>
