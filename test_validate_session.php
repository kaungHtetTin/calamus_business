<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Session Validation Test</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .container {
            max-width: 600px;
            margin: 100px auto;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="card-header text-center">
                <h4>Session Validation Test</h4>
            </div>
            <div class="card-body">
                <form id="validateForm">
                    <div class="mb-3">
                        <label for="sessionToken" class="form-label">Session Token</label>
                        <input type="text" class="form-control" id="sessionToken" placeholder="Enter session token">
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Validate Session</button>
                </form>
                
                <div id="response" class="mt-3" style="display: none;">
                    <div class="alert" id="responseAlert"></div>
                </div>
                
                <div class="mt-4">
                    <h6>Available Endpoints:</h6>
                    <ul class="list-unstyled">
                        <li><strong>POST</strong> <code>/api/validate_session.php</code> - Validate session (this page)</li>
                        <li><strong>POST</strong> <code>/api/login_partner.php?endpoint=validate_session</code> - Alternative endpoint</li>
                        <li><strong>GET</strong> <code>/api/index.php</code> - View all API endpoints</li>
                    </ul>
                </div>
                
                <div class="mt-3">
                    <a href="test_login_api.php" class="btn btn-outline-secondary">← Back to Login Test</a>
                    <a href="api/index.php" class="btn btn-outline-info">View API Index</a>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('validateForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const sessionToken = document.getElementById('sessionToken').value;
            
            if (!sessionToken) {
                showResponse('Please enter a session token', 'danger');
                return;
            }
            
            // Show loading state
            const submitBtn = e.target.querySelector('button[type="submit"]');
            const originalText = submitBtn.textContent;
            submitBtn.textContent = 'Validating...';
            submitBtn.disabled = true;
            
            // Make API call
            fetch('api/validate_session.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    session_token: sessionToken
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showResponse(`✅ Session is valid!<br><strong>Partner:</strong> ${data.partner.company_name}<br><strong>Email:</strong> ${data.partner.email}`, 'success');
                } else {
                    showResponse(`❌ Session validation failed: ${data.message}`, 'danger');
                }
            })
            .catch(error => {
                showResponse(`❌ Network error: ${error.message}`, 'danger');
            })
            .finally(() => {
                // Reset button state
                submitBtn.textContent = originalText;
                submitBtn.disabled = false;
            });
        });
        
        function showResponse(message, type) {
            const responseDiv = document.getElementById('response');
            const alertDiv = document.getElementById('responseAlert');
            
            alertDiv.className = 'alert alert-' + type;
            alertDiv.innerHTML = message;
            
            responseDiv.style.display = 'block';
            
            // Auto-hide after 10 seconds
            setTimeout(() => {
                responseDiv.style.display = 'none';
            }, 10000);
        }
        
        // Test with a sample token (for demonstration)
        document.getElementById('sessionToken').addEventListener('focus', function() {
            if (!this.value) {
                this.placeholder = 'Try: test_session_token_123';
            }
        });
    </script>
</body>
</html>
