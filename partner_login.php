<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Partner Login - Language Learning Platform</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="icon" href="logo.png" type="image/x-icon">
    <style>
        body {
            background: linear-gradient(135deg, #4a5568 0%, #718096 100%);
            min-height: 100vh;
        }
        .login-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
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
    </style>
</head>
<body>
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
                                
                                <!-- <div class="text-center">
                                    <small class="text-muted">
                                        Don't have an account? 
                                        <a href="partner_register.php" class="text-primary">Register here</a>
                                    </small>
                                </div> -->
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/partner_login.js"></script>
</body>
</html>
