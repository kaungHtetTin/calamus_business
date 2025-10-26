<?php
$pageTitle = 'Forgot Password';
ob_start();
?>

<!-- Forgot Password Section -->
<div class="content-section">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card">
                <div class="card-header text-center">
                    <h4><i class="fas fa-key me-2"></i>Forgot Password</h4>
                    <p class="text-muted mb-0">Enter your email address to receive a password reset link</p>
                </div>
                <div class="card-body">
                    <form id="forgotPasswordForm">
                        <div class="mb-3">
                            <label for="email" class="form-label">Email Address</label>
                            <input type="email" class="form-control" id="email" name="email" required 
                                   placeholder="Enter your registered email address">
                            <div class="invalid-feedback" id="emailError"></div>
                        </div>
                        
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary" id="submitBtn">
                                <i class="fas fa-paper-plane me-2"></i>Send Reset Link
                            </button>
                        </div>
                    </form>
                    
                    <div class="text-center mt-3">
                        <p class="text-muted">
                            Remember your password? 
                            <a href="partner_login.php" class="text-decoration-none">Back to Login</a>
                        </p>
                    </div>
                </div>
            </div>
            
            <!-- Success Message -->
            <div class="card mt-3" id="successMessage" style="display: none;">
                <div class="card-body text-center">
                    <div class="text-success mb-3">
                        <i class="fas fa-check-circle fa-3x"></i>
                    </div>
                    <h5 class="text-success">Reset Link Sent!</h5>
                    <p class="text-muted">
                        We've sent a password reset link to your email address. 
                        Please check your inbox and follow the instructions.
                    </p>
                    <p class="text-muted small">
                        <strong>Note:</strong> The reset link will expire in 1 hour for security reasons.
                    </p>
                    <a href="partner_login.php" class="btn btn-outline-primary">
                        <i class="fas fa-arrow-left me-2"></i>Back to Login
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('forgotPasswordForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const email = document.getElementById('email').value;
    const submitBtn = document.getElementById('submitBtn');
    const originalText = submitBtn.innerHTML;
    
    // Show loading state
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Sending...';
    submitBtn.disabled = true;
    
    // Clear previous errors
    document.getElementById('email').classList.remove('is-invalid');
    document.getElementById('emailError').textContent = '';
    
    // Send request
    fetch('api/forgot_password.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            email: email
        })
    })
    .then(response => response.json())
    .then(data => {
        console.log(data);
        if (data.success) {
            // Show success message
            document.getElementById('forgotPasswordForm').style.display = 'none';
            document.getElementById('successMessage').style.display = 'block';
        } else {
            // Show error
            document.getElementById('email').classList.add('is-invalid');
            document.getElementById('emailError').textContent = data.message || 'An error occurred. Please try again.';
            
            // Reset button
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        document.getElementById('email').classList.add('is-invalid');
        document.getElementById('emailError').textContent = 'Network error. Please try again.';
        
        // Reset button
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    });
});
</script>

<?php
$content = ob_get_clean();
include 'layout/public_layout.php';
?>
