<?php
/**
 * Admin Account Verification Page
 * Review a partner's information and verify or reject with email.
 */

require_once '../classes/admin_auth.php';
require_once '../classes/payment_methods_manager.php';
require_once '../email_config.php';

$adminAuth = new AdminAuth();

// Check if admin is logged in
if (!$adminAuth->isLoggedIn()) {
    header('Location: login.php');
    exit();
}

// Get partner ID
$partnerId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($partnerId <= 0) {
    header('Location: partners.php?error=' . urlencode('Invalid partner ID'));
    exit();
}

// Fetch partner details
$partner = $adminAuth->getPartnerById($partnerId);
if (!$partner) {
    header('Location: partners.php?error=' . urlencode('Partner not found'));
    exit();
}

// Fetch payment methods
$paymentManager = new PaymentMethodsManager();
$paymentMethods = $paymentManager->getPartnerPaymentMethods($partnerId);

// Handle actions
$successMessage = null;
$errorMessage = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'verify') {
        $db = new Database();
        $updated = $db->save("UPDATE partners SET account_verified = 1, updated_at = NOW() WHERE id = '$partnerId'");
        if ($updated) {
            $successMessage = 'Partner account verified successfully.';
            $partner = $adminAuth->getPartnerById($partnerId);
        } else {
            $errorMessage = 'Failed to verify partner account.';
        }
    } elseif ($action === 'reject') {
        $message = trim($_POST['message'] ?? '');
        if ($message === '') {
            $errorMessage = 'Please enter a message to send to the partner.';
        } else {
            $subject = 'Account Verification - Action Required';
            $variables = [
                'partner_name' => $partner['contact_name'] ?? 'Partner',
                'partner_email' => $partner['email'],
                'message' => nl2br(htmlspecialchars($message))
            ];
            // Try to use a template, fallback to simple HTML
            $template = getEmailTemplate('general_action', $variables);
            if (!$template) {
                $template = "<div style='font-family: Arial, sans-serif;'>"
                          . "<p>Dear " . htmlspecialchars($partner['contact_name'] ?? 'Partner') . ",</p>"
                          . "<p>" . nl2br(htmlspecialchars($message)) . "</p>"
                          . "<p>Regards,<br>Calamus Education</p>"
                          . "</div>";
            }
            $sent = sendEmail($partner['email'], $subject, $template, EMAIL_TEMPLATE_VERIFICATION);
            if ($sent) {
                $successMessage = 'Rejection email sent to the partner.';
            } else {
                $errorMessage = 'Failed to send email to the partner.';
            }
        }
    }
}

$pageTitle = 'Account Verification';
$isAdmin = true;
$currentPage = 'partners';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?> - Calamus Education Partner Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../css/app.css">
    <link rel="stylesheet" href="css/app.css">
