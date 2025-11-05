<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Partner Registration</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="icon" href="logo.png" type="image/x-icon">
    <link rel="stylesheet" href="css/app.css">
    <style>
        body {
            background: linear-gradient(135deg, #4a5568 0%, #718096 100%);
            min-height: 100vh;
        }
        
        /* Navbar Styles */
        .welcome-navbar {
            background: linear-gradient(135deg, #4a5568 0%, #718096 100%);
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
            color: #4a5568;
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
            padding: 2rem 0;
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
            background: linear-gradient(135deg, #4a5568 0%, #718096 100%);
            color: white;
            padding: 2rem;
            text-align: center;
        }
        .registration-body {
            padding: 2rem;
        }
        .btn-primary {
            background: linear-gradient(135deg, #4a5568 0%, #718096 100%);
            border: none;
            border-radius: 10px;
            padding: 12px 30px;
            font-weight: 600;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(74, 85, 104, 0.4);
        }
        .form-control:focus {
            border-color: #4a5568;
            box-shadow: 0 0 0 0.2rem rgba(74, 85, 104, 0.25);
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
            background: #4a5568;
            color: white;
        }
        .step.completed {
            background: #38a169;
            color: white;
        }
        
        /* Footer */
        .welcome-footer {
            background: linear-gradient(135deg, #4a5568 0%, #718096 100%);
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
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg welcome-navbar">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <img src="logo.png" alt="Calamus" width="30" height="30" class="me-2">
                Calamus Education
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="partner_login.php">Login</a>
                    </li>
                    <li class="nav-item">
                        <a class="btn btn-outline-light" href="partner_register.php">Register</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

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
                                        <div class="col-md-12">
                                            <div class="mb-3">
                                                <label class="form-label">Social Media Link</label>
                                                <input type="url" class="form-control" id="website" name="website" placeholder="facebook, tiktok, instagram, etc.">
                                            </div>
                                        </div>
                                    </div>
                                   
                                    <div class="mb-3">
                                        <label class="form-label">Company (or) Personal Description</label>
                                        <textarea class="form-control" id="description" rows="3" placeholder="Tell us about your business..."></textarea>
                                    </div>
                                </div>
                                
                                <!-- Step 2: Password -->
                                <div id="step2-content" class="step-content" style="">
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

    <?php include 'layout/welcome_footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/partner_register.js"></script>
</body>
</html>