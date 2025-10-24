<?php
$pageTitle = 'Promotion Codes';
include 'layout/header.php';

// Get promotion code data
$codeStats = $codeManager->getPartnerCodeStats($currentPartner['id']);
$recentCodes = $codeManager->getPartnerPromotionCodes($currentPartner['id'], null, 10);
?>

<!-- Promotion Codes Section -->
<div class="content-section">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-ticket-alt me-2"></i>Promotion Codes</h2>
        <button class="btn btn-primary" onclick="showCreateCodeModal()">
            <i class="fas fa-plus me-2"></i>Generate New Code
        </button>
    </div>

    <!-- Code Statistics -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card stat-card">
                <div class="card-body text-center">
                    <i class="fas fa-ticket-alt fa-2x mb-2"></i>
                    <div class="stat-number"><?php echo number_format($codeStats['total_generated']); ?></div>
                    <div>Total Generated</div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card stat-card">
                <div class="card-body text-center">
                    <i class="fas fa-check-circle fa-2x mb-2"></i>
                    <div class="stat-number"><?php echo number_format($codeStats['used_codes']); ?></div>
                    <div>Used Codes</div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card stat-card">
                <div class="card-body text-center">
                    <i class="fas fa-percentage fa-2x mb-2"></i>
                    <div class="stat-number"><?php echo $codeStats['usage_rate']; ?>%</div>
                    <div>Usage Rate</div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card stat-card">
                <div class="card-body text-center">
                    <i class="fas fa-dollar-sign fa-2x mb-2"></i>
                    <div class="stat-number">$<?php echo number_format($codeStats['commission_earned'], 2); ?></div>
                    <div>Code Earnings</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Codes -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5><i class="fas fa-clock me-2"></i>Recent Promotion Codes</h5>
            <div class="btn-group" role="group">
                <button class="btn btn-sm btn-outline-primary" onclick="filterCodes('all')">All</button>
                <button class="btn btn-sm btn-outline-success" onclick="filterCodes('active')">Active</button>
                <button class="btn btn-sm btn-outline-info" onclick="filterCodes('used')">Used</button>
                <button class="btn btn-sm btn-outline-warning" onclick="filterCodes('expired')">Expired</button>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Code</th>
                            <th>Type</th>
                            <th>Course</th>
                            <th>Client</th>
                            <th>Status</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="codes-list">
                        <?php foreach ($recentCodes as $code): ?>
                        <tr data-status="<?php echo $code['status']; ?>">
                            <td>
                                <code class="bg-light p-1 rounded"><?php echo htmlspecialchars($code['code']); ?></code>
                                <button class="btn btn-sm btn-outline-secondary ms-2" onclick="copyCode('<?php echo $code['code']; ?>')">
                                    <i class="fas fa-copy"></i>
                                </button>
                            </td>
                            <td>
                                <span class="badge bg-<?php echo $code['code_type'] == 'vip_subscription' ? 'primary' : 'info'; ?>">
                                    <?php echo ucfirst(str_replace('_', ' ', $code['code_type'])); ?>
                                </span>
                            </td>
                            <td><?php echo $code['course_title'] ? htmlspecialchars($code['course_title']) : 'All Courses'; ?></td>
                            <td><?php echo $code['generated_for'] ? htmlspecialchars($code['generated_for']) : '-'; ?></td>
                            <td>
                                <span class="badge bg-<?php echo getCodeStatusColor($code['status']); ?>">
                                    <?php echo ucfirst($code['status']); ?>
                                </span>
                            </td>
                            <td><?php echo date('M j, Y', strtotime($code['created_at'])); ?></td>
                            <td>
                                <?php if ($code['status'] == 'active'): ?>
                                <button class="btn btn-sm btn-outline-danger" onclick="cancelCode(<?php echo $code['id']; ?>)">
                                    <i class="fas fa-times"></i> Cancel
                                </button>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Create Code Modal -->
<div class="modal fade" id="createCodeModal" tabindex="-1" aria-labelledby="createCodeModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createCodeModalLabel">Generate New Promotion Code</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="createCodeForm">
                    <div class="mb-3">
                        <label for="code_type" class="form-label">Code Type</label>
                        <select class="form-select" id="code_type" required>
                            <option value="">Select Type</option>
                            <option value="vip_subscription">VIP Subscription</option>
                            <option value="package_purchase">Package Purchase</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="code_target_course" class="form-label">Target Course (Optional)</label>
                        <select class="form-select" id="code_target_course">
                            <option value="">All Courses</option>
                            <!-- Add course options here -->
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="code_target_major" class="form-label">Target Major (Optional)</label>
                        <input type="text" class="form-control" id="code_target_major" placeholder="e.g., Computer Science">
                    </div>
                    <div class="mb-3">
                        <label for="code_target_package" class="form-label">Target Package (Optional)</label>
                        <select class="form-select" id="code_target_package">
                            <option value="">All Packages</option>
                            <!-- Add package options here -->
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="client_name" class="form-label">Client Name (Optional)</label>
                        <input type="text" class="form-control" id="client_name" placeholder="Name of the client">
                    </div>
                    <div class="mb-3">
                        <label for="expires_at" class="form-label">Expiration Date (Optional)</label>
                        <input type="datetime-local" class="form-control" id="expires_at">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="generatePromotionCode()">Generate Code</button>
            </div>
        </div>
    </div>
</div>

<?php include 'layout/footer.php'; ?>
