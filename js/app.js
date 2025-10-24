/**
 * Common Application JavaScript
 * 
 * This file contains common JavaScript functionality used across all pages.
 * It handles navigation, mobile sidebar, and other shared features.
 */

console.log('App.js: File loaded successfully');

// Global variables
let sessionToken = null; // Will be set from PHP

// Initialize common app functionality
$(document).ready(function() {
    console.log('App.js: Document ready');
    
    // Get session token from PHP
    sessionToken = window.sessionToken || '';
    console.log('App.js: Session token:', sessionToken);
    
    // Wait for Bootstrap to be available
    setupCommonNavigation();
    // Setup mobile navigation (Bootstrap offcanvas)
    setupMobileNavigation();
    
    
});

// Setup common navigation functionality
function setupCommonNavigation() {
    console.log('Setting up common navigation...');
    
    // Handle navigation links that close mobile sidebar
    $('.nav-link').on('click', function() {
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