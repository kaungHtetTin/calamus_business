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
    
    // Wait for Bootstrap to be available
    function initializeDashboard() {
        if (typeof bootstrap !== 'undefined') {
            // Setup navigation
            setupNavigation();
            
            // Setup mobile navigation (Bootstrap offcanvas)
            setupMobileNavigation();
        } else {
            // Retry after a short delay
            setTimeout(initializeDashboard, 100);
        }
    }
    
    initializeDashboard();
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
    console.log('Setting up mobile navigation...');
    
    // Check if Bootstrap is available
    if (typeof bootstrap === 'undefined') {
        console.error('Bootstrap is not available');
        return;
    }
    
    // Check if offcanvas element exists
    const offcanvasElement = document.getElementById('mobileSidebar');
    if (!offcanvasElement) {
        console.error('Mobile sidebar element not found');
        return;
    }
    
    console.log('Mobile sidebar element found:', offcanvasElement);
    
    // Handle offcanvas events
    $('#mobileSidebar').on('show.bs.offcanvas', function() {
        console.log('Mobile sidebar opening');
    });
    
    $('#mobileSidebar').on('hide.bs.offcanvas', function() {
        console.log('Mobile sidebar closing');
    });
    
    // Test if the toggle button works
    const toggleButton = document.querySelector('[data-bs-toggle="offcanvas"][data-bs-target="#mobileSidebar"]');
    if (toggleButton) {
        console.log('Toggle button found:', toggleButton);
        toggleButton.addEventListener('click', function() {
            console.log('Toggle button clicked');
        });
    } else {
        console.error('Toggle button not found');
    }
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
    // Wait for Bootstrap to be available
    if (typeof bootstrap !== 'undefined' && bootstrap.Offcanvas) {
        const offcanvas = bootstrap.Offcanvas.getInstance(document.getElementById('mobileSidebar'));
        if (offcanvas) {
            offcanvas.hide();
        }
    } else {
        // Fallback: manually hide the offcanvas
        const offcanvasElement = document.getElementById('mobileSidebar');
        if (offcanvasElement) {
            offcanvasElement.classList.remove('show');
            document.body.classList.remove('offcanvas-open');
        }
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
        element.style.color = '#38a169';
        
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

// Test offcanvas functionality
function testOffcanvas() {
    console.log('Testing offcanvas...');
    
    // Check if Bootstrap is available
    if (typeof bootstrap === 'undefined') {
        alert('Bootstrap is not available!');
        return;
    }
    
    // Check if offcanvas element exists
    const offcanvasElement = document.getElementById('mobileSidebar');
    if (!offcanvasElement) {
        alert('Mobile sidebar element not found!');
        return;
    }
    
    // Try to show the offcanvas
    try {
        const offcanvas = new bootstrap.Offcanvas(offcanvasElement);
        offcanvas.show();
        console.log('Offcanvas shown successfully');
    } catch (error) {
        console.error('Error showing offcanvas:', error);
        alert('Error showing offcanvas: ' + error.message);
    }
}