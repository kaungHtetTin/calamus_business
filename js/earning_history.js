/**
 * Earning History JavaScript
 * 
 * This file handles earning history functionality
 */

// Global variables
//let sessionToken = null;
let currentPage = 1;
let isLoading = false;
let currentFilters = {
    status: '',
    period: '',
    startDate: '',
    endDate: ''
};

$(document).ready(function() {
    console.log('Earning History: Document ready');
    
    // Get session token from PHP
    sessionToken = window.sessionToken || '';
    console.log('Earning History: Session token:', sessionToken);
    
    // Setup event handlers
    setupEventHandlers();
    
    // Load initial earnings
    loadInitialEarnings();
});

// Setup event handlers
function setupEventHandlers() {
    // Load more button
    $('#loadMoreBtn').on('click', function() {
        loadMoreEarnings();
    });
    
    // Table row click for details
    $(document).on('click', '#earningHistoryTableBody tr', function() {
        const earningId = $(this).data('earning-id');
        if (earningId) {
            showEarningDetails(earningId);
        }
    });
    
    // Filter event handlers
    $('#periodFilter').on('change', function() {
        const period = $(this).val();
        if (period === 'custom') {
            $('#customDateRange').show();
        } else {
            $('#customDateRange').hide();
            $('#startDate, #endDate').val('');
        }
    });
    
    $('#applyFilters').on('click', function() {
        applyFilters();
    });
    
    $('#clearFilters').on('click', function() {
        clearFilters();
    });
}

// Load initial earnings
function loadInitialEarnings() {
    showLoadingState();
    if (isLoading) return;
    
    isLoading = true;
    currentPage = 1;
    
    // Reset load more button
    $('#loadMoreBtn').show().html('<i class="fas fa-plus me-2"></i>Load More Earnings').prop('disabled', false);
    
    const loadBtn = $('#loadMoreBtn');
    const originalText = loadBtn.html();
    loadBtn.html('<i class="fas fa-spinner fa-spin me-2"></i>Loading...').prop('disabled', true);
    
    // Build URL with current filters
    const url = buildEarningsUrl(currentPage);
    
    fetch(url)
    .then(response => response.json())
    .then(data => {
        if (data.success && data.data.length > 0) {
            updateTable(data.data);
            // Check if any filters are active and update statistics accordingly
            const hasActiveFilters = currentFilters.status || currentFilters.startDate || currentFilters.endDate;
            if (hasActiveFilters) {
                updateFilteredStatistics();
            } else {
                updateStatistics(data.stats);
            }
            showTableState();
            // Hide load more button if we got less than requested
            if (data.data.length < 20) {
                loadBtn.hide();
            }
        } else {
            showEmptyState();
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('An error occurred while loading more earnings', 'danger');
    })
    .finally(() => {
        isLoading = false;
        loadBtn.html(originalText).prop('disabled', false);
    });
}

// Load more earnings
function loadMoreEarnings() {
    if (isLoading) return;
    showLoadingState
    isLoading = true;
    currentPage++;
    
    const loadBtn = $('#loadMoreBtn');
    const originalText = loadBtn.html();
    loadBtn.html('<i class="fas fa-spinner fa-spin me-2"></i>Loading...').prop('disabled', true);
    
    // Build URL with current filters
    const url = buildEarningsUrl(currentPage);
    
    fetch(url)
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
    const tbody = $('#earningHistoryTableBody');
    showTableState();
    earnings.forEach(earning => {
        const row = createEarningRow(earning);
        tbody.append(row);
    });
}

// Create earning row HTML
function createEarningRow(earning) {
    
    const userPhone = earning.learner_phone || 'N/A';
    const userName = earning.learner_name || 'Unknown User';
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
                        <small class="text-muted">Price: ${parseFloat(earning.price).toFixed(2)} MMK</small>
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
                    ${parseFloat(earning.amount_received).toFixed(2)} MMK
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

// Build earnings URL with current filters
function buildEarningsUrl(page = 1) {
    let url = `api/partner_earnings.php?endpoint=get_earning_history&session_token=${sessionToken}&limit=20&page=${page}`;
    
    if (currentFilters.status) {
        url += `&status=${currentFilters.status}`;
    }
    
    if (currentFilters.startDate) {
        url += `&start_date=${currentFilters.startDate}`;
    }
    
    if (currentFilters.endDate) {
        url += `&end_date=${currentFilters.endDate}`;
    }
    
    return url;
}

// Apply filters
function applyFilters() {
    // Get filter values
    const status = $('#statusFilter').val();
    const period = $('#periodFilter').val();
    const startDate = $('#startDate').val();
    const endDate = $('#endDate').val();
    
    // Calculate date range based on period
    let calculatedStartDate = '';
    let calculatedEndDate = '';
    
    if (period && period !== 'custom') {
        const today = new Date();
        const dates = calculateDateRange(period, today);
        calculatedStartDate = dates.start;
        calculatedEndDate = dates.end;
    } else if (period === 'custom') {
        calculatedStartDate = startDate;
        calculatedEndDate = endDate;
    }
    
    // Update current filters
    currentFilters = {
        status: status,
        period: period,
        startDate: calculatedStartDate,
        endDate: calculatedEndDate
    };
    
    // Reset pagination
    currentPage = 1;
    
    // Load filtered earnings
    loadInitialEarnings();
}

// Clear filters
function clearFilters() {
    // Reset filter inputs
    $('#statusFilter').val('');
    $('#periodFilter').val('');
    $('#startDate').val('');
    $('#endDate').val('');
    $('#customDateRange').hide();
    
    // Reset current filters
    currentFilters = {
        status: '',
        period: '',
        startDate: '',
        endDate: ''
    };
    
    // Reset pagination
    currentPage = 1;
    
    // Load initial earnings
    loadInitialEarnings();
}

// Load filtered earnings
function loadFilteredEarnings() {
    const url = buildEarningsUrl(1);
    
    fetch(url)
    .then(response => response.json())
    .then(data => {
        if (data.success && data.data.length > 0) {
            appendEarningsToTable(data.data);
            
            // Hide load more button if we got less than requested
            if (data.data.length < 20) {
                $('#loadMoreBtn').hide();
            }
        } else {
            $('#earningHistoryTableBody').html(`
                <tr>
                    <td colspan="6" class="text-center text-muted py-4">
                        <i class="fas fa-search fa-2x mb-2"></i>
                        <p>No earnings found matching your filters.</p>
                    </td>
                </tr>
            `);
            $('#loadMoreBtn').hide();
        }
        
        // Update statistics with filtered data
        updateFilteredStatistics();
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('An error occurred while loading earnings', 'danger');
    });
}

// Calculate date range based on period
function calculateDateRange(period, today) {
    const start = new Date(today);
    const end = new Date(today);
    
    switch (period) {
        case 'today':
            start.setHours(0, 0, 0, 0);
            end.setHours(23, 59, 59, 999);
            break;
        case 'week':
            start.setDate(today.getDate() - today.getDay());
            start.setHours(0, 0, 0, 0);
            end.setHours(23, 59, 59, 999);
            break;
        case 'month':
            start.setDate(1);
            start.setHours(0, 0, 0, 0);
            end.setMonth(today.getMonth() + 1, 0);
            end.setHours(23, 59, 59, 999);
            break;
        case 'quarter':
            const quarter = Math.floor(today.getMonth() / 3);
            start.setMonth(quarter * 3, 1);
            start.setHours(0, 0, 0, 0);
            end.setMonth(quarter * 3 + 3, 0);
            end.setHours(23, 59, 59, 999);
            break;
        case 'year':
            start.setMonth(0, 1);
            start.setHours(0, 0, 0, 0);
            end.setMonth(11, 31);
            end.setHours(23, 59, 59, 999);
            break;
    }
    
    return {
        start: start.toISOString().split('T')[0],
        end: end.toISOString().split('T')[0]
    };
}

// Update filtered statistics
function updateFilteredStatistics() {
    // Build URL for filtered statistics
    let url = `api/partner_earnings.php?endpoint=get_earning_stats_filtered&session_token=${sessionToken}`;
    
    if (currentFilters.status) {
        url += `&status=${currentFilters.status}`;
    }
    
    if (currentFilters.startDate) {
        url += `&start_date=${currentFilters.startDate}`;
    }
    
    if (currentFilters.endDate) {
        url += `&end_date=${currentFilters.endDate}`;
    }
    
    fetch(url)
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update only totalEarnings and totalTransactions based on filter
            $('#totalEarnings').text(parseFloat(data.data.total_earnings).toFixed(2) + ' MMK');
            $('#totalTransactions').text(data.data.total_transactions.toLocaleString());
            
            // Keep thisMonthEarnings unchanged - don't update it
            
            // Update the header total as well
            $('.content-section .text-end .h3').text(parseFloat(data.data.total_earnings).toFixed(2) + ' MMK');
        }
    })
    .catch(error => {
        console.error('Error updating statistics:', error);
    });
}

