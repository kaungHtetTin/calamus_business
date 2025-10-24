<?php
$pageTitle = 'Profile Settings';
include 'layout/header.php';
?>

<!-- Profile Section -->
<div class="content-section">
    <h2><i class="fas fa-user me-2"></i>Profile Settings</h2>
    
    <!-- Profile Image Section -->
    <div class="card mb-4">
        <div class="card-body">
            <h5 class="card-title"><i class="fas fa-image me-2"></i>Profile Picture</h5>
            <div class="row align-items-center">
                <div class="col-md-3">
                    <div class="profile-image-container text-center">
                        <div class="profile-image-preview mb-3">
                            <?php if (!empty($currentPartner['profile_image']) && file_exists($currentPartner['profile_image'])): ?>
                                <img src="<?php echo htmlspecialchars($currentPartner['profile_image']); ?>" 
                                     alt="Profile Picture" 
                                     class="profile-image rounded-circle" 
                                     id="profileImagePreview">
                            <?php else: ?>
                                <div class="profile-image-placeholder rounded-circle d-flex align-items-center justify-content-center" id="profileImagePreview">
                                    <i class="fas fa-user fa-3x text-muted"></i>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <div class="col-md-9">
                    <div class="mb-3">
                        <label class="form-label">Upload New Profile Picture</label>
                        <input type="file" class="form-control" id="profileImageInput" accept="image/*">
                        <div class="form-text">
                            <small class="text-muted">
                                <i class="fas fa-info-circle me-1"></i>
                                Supported formats: JPG, PNG, GIF, WebP. Maximum size: 5MB.
                            </small>
                        </div>
                    </div>
                    <button type="button" class="btn btn-outline-primary" id="uploadImageBtn" disabled>
                        <i class="fas fa-upload me-2"></i>Upload Image
                    </button>
                    <?php if (!empty($currentPartner['profile_image'])): ?>
                    <button type="button" class="btn btn-outline-danger ms-2" id="removeImageBtn">
                        <i class="fas fa-trash me-2"></i>Remove Image
                    </button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Profile Information Section -->
    <div class="card">
        <div class="card-body">
            <h5 class="card-title"><i class="fas fa-user-edit me-2"></i>Profile Information</h5>
            <form id="profile-form" method="POST" action="api/update_profile.php" enctype="multipart/form-data">
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

<!-- Load profile JavaScript -->
<script src="js/profile.js"></script>

<?php include 'layout/footer.php'; ?>
