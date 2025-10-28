<?php
/**
 * Payout Detail Page
 * View detailed information about a specific payout transaction
 */

require_once '../classes/admin_auth.php';

$adminAuth = new AdminAuth();

// Check if admin is logged in
if (!$adminAuth->isLoggedIn()) {
    header('Location: login.php');
    exit();
}

// Get payment history ID from URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: payout_history.php?error=Invalid payment history ID');
    exit();
}

$paymentHistoryId = $_GET['id'];

// Get payout history detail
$history = $adminAuth->getPayoutHistoryDetail($paymentHistoryId);

if (!$history) {
    header('Location: payout_history.php?error=Payment history not found');
    exit();
}

$pageTitle = 'Payout Details';
$currentPage = 'payout_history';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?> - Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../css/app.css">
    <link rel="stylesheet" href="css/app.css">
    <link rel="icon" href="../logo.png" type="image/x-icon">
    <style>
        .transaction-image {
            max-width: 100%;
            max-height: 500px;
            border: 2px solid #e8eaed;
            border-radius: 8px;
            cursor: pointer;
            transition: transform 0.2s;
        }
        
        .transaction-image:hover {
            transform: scale(1.05);
        }
        
        .info-label {
            font-size: 13px;
            color: #5f6368;
            font-weight: 500;
            margin-bottom: 4px;
        }
        
        .info-value {
            font-size: 14px;
            color: #202124;
            font-weight: 400;
        }
        
        .amount-badge {
            font-size: 28px;
            font-weight: 600;
            color: #1a73e8;
        }
    </style>