// State management functions
function showLoadingState() {
    $('#loadingState').show();
    $('#emptyState').hide();
    $('#tableContainer').hide();
}

function hideLoadingState() {
    $('#loadingState').hide();
}

function showEmptyState() {
    $('#loadingState').hide();
    $('#emptyState').show();
    $('#tableContainer').hide();
}

function showTableState() {
    $('#loadingState').hide();
    $('#emptyState').hide();
    $('#tableContainer').show();
}

// Update table with earnings data
function updateTable(earnings) {
    const tbody = $('#earningHistoryTableBody');
    tbody.empty();
    
    earnings.forEach(earning => {
        const row = createEarningRow(earning);
        tbody.append(row);
    });
}

// Update statistics
function updateStatistics(stats) {
    if (stats) {
        $('#totalEarnings').text(parseFloat(stats.total_earnings).toFixed(2) + ' MMK');
        $('#totalTransactions').text(stats.total_transactions.toLocaleString());
        $('#thisMonthEarnings').text(parseFloat(stats.this_month_earnings).toFixed(2) + ' MMK');
        
        // Update the header total as well
        $('.content-section .text-end .h3').text(parseFloat(stats.total_earnings).toFixed(2) + ' MMK');
    }
}

// Show alert message
function showAlert(message, type = 'info') {
    const alertDiv = $(`
        <div class="alert alert-${type} alert-dismissible fade show" role="alert">
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `);
    
    $('.content-section').prepend(alertDiv);
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        alertDiv.remove();
    }, 5000);
}
