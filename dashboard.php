<?php
$pageTitle = 'Dashboard';
include 'layout/header.php';
?>


<!-- Dashboard Section -->
<div class="content-section">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-tachometer-alt me-2"></i>Dashboard</h2>
        <div class="text-muted">
            <small>Welcome to your partner dashboard</small>
        </div>
    </div>

    <!-- Welcome Message -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body text-center">
                    <i class="fas fa-handshake fa-3x text-primary mb-3"></i>
                    <h4>Welcome to Partner Portal</h4>
                    <p class="text-muted mb-4">
                        Manage your earnings and profile settings from this dashboard.
                    </p>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <a href="earning_history.php" class="btn btn-outline-primary btn-lg w-100">
                                <i class="fas fa-chart-line me-2"></i>
                                View Earnings
                            </a>
                        </div>
                        <div class="col-md-6 mb-3">
                            <a href="profile.php" class="btn btn-outline-secondary btn-lg w-100">
                                <i class="fas fa-user me-2"></i>
                                Update Profile
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
 
    <!-- Quick Stats -->
    <div class="row mb-4">
        <div class="col-md-4 mb-3">
            <div class="card stat-card">
                <div class="card-body text-center">
                    <i class="fas fa-dollar-sign fa-2x mb-2"></i>
                    <div class="stat-number">$<?php echo number_format($dashboardData['total_earnings'] ?? 0, 2); ?></div>
                    <div>Total Earnings</div>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card stat-card">
                <div class="card-body text-center">
                    <i class="fas fa-receipt fa-2x mb-2"></i>
                    <div class="stat-number"><?php echo number_format($dashboardData['total_transactions'] ?? 0); ?></div>
                    <div>Total Transactions</div>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card stat-card">
                <div class="card-body text-center">
                    <i class="fas fa-calendar-alt fa-2x mb-2"></i>
                    <div class="stat-number">$<?php echo number_format($dashboardData['this_month_earnings'] ?? 0, 2); ?></div>
                    <div>This Month</div>
                </div>
            </div>
        </div>
    </div>



    <!-- Recent Earnings -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5><i class="fas fa-chart-line me-2"></i>Recent Earnings</h5>
            <a href="earning_history.php" class="btn btn-sm btn-primary">
                <i class="fas fa-eye me-1"></i>View All
            </a>
        </div>
        <div class="card-body">
            <?php if (!empty($dashboardData['recent_earnings'])): ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Transaction</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach (array_slice($dashboardData['recent_earnings'], 0, 5) as $earning): ?>
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-shopping-cart text-primary me-2"></i>
                                    <div>
                                        <strong>
                                            <?php 
                                            if (!empty($earning['target_course_id'])) {
                                                echo 'Course Purchase';
                                            } elseif (!empty($earning['target_package_id'])) {
                                                echo 'Package Purchase';
                                            } else {
                                                echo 'Transaction';
                                            }
                                            ?>
                                        </strong>
                                        <br>
                                        <small class="text-muted">$<?php echo number_format($earning['price'], 2); ?></small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="text-success fw-bold">
                                    $<?php echo number_format($earning['amount_received'], 2); ?>
                                </div>
                            </td>
                            <td>
                                <?php if ($earning['status'] === 'paid'): ?>
                                    <span class="badge bg-success">
                                        <i class="fas fa-check-circle me-1"></i>Paid
                                    </span>
                                <?php else: ?>
                                    <span class="badge bg-warning">
                                        <i class="fas fa-clock me-1"></i>Pending
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo date('M j, Y', strtotime($earning['created_at'])); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php else: ?>
            <div class="text-center text-muted py-4">
                <i class="fas fa-chart-line fa-2x mb-2"></i>
                <p>No earnings yet.</p>
                <a href="earning_history.php" class="btn btn-primary">
                    <i class="fas fa-chart-line me-1"></i>View Earnings
                </a>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>



<!-- Load dashboard-specific JavaScript -->

<?php include 'layout/footer.php'; ?>