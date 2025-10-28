<?php
$pageTitle = 'Account Status';
include 'layout/header.php';
?>

<div class="content-section">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-user-shield me-2"></i>Account Status</h2>
    </div>

    <?php
    // Compute statuses
    $emailVerified = !empty($currentPartner['email_verified']) ? 1 : 0;
    $accountVerified = !empty($currentPartner['account_verified']) ? 1 : 0;
    $isActive = (isset($currentPartner['status']) && $currentPartner['status'] === 'active') ? 'active' : 'inactive';

    // Personal information completeness (address + national id number)
    $personalInfoComplete = (
        !empty($currentPartner['address']) &&
        !empty($currentPartner['city']) &&
        !empty($currentPartner['state']) &&
        !empty($currentPartner['national_id_card_number'])
    ) ? 1 : 0;

    // Payment method existence
    $paymentMethodsManager = new PaymentMethodsManager();
    $partnerPaymentMethods = $paymentMethodsManager->getPartnerPaymentMethods($currentPartner['id']);
    $hasPaymentMethod = !empty($partnerPaymentMethods) ? 1 : 0;
    ?>

    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">Requirements to Receive Payments</h5>
        </div>
        <div class="card-body">
            <p class="text-muted mb-4">Only the following five statuses are required to receive payments.</p>
            <div class="list-group">
                <div class="list-group-item d-flex justify-content-between align-items-center">
                    <div>
                        <strong>Email Verified</strong>
                        <div class="text-muted small">Your email must be verified.</div>
                    </div>
                    <?php if ($emailVerified): ?>
                        <span class="badge bg-success"><i class="fas fa-check me-1"></i>1</span>
                    <?php else: ?>
                        <span class="badge bg-danger"><i class="fas fa-times me-1"></i>0</span>
                    <?php endif; ?>
                </div>

                <div class="list-group-item d-flex justify-content-between align-items-center">
                    <div>
                        <strong>Payment Method Added</strong>
                        <div class="text-muted small">Add at least one payment method.</div>
                    </div>
                    <div class="d-flex align-items-center">
                        <?php if ($hasPaymentMethod): ?>
                            <span class="badge bg-success me-2"><i class="fas fa-check me-1"></i>Yes</span>
                        <?php else: ?>
                            <span class="badge bg-danger me-2"><i class="fas fa-times me-1"></i>No</span>
                        <?php endif; ?>
                        <a href="partner_payment_methods.php" class="btn btn-sm btn-outline-secondary">Manage</a>
                    </div>
                </div>

                <div class="list-group-item d-flex justify-content-between align-items-center">
                    <div>
                        <strong>Personal Information Added</strong>
                        <div class="text-muted small">Fill in address and national ID information.</div>
                    </div>
                    <div class="d-flex align-items-center">
                        <?php if ($personalInfoComplete): ?>
                            <span class="badge bg-success me-2"><i class="fas fa-check me-1"></i>Yes</span>
                        <?php else: ?>
                            <span class="badge bg-danger me-2"><i class="fas fa-times me-1"></i>No</span>
                        <?php endif; ?>
                        <a href="profile.php" class="btn btn-sm btn-outline-secondary">Update</a>
                    </div>
                </div>

                <div class="list-group-item d-flex justify-content-between align-items-center">
                    <div>
                        <strong>Account Verified (Admin)</strong>
                        <div class="text-muted small">Admin will review your information.</div>
                    </div>
                    <?php if ($accountVerified): ?>
                        <span class="badge bg-success"><i class="fas fa-check me-1"></i>1</span>
                    <?php else: ?>
                        <span class="badge bg-warning text-dark"><i class="fas fa-hourglass-half me-1"></i>0</span>
                    <?php endif; ?>
                </div>

                <div class="list-group-item d-flex justify-content-between align-items-center">
                    <div>
                        <strong>Account Status</strong>
                        <div class="text-muted small">Your account must be active.</div>
                    </div>
                    <?php if ($isActive === 'active'): ?>
                        <span class="badge bg-success"><i class="fas fa-check me-1"></i>active</span>
                    <?php else: ?>
                        <span class="badge bg-danger"><i class="fas fa-times me-1"></i>inactive</span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="alert alert-info" role="alert">
        <i class="fas fa-info-circle me-2"></i>
        To receive payments, make sure your email is verified, you have added a payment method, completed your personal information, your account is verified by admin, and your account status is active.
    </div>
</div>

<?php include 'layout/footer.php'; ?>

