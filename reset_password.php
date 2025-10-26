<?php
$pageTitle = 'Reset Password';
ob_start();

// Get token from URL
$token = $_GET['token'] ?? '';
$isValidToken = false;
$tokenError = '';

if (empty($token)) {
    $tokenError = 'Invalid or missing reset token.';
} else {
    // Validate token (we'll implement this in PartnerAuth)
    require_once 'classes/autoload.php';
    $auth = new PartnerAuth();
    $tokenValidation = $auth->validatePasswordResetToken($token);
    
    if ($tokenValidation['success']) {
        $isValidToken = true;
    } else {
        $tokenError = $tokenValidation['message'];
    }
}
?>

<!-- Reset Password Section -->
<div class="content-section">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <?php if ($isValidToken): ?>
                <!-- Valid Token - Show Reset Form -->
                <div class="card">
                    <div class="card-header text-center">
                        <h4><i class="fas fa-lock me-2"></i>Reset Your Password</h4>
                        <p class="text-muted mb-0">Enter your new password below</p>
                    </div>
                    <div class="card-body">
                        <form id="resetPasswordForm">
                            <input type="hidden" id="token" name="token" value="<?php echo htmlspecialchars($token); ?>">
                            
                            <div class="mb-3">
                                <label for="newPassword" class="form-label">New Password</label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="newPassword" name="newPassword" required 
                                           placeholder="Enter your new password" minlength="8">
                                    <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('newPassword')">
                                        <i class="fas fa-eye" id="newPasswordIcon"></i>
                                    </button>
                                </div>
                                <div class="form-text">
                                    Password must be at least 8 characters long
                                </div>
                                <div class="invalid-feedback" id="newPasswordError"></div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="confirmPassword" class="form-label">Confirm New Password</label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="confirmPassword" name="confirmPassword" required 
                                           placeholder="Confirm your new password" minlength="8">
                                    <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('confirmPassword')">
                                        <i class="fas fa-eye" id="confirmPasswordIcon"></i>
                                    </button>
                                </div>
                                <div class="invalid-feedback" id="confirmPasswordError"></div>
                            </div>
                            
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary" id="submitBtn">
                                    <i class="fas fa-save me-2"></i>Reset Password
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                
                <!-- Success Message -->
                <div class="card mt-3" id="successMessage" style="display: none;">
                    <div class="card-body text-center">
                        <div class="text-success mb-3">
                            <i class="fas fa-check-circle fa-3x"></i>
                        </div>
                        <h5 class="text-success">Password Reset Successful!</h5>
                        <p class="text-muted">
                            Your password has been successfully reset. 
                            You can now log in with your new password.
                        </p>
                        <a href="partner_login.php" class="btn btn-primary">
                            <i class="fas fa-sign-in-alt me-2"></i>Go to Login
                        </a>
                    </div>
                </div>
                
            <?php else: ?>
                <!-- Invalid Token - Show Error -->
                <div class="card">
                    <div class="card-body text-center">
                        <div class="text-danger mb-3">
                            <i class="fas fa-exclamation-triangle fa-3x"></i>
                        </div>
                        <h5 class="text-danger">Invalid Reset Link</h5>
                        <p class="text-muted">
                            <?php echo htmlspecialchars($tokenError); ?>
                        </p>
                        <p class="text-muted small">
                            This could happen if:
                        </p>
                        <ul class="text-muted small text-start">
                            <li>The link has expired (valid for 1 hour)</li>
                            <li>The link has already been used</li>
                            <li>The link is invalid or corrupted</li>
                        </ul>
                        <div class="mt-3">
                            <a href="forgot_password.php" class="btn btn-primary me-2">
                                <i class="fas fa-redo me-2"></i>Request New Link
                            </a>
                            <a href="partner_login.php" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Back to Login
                            </a>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
function togglePassword(fieldId) {
    const field = document.getElementById(fieldId);
    const icon = document.getElementById(fieldId + 'Icon');
    
    if (field.type === 'password') {
        field.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        field.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}

<?php if ($isValidToken): ?>
document.getElementById('resetPasswordForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const newPassword = document.getElementById('newPassword').value;
    const confirmPassword = document.getElementById('confirmPassword').value;
    const token = document.getElementById('token').value;
    const submitBtn = document.getElementById('submitBtn');
    const originalText = submitBtn.innerHTML;
    
    // Clear previous errors
    document.getElementById('newPassword').classList.remove('is-invalid');
    document.getElementById('confirmPassword').classList.remove('is-invalid');
    document.getElementById('newPasswordError').textContent = '';
    document.getElementById('confirmPasswordError').textContent = '';
    
    // Validate passwords
    if (newPassword.length < 8) {
        document.getElementById('newPassword').classList.add('is-invalid');
        document.getElementById('newPasswordError').textContent = 'Password must be at least 8 characters long';
        return;
    }
    
    if (newPassword !== confirmPassword) {
        document.getElementById('confirmPassword').classList.add('is-invalid');
        document.getElementById('confirmPasswordError').textContent = 'Passwords do not match';
        return;
    }
    
    // Show loading state
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Resetting...';
    submitBtn.disabled = true;
    
    // Send request
    fetch('api/reset_password.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            token: token,
            newPassword: newPassword
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Show success message
            document.getElementById('resetPasswordForm').style.display = 'none';
            document.getElementById('successMessage').style.display = 'block';
        } else {
            // Show error
            if (data.field === 'newPassword') {
                document.getElementById('newPassword').classList.add('is-invalid');
                document.getElementById('newPasswordError').textContent = data.message;
            } else if (data.field === 'confirmPassword') {
                document.getElementById('confirmPassword').classList.add('is-invalid');
                document.getElementById('confirmPasswordError').textContent = data.message;
            } else {
                alert('Error: ' + (data.message || 'An error occurred. Please try again.'));
            }
            
            // Reset button
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Network error. Please try again.');
        
        // Reset button
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    });
});
<?php endif; ?>
</script>

<?php
$content = ob_get_clean();
include 'layout/public_layout.php';
?>
