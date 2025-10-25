/**
 * Earning History JavaScript
 * 
 * This file handles earning history functionality
 */

// Global variables
let sessionToken = null;
let currentPage = 1;
let isLoading = false;

$(document).ready(function() {
    console.log('Earning History: Document ready');
    
    // Get session token from PHP
    sessionToken = window.sessionToken || '';
    console.log('Earning History: Session token:', sessionToken);
    
    // Setup event handlers
    setupEventHandlers();
});

// Setup event handlers
function setupEventHandlers() {
    // Load more button
    $('#loadMoreBtn').on('click', function() {
        loadMoreEarnings();
    });
    
    // Table row click for details
    $(document).on('click', '.table tbody tr', function() {
        const codeId = $(this).data('code-id');
        if (codeId) {
            showEarningDetails(codeId);
        }
    });
}

// Load more earnings
function loadMoreEarnings() {
    if (isLoading) return;
    
    isLoading = true;
    currentPage++;
    
    const loadBtn = $('#loadMoreBtn');
    const originalText = loadBtn.html();
    loadBtn.html('<i class="fas fa-spinner fa-spin me-2"></i>Loading...').prop('disabled', true);
    
    fetch(`api/partner_earnings.php?endpoint=get_earning_history&session_token=${sessionToken}&limit=20&page=${currentPage}`)
    .then(response => response.json())
    .then(data => {
        if (data.success && data.data.length > 0) {
            appendEarningsToTable(data.data);
            
            // Hide load more button if we got less than requested
            if (data.data.length < 20) {
                loadBtn.hide();
            }
        } else {
            loadBtn.hide();
            showAlert('No more earnings to load', 'info');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('An error occurred while loading more earnings', 'danger');
        currentPage--; // Revert page increment on error
    })
    .finally(() => {
        isLoading = false;
        loadBtn.html(originalText).prop('disabled', false);
    });
}

// Append earnings to table
function appendEarningsToTable(earnings) {
    const tbody = $('.table tbody');
    
    earnings.forEach(earning => {
        const row = createEarningRow(earning);
        tbody.append(row);
    });
}

// Create earning row HTML
function createEarningRow(earning) {
    const userPhone = earning.user_phone || 'N/A';
    const userName = earning.user_name || 'Unknown User';
    const transactionDate = new Date(earning.created_at);
    const formattedDate = transactionDate.toLocaleDateString('en-US', { 
        month: 'short', 
        day: 'numeric', 
        year: 'numeric' 
    });
    const formattedTime = transactionDate.toLocaleTimeString('en-US', { 
        hour: '2-digit', 
        minute: '2-digit' 
    });
    
    // Determine transaction type
    let transactionType = 'Transaction';
    if (earning.target_course_id) {
        transactionType = 'Course Purchase';
    } else if (earning.target_package_id) {
        transactionType = 'Package Purchase';
    }
    
    // Determine status badge
    let statusBadge = '';
    if (earning.status === 'paid') {
        statusBadge = `
            <span class="badge bg-success">
                <i class="fas fa-check-circle me-1"></i>
                Paid
            </span>
        `;
    } else {
        statusBadge = `
            <span class="badge bg-warning">
                <i class="fas fa-clock me-1"></i>
                Pending
            </span>
        `;
    }
    
    return `
        <tr data-earning-id="${earning.id}">
            <td>
                <div class="d-flex align-items-center">
                    <div class="me-2">
                        <i class="fas fa-shopping-cart text-primary"></i>
                    </div>
                    <div>
                        <strong>${transactionType}</strong>
                        <br>
                        <small class="text-muted">Price: $${parseFloat(earning.price).toFixed(2)}</small>
                    </div>
                </div>
            </td>
            <td>
                <div>
                    <strong>${userName}</strong>
                    <br>
                    <small class="text-muted">
                        <i class="fas fa-phone me-1"></i>
                        ${userPhone}
                    </small>
                </div>
            </td>
            <td>
                <div class="text-success fw-bold">
                    $${parseFloat(earning.amount_received).toFixed(2)}
                </div>
            </td>
            <td>
                <span class="badge bg-info">
                    ${parseFloat(earning.commission_rate).toFixed(1)}%
                </span>
            </td>
            <td>
                <div>
                    ${formattedDate}
                    <br>
                    <small class="text-muted">${formattedTime}</small>
                </div>
            </td>
            <td>
                ${statusBadge}
            </td>
        </tr>
    `;
}

// Show earning details modal
function showEarningDetails(earningId) {
    // For now, just show a simple alert
    // In a real implementation, you might want to fetch detailed information
    showAlert('Earning details feature coming soon!', 'info');
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

// Format currency
function formatCurrency(amount) {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD'
    }).format(amount);
}

// Format date
function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    });
}

// Format time
function formatTime(dateString) {
    const date = new Date(dateString);
    return date.toLocaleTimeString('en-US', {
        hour: '2-digit',
        minute: '2-digit'
    });
}
