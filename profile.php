<?php
$pageTitle = 'Profile Settings';
include 'layout/header.php';
?>

<style>
/* Google-Style Minimal Profile Page */
.profile-container {
    background: #fafafa;
    min-height: 100vh;
}

.profile-header {
    background: white;
    border-bottom: 1px solid #e8eaed;
    padding: 24px 48px;
}

.profile-header h1 {
    font-size: 22px;
    font-weight: 400;
    color: #202124;
    margin: 0 0 4px 0;
}

.profile-header p {
    font-size: 14px;
    color: #5f6368;
    margin: 0;
}

.content-wrapper {
    padding: 32px 48px;
    max-width: 1200px;
    margin: 0 auto;
}

.google-card {
    background: white;
    border: 1px solid #e8eaed;
    border-radius: 8px;
    margin-bottom: 24px;
}

.google-card .card-header {
    padding: 20px 24px;
    border-bottom: 1px solid #e8eaed;
}

.google-card .card-header h5 {
    font-size: 16px;
    font-weight: 500;
    color: #202124;
    margin: 0;
}

.google-card .card-body {
    padding: 24px;
}

.profile-image-preview {
    width: 96px;
    height: 96px;
    margin: 0 auto;
    border-radius: 50%;
    overflow: hidden;
    background: #dadce0;
    display: flex;
    align-items: center;
    justify-content: center;
}

.profile-image-preview img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.profile-image-placeholder {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #5f6368;
    font-size: 48px;
}

.google-form-group {
    margin-bottom: 20px;
}

.google-form-group label {
    font-size: 14px;
    font-weight: 500;
    color: #202124;
    display: block;
    margin-bottom: 8px;
}

.google-form-group .form-control {
    font-size: 14px;
    padding: 10px 14px;
    border: 1px solid #dadce0;
    border-radius: 4px;
    background: white;
    transition: border-color 0.2s, box-shadow 0.2s;
}

.google-form-group .form-control:focus {
    border-color: #202124;
    outline: none;
    box-shadow: 0 0 0 2px rgba(32, 33, 36, 0.1);
}

.google-btn {
    font-size: 14px;
    font-weight: 500;
    padding: 10px 24px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    transition: background-color 0.2s, box-shadow 0.2s;
}

.google-btn-primary {
    background: #202124;
    color: white;
}

.google-btn-primary:hover {
    background: #3c4043;
    box-shadow: 0 1px 2px 0 rgba(60,64,67,.3), 0 1px 3px 1px rgba(60,64,67,.15);
}

.google-btn-outline {
    background: white;
    color: #202124;
    border: 1px solid #dadce0;
}

.google-btn-outline:hover {
    background: #f8f9fa;
    border-color: #202124;
    color: #202124;
}

.google-btn-secondary {
    background: #f8f9fa;
    color: #202124;
    border: 1px solid #dadce0;
}

.google-btn-secondary:hover {
    background: #e8eaed;
    border-color: #dadce0;
}

.private-code-section {
    background: #f8f9fa;
    border: 1px solid #e8eaed;
    border-radius: 4px;
    padding: 16px 20px;
}

.private-code-value {
    font-family: 'Courier New', monospace;
    font-size: 16px;
    font-weight: 500;
    color: #202124;
    background: white;
    padding: 10px 16px;
    border-radius: 4px;
    display: inline-block;
    border: 1px solid #dadce0;
}

.password-section {
    background: #f8f9fa;
    border: 1px solid #e8eaed;
    border-radius: 4px;
    padding: 24px;
}

.password-strength {
    background: white;
    border: 1px solid #dadce0;
    border-radius: 4px;
    padding: 12px;
    margin-top: 16px;
}

.strength-bar {
    height: 4px;
    border-radius: 2px;
    background: #e8eaed;
    overflow: hidden;
    margin-bottom: 8px;
}

.strength-fill {
    height: 100%;
    transition: all 0.3s ease;
}

