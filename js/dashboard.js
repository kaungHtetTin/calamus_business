/**
 * Partner Dashboard JavaScript
 * 
 * This file contains all JavaScript functionality for the partner dashboard.
 * It handles navigation, mobile sidebar using Bootstrap offcanvas, and basic interactions.
 */

// Global variables
let sessionToken = null; // Will be set from PHP

// Initialize dashboard
$(document).ready(function() {
    // Get session token from PHP
    sessionToken = window.sessionToken || '';
    
    // Setup navigation
    setupNavigation();
    
    // Setup mobile navigation (Bootstrap offcanvas)
    setupMobileNavigation();
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
}

// Show specific section
function showSection(section) {
    // Hide all sections
    $('.content-section').hide();
    
    // Show selected section
    $(`#${section}-section`).show();
}

// Close mobile sidebar
function closeMobileSidebar() {
    const offcanvas = bootstrap.Offcanvas.getInstance(document.getElementById('mobileSidebar'));
    if (offcanvas) {
        offcanvas.hide();
    }
}

// Copy to clipboard utility
function copyToClipboard(element) {
    element.select();
    element.setSelectionRange(0, 99999); // For mobile devices
    
    try {
        document.execCommand('copy');
        
        // Show success feedback
        const originalText = element.value;
        element.value = 'Copied!';
        element.style.color = '#28a745';
        
        setTimeout(() => {
            element.value = originalText;
            element.style.color = '';
        }, 1000);
        
    } catch (err) {
        console.error('Failed to copy: ', err);
        alert('Failed to copy to clipboard');
    }
}

// Show alert message
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