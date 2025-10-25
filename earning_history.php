<?php
require_once 'classes/autoload.php';

$pageTitle = 'Earning History';
include 'layout/header.php';

// Get earning data
$codeManager = new PromotionCodeManager();
$earningHistory = $codeManager->getPartnerEarningHistory($currentPartner['id'], 50);
$earningStats = $codeManager->getPartnerEarningStats($currentPartner['id']);
?>

<div class="content-section">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">
                <i class="fas fa-chart-line me-2"></i>Earning History
            </h4>
            <p class="text-muted mb-0">Track your earnings from approved promotion codes</p>
        </div>
        <div class="text-end">
            <div class="h3 mb-0 text-success">$<?php echo number_format($earningStats['total_earnings'], 2); ?></div>
            <small class="text-muted">Total Earnings</small>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card stat-card">
                <div class="card-body text-center">
                    <i class="fas fa-dollar-sign fa-2x mb-2"></i>
                    <div class="stat-number">$<?php echo number_format($earningStats['total_earnings'], 2); ?></div>
                    <div>Total Earnings</div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card stat-card">
                <div class="card-body text-center">
                    <i class="fas fa-receipt fa-2x mb-2"></i>
                    <div class="stat-number"><?php echo number_format($earningStats['total_transactions']); ?></div>
                    <div>Total Transactions</div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card stat-card">
                <div class="card-body text-center">
                    <i class="fas fa-chart-bar fa-2x mb-2"></i>
                    <div class="stat-number">$<?php echo number_format($earningStats['average_earning'], 2); ?></div>
                    <div>Average per Transaction</div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card stat-card">
                <div class="card-body text-center">
                    <i class="fas fa-calendar-alt fa-2x mb-2"></i>
                    <div class="stat-number">$<?php echo number_format($earningStats['this_month_earnings'], 2); ?></div>
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
            <?php if (empty($earningHistory)): ?>
                <div class="text-center py-5">
                    <i class="fas fa-chart-line fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No Earnings Yet</h5>
                    <p class="text-muted">Your earnings will appear here once your promotion codes are approved and used.</p>
                    <button class="btn btn-primary" onclick="window.location.href='promotion_code_generator.php'">
                        <i class="fas fa-magic me-2"></i>Generate Promotion Codes
                    </button>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Promotion Code</th>
                                <th>User Details</th>
                                <th>Amount Earned</th>
                                <th>Commission Rate</th>
                                <th>Approved Date</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($earningHistory as $earning): ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="me-2">
                                                <i class="fas fa-ticket-alt text-primary"></i>
                                            </div>
                                            <div>
                                                <code class="text-dark"><?php echo htmlspecialchars($earning['code']); ?></code>
                                                <br>
                                                <small class="text-muted">
                                                    <?php echo htmlspecialchars($earning['payment_method'] ?? 'N/A'); ?>
                                                </small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div>
                                            <strong><?php echo htmlspecialchars($earning['user_name'] ?? 'Unknown User'); ?></strong>
                                            <br>
                                            <small class="text-muted">
                                                <i class="fas fa-phone me-1"></i>
                                                <?php echo htmlspecialchars($earning['user_phone'] ?? 'N/A'); ?>
                                            </small>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="text-success fw-bold">
                                            $<?php echo number_format($earning['amount_received'], 2); ?>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-info">
                                            <?php echo number_format($earning['commission_rate'], 1); ?>%
                                        </span>
                                    </td>
                                    <td>
                                        <div>
                                            <?php echo date('M j, Y', strtotime($earning['updated_at'])); ?>
                                            <br>
                                            <small class="text-muted">
                                                <?php echo date('H:i', strtotime($earning['updated_at'])); ?>
                                            </small>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-success">
                                            <i class="fas fa-check-circle me-1"></i>
                                            Approved
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
                <!-- Load More Button -->
                <div class="text-center mt-3">
                    <button class="btn btn-outline-primary" id="loadMoreBtn">
                        <i class="fas fa-plus me-2"></i>Load More Earnings
                    </button>
                </div>
            <?php endif; ?>
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
