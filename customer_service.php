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
    <script src="js/customer_service.js"></script>
</body>
</html>
