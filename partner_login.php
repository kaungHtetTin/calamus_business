<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Partner Login - Language Learning Platform</title>
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
        
        .login-container {
            min-height: calc(100vh - 80px);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 0;
        }
        .login-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            max-width: 400px;
            width: 100%;
        }
        .login-header {
            background: linear-gradient(135deg, #4a5568 0%, #718096 100%);
            color: white;
            padding: 2rem;
            text-align: center;
        }
        .login-body {
            padding: 2rem;
        }
        .form-control {
            border-radius: 10px;
            border: 2px solid #e9ecef;
            padding: 12px 15px;
            transition: all 0.3s;
        }
        .form-control:focus {
            border-color: #4a5568;
            box-shadow: 0 0 0 0.2rem rgba(74, 85, 104, 0.25);
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
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
        .alert {
            border-radius: 10px;
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
                        <a class="nav-link active" href="partner_login.php">Login</a>
                    </li>
                    <li class="nav-item">
                        <a class="btn btn-outline-light" href="partner_register.php">Register</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="login-container">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-4 col-lg-4">
                    <div class="login-card">
                        <div class="login-header">
                            <h3 class="mb-3">
                                <i class="fas fa-handshake me-2"></i>
                                Partner Login
                            </h3>
                            <p class="mb-0">Calamus Education Partner Login</p>
                        </div>
                        
                        <div class="login-body">
                            <div id="alertContainer"></div>
                            
                            <form id="loginForm">
                                <div class="mb-3">
                                    <label class="form-label">Email Address</label>
                                    <input type="email" class="form-control" id="email" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Password</label>
                                    <input type="password" class="form-control" id="password" required>
                                </div>
                                
                                <div class="mb-3 form-check">
                                    <input type="checkbox" class="form-check-input" id="remember">
                                    <label class="form-check-label" for="remember">
                                        Remember me
                                    </label>
                                </div>
                                
                                <button type="submit" class="btn btn-primary w-100 mb-3">
                                    <i class="fas fa-sign-in-alt me-2"></i>Sign In
                                </button>
                                
                                <div class="text-center mb-3">
                                    <a href="forgot_password.php" class="text-primary">
                                        <i class="fas fa-key me-1"></i>Forgot your password?
                                    </a>
                                </div>
                                
                                <div class="text-center">
                                    <small class="text-muted">
                                        Don't have an account? 
                                        <a href="partner_register.php" class="text-primary">Register here</a>
                                    </small>
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
    <script src="js/partner_login.js"></script>
</body>
</html>
