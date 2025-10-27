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

// Get admins/staff with ranking = 1
$admins = $adminAuth->getStaffByRanking(1);

$pageTitle = 'Process Payout';
$currentPage = 'payout_logs'; // Set current page for sidebar active state

$error = '';
$formData = [];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $paymentMethodId = isset($_POST['payment_method_id']) ? $_POST['payment_method_id'] : '';
    $staffId = isset($_POST['staff_id']) ? $_POST['staff_id'] : '';
    
    // Validate inputs
    if (empty($paymentMethodId) || empty($staffId)) {
        $error = 'Please select a payment method and admin';
    } elseif (empty($pendingAmount) || $pendingAmount <= 0) {
        $error = 'No pending amount to payout';
    } elseif ($_FILES['transaction_screenshot']['error'] === UPLOAD_ERR_OK) {
        // Validate and upload screenshot
        $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
        $maxSize = 5 * 1024 * 1024; // 5MB
        
        if (!in_array($_FILES['transaction_screenshot']['type'], $allowedTypes)) {
            $error = 'Invalid file type. Only JPEG, PNG, and GIF images are allowed.';
        } elseif ($_FILES['transaction_screenshot']['size'] > $maxSize) {
            $error = 'File size exceeds 5MB limit.';
        } else {
            // Generate unique filename
            $filename = time() . '_' . basename($_FILES['transaction_screenshot']['name']);
            $uploadDir = '../uploads/payment_screenshots/';
            
            // Create directory if it doesn't exist
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            
            $uploadFile = $uploadDir . $filename;
            
            if (move_uploaded_file($_FILES['transaction_screenshot']['tmp_name'], $uploadFile)) {
                $screenshotPath = 'uploads/payment_screenshots/' . $filename;
                
                // Process the payout
                $result = $adminAuth->processPartnerPayout($partnerId, $paymentMethodId, $staffId, $pendingAmount, $screenshotPath);
                
                if ($result['success']) {
                    header('Location: payout_logs.php?success=' . urlencode('Payout processed successfully'));
                    exit();
                } else {
                    $error = $result['message'];
                    // Delete uploaded file on error
                    unlink($uploadFile);
                }
            } else {
                $error = 'Failed to upload screenshot';
            }
        }
    } else {
        $error = 'Please upload a payment screenshot';
    }
    
    $formData = [
        'payment_method_id' => $paymentMethodId,
        'staff_id' => $staffId
    ];
}
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
        
        <?php if ($error): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php echo htmlspecialchars($error); ?>
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

        <?php if ($pendingAmount > 0 && count($paymentMethods) > 0 && count($admins) > 0): ?>
        <!-- Payout Form -->
        <div class="info-card">
            <h5 class="card-title">
                <i class="fas fa-money-bill-wave me-2"></i>Process Payout
            </h5>
            
            <form method="POST" action="process_payout.php?partner_id=<?php echo $partner['id']; ?>" enctype="multipart/form-data">
                <!-- Payment Method Selector -->
                <div class="mb-4">
                    <label for="payment_method_id" class="form-label fw-bold">
                        <i class="fas fa-credit-card me-2"></i>Select Payment Method <span class="text-danger">*</span>
                    </label>
                    <select class="form-select" id="payment_method_id" name="payment_method_id" required>
                        <option value="">-- Select Payment Method --</option>
                        <?php foreach ($paymentMethods as $method): ?>
                            <option value="<?php echo $method['id']; ?>" <?php echo isset($formData['payment_method_id']) && $formData['payment_method_id'] == $method['id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($method['payment_method']); ?> - 
                                <?php echo htmlspecialchars($method['account_name']); ?> 
                                (<?php echo htmlspecialchars($method['account_number']); ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <!-- Admin/Staff Selector -->
                <div class="mb-4">
                    <label for="staff_id" class="form-label fw-bold">
                        <i class="fas fa-user-shield me-2"></i>Select Admin <span class="text-danger">*</span>
                    </label>
                    <select class="form-select" id="staff_id" name="staff_id" required>
                        <option value="">-- Select Admin --</option>
                        <?php foreach ($admins as $admin): ?>
                            <option value="<?php echo $admin['id']; ?>" <?php echo isset($formData['staff_id']) && $formData['staff_id'] == $admin['id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($admin['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <!-- Screenshot Upload -->
                <div class="mb-4">
                    <label for="transaction_screenshot" class="form-label fw-bold">
                        <i class="fas fa-file-image me-2"></i>Payment Screenshot <span class="text-danger">*</span>
                    </label>
                    <input type="file" class="form-control" id="transaction_screenshot" name="transaction_screenshot" accept="image/*" required>
                    <div class="form-text">Accepted formats: JPEG, PNG, GIF (Max: 5MB)</div>
                </div>
                
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>Amount to be paid:</strong> <?php echo number_format($pendingAmount, 2); ?> MMK
                </div>
                
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-payout" onclick="return confirm('Are you sure you want to process this payout? This action cannot be undone.');">
                        <i class="fas fa-check me-2"></i>Confirm Payout
                    </button>
                    <a href="payout_logs.php" class="btn btn-outline-secondary">
                        <i class="fas fa-times me-2"></i>Cancel
                    </a>
                </div>
            </form>
        </div>
        <?php else: ?>
        <!-- Warning Messages -->
        <?php if ($pendingAmount <= 0): ?>
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle me-2"></i>
                No pending amount to payout for this partner.
            </div>
        <?php elseif (count($paymentMethods) == 0): ?>
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle me-2"></i>
                This partner has no payment methods. Please add a payment method first.
            </div>
        <?php elseif (count($admins) == 0): ?>
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle me-2"></i>
                No admins found in the system. Please add an admin with ranking = 1.
            </div>
        <?php endif; ?>
        
        <div class="d-flex gap-2">
            <a href="payout_logs.php" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to Payout Logs
            </a>
        </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>