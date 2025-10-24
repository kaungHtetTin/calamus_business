/**
 * Partner Dashboard JavaScript
 * 
 * This file contains all JavaScript functionality for the partner dashboard.
 * It handles navigation, mobile sidebar using Bootstrap offcanvas, charts, and API interactions.
 */

// Global variables
let earningsChart = null;
let sessionToken = null; // Will be set from PHP

// Initialize dashboard
$(document).ready(function() {
    // Get session token from PHP
    sessionToken = window.sessionToken || '';
    
    // Setup navigation
    setupNavigation();
    
    // Setup mobile navigation (Bootstrap offcanvas)
    setupMobileNavigation();
    
    // Load earnings chart
    loadEarningsChart();
});

// Setup navigation
function setupNavigation() {
    $('.nav-link[data-section]').on('click', function(e) {
        e.preventDefault();
        const section = $(this).data('section');
        showSection(section);
        
        // Update active state
        $('.nav-link').removeClass('active');
        $(this).addClass('active');
        
        // Close mobile sidebar after navigation
        closeMobileSidebar();
    });
}

// Setup mobile navigation using Bootstrap offcanvas
function setupMobileNavigation() {
    // Handle offcanvas events
    $('#mobileSidebar').on('show.bs.offcanvas', function() {
        console.log('Mobile sidebar opening');
    });
    
    $('#mobileSidebar').on('hide.bs.offcanvas', function() {
        console.log('Mobile sidebar closing');
    });
    
    // Handle navigation clicks in mobile sidebar
    $('#mobileSidebar .nav-link[data-section]').on('click', function() {
        // Close the offcanvas after navigation
        const offcanvas = bootstrap.Offcanvas.getInstance($('#mobileSidebar')[0]);
        if (offcanvas) {
            offcanvas.hide();
        }
    });
}

// Close mobile sidebar
function closeMobileSidebar() {
    const offcanvas = bootstrap.Offcanvas.getInstance($('#mobileSidebar')[0]);
    if (offcanvas) {
        offcanvas.hide();
    }
}

// Show section
function showSection(sectionName) {
    $('.content-section').hide();
    $(`#${sectionName}-section`).show();
}

// Load earnings chart
function loadEarningsChart() {
    const ctx = document.getElementById('earningsChart');
    if (!ctx) {
        console.error('Earnings chart canvas not found');
        return;
    }
    
    const monthlyData = window.monthlyEarningsData || [];
    
    const labels = monthlyData.map(item => item.month);
    const earnings = monthlyData.map(item => parseFloat(item.earnings));
    
    earningsChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Monthly Earnings',
                data: earnings,
                borderColor: '#667eea',
                backgroundColor: 'rgba(102, 126, 234, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return '$' + value.toFixed(2);
                        }
                    }
                }
            }
        }
    });
}

// Show create link modal
function showCreateLinkModal() {
    const modal = new bootstrap.Modal(document.getElementById('createLinkModal'));
    modal.show();
}

// Show create code modal
function showCreateCodeModal() {
    const modal = new bootstrap.Modal(document.getElementById('createCodeModal'));
    modal.show();
}

// Create affiliate link
function createAffiliateLink() {
    const formData = {
        campaign_name: $('#campaign_name').val(),
        target_course_id: $('#target_course').val() || null,
        target_major: $('#target_major').val() || null,
        custom_url: $('#custom_url').val() || ''
    };
    
    $.ajax({
        url: 'api/create_affiliate_link.php',
        method: 'POST',
        contentType: 'application/json',
        data: JSON.stringify({
            session_token: sessionToken,
            ...formData
        }),
        success: function(data) {
            if (data.success) {
                alert('Affiliate link created successfully!');
                bootstrap.Modal.getInstance(document.getElementById('createLinkModal')).hide();
                location.reload(); // Reload to show new link
            } else {
                alert('Error: ' + data.message);
            }
        },
        error: function(xhr, status, error) {
            console.error('Error creating link:', error);
            alert('Error creating affiliate link');
        }
    });
}

