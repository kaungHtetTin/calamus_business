<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Partner Registration</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        .registration-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .registration-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            max-width: 800px;
            width: 100%;
        }
        .registration-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 2rem;
            text-align: center;
        }
        .registration-body {
            padding: 2rem;
        }
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 10px;
            padding: 12px 30px;
            font-weight: 600;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        .step-indicator {
            display: flex;
            justify-content: center;
            margin-bottom: 2rem;
        }
        .step {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #e9ecef;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 10px;
            font-weight: bold;
            color: #6c757d;
        }
        .step.active {
            background: #667eea;
            color: white;
        }
        .step.completed {
            background: #28a745;
            color: white;
        }
    </style>
</head>
<body>
    <div class="registration-container">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="registration-card">
                        <div class="registration-header">
                            <h2 class="mb-3">
                                <i class="fas fa-handshake me-2"></i>
                                Partner Registration
                            </h2>
                            <p class="mb-0">Join our affiliate program and start earning commissions</p>
                        </div>
                        
                        <div class="registration-body">
                            <!-- Step Indicator -->
                            <div class="step-indicator">
                                <div class="step active" id="step1">1</div>
                                <div class="step" id="step2">2</div>
                                <div class="step" id="step3">3</div>
                            </div>
                            
                            <!-- Registration Form -->
                            <form id="registrationForm">
                                <!-- Step 1: Basic Information -->
                                <div id="step1-content" class="step-content">
                                    <h4 class="mb-4">Basic Information</h4>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Company Name *</label>
                                                <input type="text" class="form-control" id="company_name" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Contact Name *</label>
                                                <input type="text" class="form-control" id="contact_name" required>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Email Address *</label>
                                                <input type="email" class="form-control" id="email" required>
                                                <div class="form-text" id="email-status"></div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Phone Number *</label>
                                                <input type="tel" class="form-control" id="phone" required>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Website</label>
                                                <input type="url" class="form-control" id="website" placeholder="https://example.com">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Code Prefix *</label>
                                                <input type="text" class="form-control" id="code_prefix" maxlength="4" required>
                                                <div class="form-text" id="prefix-status"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Company Description</label>
                                        <textarea class="form-control" id="description" rows="3" placeholder="Tell us about your business..."></textarea>
                                    </div>
                                </div>
                                
                                <!-- Step 2: Commission & Payment -->
                                <div id="step2-content" class="step-content" style="display: none;">
                                    <h4 class="mb-4">Commission & Payment Settings</h4>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Commission Rate (%)</label>
                                                <input type="number" class="form-control" id="commission_rate" min="0" max="50" step="0.1" value="10">
                                                <div class="form-text">Default: 10%</div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Payment Method</label>
                                                <select class="form-control" id="payment_method">
                                                    <option value="bank_transfer">Bank Transfer</option>
                                                    <option value="paypal">PayPal</option>
                                                    <option value="stripe">Stripe</option>
                                                    <option value="check">Check</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Payment Details</label>
                                        <textarea class="form-control" id="payment_details" rows="3" placeholder="Bank account details, PayPal email, etc."></textarea>
                                    </div>
                                </div>
                                
                                <!-- Step 3: Password -->
                                <div id="step3-content" class="step-content" style="display: none;">
                                    <h4 class="mb-4">Create Password</h4>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Password *</label>
                                                <input type="password" class="form-control" id="password" required minlength="8">
                                                <div class="form-text">Minimum 8 characters</div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Confirm Password *</label>
                                                <input type="password" class="form-control" id="confirm_password" required>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="alert alert-info">
                                        <i class="fas fa-info-circle me-2"></i>
                                        <strong>Terms & Conditions:</strong> By registering, you agree to our partner terms and conditions. Your account will be reviewed and activated within 24-48 hours.
                                    </div>
                                </div>
                                
                                <!-- Navigation Buttons -->
                                <div class="d-flex justify-content-between mt-4">
                                    <button type="button" class="btn btn-outline-secondary" id="prevBtn" style="display: none;">
                                        <i class="fas fa-arrow-left me-2"></i>Previous
                                    </button>
                                    <button type="button" class="btn btn-primary" id="nextBtn">
                                        Next<i class="fas fa-arrow-right ms-2"></i>
                                    </button>
                                    <button type="submit" class="btn btn-success" id="submitBtn" style="display: none;">
                                        <i class="fas fa-check me-2"></i>Register
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let currentStep = 1;
        const totalSteps = 3;

        // Step navigation
        document.getElementById('nextBtn').addEventListener('click', function() {
            if (validateCurrentStep()) {
                if (currentStep < totalSteps) {
                    currentStep++;
                    updateStepDisplay();
                }
            }
        });

        document.getElementById('prevBtn').addEventListener('click', function() {
            if (currentStep > 1) {
                currentStep--;
                updateStepDisplay();
            }
        });

        // Update step display
        function updateStepDisplay() {
            // Hide all step contents
            for (let i = 1; i <= totalSteps; i++) {
                document.getElementById(`step${i}-content`).style.display = 'none';
            }
            
            // Show current step content
            document.getElementById(`step${currentStep}-content`).style.display = 'block';
            
            // Update step indicators
            for (let i = 1; i <= totalSteps; i++) {
                const step = document.getElementById(`step${i}`);
                step.classList.remove('active', 'completed');
                
                if (i < currentStep) {
                    step.classList.add('completed');
                } else if (i === currentStep) {
                    step.classList.add('active');
                }
            }
            
            // Update navigation buttons
            document.getElementById('prevBtn').style.display = currentStep > 1 ? 'block' : 'none';
            document.getElementById('nextBtn').style.display = currentStep < totalSteps ? 'block' : 'none';
            document.getElementById('submitBtn').style.display = currentStep === totalSteps ? 'block' : 'none';
        }

        // Validate current step
        function validateCurrentStep() {
            const stepContent = document.getElementById(`step${currentStep}-content`);
            const requiredFields = stepContent.querySelectorAll('[required]');
            
            for (let field of requiredFields) {
                if (!field.value.trim()) {
                    field.focus();
                    showAlert(`${field.previousElementSibling.textContent} is required`, 'danger');
                    return false;
                }
            }
            
            // Additional validations
            if (currentStep === 1) {
                const email = document.getElementById('email').value;
                const codePrefix = document.getElementById('code_prefix').value;
                
                if (!isValidEmail(email)) {
                    showAlert('Please enter a valid email address', 'danger');
                    return false;
                }
                
                if (codePrefix.length < 2) {
                    showAlert('Code prefix must be at least 2 characters', 'danger');
                    return false;
                }
            }
            
            if (currentStep === 3) {
                const password = document.getElementById('password').value;
                const confirmPassword = document.getElementById('confirm_password').value;
                
                if (password !== confirmPassword) {
                    showAlert('Passwords do not match', 'danger');
                    return false;
                }
            }
            
            return true;
        }

        // Email availability check
        document.getElementById('email').addEventListener('blur', function() {
            const email = this.value;
            if (email && isValidEmail(email)) {
                checkEmailAvailability(email);
            }
        });

        // Code prefix availability check
        document.getElementById('code_prefix').addEventListener('blur', function() {
            const prefix = this.value.toUpperCase();
            if (prefix.length >= 2) {
                checkCodePrefixAvailability(prefix);
            }
        });

        // Check email availability
        function checkEmailAvailability(email) {
            fetch('api/register_partner.php?endpoint=check_email', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ email: email })
            })
            .then(response => response.json())
            .then(data => {
                const statusDiv = document.getElementById('email-status');
                if (data.success) {
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

        // Check code prefix availability
        function checkCodePrefixAvailability(prefix) {
            fetch('api/register_partner.php?endpoint=check_code_prefix', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ code_prefix: prefix })
            })
            .then(response => response.json())
            .then(data => {
                const statusDiv = document.getElementById('prefix-status');
                if (data.success) {
                    if (data.available) {
                        statusDiv.innerHTML = '<i class="fas fa-check text-success"></i> Prefix is available';
                        statusDiv.className = 'form-text text-success';
                    } else {
                        statusDiv.innerHTML = '<i class="fas fa-times text-danger"></i> Prefix already taken';
                        statusDiv.className = 'form-text text-danger';
                    }
                }
            })
            .catch(error => {
                console.error('Error checking prefix:', error);
            });
        }

        // Form submission
        document.getElementById('registrationForm').addEventListener('submit', function(e) {
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
                code_prefix: document.getElementById('code_prefix').value.toUpperCase(),
                commission_rate: document.getElementById('commission_rate').value,
                payment_method: document.getElementById('payment_method').value,
                payment_details: document.getElementById('payment_details').value,
                password: document.getElementById('password').value
            };
            
            // Submit registration
            fetch('api/register_partner.php?endpoint=register', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(formData)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showAlert('Registration successful! Please check your email for verification instructions.', 'success');
                    document.getElementById('registrationForm').reset();
                    currentStep = 1;
                    updateStepDisplay();
                } else {
                    showAlert('Registration failed: ' + data.message, 'danger');
                }
            })
            .catch(error => {
                console.error('Registration error:', error);
                showAlert('Registration failed. Please try again.', 'danger');
            });
        });

        // Utility functions
        function isValidEmail(email) {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return emailRegex.test(email);
        }

        function showAlert(message, type) {
            const alertDiv = document.createElement('div');
            alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
            alertDiv.innerHTML = `
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            
            // Remove existing alerts
            document.querySelectorAll('.alert').forEach(alert => alert.remove());
            
            // Add new alert
            document.querySelector('.registration-body').insertBefore(alertDiv, document.querySelector('.registration-body').firstChild);
        }

        // Auto-format code prefix
        document.getElementById('code_prefix').addEventListener('input', function(e) {
            let value = e.target.value.toUpperCase();
            // Remove any characters that aren't letters or numbers
            value = value.replace(/[^A-Z0-9]/g, '');
            e.target.value = value;
        });
    </script>
</body>
</html>