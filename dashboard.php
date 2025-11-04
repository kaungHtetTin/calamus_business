<?php
$pageTitle = 'Dashboard';
include 'layout/header.php';

?>


<!-- Dashboard Section -->
<div class="content-section">
    <?php
    // Check if personal information is missing
    $missingPersonalInfo = empty($currentPartner['address']) || empty($currentPartner['city']) || empty($currentPartner['state']) || empty($currentPartner['national_id_card_number']);
    
    // Check if payment method is missing
    require_once 'classes/payment_methods_manager.php';
    $paymentManager = new PaymentMethodsManager();
    $paymentMethods = $paymentManager->getPartnerPaymentMethods($currentPartner['id']);
    $missingPaymentMethod = empty($paymentMethods);
    ?>
    
    <!-- Private Code Display -->
    <?php if (!empty($currentPartner['private_code'])): ?>
    <div class="card mb-3" style="background: linear-gradient(135deg, #4a5568 0%, #718096 100%); border: none;">
        <div class="card-body text-white p-3">
            <div class="row align-items-center g-3">
                <div class="col-md-8">
                    <div class="d-flex align-items-center gap-3">
                        <i class="fas fa-key text-white"></i>
                        <div class="flex-grow-1">
                            <div class="d-flex align-items-center gap-2 flex-wrap">
                                <span class="small text-white-50">Your Private Code:</span>
                                <code class=" text-primary px-3 py-1 rounded" style="font-size: 1.1rem;color: white; font-weight: 600; letter-spacing: 0.2em; font-family: 'Courier New', monospace;">
                                   <span id="privateCode" style="color: white;"><?php echo htmlspecialchars($currentPartner['private_code']); ?></span>
                                </code>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 text-md-end">
                    <button type="button" class="btn btn-light btn-sm" onclick="copyPrivateCode(event)" title="Copy to clipboard">
                        <i class="fas fa-copy me-1"></i>Copy
                    </button>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
    
    <?php if ($missingPersonalInfo): ?>
    <div class="alert alert-warning d-flex align-items-center" role="alert">
        <i class="fas fa-exclamation-triangle me-2"></i>
        <div>
            Please fill out the address information and the national id card information. Otherwise, you will not be able to receive payments.
            <a href="profile.php" class="ms-2">Go to profile</a>
        </div>
    </div>
    <?php endif; ?>
    
    <?php if ($missingPaymentMethod): ?>
    <div class="alert alert-warning d-flex align-items-center" role="alert">
        <i class="fas fa-credit-card me-2"></i>
        <div>
            Please add at least one payment method to receive payments.
            <a href="partner_payment_methods.php" class="ms-2">Add payment method</a>
        </div>
    </div>
    <?php endif; ?>
    
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
 
    <!-- Earnings Overview -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-light rounded p-3">
                                <i class="fas fa-wallet fa-lg text-muted"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <div class="h5 mb-1"><?php echo number_format($dashboardData['total_earnings'] ?? 0, 2); ?> MMK</div>
                            <div class="text-muted small">Total Earnings</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-light rounded p-3">
                                <i class="fas fa-sun fa-lg text-muted"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <div class="h5 mb-1"><?php echo number_format($dashboardData['today_earnings'] ?? 0, 2); ?> MMK</div>
                            <div class="text-muted small">Today's Earnings</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-light rounded p-3">
                                <i class="fas fa-moon fa-lg text-muted"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <div class="h5 mb-1"><?php echo number_format($dashboardData['yesterday_earnings'] ?? 0, 2); ?> MMK</div>
                            <div class="text-muted small">Yesterday's Earnings</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-light rounded p-3">
                                <i class="fas fa-calendar-alt fa-lg text-muted"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <div class="h5 mb-1"><?php echo number_format($dashboardData['this_month_earnings'] ?? 0, 2); ?> MMK</div>
                            <div class="text-muted small">This Month</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>



    <!-- Recent Earnings -->
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Recent Earnings</h5>
                <a href="earning_history.php" class="btn btn-outline-secondary btn-sm">
                    View All
                </a>
            </div>
        </div>
        <div class="card-body p-0">
            <?php if (!empty($dashboardData['recent_earnings'])): ?>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th class="border-0 py-3 px-3">Transaction</th>
                            <th class="border-0 py-3 px-3">Amount</th>
                            <th class="border-0 py-3 px-3">Status</th>
                            <th class="border-0 py-3 px-3">Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach (array_slice($dashboardData['recent_earnings'], 0, 5) as $earning): ?>
                        <tr>
                            <td class="py-3 px-3">
                                <div class="d-flex align-items-center">
                                    <div class="bg-light rounded p-2 me-3">
                                        <i class="fas fa-shopping-cart text-muted"></i>
                                    </div>
                                    <div>
                                        <div class="fw-medium">
                                            <?php 
                                            if (!empty($earning['target_course_id'])) {
                                                echo 'Course Purchase';
                                            } elseif (!empty($earning['target_package_id'])) {
                                                echo 'Package Purchase';
                                            } else {
                                                echo 'Transaction';
                                            }
                                            ?>
                                        </div>
                                        <small class="text-muted"><?php echo number_format($earning['price'], 2); ?> MMK</small>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3 px-3">
                                <div class="fw-medium">
                                    <?php echo number_format($earning['amount_received'], 2); ?> MMK
                                </div>
                            </td>
                            <td class="py-3 px-3">
                                <?php if ($earning['status'] === 'paid'): ?>
                                    <span class="badge bg-light text-dark border">
                                        Paid
                                    </span>
                                <?php else: ?>
                                    <span class="badge bg-light text-dark border">
                                        Pending
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="py-3 px-3 text-muted">
                                <?php echo date('M j, Y', strtotime($earning['created_at'])); ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php else: ?>
            <div class="text-center py-5">
                <div class="mb-3">
                    <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                        <i class="fas fa-chart-line text-muted"></i>
                    </div>
                </div>
                <h6 class="text-muted mb-3">No earnings yet</h6>
                <a href="earning_history.php" class="btn btn-outline-secondary">
                    View Earnings
                </a>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>