// Generate promotion code
function generatePromotionCode() {
    const formData = {
        code_type: $('#code_type').val(),
        target_course_id: $('#code_target_course').val() || null,
        target_major: $('#code_target_major').val() || null,
        package_id: $('#code_target_package').val() || null,
        client_name: $('#client_name').val() || '',
        expires_at: $('#expires_at').val() || null
    };
    
    if (!formData.code_type) {
        alert('Please select a code type');
        return;
    }
    
    $.ajax({
        url: 'api/generate_promotion_code.php',
        method: 'POST',
        contentType: 'application/json',
        data: JSON.stringify({
            session_token: sessionToken,
            ...formData
        }),
        success: function(data) {
            if (data.success) {
                alert('Promotion code generated successfully!\nCode: ' + data.code);
                bootstrap.Modal.getInstance(document.getElementById('createCodeModal')).hide();
                location.reload(); // Reload to show new code
            } else {
                alert('Error: ' + data.message);
            }
        },
        error: function(xhr, status, error) {
            console.error('Error generating code:', error);
            alert('Error generating promotion code');
        }
    });
}

// Copy code to clipboard
function copyCode(code) {
    navigator.clipboard.writeText(code).then(() => {
        // Visual feedback
        const button = event.target.closest('button');
        const originalHTML = button.innerHTML;
        button.innerHTML = '<i class="fas fa-check"></i>';
        button.classList.remove('btn-outline-secondary');
        button.classList.add('btn-success');
        
        setTimeout(() => {
            button.innerHTML = originalHTML;
            button.classList.remove('btn-success');
            button.classList.add('btn-outline-secondary');
        }, 2000);
    }).catch(err => {
        console.error('Failed to copy code: ', err);
        alert('Failed to copy code');
    });
}

// Filter codes by status
function filterCodes(status) {
    $('#codes-list tr').each(function() {
        const row = $(this);
        if (status === 'all' || row.data('status') === status) {
            row.show();
        } else {
            row.hide();
        }
    });
    
    // Update button states
    $('.btn-group button').removeClass('active');
    event.target.classList.add('active');
}

// Cancel promotion code
function cancelCode(codeId) {
    if (!confirm('Are you sure you want to cancel this promotion code?')) {
        return;
    }
    
    $.ajax({
        url: 'api/cancel_promotion_code.php',
        method: 'POST',
        contentType: 'application/json',
        data: JSON.stringify({
            session_token: sessionToken,
            code_id: codeId
        }),
        success: function(data) {
            if (data.success) {
                alert('Promotion code cancelled successfully');
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        },
        error: function(xhr, status, error) {
            console.error('Error cancelling code:', error);
            alert('Error cancelling promotion code');
        }
    });
}

// Profile form submission
$(document).ready(function() {
    $('#profile-form').on('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        formData.append('session_token', sessionToken);
        
        $.ajax({
            url: 'api/update_profile.php',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(data) {
                if (data.success) {
                    alert('Profile updated successfully!');
                } else {
                    alert('Error: ' + data.message);
                }
            },
            error: function(xhr, status, error) {
                console.error('Error updating profile:', error);
                alert('Error updating profile');
            }
        });
    });
});

// Copy to clipboard utility
function copyToClipboard(element) {
    element.select();
    element.setSelectionRange(0, 99999); // For mobile devices
    
    try {
        document.execCommand('copy');
        // Visual feedback
        const button = element.nextElementSibling;
        if (button) {
            const originalHTML = button.innerHTML;
            button.innerHTML = '<i class="fas fa-check"></i>';
            button.classList.remove('btn-outline-secondary');
            button.classList.add('btn-success');
            
            setTimeout(() => {
                button.innerHTML = originalHTML;
                button.classList.remove('btn-success');
                button.classList.add('btn-outline-secondary');
            }, 2000);
        }
    } catch (err) {
        console.error('Failed to copy: ', err);
        alert('Failed to copy to clipboard');
    }
}