</head>
<body>
    <!-- Admin Header -->
    <?php include 'layout/admin_header.php'; ?>
    
    <?php include 'layout/admin_sidebar.php'; ?>
    
    <div class="container-fluid" style="padding: 24px;">
        <!-- Back Link -->
        <div class="mb-3">
            <a href="payout_history.php" class="btn btn-sm btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to Payout History
            </a>
        </div>
        
        <!-- Page Title -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="mb-0" style="color: #202124;">
                <i class="fas fa-money-bill-wave me-2"></i>Payout Transaction Details
            </h4>
            <span class="badge bg-<?php echo $history['status'] === 'completed' ? 'success' : ($history['status'] === 'received' ? 'success' : 'warning'); ?>">
                <?php echo ucfirst($history['status']); ?>
            </span>
        </div>
        
        <!-- Amount Card -->
        <div class="card mb-4">
            <div class="card-body text-center">
                <div class="info-label">Amount Paid</div>
                <div class="amount-badge">
                    <?php echo number_format($history['amount'], 2); ?> MMK
                </div>
            </div>
        </div>
        
        <!-- Details Grid -->
        <div class="row">
            <!-- Partner Information -->
            <div class="col-md-6">
                <div class="info-card">
                    <h5 class="mb-3" style="color: #202124;">
                        <i class="fas fa-user me-2"></i>Partner Information
                    </h5>
                    
                    <div class="mb-3">
                        <div class="info-label">Partner Name</div>
                        <div class="info-value"><?php echo htmlspecialchars($history['contact_name'] ?? 'N/A'); ?></div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="info-label">Company Name</div>
                        <div class="info-value"><?php echo htmlspecialchars($history['company_name'] ?? 'N/A'); ?></div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="info-label">Email</div>
                        <div class="info-value">
                            <i class="fas fa-envelope me-2"></i><?php echo htmlspecialchars($history['email'] ?? 'N/A'); ?>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="info-label">Phone</div>
                        <div class="info-value">
                            <i class="fas fa-phone me-2"></i><?php echo htmlspecialchars($history['phone'] ?? 'N/A'); ?>
                        </div>
                    </div>
                    
                    <?php if ($history['website']): ?>
                    <div class="mb-3">
                        <div class="info-label">Website</div>
                        <div class="info-value">
                            <a href="<?php echo htmlspecialchars($history['website']); ?>" target="_blank" rel="noopener noreferrer">
                                <i class="fas fa-globe me-2"></i><?php echo htmlspecialchars($history['website']); ?>
                            </a>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <div>
                        <a href="view_partner.php?id=<?php echo $history['partner_id']; ?>" class="btn btn-sm btn-primary">
                            <i class="fas fa-eye me-2"></i>View Partner Profile
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Payment Information -->
            <div class="col-md-6">
                <div class="info-card">
                    <h5 class="mb-3" style="color: #202124;">
                        <i class="fas fa-credit-card me-2"></i>Payment Information
                    </h5>
                    
                    <div class="mb-3">
                        <div class="info-label">Payment Method</div>
                        <div class="info-value">
                            <i class="fas fa-<?php echo strtolower($history['payment_method']) === 'bank transfer' ? 'university' : 'wallet'; ?> me-2"></i>
                            <?php echo htmlspecialchars($history['payment_method']); ?>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="info-label">Account Name</div>
                        <div class="info-value"><strong><?php echo htmlspecialchars($history['account_name']); ?></strong></div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="info-label">Account Number</div>
                        <div class="info-value"><?php echo htmlspecialchars($history['account_number']); ?></div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="info-label">Transaction Date</div>
                        <div class="info-value">
                            <i class="fas fa-calendar me-2"></i><?php echo date('F d, Y', strtotime($history['created_at'])); ?>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="info-label">Transaction Time</div>
                        <div class="info-value">
                            <i class="fas fa-clock me-2"></i><?php echo date('h:i A', strtotime($history['created_at'])); ?>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="info-label">Status</div>
                        <div class="info-value">
                            <span class="badge bg-<?php echo $history['status'] === 'completed' ? 'success' : ($history['status'] === 'received' ? 'success' : 'warning'); ?>">
                                <?php echo ucfirst($history['status']); ?>
                            </span>
                        </div>
                    </div>
                    
                    <?php if ($history['updated_at'] && $history['updated_at'] != $history['created_at']): ?>
                    <div>
                        <div class="info-label">Last Updated</div>
                        <div class="info-value">
                            <i class="fas fa-calendar me-2"></i><?php echo date('M d, Y h:i A', strtotime($history['updated_at'])); ?>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <!-- Staff/Admin Information -->
        <?php if ($history['staff_name']): ?>
        <div class="row mt-4">
            <div class="col-md-12">
                <div class="info-card">
                    <h5 class="mb-3" style="color: #202124;">
                        <i class="fas fa-user-shield me-2"></i>Processed By
                    </h5>
                    
                    <div class="mb-3">
                        <div class="info-label">Staff Name</div>
                        <div class="info-value">
                            <strong><?php echo htmlspecialchars($history['staff_name']); ?></strong>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="info-label">Role</div>
                        <div class="info-value">
                            <span class="badge bg-primary">Admin (Ranking: <?php echo $history['staff_ranking']; ?>)</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
        
        <!-- Transaction Screenshot -->
        <?php if ($history['transaction_screenshot']): ?>
        <div class="row mt-4">
            <div class="col-md-12">
                <div class="info-card">
                    <h5 class="mb-3" style="color: #202124;">
                        <i class="fas fa-image me-2"></i>Payment Screenshot
                    </h5>
                    
                    <div class="text-center">
                        <img src="../<?php echo htmlspecialchars($history['transaction_screenshot']); ?>" 
                             alt="Transaction Screenshot" 
                             class="transaction-image"
                             onclick="window.open(this.src, '_blank')">
                        <div class="mt-2">
                            <button class="btn btn-sm btn-outline-secondary" onclick="window.open('../<?php echo htmlspecialchars($history['transaction_screenshot']); ?>', '_blank')">
                                <i class="fas fa-external-link-alt me-2"></i>View Full Size
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
        
        <!-- Transaction Metadata -->
        <div class="row mt-4">
            <div class="col-md-12">
                <div class="info-card">
                    <h5 class="mb-3" style="color: #202124;">
                        <i class="fas fa-info-circle me-2"></i>Transaction Metadata
                    </h5>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <div class="info-label">Payment History ID</div>
                                <div class="info-value"><code>#<?php echo htmlspecialchars($history['id']); ?></code></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <div class="info-label">Partner ID</div>
                                <div class="info-value"><code>#<?php echo htmlspecialchars($history['partner_id']); ?></code></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Action Buttons -->
        <div class="d-flex gap-2 mt-4">
            <a href="payout_history.php" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to History
            </a>
            <a href="view_partner.php?id=<?php echo $history['partner_id']; ?>" class="btn btn-primary">
                <i class="fas fa-user me-2"></i>View Partner Profile
            </a>
            <?php if ($history['transaction_screenshot']): ?>
            <a href="../<?php echo htmlspecialchars($history['transaction_screenshot']); ?>" 
               class="btn btn-success" 
               download>
                <i class="fas fa-download me-2"></i>Download Screenshot
            </a>
            <?php endif; ?>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