.strength-weak { background: #ea4335; }
.strength-fair { background: #fbbc04; }
.strength-good { background: #34a853; }
.strength-strong { background: #1e8e3e; }

.input-group-google {
    position: relative;
}

.input-group-google .form-control {
    padding-right: 48px;
}

.input-group-google .btn {
    position: absolute;
    right: 8px;
    top: 50%;
    transform: translateY(-50%);
    background: transparent;
    border: none;
    padding: 8px;
    color: #5f6368;
    cursor: pointer;
}

.input-group-google .btn:hover {
    color: #202124;
}

.alert-google {
    background: #fef7e0;
    border: 1px solid #f9ab00;
    border-radius: 4px;
    padding: 12px 16px;
    margin-bottom: 16px;
    font-size: 14px;
}

.alert-success {
    background: #e6f4ea;
    border-color: #137333;
    color: #137333;
}

.alert-error {
    background: #fce8e6;
    border-color: #d93025;
    color: #d93025;
}

.alert-info {
    background: #f8f9fa;
    border-color: #e8eaed;
    color: #202124;
}

.help-text {
    font-size: 13px;
    color: #5f6368;
    margin-top: 6px;
}

.badge-google {
    font-size: 12px;
    font-weight: 500;
    padding: 4px 12px;
    border-radius: 16px;
    background: #f8f9fa;
    color: #202124;
}

/* Responsive */
@media (max-width: 768px) {
    .profile-header {
        padding: 20px 24px;
    }
    
    .content-wrapper {
        padding: 24px;
    }
    
    .google-card .card-body {
        padding: 20px;
    }
}
</style>

<div class="profile-container">
    <!-- Profile Header -->
    <div class="profile-header">
        <h1>Profile settings</h1>
        <p>Manage your account information and security settings</p>
    </div>

    <div class="content-wrapper">
        <!-- Profile Image Section -->
        <div class="google-card">
            <div class="card-header">
                <h5>Profile picture</h5>
            </div>
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-4 text-center mb-3 mb-md-0">
                        <div class="profile-image-preview">
                            <?php if (!empty($currentPartner['profile_image']) && file_exists($currentPartner['profile_image'])): ?>
                                <img src="<?php echo htmlspecialchars($currentPartner['profile_image']); ?>" 
                                     alt="Profile Picture" 
                                     id="profileImagePreview"
                                     style="width: 100%; height: 100%; object-fit: cover;">
                            <?php else: ?>
                                <div class="profile-image-placeholder" id="profileImagePreview">
                                    <i class="fas fa-user"></i>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="google-form-group">
                            <label>Upload new picture</label>
                            <input type="file" class="form-control" id="profileImageInput" accept="image/*">
                            <div class="help-text">
                                JPG, PNG, GIF, WebP up to 5MB
                            </div>
                        </div>
                        <div class="d-flex gap-2 flex-wrap mt-2">
                            <button type="button" class="google-btn google-btn-outline" id="uploadImageBtn" disabled>
                                Upload
                            </button>
                            <?php if (!empty($currentPartner['profile_image'])): ?>
                            <button type="button" class="google-btn google-btn-secondary" id="removeImageBtn">
                                Remove
                            </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    
    <!-- Profile Information Section -->
        <div class="google-card">
            <div class="card-header">
                <h5>Personal information</h5>
            </div>
        <div class="card-body">
            <!-- Private Code Display -->
                <div class="private-code-section mb-4">
                <div class="row align-items-center">
                    <div class="col-md-8">
                            <strong style="font-size: 14px; color: #202124;">Partner code</strong>
                            <p style="font-size: 13px; color: #5f6368; margin: 4px 0 0 0;">
                                Use this code to generate promotion links
                            </p>
                    </div>
                    <div class="col-md-4 text-end">
                            <div class="private-code-value" id="privateCodeDisplay">
                                <?php echo htmlspecialchars($currentPartner['private_code'] ?? 'N/A'); ?>
                            </div>
                            <button type="button" class="google-btn google-btn-secondary mt-2" onclick="copyPrivateCode()">
                                Copy code
                            </button>
                    </div>
                </div>
            </div>
            
            <form id="profile-form" method="POST" action="api/update_profile.php" enctype="multipart/form-data">
                <div class="row">
                    <div class="col-md-6">
                            <div class="google-form-group">
                                <label>Contact name</label>
                                <input type="text" class="form-control" id="contact_name" name="contact_name" 
                                       value="<?php echo htmlspecialchars($currentPartner['contact_name']); ?>" required>
                            </div>
                    </div>
                    <div class="col-md-6">
                            <div class="google-form-group">
                                <label>Company name</label>
                                <input type="text" class="form-control" id="company_name" name="company_name" 
                                       value="<?php echo htmlspecialchars($currentPartner['company_name']); ?>">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                            <div class="google-form-group">
                                <label>Phone number</label>
                                <input type="tel" class="form-control" id="phone" name="phone" 
                                       value="<?php echo htmlspecialchars($currentPartner['phone']); ?>">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="google-form-group">
                                <label>Social Media Link</label>
                                <input type="url" class="form-control" id="website" name="website" 
                                       value="<?php echo htmlspecialchars($currentPartner['website']); ?>">
                            </div>
                        </div>
                    </div>
                    <!-- Address Information -->
                    <div class="row mt-2">
                        <div class="col-12">
                            <hr>
                            <h6 style="font-weight:500;color:#202124;">Address information</h6>
                        </div>
                        <div class="col-md-12">
                            <div class="google-form-group">
                                <label>Address</label>
                                <input type="text" class="form-control" id="address" name="address" 
                                       value="<?php echo htmlspecialchars($currentPartner['address'] ?? ''); ?>">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="google-form-group">
                                <label>City</label>
                                <input type="text" class="form-control" id="city" name="city" 
                                       value="<?php echo htmlspecialchars($currentPartner['city'] ?? ''); ?>">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="google-form-group">
                                <label>State</label>
                                <input type="text" class="form-control" id="state" name="state" 
                                       value="<?php echo htmlspecialchars($currentPartner['state'] ?? ''); ?>">
                            </div>
                        </div>
                    </div>

                    <!-- National ID Card Information -->
                    <div class="row mt-2">
                        <div class="col-12">
                            <hr>
                            <h6 style="font-weight:500;color:#202124;">National ID card</h6>
                            <div class="help-text">Upload clear images of the front and back of your national ID card.</div>
                        </div>
                        <div class="col-md-6">
                            <div class="google-form-group">
                                <label>National ID card number</label>
                                <input type="text" class="form-control" id="national_id_card_number" name="national_id_card_number" 
                                       value="<?php echo htmlspecialchars($currentPartner['national_id_card_number'] ?? ''); ?>">
                            </div>
                        </div>
                        <div class="col-md-6"></div>
                        <div class="col-md-6">
                            <div class="google-form-group">
                                <label>Front image</label>
                                <input type="file" class="form-control" id="national_id_card_front_image" name="national_id_card_front_image" accept="image/*">
                                <?php if (!empty($currentPartner['national_id_card_front_image'])): ?>
                                    <div style="margin-top:12px;">
                                        <a href="<?php echo htmlspecialchars($currentPartner['national_id_card_front_image']); ?>" target="_blank">
                                            <img src="<?php echo htmlspecialchars($currentPartner['national_id_card_front_image']); ?>" alt="NID Front" class="img-fluid border rounded" style="max-height: 200px; width: 100%; object-fit: contain;">
                                        </a>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="google-form-group">
                                <label>Back image</label>
                                <input type="file" class="form-control" id="national_id_card_back_image" name="national_id_card_back_image" accept="image/*">
                                <?php if (!empty($currentPartner['national_id_card_back_image'])): ?>
                                    <div style="margin-top:12px;">
                                        <a href="<?php echo htmlspecialchars($currentPartner['national_id_card_back_image']); ?>" target="_blank">
                                            <img src="<?php echo htmlspecialchars($currentPartner['national_id_card_back_image']); ?>" alt="NID Back" class="img-fluid border rounded" style="max-height: 200px; width: 100%; object-fit: contain;">
                                        </a>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <div class="text-end mt-4">
                        <button type="submit" class="google-btn google-btn-primary">
                            Save
                        </button>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Password Change Section -->
        <div class="google-card" id="password-change">
            <div class="card-header">
                <h5>Password</h5>
            </div>
            <div class="card-body">
                <div class="password-section">
                    <form id="password-change-form">
                        <div class="google-form-group">
                            <label>Current password</label>
                            <div class="input-group-google">
                                <input type="password" class="form-control" id="currentPassword" name="currentPassword" required>
                                <button class="btn" type="button" onclick="togglePassword('currentPassword')">
                                    <i class="fas fa-eye" id="currentPasswordIcon"></i>
                                </button>
                            </div>
                            <div class="" id="currentPasswordError" style="color: #d93025; font-size: 13px; margin-top: 4px; display: none;"></div>
                        </div>
                        <div class="google-form-group">
                            <label>New password</label>
                            <div class="input-group-google">
                                <input type="password" class="form-control" id="newPassword" name="newPassword" required minlength="8">
                                <button class="btn" type="button" onclick="togglePassword('newPassword')">
                                    <i class="fas fa-eye" id="newPasswordIcon"></i>
                                </button>
                            </div>
                            <div class="help-text">
                                Use at least 8 characters
                            </div>
                            <div class="" id="newPasswordError" style="color: #d93025; font-size: 13px; margin-top: 4px; display: none;"></div>
                        </div>
                        
                        <div class="google-form-group">
                            <label>Confirm password</label>
                            <div class="input-group-google">
                                <input type="password" class="form-control" id="confirmPassword" name="confirmPassword" required minlength="8">
                                <button class="btn" type="button" onclick="togglePassword('confirmPassword')">
                                    <i class="fas fa-eye" id="confirmPasswordIcon"></i>
                                </button>
                            </div>
                            <div class="" id="confirmPasswordError" style="color: #d93025; font-size: 13px; margin-top: 4px; display: none;"></div>
                        </div>
                        
                        <!-- Password Strength Indicator -->
                        <div class="password-strength" id="passwordStrength" style="display: none;">
                            <div class="strength-bar">
                                <div class="strength-fill" id="strengthBar"></div>
                            </div>
                            <div style="font-size: 12px; color: #5f6368;" id="strengthText">Password strength</div>
                        </div>

                    </form>
                    <div class="mt-4">
                        <button type="button" class="google-btn google-btn-primary" id="changePasswordBtn">
                            Change password
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'layout/footer.php'; ?>
<script src="js/profile.js"></script>