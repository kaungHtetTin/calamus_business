/**
 * Payment Methods JavaScript
 * 
 * This file handles all payment methods CRUD operations
 */

// Global variables
let sessionToken = null;

$(document).ready(function() {
    console.log('Payment Methods: Document ready');
    
    // Get session token from PHP
    sessionToken = window.sessionToken || '';
    console.log('Payment Methods: Session token:', sessionToken);
    
    // Setup form handlers
    setupFormHandlers();
});

// Setup form event handlers
function setupFormHandlers() {
    // Add payment method form
    $('#addPaymentMethodForm').on('submit', function(e) {
        e.preventDefault();
        addPaymentMethod();
    });
    
    // Edit payment method form
    $('#editPaymentMethodForm').on('submit', function(e) {
        e.preventDefault();
        updatePaymentMethod();
    });
    
    // Clear forms when modals are hidden
    $('#addPaymentMethodModal').on('hidden.bs.modal', function() {
        $('#addPaymentMethodForm')[0].reset();
    });
    
    $('#editPaymentMethodModal').on('hidden.bs.modal', function() {
        $('#editPaymentMethodForm')[0].reset();
    });
}

// Add new payment method
function addPaymentMethod() {
    const formData = {
        session_token: sessionToken,
        payment_method: $('#paymentMethod').val(),
        account_name: $('#accountName').val(),
        account_number: $('#accountNumber').val()
    };
    
    // Validate form
    if (!formData.payment_method || !formData.account_name || !formData.account_number) {
        showAlert('Please fill in all fields', 'warning');
        return;
    }
    
    // Show loading state
    const submitBtn = $('#addPaymentMethodForm button[type="submit"]');
    const originalText = submitBtn.html();
    submitBtn.html('<i class="fas fa-spinner fa-spin me-2"></i>Adding...').prop('disabled', true);
    
    // Send request
    fetch('api/payment_methods.php?endpoint=add_payment_method', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(formData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert(data.message, 'success');
            $('#addPaymentMethodModal').modal('hide');
            // Reload page to show new payment method
            setTimeout(() => {
                location.reload();
            }, 1000);
        } else {
            showAlert(data.message, 'danger');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('An error occurred while adding the payment method', 'danger');
    })
    .finally(() => {
        // Reset button state
        submitBtn.html(originalText).prop('disabled', false);
    });
}

// Edit payment method
function editPaymentMethod(paymentMethod) {
    console.log('Editing payment method:', paymentMethod);
    
    // Populate form fields
    $('#editPaymentMethodId').val(paymentMethod.id);
    $('#editPaymentMethod').val(paymentMethod.payment_method);
    $('#editAccountName').val(paymentMethod.account_name);
    $('#editAccountNumber').val(paymentMethod.account_number);
    
    // Show modal
    $('#editPaymentMethodModal').modal('show');
}

// Update payment method
function updatePaymentMethod() {
    const formData = {
        session_token: sessionToken,
        id: $('#editPaymentMethodId').val(),
        payment_method: $('#editPaymentMethod').val(),
        account_name: $('#editAccountName').val(),
        account_number: $('#editAccountNumber').val()
    };
    
    // Validate form
    if (!formData.payment_method || !formData.account_name || !formData.account_number) {
        showAlert('Please fill in all fields', 'warning');
        return;
    }
    
    // Show loading state
    const submitBtn = $('#editPaymentMethodForm button[type="submit"]');
    const originalText = submitBtn.html();
    submitBtn.html('<i class="fas fa-spinner fa-spin me-2"></i>Updating...').prop('disabled', true);
    
    // Send request
    fetch('api/payment_methods.php?endpoint=update_payment_method', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(formData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert(data.message, 'success');
            $('#editPaymentMethodModal').modal('hide');
            // Reload page to show updated payment method
            setTimeout(() => {
                location.reload();
            }, 1000);
        } else {
            showAlert(data.message, 'danger');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('An error occurred while updating the payment method', 'danger');
    })
    .finally(() => {
        // Reset button state
        submitBtn.html(originalText).prop('disabled', false);
    });
}

// Delete payment method
function deletePaymentMethod(paymentMethodId) {
    // Confirm deletion
    if (!confirm('Are you sure you want to delete this mobile money account? This action cannot be undone.')) {
        return;
    }
    
    const formData = {
        session_token: sessionToken,
        id: paymentMethodId
    };
    
    // Send request
    fetch('api/payment_methods.php?endpoint=delete_payment_method', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(formData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert(data.message, 'success');
            // Reload page to show updated list
            setTimeout(() => {
                location.reload();
            }, 1000);
        } else {
            showAlert(data.message, 'danger');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('An error occurred while deleting the payment method', 'danger');
    });
}

// Show alert message (using common function from app.js)
function showAlert(message, type = 'info') {
    // Remove existing alerts
    $('.alert').remove();
    
    // Create new alert
    const alertHtml = `
        <div class="alert alert-${type} alert-dismissible fade show" role="alert">
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;
    
    // Insert alert at the top of the content section
    $('.content-section').prepend(alertHtml);
    
    // Auto-dismiss after 5 seconds
    setTimeout(function() {
        $('.alert').fadeOut();
    }, 5000);
}
