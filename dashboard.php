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
                        Manage your promotion codes and profile settings from this dashboard.
                    </p>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <a href="codes.php" class="btn btn-outline-primary btn-lg w-100">
                                <i class="fas fa-ticket-alt me-2"></i>
                                Manage Promotion Codes
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
                    <i class="fas fa-ticket-alt fa-2x mb-2"></i>
                    <div class="stat-number"><?php echo number_format($codeStats['total_generated'] ?? 0); ?></div>
                    <div>Total Codes Generated</div>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card stat-card">
                <div class="card-body text-center">
                    <i class="fas fa-check-circle fa-2x mb-2"></i>
                    <div class="stat-number"><?php echo number_format($codeStats['used_codes'] ?? 0); ?></div>
                    <div>Codes Used</div>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card stat-card">
                <div class="card-body text-center">
                    <i class="fas fa-percentage fa-2x mb-2"></i>
                    <div class="stat-number"><?php echo $codeStats['usage_rate'] ?? 0; ?>%</div>
                    <div>Usage Rate</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Promotion Codes -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5><i class="fas fa-clock me-2"></i>Recent Promotion Codes</h5>
            <a href="codes.php" class="btn btn-sm btn-primary">
                <i class="fas fa-eye me-1"></i>View All
            </a>
        </div>
        <div class="card-body">
            <?php if (!empty($recentCodes)): ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Code</th>
                            <th>Type</th>
                            <th>Status</th>
                            <th>Created</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach (array_slice($recentCodes, 0, 5) as $code): ?>
                        <tr>
                            <td>
                                <code class="bg-light p-1 rounded"><?php echo htmlspecialchars($code['code']); ?></code>
                            </td>
                            <td>
                                <span class="badge bg-<?php echo $code['code_type'] == 'vip_subscription' ? 'primary' : 'info'; ?>">
                                    <?php echo ucfirst(str_replace('_', ' ', $code['code_type'])); ?>
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-<?php echo getCodeStatusColor($code['status']); ?>">
                                    <?php echo ucfirst($code['status']); ?>
                                </span>
                            </td>
                            <td><?php echo date('M j, Y', strtotime($code['created_at'])); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php else: ?>
            <div class="text-center text-muted py-4">
                <i class="fas fa-ticket-alt fa-2x mb-2"></i>
                <p>No promotion codes generated yet.</p>
                <a href="codes.php" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Generate Your First Code
                </a>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include 'layout/footer.php'; ?>