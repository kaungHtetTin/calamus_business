<?php
$pageTitle = 'Conversions';
include 'layout/header.php';

// Get conversion history data
$conversions = $dashboard->getConversionHistory($currentPartner['id']);
?>

<!-- Conversions Section -->
<div class="content-section">
    <h2><i class="fas fa-chart-line me-2"></i>Conversion History</h2>
    
    <div class="card">
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
                    <tbody id="conversions-list">
                        <?php foreach ($conversions as $conversion): ?>
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
