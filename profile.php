<?php
$pageTitle = 'Profile Settings';
include 'layout/header.php';
?>

<!-- Profile Section -->
<div class="content-section">
    <h2><i class="fas fa-user me-2"></i>Profile Settings</h2>
    
    <div class="card">
        <div class="card-body">
            <form id="profile-form" method="POST" action="api/update_profile.php">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Contact Name</label>
                            <input type="text" class="form-control" id="contact_name" name="contact_name" value="<?php echo htmlspecialchars($currentPartner['contact_name']); ?>" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Company Name</label>
                            <input type="text" class="form-control" id="company_name" name="company_name" value="<?php echo htmlspecialchars($currentPartner['company_name']); ?>">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Phone</label>
                            <input type="tel" class="form-control" id="phone" name="phone" value="<?php echo htmlspecialchars($currentPartner['phone']); ?>">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Website</label>
                            <input type="url" class="form-control" id="website" name="website" value="<?php echo htmlspecialchars($currentPartner['website']); ?>">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Payment Method</label>
                            <select class="form-control" id="payment_method" name="payment_method">
                                <option value="bank_transfer" <?php echo $currentPartner['payment_method'] == 'bank_transfer' ? 'selected' : ''; ?>>Bank Transfer</option>
                                <option value="paypal" <?php echo $currentPartner['payment_method'] == 'paypal' ? 'selected' : ''; ?>>PayPal</option>
                                <option value="stripe" <?php echo $currentPartner['payment_method'] == 'stripe' ? 'selected' : ''; ?>>Stripe</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Payment Details</label>
                            <textarea class="form-control" id="payment_details" name="payment_details" rows="3"><?php echo htmlspecialchars($currentPartner['payment_details']); ?></textarea>
                        </div>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-2"></i>Update Profile
                </button>
            </form>
        </div>
    </div>
</div>

<?php include 'layout/footer.php'; ?>
