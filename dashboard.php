<?php
$pageTitle = 'Dashboard';
include 'layout/header.php';
?>

<!-- Dashboard Section -->
<div class="content-section">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-tachometer-alt me-2"></i>Dashboard</h2>
        <button class="btn btn-primary" onclick="showCreateLinkModal()">
            <i class="fas fa-plus me-2"></i>Create New Link
        </button>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card stat-card">
                <div class="card-body text-center">
                    <i class="fas fa-mouse-pointer fa-2x mb-2"></i>
                    <div class="stat-number"><?php echo number_format($dashboardData['stats']['total_clicks']); ?></div>
                    <div>Total Clicks</div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card stat-card">
                <div class="card-body text-center">
                    <i class="fas fa-check-circle fa-2x mb-2"></i>
                    <div class="stat-number"><?php echo number_format($dashboardData['stats']['total_conversions']); ?></div>
                    <div>Conversions</div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card stat-card">
                <div class="card-body text-center">
                    <i class="fas fa-percentage fa-2x mb-2"></i>
                    <div class="stat-number"><?php echo $dashboardData['stats']['conversion_rate']; ?>%</div>
                    <div>Conversion Rate</div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card stat-card">
                <div class="card-body text-center">
                    <i class="fas fa-dollar-sign fa-2x mb-2"></i>
                    <div class="stat-number">$<?php echo number_format($dashboardData['stats']['total_earnings'], 2); ?></div>
                    <div>Total Earnings</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row mb-4">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5><i class="fas fa-chart-line me-2"></i>Monthly Earnings</h5>
                </div>
                <div class="card-body">
                    <canvas id="earningsChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5><i class="fas fa-trophy me-2"></i>Top Performing Links</h5>
                </div>
                <div class="card-body" id="top-links">
                    <?php foreach ($dashboardData['top_links'] as $link): ?>
                    <div class="mb-3 p-3 border rounded">
                        <div class="d-flex justify-content-between">
                            <div>
                                <strong><?php echo htmlspecialchars($link['campaign_name']); ?></strong>
                                <br>
                                <small class="text-muted">Code: <?php echo htmlspecialchars($link['link_code']); ?></small>
                            </div>
                            <div class="text-end">
                                <div class="fw-bold">$<?php echo number_format($link['total_commission'] ?? 0, 2); ?></div>
                                <small class="text-muted"><?php echo $link['conversions'] ?? 0; ?> conversions</small>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Conversions -->
    <div class="card">
        <div class="card-header">
            <h5><i class="fas fa-clock me-2"></i>Recent Conversions</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Campaign</th>
                            <th>Type</th>
                            <th>Value</th>
                            <th>Commission</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($dashboardData['recent_conversions'] as $conversion): ?>
                        <tr>
                            <td><?php echo date('M j, Y', strtotime($conversion['conversion_date'])); ?></td>
                            <td><?php echo htmlspecialchars($conversion['campaign_name']); ?></td>
                            <td><span class="badge bg-primary"><?php echo htmlspecialchars($conversion['conversion_type']); ?></span></td>
                            <td>$<?php echo number_format($conversion['conversion_value'], 2); ?></td>
                            <td>$<?php echo number_format($conversion['commission_amount'], 2); ?></td>
                            <td><span class="badge bg-<?php echo getStatusColor($conversion['status']); ?>"><?php echo htmlspecialchars($conversion['status']); ?></span></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include 'layout/footer.php'; ?>
