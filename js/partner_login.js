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

// Show verification alert for unverified accounts
function showVerificationAlert(message, verificationCode) {
    const alertContainer = document.getElementById('alertContainer');
    if (alertContainer) {
        const verificationLink = `verify_email.php?code=${verificationCode}`;
        alertContainer.innerHTML = `
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <h6 class="alert-heading"><i class="fas fa-envelope me-2"></i>${message}</h6>
                <p class="mb-2">A verification email has been sent to your email address. Please check your inbox.</p>
                <p class="mb-2">If you haven't received the email, click below to verify:</p>
                <a href="${verificationLink}" class="btn btn-sm btn-warning mb-2">
                    <i class="fas fa-envelope me-2"></i>Verify Email
                </a>
                <p class="mb-0 small">After verifying, you can login to your account.</p>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;
    }
}

// Initialize login page
document.addEventListener('DOMContentLoaded', function() {
    // Setup login form
    setupLoginForm();
    
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
        
        fetch('api/login.php?endpoint=login', {
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
            } else if (data.needs_verification) {
                // Show special message for unverified accounts
                showVerificationAlert(data.message, data.verification_code);
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
