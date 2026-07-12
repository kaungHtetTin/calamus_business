<?php
require_once 'classes/autoload.php';

$pageTitle = 'Earning History';
include 'layout/header.php';

// The shared header already loads this aggregate for the topbar.
$earningStats = $earningStats ?? (new PartnerEarningsManager())->getPartnerEarningStats($currentPartner['id']);
?>

<div class="content-section">
    <!-- Page Header -->
    <div class="admin-page-heading">
        <div>
            <p class="eyebrow">Revenue</p>
            <h1>Earning History</h1>
            <p class="text-muted mb-0">Track your earnings from completed transactions</p>
        </div>
        <div class="heading-summary text-end">
            <div class="h3 mb-0" id="headingTotalEarnings"><?php echo number_format($earningStats['total_earnings'], 2); ?> MMK</div>
            <small class="text-muted">Total Earnings</small>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="filter-card glass">
                    <h6 class="card-title mb-3">
                        <i class="fas fa-filter me-2"></i>Filter Earnings
                    </h6>
                    <div class="filter-toolbar partner-filter-toolbar">
                        <!-- Status Filter -->
                        <div>
                            <label class="form-label">Status</label>
                            <select class="form-select" id="statusFilter">
                                <option value="">All Status</option>
                                <option value="paid">Paid</option>
                                <option value="pending">Pending</option>
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
    <div class="metrics-grid metrics-grid-three">
        <div class="stat-card">
            <span class="metric-icon"><i class="fas fa-dollar-sign"></i></span>
            <span class="stat-label">Total Earnings</span>
            <div class="stat-number" id="totalEarnings"><?php echo number_format($earningStats['total_earnings'], 2); ?> MMK</div>
        </div>
        <div class="stat-card">
            <span class="metric-icon"><i class="fas fa-receipt"></i></span>
            <span class="stat-label">Total Transactions</span>
            <div class="stat-number" id="totalTransactions"><?php echo number_format($earningStats['total_transactions']); ?></div>
        </div>
        <div class="stat-card">
            <span class="metric-icon"><i class="fas fa-calendar-alt"></i></span>
            <span class="stat-label">This Month</span>
            <div class="stat-number" id="thisMonthEarnings"><?php echo number_format($earningStats['this_month_earnings'], 2); ?> MMK</div>
        </div>
    </div>

    <!-- Earning History Table -->
    <div class="panel glass p-0">
        <div class="panel-heading">
            <h5 class="card-title mb-0">
                <i class="fas fa-history me-2"></i>Recent Earnings
            </h5>
        </div>
        <div class="panel-body">
            <!-- Loading state -->
            <div id="loadingState" class="text-center py-5">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-3 text-muted">Loading earning history...</p>
            </div>
            
            <!-- Empty state (hidden initially) -->
            <div id="emptyState" class="empty-state" style="display: none;">
                <i class="fas fa-chart-line fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">No Earnings Yet</h5>
                <p class="text-muted">Your earnings will appear here once transactions are completed and payments are processed.</p>
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

<script src="js/earning_history.js"></script>
<?php include 'layout/footer.php'; ?>
