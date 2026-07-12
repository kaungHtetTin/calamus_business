<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Partner Login - Language Learning Platform</title>
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
        
        .login-container {
            min-height: calc(100vh - 80px);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 3rem 1rem;
        }
        .login-card {
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.06);
            border: 1px solid #e9ecef;
            overflow: hidden;
            max-width: 420px;
            width: 100%;
        }
        .login-header {
            background: #ffffff;
            color: #1f7a45;
            padding: 1.5rem 1.5rem 1rem 1.5rem;
            text-align: center;
            border-bottom: 1px solid #eef2f6;
        }
        .login-body {
            padding: 1.5rem;
        }
        .form-control {
            border-radius: 10px;
            border: 1px solid #e2e8f0;
            padding: 12px 14px;
            transition: all 0.2s ease;
        }
        .form-control:focus {
            border-color: #3a9958;
            box-shadow: 0 0 0 3px rgba(113, 128, 150, 0.15);
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
        .alert {
            border-radius: 10px;
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
                        <a class="nav-link active" href="partner_login.php" aria-label="Login">
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

    <main class="login-container">
        <div class="container">
            <div class="auth-shell auth-shell-login">
                <aside class="auth-showcase">
                    <span class="auth-kicker">CALAMUS PARTNER PROGRAM</span>
                    <h1>Grow your income while helping students learn.</h1>
                    <p>Access your partner workspace, monitor commissions, and manage every referral from one place.</p>
                    <div class="auth-benefit-list">
                        <div><i class="fas fa-chart-line"></i><span><strong>Clear earnings</strong>Track approved and pending commissions.</span></div>
                        <div><i class="fas fa-ticket"></i><span><strong>Your unique code</strong>Give students a discount with every referral.</span></div>
                        <div><i class="fas fa-shield-halved"></i><span><strong>Secure workspace</strong>Your partner details stay protected.</span></div>
                    </div>
                </aside>

                <section class="auth-form-panel">
                    <div class="login-card">
                        <div class="login-header">
                            <span class="auth-mobile-kicker">PARTNER ACCESS</span>
                            <h3 class="mb-3">Welcome back</h3>
                            <p class="mb-0">Sign in to continue to your partner dashboard.</p>
                        </div>

                        <div class="login-body">
                            <div id="alertContainer"></div>

                            <form id="loginForm">
                                <div class="mb-3">
                                    <label class="form-label" for="email">Email Address</label>
                                    <div class="auth-input">
                                        <i class="fas fa-envelope" aria-hidden="true"></i>
                                        <input type="email" class="form-control" id="email" autocomplete="email" required>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label" for="password">Password</label>
                                    <div class="auth-input">
                                        <i class="fas fa-lock" aria-hidden="true"></i>
                                        <input type="password" class="form-control" id="password" autocomplete="current-password" required>
                                        <button type="button" class="password-toggle" data-password-toggle="password" aria-label="Show password">
                                            <i class="fas fa-eye" aria-hidden="true"></i>
                                        </button>
                                    </div>
                                </div>

                                <div class="auth-form-options">
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" id="remember">
                                        <label class="form-check-label" for="remember">Remember me</label>
                                    </div>
                                    <a href="forgot_password.php">Forgot password?</a>
                                </div>

                                <button type="submit" class="btn btn-primary w-100 mb-3">
                                    <i class="fas fa-arrow-right-to-bracket me-2"></i><span>Sign In</span>
                                </button>

                                <div class="auth-switch">
                                    New to the partner program?
                                    <a href="partner_register.php">Create an account</a>
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
    <script src="js/partner_login.js"></script>
</body>
</html>
