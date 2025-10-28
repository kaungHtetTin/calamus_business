<?php
/**
 * View Partner Details Page
 */

require_once '../classes/admin_auth.php';

$adminAuth = new AdminAuth();

// Check if admin is logged in
if (!$adminAuth->isLoggedIn()) {
    header('Location: login.php');
    exit();
}

// Get partner ID from URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: index.php?error=Partner ID required');
    exit();
}

$partnerId = $_GET['id'];

// Get partner details
$partner = $adminAuth->getPartnerById($partnerId);

if (!$partner) {
    header('Location: index.php?error=Partner not found');
    exit();
}

$pageTitle = 'Partner Details - ' . htmlspecialchars($partner['contact_name']);
$currentPage = 'partners';

// Get pending payout amount for this partner
$pendingAmount = $adminAuth->getPendingPayoutAmount($partnerId);

// Get partner payment methods
require_once '../classes/payment_methods_manager.php';
$paymentManager = new PaymentMethodsManager();
$paymentMethods = $paymentManager->getPartnerPaymentMethods($partnerId);

// Handle password reset
$resetError = '';
$resetSuccess = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reset_password'])) {
    $newPassword = isset($_POST['new_password']) ? $_POST['new_password'] : '';
    $confirmPassword = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : '';
    
    if (empty($newPassword) || empty($confirmPassword)) {
        $resetError = 'Please fill in all fields';
    } elseif (strlen($newPassword) < 8) {
        $resetError = 'Password must be at least 8 characters long';
    } elseif ($newPassword !== $confirmPassword) {
        $resetError = 'Passwords do not match';
    } else {
        $result = $adminAuth->resetPartnerPassword($partnerId, $newPassword);
        if ($result['success']) {
            $resetSuccess = $result['message'];
        } else {
            $resetError = $result['message'];
        }
    }
}

// Handle account suspension
$suspendError = '';
$suspendSuccess = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['suspend_account'])) {
    require_once '../email_config.php';
    
    $message = isset($_POST['suspend_message']) ? trim($_POST['suspend_message']) : '';
    
    if (empty($message)) {
        $suspendError = 'Please enter a suspension message.';
    } else {
        // Update partner status to suspended
        $db = new Database();
        $updateResult = $db->save("UPDATE partners SET status = 'suspended', updated_at = NOW() WHERE id = '$partnerId'");
        
        if ($updateResult) {
            // Refresh partner details
            $partner = $adminAuth->getPartnerById($partnerId);
            
            // Send suspension email using general_action template
            $subject = 'Account Suspension Notice - Calamus Education';
            $variables = [
                'partner_name' => $partner['contact_name'] ?? 'Partner',
                'message' => nl2br(htmlspecialchars($message))
            ];
            $template = getEmailTemplate('general_action', $variables);
            if (!$template) {
                $template = "<div style='font-family: Arial, sans-serif;'>"
                          . "<p>Dear " . htmlspecialchars($partner['contact_name'] ?? 'Partner') . ",</p>"
                          . "<p>" . nl2br(htmlspecialchars($message)) . "</p>"
                          . "<p>Regards,<br>Calamus Education</p>"
                          . "</div>";
            }
            $emailSent = sendEmail($partner['email'], $subject, $template, 'general_action');
            
            $suspendSuccess = 'Account suspended successfully. ' . ($emailSent ? 'Suspension email sent to partner.' : 'Email sending failed.');
        } else {
            $suspendError = 'Failed to suspend account.';
        }
    }
}
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
    <style>
        .partner-avatar {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #e8eaed;
        }
        
        .profile-placeholder {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: #f1f3f4;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 32px;
            color: #5f6368;
        }
        
        .action-buttons {
            display: flex;
            gap: 12px;
            margin-top: 24px;
        }
    </style>
