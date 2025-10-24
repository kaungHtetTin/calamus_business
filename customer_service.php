<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Service - Code Validation</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        .validation-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .validation-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            max-width: 800px;
            width: 100%;
        }
        .validation-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 2rem;
            text-align: center;
        }
        .validation-body {
            padding: 2rem;
        }
        .code-input {
            font-family: 'Courier New', monospace;
            font-size: 1.2rem;
            letter-spacing: 2px;
        }
        .result-card {
            border-left: 4px solid #28a745;
        }
        .result-card.invalid {
            border-left-color: #dc3545;
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
    </style>
</head>
<body>
    <div class="validation-container">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="validation-card">
                        <div class="validation-header">
                            <h2 class="mb-3">
                                <i class="fas fa-ticket-alt me-2"></i>
                                Promotion Code Validation
                            </h2>
                            <p class="mb-0">Customer Service Portal</p>
                        </div>
                        
                        <div class="validation-body">
                            <form id="validationForm">
                                <div class="mb-4">
                                    <label class="form-label h5">Enter Promotion Code</label>
                                    <input type="text" class="form-control code-input text-center" id="promotionCode" 
                                           placeholder="PART-VIP-001-1234" required>
                                    <div class="form-text">Enter the promotion code provided by the client</div>
                                </div>
                                
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary btn-lg">
                                        <i class="fas fa-search me-2"></i>Validate Code
                                    </button>
                                </div>
                            </form>
                            
                            <!-- Validation Result -->
                            <div id="validationResult" class="mt-4" style="display: none;">
                                <div class="card result-card">
                                    <div class="card-body">
                                        <div id="resultContent"></div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- VIP Processing Form -->
                            <div id="vipProcessingForm" class="mt-4" style="display: none;">
                                <div class="card">
                                    <div class="card-header">
                                        <h5><i class="fas fa-crown me-2"></i>Process Purchase</h5>
                                    </div>
                                    <div class="card-body">
                                        <form id="purchaseForm">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label class="form-label">Learner Phone</label>
                                                        <input type="number" class="form-control" id="learnerPhone" required>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label class="form-label">Purchase Type</label>
                                                        <select class="form-control" id="purchaseType" required>
                                                            <option value="">Select Type</option>
                                                            <option value="vip_subscription">VIP Subscription</option>
                                                            <option value="package_purchase">Package Purchase</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label class="form-label">Course ID (for VIP)</label>
                                                        <input type="number" class="form-control" id="courseId">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label class="form-label">Package ID (for Package)</label>
                                                        <input type="number" class="form-control" id="packageId">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label class="form-label">Amount ($)</label>
                                                        <input type="number" class="form-control" id="amount" step="0.01" required>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label class="form-label">Commission Rate</label>
                                                        <input type="text" class="form-control" id="commissionRate" readonly>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="d-grid">
                                                <button type="submit" class="btn btn-success btn-lg">
                                                    <i class="fas fa-check me-2"></i>Process Purchase
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let currentCodeData = null;

        // Validation form submission
        document.getElementById('validationForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const code = document.getElementById('promotionCode').value.trim().toUpperCase();
            
            if (!code) {
                showAlert('Please enter a promotion code', 'danger');
                return;
            }
            
            validateCode(code);
        });

        // VIP processing form submission
        document.getElementById('purchaseForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const learnerPhone = document.getElementById('learnerPhone').value;
            const purchaseType = document.getElementById('purchaseType').value;
            const courseId = document.getElementById('courseId').value;
            const packageId = document.getElementById('packageId').value;
            const amount = document.getElementById('amount').value;
            
            if (!learnerPhone || !purchaseType || !amount) {
                showAlert('Please fill in all required fields', 'danger');
                return;
            }
            
            if (purchaseType === 'vip_subscription' && !courseId) {
                showAlert('Course ID is required for VIP subscription', 'danger');
                return;
            }
            
            if (purchaseType === 'package_purchase' && !packageId) {
                showAlert('Package ID is required for package purchase', 'danger');
                return;
            }
            
            processPurchase(learnerPhone, purchaseType, courseId, packageId, amount);
        });

        // Validate promotion code
        function validateCode(code) {
            fetch('api/code_validation.php?endpoint=validate_code', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ code: code })
            })
            .then(response => response.json())
            .then(data => {
                displayValidationResult(data);
                
                if (data.valid) {
                    currentCodeData = data.code_data;
                    showVipProcessingForm(data.code_data);
                } else {
                    currentCodeData = null;
                    hideVipProcessingForm();
                }
            })
            .catch(error => {
                console.error('Validation error:', error);
                showAlert('Error validating code. Please try again.', 'danger');
            });
        }

        // Display validation result
        function displayValidationResult(data) {
            const resultDiv = document.getElementById('validationResult');
            const contentDiv = document.getElementById('resultContent');
            
            if (data.valid) {
                const codeData = data.code_data;
                contentDiv.innerHTML = `
                    <div class="d-flex align-items-center mb-3">
                        <i class="fas fa-check-circle text-success fa-2x me-3"></i>
                        <div>
                            <h5 class="mb-1 text-success">Valid Promotion Code</h5>
                            <p class="mb-0 text-muted">Code is active and ready to use</p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <strong>Code:</strong> <code class="bg-light p-1 rounded">${codeData.code}</code>
                        </div>
                        <div class="col-md-6">
                            <strong>Partner:</strong> ${codeData.contact_name} (${codeData.company_name})
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-md-6">
                            <strong>Type:</strong> ${codeData.code_type.replace('_', ' ').toUpperCase()}
                        </div>
                        <div class="col-md-6">
                            <strong>Commission Rate:</strong> ${codeData.commission_rate}%
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-md-6">
                            <strong>Target Course:</strong> ${codeData.target_course_id || 'All Courses'}
                        </div>
                        <div class="col-md-6">
                            <strong>Generated For:</strong> ${codeData.generated_for || 'N/A'}
                        </div>
                    </div>
                    ${codeData.expires_at ? `<div class="mt-2"><strong>Expires:</strong> ${new Date(codeData.expires_at).toLocaleString()}</div>` : ''}
                `;
                resultDiv.querySelector('.result-card').classList.remove('invalid');
            } else {
                contentDiv.innerHTML = `
                    <div class="d-flex align-items-center">
                        <i class="fas fa-times-circle text-danger fa-2x me-3"></i>
                        <div>
                            <h5 class="mb-1 text-danger">Invalid Promotion Code</h5>
                            <p class="mb-0 text-muted">${data.message}</p>
                        </div>
                    </div>
                `;
                resultDiv.querySelector('.result-card').classList.add('invalid');
            }
            
            resultDiv.style.display = 'block';
        }

        // Show VIP processing form
        function showVipProcessingForm(codeData) {
            const form = document.getElementById('vipProcessingForm');
            document.getElementById('commissionRate').value = codeData.commission_rate + '%';
            
            // Set default purchase type based on code type
            const purchaseType = document.getElementById('purchaseType');
            if (codeData.code_type === 'package_purchase') {
                purchaseType.value = 'package_purchase';
            } else {
                purchaseType.value = 'vip_subscription';
            }
            
            form.style.display = 'block';
        }

        // Hide VIP processing form
        function hideVipProcessingForm() {
            document.getElementById('vipProcessingForm').style.display = 'none';
        }

        // Process purchase (VIP or Package)
        function processPurchase(learnerPhone, purchaseType, courseId, packageId, amount) {
            if (!currentCodeData) {
                showAlert('No valid code data available', 'danger');
                return;
            }
            
            let endpoint = '';
            let requestData = {
                code: currentCodeData.code,
                learner_phone: learnerPhone,
                amount: amount
            };
            
            if (purchaseType === 'vip_subscription') {
                endpoint = 'process_vip_with_code';
                requestData.course_id = courseId;
            } else if (purchaseType === 'package_purchase') {
                endpoint = 'process_package_with_code';
                requestData.package_id = packageId;
            }
            
            fetch(`api/code_validation.php?endpoint=${endpoint}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(requestData)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    let successMessage = '';
                    if (purchaseType === 'vip_subscription') {
                        successMessage = `VIP subscription processed successfully!<br>
                                        Subscription ID: ${data.subscription_id}<br>
                                        Commission: $${data.commission_amount}`;
                    } else {
                        successMessage = `Package purchase processed successfully!<br>
                                        Purchase ID: ${data.purchase_id}<br>
                                        Commission: $${data.commission_amount}`;
                    }
                    
                    showAlert(successMessage, 'success');
                    
                    // Reset form
                    document.getElementById('purchaseForm').reset();
                    document.getElementById('promotionCode').value = '';
                    hideVipProcessingForm();
                    document.getElementById('validationResult').style.display = 'none';
                    currentCodeData = null;
                } else {
                    showAlert('Error: ' + data.message, 'danger');
                }
            })
            .catch(error => {
                console.error('Purchase processing error:', error);
                showAlert('Error processing purchase. Please try again.', 'danger');
            });
        }

        // Show alert
        function showAlert(message, type) {
            const alertDiv = document.createElement('div');
            alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
            alertDiv.innerHTML = `
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            
            // Remove existing alerts
            document.querySelectorAll('.alert').forEach(alert => alert.remove());
            
            // Add new alert
            document.querySelector('.validation-body').insertBefore(alertDiv, document.querySelector('.validation-body').firstChild);
        }

        // Auto-format code input
        document.getElementById('promotionCode').addEventListener('input', function(e) {
            let value = e.target.value.toUpperCase();
            // Remove any characters that aren't letters, numbers, or hyphens
            value = value.replace(/[^A-Z0-9-]/g, '');
            e.target.value = value;
        });
    </script>
</body>
</html>