</head>
<body>
    <?php include 'layout/admin_header.php'; ?>
    <?php include 'layout/admin_sidebar.php'; ?>

    <div class="container-fluid" style="padding: 24px;">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2 class="mb-0">Account Verification</h2>
            <a href="partners.php" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-2"></i>Back to Partners</a>
        </div>

        <?php if ($successMessage): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php echo htmlspecialchars($successMessage); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        <?php if ($errorMessage): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php echo htmlspecialchars($errorMessage); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Partner Overview -->
        <div class="card mb-4">
            <div class="card-header">
                <strong>Partner</strong>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-3">
                        <div class="text-muted">Partner ID</div>
                        <div>#<?php echo htmlspecialchars($partner['id']); ?></div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-muted">Contact</div>
                        <div><?php echo htmlspecialchars($partner['contact_name'] ?? 'N/A'); ?></div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-muted">Email</div>
                        <div><?php echo htmlspecialchars($partner['email']); ?></div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-muted">Status</div>
                        <div>
                            <span class="badge bg-<?php echo ($partner['account_verified'] ?? 0) ? 'success' : 'warning'; ?>">
                                <?php echo ($partner['account_verified'] ?? 0) ? 'Verified' : 'In Review'; ?>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-7">
                <!-- Personal Information -->
                <div class="card mb-4">
                    <div class="card-header"><strong>Personal Information</strong></div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="text-muted">Company Name</div>
                                <div><?php echo htmlspecialchars($partner['company_name'] ?? 'N/A'); ?></div>
                            </div>
                            <div class="col-md-6">
                                <div class="text-muted">Phone</div>
                                <div><?php echo htmlspecialchars($partner['phone'] ?? 'N/A'); ?></div>
                            </div>
                            <div class="col-md-12">
                                <div class="text-muted">Address</div>
                                <div><?php echo htmlspecialchars(trim(($partner['address'] ?? '') . ', ' . ($partner['city'] ?? '') . ', ' . ($partner['state'] ?? ''))); ?></div>
                            </div>
                            <div class="col-md-6">
                                <div class="text-muted">National ID Number</div>
                                <div><?php echo htmlspecialchars($partner['national_id_card_number'] ?? ''); ?></div>
                            </div>
                            <div class="col-md-6"></div>
                            <div class="col-md-6">
                                <div class="text-muted">NID Front Image</div>
                                <?php if (!empty($partner['national_id_card_front_image'])): ?>
                                    <div class="mt-1">
                                        <a href="../<?php echo htmlspecialchars($partner['national_id_card_front_image']); ?>" target="_blank">
                                            <img src="../<?php echo htmlspecialchars($partner['national_id_card_front_image']); ?>" alt="NID Front" class="img-fluid border rounded" style="max-height: 220px; object-fit: cover;">
                                        </a>
                                    </div>
                                <?php else: ?>
                                    <div>N/A</div>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-6">
                                <div class="text-muted">NID Back Image</div>
                                <?php if (!empty($partner['national_id_card_back_image'])): ?>
                                    <div class="mt-1">
                                        <a href="../<?php echo htmlspecialchars($partner['national_id_card_back_image']); ?>" target="_blank">
                                            <img src="../<?php echo htmlspecialchars($partner['national_id_card_back_image']); ?>" alt="NID Back" class="img-fluid border rounded" style="max-height: 220px; object-fit: cover;">
                                        </a>
                                    </div>
                                <?php else: ?>
                                    <div>N/A</div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Payment Methods -->
                <div class="card mb-4">
                    <div class="card-header"><strong>Payment Methods</strong></div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table mb-0">
                                <thead>
                                    <tr>
                                        <th>Method</th>
                                        <th>Account Name</th>
                                        <th>Account Number</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if ($paymentMethods): ?>
                                        <?php foreach ($paymentMethods as $pm): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($pm['payment_method']); ?></td>
                                            <td><?php echo htmlspecialchars($pm['account_name']); ?></td>
                                            <td><?php echo htmlspecialchars($pm['account_number']); ?></td>
                                        </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="3" class="text-muted">No payment methods found.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-5">
                <!-- Actions -->
                <div class="card mb-4">
                    <div class="card-header"><strong>Actions</strong></div>
                    <div class="card-body">
                        <form method="POST" class="mb-3">
                            <input type="hidden" name="action" value="verify">
                            <button type="submit" class="btn btn-success w-100">
                                <i class="fas fa-badge-check me-2"></i>Verify Account
                            </button>
                        </form>
                        <hr>
                        <form method="POST">
                            <input type="hidden" name="action" value="reject">
                            <div class="mb-3">
                                <label class="form-label">Rejection message (will be emailed to partner)</label>
                                <textarea class="form-control" name="message" rows="5" placeholder="Explain what needs to be fixed (e.g., missing ID, incorrect address)..."></textarea>
                            </div>
                            <button type="submit" class="btn btn-outline-danger w-100">
                                <i class="fas fa-paper-plane me-2"></i>Send Email (Reject)
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>


