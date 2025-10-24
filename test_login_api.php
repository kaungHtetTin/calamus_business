<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Partner Login API Test</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .login-container {
            max-width: 400px;
            margin: 100px auto;
        }
        .card {
            border: none;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="login-container">
            <div class="card">
                <div class="card-header text-center">
                    <h4>Partner Login API Test</h4>
                </div>
                <div class="card-body">
                    <!-- Login Form -->
                    <form id="loginForm">
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" required>
                        </div>
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="remember">
                            <label class="form-check-label" for="remember">
                                Remember me
                            </label>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Login</button>
                    </form>
                    
                    <!-- API Response -->
                    <div id="response" class="mt-3" style="display: none;">
                        <div class="alert" id="responseAlert"></div>
                    </div>
                    
                    <!-- Session Info -->
                    <div id="sessionInfo" class="mt-3" style="display: none;">
                        <h6>Session Information:</h6>
                        <div id="sessionDetails"></div>
                        <button class="btn btn-outline-danger btn-sm mt-2" id="logoutBtn">Logout</button>
                    </div>
                </div>
            </div>
            
            <!-- API Endpoints Info -->
            <div class="card mt-4">
                <div class="card-header">
                    <h6>Available API Endpoints</h6>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled">
                        <li><strong>POST</strong> <code>/api/login_partner.php?endpoint=login</code> - Login</li>
                        <li><strong>POST</strong> <code>/api/login_partner.php?endpoint=logout</code> - Logout</li>
                        <li><strong>POST</strong> <code>/api/login_partner.php?endpoint=validate_session</code> - Validate Session</li>
                        <li><strong>POST</strong> <code>/api/login_partner.php?endpoint=forgot_password</code> - Forgot Password</li>
                        <li><strong>POST</strong> <code>/api/login_partner.php?endpoint=reset_password</code> - Reset Password</li>
                        <li><strong>POST</strong> <code>/api/login_partner.php?endpoint=change_password</code> - Change Password</li>
                        <li><strong>POST</strong> <code>/api/login_partner.php?endpoint=get_partner_info</code> - Get Partner Info</li>
                        <li><strong>POST</strong> <code>/api/login_partner.php?endpoint=update_profile</code> - Update Profile</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let currentSessionToken = null;
        
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;
            const remember = document.getElementById('remember').checked;
            
            // Show loading state
            const submitBtn = e.target.querySelector('button[type="submit"]');
            const originalText = submitBtn.textContent;
            submitBtn.textContent = 'Logging in...';
            submitBtn.disabled = true;
            
            // Make API call
            fetch('api/login_partner.php?endpoint=login', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    email: email,
                    password: password,
                    remember: remember
                })
            })
            .then(response => response.json())
            .then(data => {
                showResponse(data);
                
                if (data.success) {
                    currentSessionToken = data.session_token;
                    showSessionInfo(data.partner);
                }
            })
            .catch(error => {
                showResponse({
                    success: false,
                    message: 'Network error: ' + error.message
                });
            })
            .finally(() => {
                // Reset button state
                submitBtn.textContent = originalText;
                submitBtn.disabled = false;
            });
        });
        
        document.getElementById('logoutBtn').addEventListener('click', function() {
            if (!currentSessionToken) return;
            
            fetch('api/login_partner.php?endpoint=logout', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    session_token: currentSessionToken
                })
            })
            .then(response => response.json())
            .then(data => {
                showResponse(data);
                if (data.success) {
                    currentSessionToken = null;
                    document.getElementById('sessionInfo').style.display = 'none';
                    document.getElementById('loginForm').reset();
                }
            })
            .catch(error => {
                showResponse({
                    success: false,
                    message: 'Network error: ' + error.message
                });
            });
        });
        
        function showResponse(data) {
            const responseDiv = document.getElementById('response');
            const alertDiv = document.getElementById('responseAlert');
            
            alertDiv.className = 'alert ' + (data.success ? 'alert-success' : 'alert-danger');
            alertDiv.innerHTML = '<strong>' + (data.success ? 'Success!' : 'Error!') + '</strong> ' + data.message;
            
            if (data.session_token) {
                alertDiv.innerHTML += '<br><small>Session Token: ' + data.session_token.substring(0, 20) + '...</small>';
            }
            
            responseDiv.style.display = 'block';
            
            // Auto-hide after 5 seconds
            setTimeout(() => {
                responseDiv.style.display = 'none';
            }, 5000);
        }
        
        function showSessionInfo(partner) {
            const sessionInfo = document.getElementById('sessionInfo');
            const sessionDetails = document.getElementById('sessionDetails');
            
            sessionDetails.innerHTML = `
                <p><strong>Company:</strong> ${partner.company_name}</p>
                <p><strong>Contact:</strong> ${partner.contact_name}</p>
                <p><strong>Email:</strong> ${partner.email}</p>
                <p><strong>Partner ID:</strong> ${partner.id}</p>
            `;
            
            sessionInfo.style.display = 'block';
        }
        
        // Test session validation on page load
        window.addEventListener('load', function() {
            // Check if there's a stored session token
            const storedToken = localStorage.getItem('partner_session_token');
            if (storedToken) {
                validateSession(storedToken);
            }
        });
        
        function validateSession(token) {
            fetch('api/login_partner.php?endpoint=validate_session', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    session_token: token
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    currentSessionToken = token;
                    showSessionInfo(data.partner);
                    showResponse({
                        success: true,
                        message: 'Session restored successfully'
                    });
                } else {
                    localStorage.removeItem('partner_session_token');
                }
            })
            .catch(error => {
                console.error('Session validation error:', error);
            });
        }
        
        // Store session token when login is successful
        function storeSessionToken(token) {
            localStorage.setItem('partner_session_token', token);
        }
        
        // Update the login success handler to store the token
        const originalShowResponse = showResponse;
        showResponse = function(data) {
            originalShowResponse(data);
            if (data.success && data.session_token) {
                storeSessionToken(data.session_token);
            }
        };
    </script>
</body>
</html>
