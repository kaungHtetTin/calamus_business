/**
 * Partner Registration JavaScript
 * 
 * This file contains all JavaScript functionality for partner registration.
 * It handles multi-step form navigation, validation, and API interactions.
 */

let currentStep = 1;
const totalSteps = 2;

// Initialize registration page
document.addEventListener('DOMContentLoaded', function() {
    setupStepNavigation();
    setupFormValidation();
    setupAvailabilityChecks();
    setupFormSubmission();
    updateStepDisplay();
});

// Setup step navigation
function setupStepNavigation() {
    const nextBtn = document.getElementById('nextBtn');
    const prevBtn = document.getElementById('prevBtn');
    
    if (nextBtn) {
        nextBtn.addEventListener('click', function() {
            if (validateCurrentStep()) {
                if (currentStep < totalSteps) {
                    currentStep++;
                    updateStepDisplay();
                }
            }
        });
    }
    
    if (prevBtn) {
        prevBtn.addEventListener('click', function() {
            if (currentStep > 1) {
                currentStep--;
                updateStepDisplay();
            }
        });
    }
}

// Update step display
function updateStepDisplay() {
    // Hide all step contents
    for (let i = 1; i <= totalSteps; i++) {
        const stepContent = document.getElementById(`step${i}-content`);
        if (stepContent) {
            stepContent.style.display = 'none';
        }
    }
    
    // Show current step content
    const currentStepContent = document.getElementById(`step${currentStep}-content`);
    if (currentStepContent) {
        currentStepContent.style.display = 'block';
    }
    
    // Update step indicators
    for (let i = 1; i <= totalSteps; i++) {
        const step = document.getElementById(`step${i}`);
        if (step) {
            step.classList.remove('active', 'completed');
            
            if (i < currentStep) {
                step.classList.add('completed');
            } else if (i === currentStep) {
                step.classList.add('active');
            }
        }
    }
    
    // Update navigation buttons
    const prevBtn = document.getElementById('prevBtn');
    const nextBtn = document.getElementById('nextBtn');
    const submitBtn = document.getElementById('submitBtn');
    
    if (prevBtn) {
        prevBtn.style.display = currentStep > 1 ? 'block' : 'none';
    }
    if (nextBtn) {
        nextBtn.style.display = currentStep < totalSteps ? 'block' : 'none';
    }
    if (submitBtn) {
        submitBtn.style.display = currentStep === totalSteps ? 'block' : 'none';
    }
}

// Validate current step
function validateCurrentStep() {
    const stepContent = document.getElementById(`step${currentStep}-content`);
    if (!stepContent) return false;
    
    const requiredFields = stepContent.querySelectorAll('[required]');
    
    for (let field of requiredFields) {
        if (!field.value.trim()) {
            field.focus();
            const label = field.previousElementSibling;
            const fieldName = label ? label.textContent : field.name || 'Field';
            showAlert(`${fieldName} is required`, 'danger');
            return false;
        }
    }
    
    // Additional validations
    if (currentStep === 1) {
        const email = document.getElementById('email').value;
        
        if (!isValidEmail(email)) {
            showAlert('Please enter a valid email address', 'danger');
            return false;
        }
    }
    
    if (currentStep === 2) {
        const password = document.getElementById('password').value;
        const confirmPassword = document.getElementById('confirm_password').value;
        
        if (password !== confirmPassword) {
            showAlert('Passwords do not match', 'danger');
            return false;
        }
        
        if (password.length < 8) {
            showAlert('Password must be at least 8 characters long', 'danger');
            return false;
        }
    }
    
    return true;
}

// Setup form validation
function setupFormValidation() {
    // Email validation
    const emailField = document.getElementById('email');
    if (emailField) {
        emailField.addEventListener('blur', function() {
            const email = this.value;
            if (email && !isValidEmail(email)) {
                showAlert('Please enter a valid email address', 'danger');
            }
        });
    }
}

// Setup availability checks
function setupAvailabilityChecks() {
    // Email availability check
    const emailField = document.getElementById('email');
    if (emailField) {
        emailField.addEventListener('blur', function() {
            const email = this.value;
            if (email && isValidEmail(email)) {
                checkEmailAvailability(email);
            }
        });
    }
}

// Check email availability
function checkEmailAvailability(email) {
    fetch('api/register.php?endpoint=check_email', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ email: email })
    })
    .then(response => response.json())
    .then(data => {
        const statusDiv = document.getElementById('email-status');
        if (statusDiv && data.success) {
            if (data.available) {
                statusDiv.innerHTML = '<i class="fas fa-check text-success"></i> Email is available';
                statusDiv.className = 'form-text text-success';
            } else {
                statusDiv.innerHTML = '<i class="fas fa-times text-danger"></i> Email already registered';
                statusDiv.className = 'form-text text-danger';
            }
        }
    })
    .catch(error => {
        console.error('Error checking email:', error);
    });
}

// Setup form submission
function setupFormSubmission() {
    const registrationForm = document.getElementById('registrationForm');
    if (!registrationForm) return;
    
    registrationForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        if (!validateCurrentStep()) {
            return;
        }
        
        const formData = {
            company_name: document.getElementById('company_name').value,
            contact_name: document.getElementById('contact_name').value,
            email: document.getElementById('email').value,
            phone: document.getElementById('phone').value,
            website: document.getElementById('website').value,
            description: document.getElementById('description').value,
            commission_rate: document.getElementById('commission_rate').value,
            password: document.getElementById('password').value
        };
        
        // Show loading state
        const submitBtn = document.getElementById('submitBtn');
        const originalText = submitBtn.textContent;
        submitBtn.textContent = 'Registering...';
        submitBtn.disabled = true;
        
        // Submit registration
        fetch('api/register.php?endpoint=register', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(formData)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Display success message with private code
                const privateCode = data.private_code;
                showAlert(`Registration successful! Your private code is: <strong>${privateCode}</strong><br>Please save this code and check your email for verification instructions.`, 'success');
                
                // Reset form
                registrationForm.reset();
                currentStep = 1;
                updateStepDisplay();
            } else {
                showAlert('Registration failed: ' + data.message, 'danger');
            }
        })
        .catch(error => {
            console.error('Registration error:', error);
            showAlert('Registration failed. Please try again.', 'danger');
        })
        .finally(() => {
            // Reset button state
            submitBtn.textContent = originalText;
            submitBtn.disabled = false;
        });
    });
}

// Utility functions
function isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

function showAlert(message, type) {
    // Remove existing alerts
    document.querySelectorAll('.alert').forEach(alert => alert.remove());
    
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
    alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    // Add new alert
    const registrationBody = document.querySelector('.registration-body');
    if (registrationBody) {
        registrationBody.insertBefore(alertDiv, registrationBody.firstChild);
    }
}

// Password strength indicator
function checkPasswordStrength(password) {
    let strength = 0;
    if (password.length >= 8) strength++;
    if (/[a-z]/.test(password)) strength++;
    if (/[A-Z]/.test(password)) strength++;
    if (/[0-9]/.test(password)) strength++;
    if (/[^A-Za-z0-9]/.test(password)) strength++;
    
    return strength;
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
