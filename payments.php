<?php
$pageTitle = 'Payments';
include 'layout/header.php';

// Get payment history data
$payments = $dashboard->getPaymentHistory($currentPartner['id']);
?>

<!-- Payments Section -->
<div class="content-section">
    <h2><i class="fas fa-money-bill-wave me-2"></i>Payment History</h2>
    
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Period</th>
                            <th>Amount</th>
                            <th>Method</th>
                            <th>Status</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($payments as $payment): ?>
                        <tr>
                            <td><?php echo date('M j', strtotime($payment['payment_period_start'])); ?> - <?php echo date('M j, Y', strtotime($payment['payment_period_end'])); ?></td>
                            <td>$<?php echo number_format($payment['total_commission'], 2); ?></td>
                            <td><?php echo ucfirst(str_replace('_', ' ', $payment['payment_method'])); ?></td>
                            <td><span class="badge bg-<?php echo getPaymentStatusColor($payment['payment_status']); ?>"><?php echo ucfirst($payment['payment_status']); ?></span></td>
                            <td><?php echo date('M j, Y', strtotime($payment['created_at'])); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include 'layout/footer.php'; ?>
