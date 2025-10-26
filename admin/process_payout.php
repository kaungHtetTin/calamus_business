<?php
/**
 * Process Payout Page
 * Display partner information, payment methods, and payout amount
 */

require_once '../classes/admin_auth.php';

$adminAuth = new AdminAuth();

// Check if admin is logged in
if (!$adminAuth->isLoggedIn()) {
    header('Location: login.php');
    exit();
}

// Get partner ID from URL
if (!isset($_GET['partner_id']) || empty($_GET['partner_id'])) {
    header('Location: payout_logs.php?error=Invalid partner ID');
    exit();
}

$partnerId = $_GET['partner_id'];

// Get partner information
$partner = $adminAuth->getPartnerById($partnerId);

if (!$partner) {
    header('Location: payout_logs.php?error=Partner not found');
    exit();
}

// Get partner payment methods
$paymentMethods = $adminAuth->getPartnerPaymentMethods($partnerId);

// Get pending payout amount
$pendingAmount = $adminAuth->getPendingPayoutAmount($partnerId);

$pageTitle = 'Process Payout';
$currentPage = 'payout_logs'; // Set current page for sidebar active state
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?> - Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/app.css">
    <link rel="stylesheet" href="css/app.css">
</head>
<body>
    <?php include 'layout/admin_header.php'; ?>
    <?php include 'layout/admin_sidebar.php'; ?>
    
    <div class="container-fluid admin-container">
        <!-- Back Link -->
        <a href="payout_logs.php" class="back-link">
            <i class="fas fa-arrow-left me-2"></i>Back to Payout Logs
        </a>

        <!-- Alert Messages -->
        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php echo htmlspecialchars($_GET['success']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php echo htmlspecialchars($_GET['error']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Partner Information -->
        <div class="info-card">
            <h5 class="card-title">
                <i class="fas fa-user me-2"></i>Partner Information
            </h5>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="info-label">Company Name</div>
                    <div class="info-value"><?php echo htmlspecialchars($partner['company_name']); ?></div>
                </div>
                <div class="col-md-6">
                    <div class="info-label">Contact Name</div>
                    <div class="info-value"><?php echo htmlspecialchars($partner['contact_name']); ?></div>
                </div>
                <div class="col-md-6">
                    <div class="info-label">Email</div>
                    <div class="info-value"><?php echo htmlspecialchars($partner['email']); ?></div>
                </div>
                <div class="col-md-6">
                    <div class="info-label">Phone</div>
                    <div class="info-value"><?php echo htmlspecialchars($partner['phone']); ?></div>
                </div>
                <div class="col-md-6">
                    <div class="info-label">Website</div>
                    <div class="info-value">
                        <?php if ($partner['website']): ?>
                            <a href="<?php echo htmlspecialchars($partner['website']); ?>" target="_blank" rel="noopener noreferrer">
                                <?php echo htmlspecialchars($partner['website']); ?>
                            </a>
                        <?php else: ?>
                            <span class="text-muted">Not provided</span>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="info-label">Status</div>
                    <div class="info-value">
                        <span class="badge bg-<?php echo $partner['status'] === 'active' ? 'success' : ($partner['status'] === 'pending' ? 'warning' : 'danger'); ?>">
                            <?php echo ucfirst($partner['status']); ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pending Amount to Payout -->
        <div class="amount-card">
            <div class="amount-label">Pending Amount to Payout</div>
            <div class="amount-value"><?php echo number_format($pendingAmount, 2); ?></div>
            <div class="currency">MMK</div>
        </div>

        <!-- Payment Methods -->
        <div class="info-card">
            <h5 class="card-title">
                <i class="fas fa-credit-card me-2"></i>Payment Methods
            </h5>
            
            <?php if (count($paymentMethods) > 0): ?>
                <?php foreach ($paymentMethods as $method): ?>
                    <div class="payment-method-item">
                        <div class="method-name">
                            <i class="fas fa-<?php echo strtolower($method['payment_method']) === 'bank transfer' ? 'university' : 'wallet'; ?> me-2"></i>
                            <?php echo htmlspecialchars($method['payment_method']); ?>
                        </div>
                        <div class="method-details">
                            <div><strong>Account Name:</strong> <?php echo htmlspecialchars($method['account_name']); ?></div>
                            <div><strong>Account Number:</strong> <?php echo htmlspecialchars($method['account_number']); ?></div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-credit-card"></i>
                    <p>No payment methods found for this partner.</p>
                </div>
            <?php endif; ?>
        </div>

        <!-- Payout Action -->
        <div class="payout-action">
            <?php if ($pendingAmount > 0): ?>
                <form method="POST" action="confirm_payout.php">
                    <input type="hidden" name="partner_id" value="<?php echo $partner['id']; ?>">
                    <input type="hidden" name="amount" value="<?php echo $pendingAmount; ?>">
                    <button type="submit" class="btn btn-payout">
                        <i class="fas fa-money-bill-wave me-2"></i>Process Payout
                    </button>
                </form>
            <?php else: ?>
                <button type="button" class="btn btn-payout" disabled>
                    <i class="fas fa-check me-2"></i>No Pending Amount
                </button>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>