</head>
<body>
    <!-- Admin Header -->
    <?php include 'layout/admin_header.php'; ?>

    <?php include 'layout/admin_sidebar.php'; ?>
    <div class="container-fluid" style="padding: 24px;">
        <!-- Back Button -->
        <div class="mb-3">
            <a href="partners.php" class="btn btn-sm btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to Partners
            </a>
        </div>
        
        <!-- Partner Header -->
        <div class="partner-header">
            <div class="d-flex align-items-center">
                <div class="flex-shrink-0">
                    <?php if ($partner['profile_image']): ?>
                        <img src="../<?php echo htmlspecialchars($partner['profile_image']); ?>" alt="Profile" class="partner-avatar">
                    <?php else: ?>
                        <div class="profile-placeholder">
                            <i class="fas fa-user"></i>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="flex-grow-1 ms-4">
                    <h2 class="mb-2" style="color: #202124;"><?php echo htmlspecialchars($partner['contact_name']); ?></h2>
                    <p class="mb-1" style="color: #5f6368;"><?php echo htmlspecialchars($partner['company_name'] ?? 'N/A'); ?></p>
                    <p class="mb-0" style="color: #5f6368; font-size: 14px;">
                        <i class="fas fa-envelope me-2"></i><?php echo htmlspecialchars($partner['email']); ?>
                    </p>
                </div>
                <div class="flex-shrink-0">
                    <?php
                    $status = $partner['status'] ?? 'active';
                    $statusClass = 'status-active';
                    if ($status === 'inactive') $statusClass = 'status-inactive';
                    if ($status === 'suspended') $statusClass = 'status-suspended';
                    ?>
                    <span class="status-badge <?php echo $statusClass; ?>"><?php echo ucfirst($status); ?></span>
                </div>
            </div>
        </div>
        
        <!-- Partner Information -->
        <div class="row">
            <div class="col-md-6">
                <div class="info-card">
                    <h5 class="mb-3" style="color: #202124;">
                        <i class="fas fa-info-circle me-2"></i>Basic Information
                    </h5>
                    <div class="info-row">
                        <div class="info-label">Partner ID</div>
                        <div class="info-value">#<?php echo htmlspecialchars($partner['id']); ?></div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Company Name</div>
                        <div class="info-value"><?php echo htmlspecialchars($partner['company_name'] ?? 'N/A'); ?></div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Contact Name</div>
                        <div class="info-value"><?php echo htmlspecialchars($partner['contact_name'] ?? 'N/A'); ?></div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Email</div>
                        <div class="info-value">
                            <i class="fas fa-envelope me-2"></i><?php echo htmlspecialchars($partner['email']); ?>
                        </div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Phone</div>
                        <div class="info-value">
                            <i class="fas fa-phone me-2"></i><?php echo htmlspecialchars($partner['phone'] ?? 'N/A'); ?>
                        </div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Website</div>
                        <div class="info-value">
                            <?php if ($partner['website']): ?>
                                <a href="<?php echo htmlspecialchars($partner['website']); ?>" target="_blank" style="color: #1a73e8;">
                                    <i class="fas fa-globe me-2"></i><?php echo htmlspecialchars($partner['website']); ?>
                                </a>
                            <?php else: ?>
                                N/A
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="info-card">
                    <h5 class="mb-3" style="color: #202124;">
                        <i class="fas fa-cog me-2"></i>Account Information
                    </h5>
                    <div class="info-row">
                        <div class="info-label">Status</div>
                        <div class="info-value">
                            <span class="status-badge <?php echo $statusClass; ?>"><?php echo ucfirst($status); ?></span>
                        </div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Email Verified</div>
                        <div class="info-value">
                            <?php if ($partner['email_verified']): ?>
                                <i class="fas fa-check-circle text-success"></i> Yes
                            <?php else: ?>
                                <i class="fas fa-times-circle text-danger"></i> No
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Private Code</div>
                        <div class="info-value">
                            <code><?php echo htmlspecialchars($partner['private_code'] ?? 'N/A'); ?></code>
                        </div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Commission Rate</div>
                        <div class="info-value"><?php echo htmlspecialchars($partner['commission_rate'] ?? 'N/A'); ?>%</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Account Created</div>
                        <div class="info-value">
                            <i class="fas fa-calendar me-2"></i><?php echo date('M d, Y', strtotime($partner['created_at'])); ?>
                        </div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Last Login</div>
                        <div class="info-value">
                            <i class="fas fa-clock me-2"></i><?php echo $partner['last_login'] ? date('M d, Y H:i', strtotime($partner['last_login'])) : 'Never'; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Personal Information -->
        <div class="info-card">
            <h5 class="mb-3" style="color: #202124;">
                <i class="fas fa-id-card me-2"></i>Personal Information
            </h5>
            <div class="row">
                <div class="col-md-6">
                    <div class="info-row">
                        <div class="info-label">Address</div>
                        <div class="info-value"><?php echo htmlspecialchars($partner['address'] ?? 'N/A'); ?></div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="info-row">
                        <div class="info-label">City</div>
                        <div class="info-value"><?php echo htmlspecialchars($partner['city'] ?? 'N/A'); ?></div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="info-row">
                        <div class="info-label">State</div>
                        <div class="info-value"><?php echo htmlspecialchars($partner['state'] ?? 'N/A'); ?></div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="info-row">
                        <div class="info-label">National ID Number</div>
                        <div class="info-value"><?php echo htmlspecialchars($partner['national_id_card_number'] ?? 'N/A'); ?></div>
                    </div>
                </div>
            </div>

            <!-- National ID Card Images -->
            <?php if (!empty($partner['national_id_card_front_image']) || !empty($partner['national_id_card_back_image'])): ?>
            <div class="row mt-3">
                <div class="col-md-6">
                    <div class="text-muted mb-2">National ID Card (Front)</div>
                    <?php if (!empty($partner['national_id_card_front_image'])): ?>
                        <div>
                            <a href="../<?php echo htmlspecialchars($partner['national_id_card_front_image']); ?>" target="_blank">
                                <img src="../<?php echo htmlspecialchars($partner['national_id_card_front_image']); ?>" alt="NID Front" class="img-fluid border rounded" style="max-height: 250px; width: 100%; object-fit: contain;">
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="text-muted">Not provided</div>
                    <?php endif; ?>
                </div>
                <div class="col-md-6">
                    <div class="text-muted mb-2">National ID Card (Back)</div>
                    <?php if (!empty($partner['national_id_card_back_image'])): ?>
                        <div>
                            <a href="../<?php echo htmlspecialchars($partner['national_id_card_back_image']); ?>" target="_blank">
                                <img src="../<?php echo htmlspecialchars($partner['national_id_card_back_image']); ?>" alt="NID Back" class="img-fluid border rounded" style="max-height: 250px; width: 100%; object-fit: contain;">
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="text-muted">Not provided</div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
        
        <!-- Description -->
        <?php if ($partner['description']): ?>
        <div class="info-card">
            <h5 class="mb-3" style="color: #202124;">
                <i class="fas fa-align-left me-2"></i>Description
            </h5>
            <p style="color: #202124; line-height: 1.6;"><?php echo nl2br(htmlspecialchars($partner['description'])); ?></p>
        </div>
        <?php endif; ?>
        
        <!-- Pending Payout Amount -->
        <?php if ($pendingAmount > 0): ?>
        <div class="amount-card">
            <div class="amount-label">Pending Amount to Payout</div>
            <div class="amount-value"><?php echo number_format($pendingAmount, 2); ?></div>
            <div class="currency">MMK</div>
        </div>
        
        <div class="text-center mb-4">
            <a href="process_payout.php?partner_id=<?php echo htmlspecialchars($partner['id']); ?>" class="btn btn-success btn-lg">
                <i class="fas fa-money-bill-wave me-2"></i>Process Payout
            </a>
        </div>
        <?php endif; ?>
        
        <!-- Payment Methods -->
        <div class="info-card">
            <h5 class="mb-3" style="color: #202124;">
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
        
        <!-- Password Reset -->
        <div class="info-card">
            <h5 class="mb-3" style="color: #202124;">
                <i class="fas fa-key me-2"></i>Reset Password
            </h5>
            
            <?php if ($resetError): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?php echo htmlspecialchars($resetError); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            
            <?php if ($resetSuccess): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?php echo htmlspecialchars($resetSuccess); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            
            <div class="alert alert-warning" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i>
                Resetting the password will terminate all active sessions for this partner. They will need to log in again with the new password.
            </div>
            
            <form method="POST" action="view_partner.php?id=<?php echo htmlspecialchars($partner['id']); ?>" style="margin-top: 20px;">
                <input type="hidden" name="reset_password" value="1">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="new_password" class="form-label">New Password <span class="text-danger">*</span></label>
                            <input type="password" class="form-control" id="new_password" name="new_password" required minlength="8">
                            <div class="form-text">Minimum 8 characters</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="confirm_password" class="form-label">Confirm Password <span class="text-danger">*</span></label>
                            <input type="password" class="form-control" id="confirm_password" name="confirm_password" required minlength="8">
                        </div>
                    </div>
                </div>
                <button type="submit" class="btn btn-warning" onclick="return confirm('Are you sure you want to reset this partner\'s password? All active sessions will be terminated.');">
                    <i class="fas fa-key me-2"></i>Reset Password
                </button>
            </form>
        </div>
        
        <!-- Account Suspension -->
        <?php if (($partner['status'] ?? 'active') !== 'suspended'): ?>
        <div class="info-card">
            <h5 class="mb-3" style="color: #202124;">
                <i class="fas fa-ban me-2"></i>Suspend Account
            </h5>
            
            <?php if ($suspendError): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?php echo htmlspecialchars($suspendError); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            
            <?php if ($suspendSuccess): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?php echo htmlspecialchars($suspendSuccess); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            
            <div class="alert alert-danger" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i>
                Suspending the account will prevent the partner from accessing their account and will send them a notification email.
            </div>
            
            <form method="POST" action="view_partner.php?id=<?php echo htmlspecialchars($partner['id']); ?>" style="margin-top: 20px;">
                <input type="hidden" name="suspend_account" value="1">
                <div class="mb-3">
                    <label for="suspend_message" class="form-label">Suspension Reason <span class="text-danger">*</span></label>
                    <textarea class="form-control" id="suspend_message" name="suspend_message" rows="4" placeholder="Enter the reason for suspension..." required></textarea>
                    <div class="form-text">This message will be sent to the partner via email.</div>
                </div>
                <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to suspend this partner\'s account?');">
                    <i class="fas fa-ban me-2"></i>Suspend Account
                </button>
            </form>
        </div>
        <?php endif; ?>
        
        <!-- Action Buttons -->
        <div class="action-buttons">
            <a href="partners.php" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to List
            </a>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
