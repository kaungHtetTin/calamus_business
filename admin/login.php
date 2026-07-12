<?php
/**
 * Admin Login Page
 */

// Check if already logged in
require_once '../classes/admin_auth.php';
$adminAuth = new AdminAuth();

if ($adminAuth->isLoggedIn()) {
    header('Location: index.php');
    exit();
}

$pageTitle = 'Admin Login';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?> - Calamus Education Partner Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../css/app.css">
    <link rel="icon" href="../assets/favicon.png" type="image/png">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
        }
        
        .login-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .login-card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            padding: 40px;
            width: 100%;
            max-width: 400px;
        }
        
        .login-header {
            text-align: center;
            margin-bottom: 32px;
        }
        
        .login-header h1 {
            font-size: 24px;
            font-weight: 400;
            color: #202124;
            margin-bottom: 8px;
        }
        
        .login-header p {
            color: #5f6368;
            font-size: 14px;
        }
        
        .form-label {
            color: #202124;
            font-size: 14px;
            font-weight: 500;
            margin-bottom: 8px;
        }
        
        .form-control {
            border: 1px solid #dadce0;
            border-radius: 4px;
            padding: 12px 14px;
            font-size: 16px;
            color: #202124;
            background-color: white;
            transition: border-color 0.2s, box-shadow 0.2s;
        }
        
        .form-control:focus {
            border-color: #1a73e8;
            box-shadow: 0 0 0 2px rgba(26, 115, 232, 0.1);
            outline: none;
        }
        
        .btn-primary {
            background: #202124;
            border: none;
            color: white;
            padding: 12px 24px;
            font-weight: 500;
            border-radius: 4px;
            width: 100%;
            transition: background 0.2s;
        }
        
        .btn-primary:hover {
            background: #3c4043;
        }
        
        .alert {
            border-radius: 4px;
            padding: 12px 16px;
            margin-bottom: 16px;
        }
        
        .back-link {
            text-align: center;
            margin-top: 24px;
        }
        
        .back-link a {
            color: #1a73e8;
            text-decoration: none;
            font-size: 14px;
        }
        
        .back-link a:hover {
            text-decoration: underline;
        }
    </style>
    <link rel="stylesheet" href="../css/welcome.css?v=11">
</head>
<body class="welcome-page auth-page admin-auth-page">
    <main class="login-container">
        <div class="container">
            <div class="auth-shell auth-shell-login">
                <aside class="auth-showcase">
                    <span class="auth-kicker">CALAMUS ADMINISTRATION</span>
                    <h1>Manage the partner program with confidence.</h1>
                    <p>Review partner activity, verify accounts, monitor earnings, and process payouts from one secure workspace.</p>
                    <div class="auth-benefit-list">
                        <div><i class="fas fa-user-check"></i><span><strong>Partner management</strong>Review and manage partner accounts.</span></div>
                        <div><i class="fas fa-chart-column"></i><span><strong>Program oversight</strong>Monitor earnings and performance.</span></div>
                        <div><i class="fas fa-shield-halved"></i><span><strong>Restricted access</strong>Authorized administrators only.</span></div>
                    </div>
                </aside>

                <section class="auth-form-panel">
                    <div class="login-card">
                        <div class="login-header">
                            <img class="admin-login-logo" src="../assets/app_logo.png" alt="Calamus Education">
                            <span class="auth-mobile-kicker">ADMIN PORTAL</span>
                            <h1>Welcome back</h1>
                            <p>Sign in to access the administration workspace.</p>
                        </div>

                        <div class="login-body">
                            <div id="alertContainer"></div>

                            <form id="adminLoginForm">
                                <div class="mb-3">
                                    <label for="username" class="form-label">Username</label>
                                    <div class="auth-input">
                                        <i class="fas fa-user-shield" aria-hidden="true"></i>
                                        <input type="text" class="form-control" id="username" name="username" autocomplete="username" required autofocus>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="password" class="form-label">Password</label>
                                    <div class="auth-input">
                                        <i class="fas fa-lock" aria-hidden="true"></i>
                                        <input type="password" class="form-control" id="password" name="password" autocomplete="current-password" required>
                                        <button type="button" class="password-toggle" data-password-toggle="password" aria-label="Show password">
                                            <i class="fas fa-eye" aria-hidden="true"></i>
                                        </button>
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-arrow-right-to-bracket me-2"></i><span>Sign In</span>
                                </button>
                            </form>

                            <div class="back-link">
                                <a href="../partner_login.php"><i class="fas fa-arrow-left me-2"></i>Back to Partner Portal</a>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </main>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../js/auth-ui.js"></script>
    <script>
        // Setup login form
        document.getElementById('adminLoginForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const username = document.getElementById('username').value;
            const password = document.getElementById('password').value;
            
            if (!username || !password) {
                showAlert('Please fill in all fields', 'danger');
                return;
            }
            
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalContent = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="fas fa-circle-notch fa-spin me-2"></i><span>Signing in...</span>';
            submitBtn.disabled = true;
            
            fetch('../api/admin_login.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ username, password })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.location.href = 'index.php';
                } else {
                    showAlert(data.message, 'danger');
                }
            })
            .catch(error => {
                console.error('Login error:', error);
                showAlert('Login failed. Please try again.', 'danger');
            })
            .finally(() => {
                submitBtn.innerHTML = originalContent;
                submitBtn.disabled = false;
            });
        });
        
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
    </script>
</body>
</html>