<!-- Load dashboard-specific JavaScript -->
<script>
function copyPrivateCode(event) {
    const privateCode = '<?php echo htmlspecialchars($currentPartner['private_code'] ?? ''); ?>';
    const btn = event ? event.target.closest('button') : document.querySelector('button[onclick*="copyPrivateCode"]');
    
    if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(privateCode).then(function() {
            // Show success feedback
            if (btn) {
                const originalHTML = btn.innerHTML;
                btn.innerHTML = '<i class="fas fa-check me-2"></i>Copied!';
                btn.classList.remove('btn-light');
                btn.classList.add('btn-success');
                
                setTimeout(function() {
                    btn.innerHTML = originalHTML;
                    btn.classList.remove('btn-success');
                    btn.classList.add('btn-light');
                }, 2000);
            }
        }).catch(function(err) {
            console.error('Failed to copy:', err);
            alert('Failed to copy private code. Please copy manually: ' + privateCode);
        });
    } else {
        // Fallback for older browsers
        const textArea = document.createElement('textarea');
        textArea.value = privateCode;
        textArea.style.position = 'fixed';
        textArea.style.left = '-999999px';
        document.body.appendChild(textArea);
        textArea.select();
        try {
            document.execCommand('copy');
            if (btn) {
                const originalHTML = btn.innerHTML;
                btn.innerHTML = '<i class="fas fa-check me-2"></i>Copied!';
                btn.classList.remove('btn-light');
                btn.classList.add('btn-success');
                setTimeout(function() {
                    btn.innerHTML = originalHTML;
                    btn.classList.remove('btn-success');
                    btn.classList.add('btn-light');
                }, 2000);
            } else {
                alert('Private code copied to clipboard!');
            }
        } catch (err) {
            console.error('Failed to copy:', err);
            alert('Failed to copy. Please copy manually: ' + privateCode);
        }
        document.body.removeChild(textArea);
    }
}
</script>

<?php include 'layout/footer.php'; ?>