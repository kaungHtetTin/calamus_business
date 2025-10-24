/**
 * Dashboard-Specific JavaScript
 * 
 * This file contains JavaScript functionality specific to the dashboard page.
 * Common functionality is handled in app.js
 */

// Dashboard-specific variables
let monthlyEarningsData = null;

// Initialize dashboard-specific functionality
$(document).ready(function() {
    // Get dashboard data from PHP
    monthlyEarningsData = window.monthlyEarningsData || null;
    
    // Setup dashboard-specific features
    setupDashboardFeatures();
});

// Setup dashboard-specific features
function setupDashboardFeatures() {
    console.log('Setting up dashboard features...');
    
    // Setup dashboard navigation (if needed)
    setupDashboardNavigation();
    
    // Initialize charts or other dashboard-specific features
    initializeDashboardCharts();
}

// Setup dashboard navigation
function setupDashboardNavigation() {
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

// Show specific section (dashboard-specific)
function showSection(section) {
    // Hide all sections
    $('.content-section').hide();
    
    // Show selected section
    $(`#${section}-section`).show();
}

// Initialize dashboard charts
function initializeDashboardCharts() {
    // Initialize any dashboard-specific charts here
    console.log('Dashboard charts initialized');
    
    // Example: Monthly earnings chart
    if (monthlyEarningsData && monthlyEarningsData.length > 0) {
        console.log('Monthly earnings data available:', monthlyEarningsData);
        // Chart initialization code would go here
    }
}