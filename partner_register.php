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
    <script src="js/partner_register.js"></script>
</body>
</html>