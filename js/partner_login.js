/**
 * Partner Login JavaScript
 * 
 * This file contains all JavaScript functionality for partner login.
 * It handles login form submission, session validation, and password reset.
 */

// Show alert function
function showAlert(message, type = 'danger') {
    const alertContainer = document.getElementById('alertContainer');
    if (alertContainer) {
        alertContainer.innerHTML = `
            <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;
    }
}

// Show forgot password modal
function showForgotPassword() {
    const modal = new bootstrap.Modal(document.getElementById('forgotPasswordModal'));
    modal.show();
}

// Initialize login page
document.addEventListener('DOMContentLoaded', function() {
    // Setup login form
    setupLoginForm();
    
    // Setup forgot password form
    setupForgotPasswordForm();
    
    // Check if user is already logged in
    checkExistingSession();
});

// Setup login form
function setupLoginForm() {
    const loginForm = document.getElementById('loginForm');
    if (!loginForm) return;
    
    loginForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const email = document.getElementById('email').value;
        const password = document.getElementById('password').value;
        
        if (!email || !password) {
            showAlert('Please fill in all fields');
            return;
        }
        
        // Show loading state
        const submitBtn = loginForm.querySelector('button[type="submit"]');
        const originalText = submitBtn.textContent;
        submitBtn.textContent = 'Logging in...';
        submitBtn.disabled = true;
        
        fetch('api/login_partner.php?endpoint=login', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ email, password })
        })
        .then(response => response.json())
        .then(data => {
            console.log('Login response:', data);
            if (data.success) {
                // Store session token
                localStorage.setItem('partner_session_token', data.session_token);
                window.location.href = 'index.php';
            } else {
                showAlert(data.message);
            }
        })
        .catch(error => {
            console.error('Login error:', error);
            showAlert('Login failed. Please try again.');
        })
        .finally(() => {
            // Reset button state
            submitBtn.textContent = originalText;
            submitBtn.disabled = false;
        });
    });
}

// Setup forgot password form
function setupForgotPasswordForm() {
    const forgotPasswordForm = document.getElementById('forgotPasswordForm');
    if (!forgotPasswordForm) return;
    
    forgotPasswordForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const email = document.getElementById('reset_email').value;
        
        if (!email) {
            showAlert('Please enter your email address');
            return;
        }
        
        // Show loading state
        const submitBtn = forgotPasswordForm.querySelector('button[type="submit"]');
        const originalText = submitBtn.textContent;
        submitBtn.textContent = 'Sending...';
        submitBtn.disabled = true;
        
        fetch('api/login_partner.php?endpoint=forgot_password', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ email })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAlert(data.message, 'success');
                bootstrap.Modal.getInstance(document.getElementById('forgotPasswordModal')).hide();
                forgotPasswordForm.reset();
            } else {
                showAlert(data.message);
            }
        })
        .catch(error => {
            console.error('Forgot password error:', error);
            showAlert('Failed to send reset email. Please try again.');
        })
        .finally(() => {
            // Reset button state
            submitBtn.textContent = originalText;
            submitBtn.disabled = false;
        });
    });
}

// Check if user is already logged in
function checkExistingSession() {
    const sessionToken = localStorage.getItem('partner_session_token');
    if (sessionToken) {
        // Validate session
        fetch('api/validate_session.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ session_token: sessionToken })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.href = 'index.php';
            } else {
                localStorage.removeItem('partner_session_token');
            }
        })
        .catch(error => {
            console.error('Session validation error:', error);
            localStorage.removeItem('partner_session_token');
        });
    }
}

// Form validation helpers
function validateEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

function validatePassword(password) {
    return password.length >= 8;
}

// Show/hide password toggle
function togglePasswordVisibility(inputId) {
    const input = document.getElementById(inputId);
    const toggleBtn = input.nextElementSibling;
    
    if (input.type === 'password') {
        input.type = 'text';
        toggleBtn.innerHTML = '<i class="fas fa-eye-slash"></i>';
    } else {
        input.type = 'password';
        toggleBtn.innerHTML = '<i class="fas fa-eye"></i>';
    }
}
