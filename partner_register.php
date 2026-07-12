<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Partner Registration</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="icon" href="assets/favicon.png" type="image/png">
    <link rel="stylesheet" href="css/theme.css">
    <link rel="stylesheet" href="css/app.css">
    <style>
        body {
            background: #f5f7fb;
            min-height: 100vh;
        }
        
        /* Navbar Styles */
        .welcome-navbar {
            background: linear-gradient(135deg, #1f7a45 0%, #3a9958 100%);
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            padding: 0.25rem 0;
            min-height: auto;
        }
        
        .welcome-navbar .navbar-brand {
            font-size: 1.25rem;
            font-weight: 600;
            color: white !important;
            padding: 0.25rem 0;
        }
        
        .welcome-navbar .nav-link {
            color: rgba(255, 255, 255, 0.9) !important;
            font-weight: 500;
            padding: 0.375rem 0.75rem;
            margin: 0 0.125rem;
            border-radius: 6px;
            transition: all 0.3s ease;
        }
        
        .welcome-navbar .nav-link:hover {
            background-color: rgba(255, 255, 255, 0.1);
            color: white !important;
        }
        
        .welcome-navbar .btn-outline-light {
            border-color: rgba(255, 255, 255, 0.5);
            color: white;
        }
        
        .welcome-navbar .btn-outline-light:hover {
            background-color: white;
            color: #1f7a45;
        }
        
        .welcome-navbar .navbar-toggler {
            border-color: rgba(255, 255, 255, 0.5);
        }
        
        .welcome-navbar .navbar-toggler-icon {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='rgba%28255, 255, 255, 1%29' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
        }
        
        .registration-container {
            min-height: calc(100vh - 80px);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 3rem 1rem;
        }
        .registration-card {
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.06);
            border: 1px solid #e9ecef;
            overflow: hidden;
            max-width: 860px;
            width: 100%;
        }
        .registration-header {
            background: #ffffff;
            color: #1f7a45;
            padding: 1.5rem 1.5rem 1rem 1.5rem;
            text-align: center;
            border-bottom: 1px solid #eef2f6;
        }
        .registration-body {
            padding: 1.5rem;
        }
        .btn-primary {
            background: #1f7a45;
            border: none;
            border-radius: 10px;
            padding: 12px 30px;
            font-weight: 600;
        }
        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(74, 85, 104, 0.20);
        }
        .form-control:focus {
            border-color: #3a9958;
            box-shadow: 0 0 0 3px rgba(113, 128, 150, 0.15);
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
            background: #eef2f6;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 10px;
            font-weight: bold;
            color: #6c757d;
        }
        .step.active {
            background: #1f7a45;
            color: #ffffff;
        }
        .step.completed {
            background: #38a169;
            color: #ffffff;
        }
        
        /* Footer */
        .welcome-footer {
            background: linear-gradient(135deg, #1f7a45 0%, #3a9958 100%);
            color: white;
            padding: 3rem 0 1.5rem 0;
            margin-top: 2rem;
        }
        
        .welcome-footer h5 {
            color: white;
            font-weight: 600;
            margin-bottom: 1rem;
        }
        
        .welcome-footer a {
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            transition: color 0.3s ease;
        }
        
        .welcome-footer a:hover {
            color: white;
        }
        
        .welcome-footer ul {
            list-style: none;
            padding: 0;
        }
        
        .welcome-footer ul li {
            margin-bottom: 0.5rem;
        }
    </style>
    <link rel="stylesheet" href="css/welcome.css?v=12">
</head>
<body class="welcome-page auth-page">
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg welcome-navbar">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <img src="assets/app_logo.png" alt="Calamus" width="30" height="30" class="me-2">
                Calamus Education
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php" aria-label="Home">
                            <i class="fas fa-house mobile-nav-icon" aria-hidden="true"></i>
                            <span class="nav-label">Home</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="partner_login.php" aria-label="Login">
                            <i class="fas fa-arrow-right-to-bracket mobile-nav-icon" aria-hidden="true"></i>
                            <span class="nav-label">Login</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="btn btn-outline-light" href="partner_register.php" aria-label="Register">
                            <i class="fas fa-user-plus mobile-nav-icon" aria-hidden="true"></i>
                            <span class="nav-label">Register</span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <main class="registration-container">
        <div class="container">
            <div class="auth-shell auth-shell-register">
                <aside class="auth-showcase">
                    <span class="auth-kicker">BECOME A PARTNER</span>
                    <h1>Turn your audience into a growing business.</h1>
                    <p>Join the Calamus partner network and earn commission by connecting students with quality language courses.</p>
                    <div class="auth-benefit-list">
                        <div><i class="fas fa-percent"></i><span><strong>Earn commission</strong>Receive commission from confirmed referrals.</span></div>
                        <div><i class="fas fa-tags"></i><span><strong>Student benefits</strong>Your code gives referred students a discount.</span></div>
                        <div><i class="fas fa-gauge-high"></i><span><strong>Simple dashboard</strong>Track performance and payouts in one place.</span></div>
                    </div>
                </aside>
                <section class="auth-form-panel">
                    <div class="registration-card">
                        <div class="registration-header">
                            <span class="auth-mobile-kicker">PARTNER APPLICATION</span>
                            <h2 class="mb-3">
                                Create your account
                            </h2>
                            <p class="mb-0">Complete two quick steps to join the partner program.</p>
                        </div>
                        
                        <div class="registration-body">
                            <!-- Step Indicator -->
                            <div class="step-indicator">
                                <div class="step-item">
                                    <div class="step active" id="step1">1</div>
                                    <span>Partner details</span>
                                </div>
                                <div class="step-item">
                                    <div class="step" id="step2">2</div>
                                    <span>Security</span>
                                </div>
                            </div>
                            
                            <!-- Registration Form -->
                            <form id="registrationForm" novalidate>
                                <!-- Step 1: Basic Information -->
                                <div id="step1-content" class="step-content">
                                    <h4 class="mb-4">Basic Information</h4>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Company Name *</label>
                                                <input type="text" class="form-control" id="company_name" required placeholder="Company Name, Personal Name, Channel Name, etc.">
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
                                        <div class="col-md-12">
                                            <div class="mb-3">
                                                <label class="form-label">Social Media Link</label>
                                                <input type="url" class="form-control" id="website" name="website" placeholder="facebook, tiktok, instagram, etc.">
                                            </div>
                                        </div>
                                    </div>
                                   
                                    <div class="mb-3">
                                        <label class="form-label">How will you promote or sell our products?</label>
                                        <textarea class="form-control" id="description" rows="3" placeholder="Tell us about your business. Express your channel, page links. Tell us how will you  
                                        promote or sell our products?"></textarea>
                                    </div>
                                </div>
                                
                                <!-- Step 2: Password -->
                                <div id="step2-content" class="step-content" style="">
                                    <h4 class="mb-4">Create Password</h4>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label" for="password">Password *</label>
                                                <div class="auth-input">
                                                    <i class="fas fa-lock" aria-hidden="true"></i>
                                                    <input type="password" class="form-control" id="password" autocomplete="new-password" required minlength="8">
                                                    <button type="button" class="password-toggle" data-password-toggle="password" aria-label="Show password">
                                                        <i class="fas fa-eye" aria-hidden="true"></i>
                                                    </button>
                                                </div>
                                                <div class="password-requirement" data-password-requirement>
                                                    <i class="fas fa-circle-check"></i> At least 8 characters
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label" for="confirm_password">Confirm Password *</label>
                                                <div class="auth-input">
                                                    <i class="fas fa-lock" aria-hidden="true"></i>
                                                    <input type="password" class="form-control" id="confirm_password" autocomplete="new-password" required>
                                                    <button type="button" class="password-toggle" data-password-toggle="confirm_password" aria-label="Show password">
                                                        <i class="fas fa-eye" aria-hidden="true"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="alert alert-info">
                                        <i class="fas fa-info-circle me-2"></i>
                                        <strong>Terms & Conditions:</strong> By registering, you agree to our partner terms and conditions.
                                        <a href="index.php#terms-conditions-section" class="text-primary">Terms and Conditions</a>
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
                </section>
            </div>
        </div>
    </main>

    <?php include 'layout/welcome_footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/auth-ui.js"></script>
    <script src="js/partner_register.js"></script>
</body>
</html>