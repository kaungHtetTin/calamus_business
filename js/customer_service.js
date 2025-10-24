/**
 * Customer Service JavaScript
 * 
 * This file contains all JavaScript functionality for customer service.
 * It handles promotion code validation and purchase processing.
 */

let currentCodeData = null;

// Initialize customer service page
document.addEventListener('DOMContentLoaded', function() {
    setupValidationForm();
    setupPurchaseForm();
    setupCodeInputFormatting();
});

// Setup validation form
function setupValidationForm() {
    const validationForm = document.getElementById('validationForm');
    if (!validationForm) return;
    
    validationForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const code = document.getElementById('promotionCode').value.trim().toUpperCase();
        
        if (!code) {
            showAlert('Please enter a promotion code', 'danger');
            return;
        }
        
        validateCode(code);
    });
}

// Setup purchase form
function setupPurchaseForm() {
    const purchaseForm = document.getElementById('purchaseForm');
    if (!purchaseForm) return;
    
    purchaseForm.addEventListener('submit', function(e) {
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
}

// Setup code input formatting
function setupCodeInputFormatting() {
    const promotionCodeInput = document.getElementById('promotionCode');
    if (!promotionCodeInput) return;
    
    promotionCodeInput.addEventListener('input', function(e) {
        let value = e.target.value.toUpperCase();
        // Remove any characters that aren't letters, numbers, or hyphens
        value = value.replace(/[^A-Z0-9-]/g, '');
        e.target.value = value;
    });
}

// Validate promotion code
function validateCode(code) {
    // Show loading state
    const validateBtn = document.querySelector('#validationForm button[type="submit"]');
    const originalText = validateBtn.textContent;
    validateBtn.textContent = 'Validating...';
    validateBtn.disabled = true;
    
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
    })
    .finally(() => {
        // Reset button state
        validateBtn.textContent = originalText;
        validateBtn.disabled = false;
    });
}

// Display validation result
function displayValidationResult(data) {
    const resultDiv = document.getElementById('validationResult');
    const contentDiv = document.getElementById('resultContent');
    
    if (!resultDiv || !contentDiv) return;
    
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
    const commissionRateField = document.getElementById('commissionRate');
    
    if (!form) return;
    
    if (commissionRateField) {
        commissionRateField.value = codeData.commission_rate + '%';
    }
    
    // Set default purchase type based on code type
    const purchaseType = document.getElementById('purchaseType');
    if (purchaseType) {
        if (codeData.code_type === 'package_purchase') {
            purchaseType.value = 'package_purchase';
        } else {
            purchaseType.value = 'vip_subscription';
        }
    }
    
    form.style.display = 'block';
}

// Hide VIP processing form
function hideVipProcessingForm() {
    const form = document.getElementById('vipProcessingForm');
    if (form) {
        form.style.display = 'none';
    }
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
    
    // Show loading state
    const submitBtn = document.querySelector('#purchaseForm button[type="submit"]');
    const originalText = submitBtn.textContent;
    submitBtn.textContent = 'Processing...';
    submitBtn.disabled = true;
    
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
    })
    .finally(() => {
        // Reset button state
        submitBtn.textContent = originalText;
        submitBtn.disabled = false;
    });
}

// Show alert
function showAlert(message, type) {
    // Remove existing alerts
    document.querySelectorAll('.alert').forEach(alert => alert.remove());
    
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
    alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    // Add new alert
    const validationBody = document.querySelector('.validation-body');
    if (validationBody) {
        validationBody.insertBefore(alertDiv, validationBody.firstChild);
    }
}

// Utility functions
function formatCurrency(amount) {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD'
    }).format(amount);
}

function formatDate(dateString) {
    return new Date(dateString).toLocaleString();
}

// Clear all forms
function clearAllForms() {
    document.getElementById('validationForm').reset();
    document.getElementById('purchaseForm').reset();
    hideVipProcessingForm();
    document.getElementById('validationResult').style.display = 'none';
    currentCodeData = null;
}
