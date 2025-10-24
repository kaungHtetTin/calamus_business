<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Redirect Test</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h4>Login Redirect Test</h4>
                    </div>
                    <div class="card-body">
                        <form id="testLoginForm">
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" required>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" id="password" required>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Test Login</button>
                        </form>
                        
                        <div id="result" class="mt-3" style="display: none;">
                            <div class="alert" id="resultAlert"></div>
                        </div>
                        
                        <div class="mt-3">
                            <h6>Expected Behavior:</h6>
                            <ul>
                                <li>✅ Login should redirect to <code>index.php</code> (Partner Dashboard)</li>
                                <li>❌ Should NOT redirect to <code>dashboard.html</code> (doesn't exist)</li>
                            </ul>
                        </div>
                        
                        <div class="mt-3">
                            <h6>Current Files:</h6>
                            <ul>
                                <li><strong>index.php</strong> - Partner Dashboard ✅</li>
                                <li><strong>dashboard.html</strong> - Does not exist ❌</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('testLoginForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;
            
            // Show loading state
            const submitBtn = e.target.querySelector('button[type="submit"]');
            const originalText = submitBtn.textContent;
            submitBtn.textContent = 'Testing...';
            submitBtn.disabled = true;
            
            // Make login request
            fetch('api/login_partner.php?endpoint=login', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ email, password })
            })
            .then(response => response.json())
            .then(data => {
                console.log('Login response:', data);
                
                if (data.success) {
                    showResult(`✅ Login successful!<br>
                        <strong>Redirect URL:</strong> ${data.redirect_url}<br>
                        <strong>Session Token:</strong> ${data.session_token.substring(0, 20)}...<br>
                        <strong>Partner:</strong> ${data.partner.contact_name}<br><br>
                        <strong>Expected redirect:</strong> index.php (Partner Dashboard)`, 'success');
                    
                    // Test the redirect
                    setTimeout(() => {
                        if (data.redirect_url === '../index.php') {
                            showResult('✅ Redirect URL is correct: ../index.php<br>This will redirect to the Partner Dashboard (index.php)', 'info');
                        } else {
                            showResult(`❌ Unexpected redirect URL: ${data.redirect_url}`, 'danger');
                        }
                    }, 1000);
                    
                } else {
                    showResult(`❌ Login failed: ${data.message}`, 'danger');
                }
            })
            .catch(error => {
                console.error('Login error:', error);
                showResult(`❌ Network error: ${error.message}`, 'danger');
            })
            .finally(() => {
                // Reset button state
                submitBtn.textContent = originalText;
                submitBtn.disabled = false;
            });
        });
        
        function showResult(message, type) {
            const resultDiv = document.getElementById('result');
            const alertDiv = document.getElementById('resultAlert');
            
            alertDiv.className = 'alert alert-' + type;
            alertDiv.innerHTML = message;
            
            resultDiv.style.display = 'block';
        }
    </script>
</body>
</html>
