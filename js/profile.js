/**
 * Profile Management JavaScript
 * 
 * This file contains all JavaScript functionality for profile management,
 * including profile image upload, preview, and form handling.
 */

// Global variables
let selectedFile = null;
let sessionToken = null;

// Initialize profile page
$(document).ready(function() {
    // Get session token from PHP
    sessionToken = window.sessionToken || '';
    
    // Setup profile image upload
    setupProfileImageUpload();
    
    // Setup profile form submission
    setupProfileFormSubmission();
    
    // Setup image removal
    setupImageRemoval();
});

// Setup profile image upload functionality
function setupProfileImageUpload() {
    const imageInput = document.getElementById('profileImageInput');
    const uploadBtn = document.getElementById('uploadImageBtn');
    const preview = document.getElementById('profileImagePreview');
    
    if (!imageInput || !uploadBtn || !preview) return;
    
    // Handle file selection
    imageInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        
        if (file) {
            // Validate file type
            const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
            if (!allowedTypes.includes(file.type)) {
                showAlert('Invalid file type. Please select a JPG, PNG, GIF, or WebP image.', 'danger');
                imageInput.value = '';
                return;
            }
            
            // Validate file size (5MB max)
            if (file.size > 5 * 1024 * 1024) {
                showAlert('File size too large. Please select an image smaller than 5MB.', 'danger');
                imageInput.value = '';
                return;
            }
            
            selectedFile = file;
            uploadBtn.disabled = false;
            
            // Preview image
            previewImage(file, preview);
        } else {
            selectedFile = null;
            uploadBtn.disabled = true;
        }
    });
    
    // Handle upload button click
    uploadBtn.addEventListener('click', function() {
        if (selectedFile) {
            uploadProfileImage(selectedFile);
        }
    });
}

// Preview selected image
function previewImage(file, previewElement) {
    const reader = new FileReader();
    
    reader.onload = function(e) {
        if (previewElement.tagName === 'IMG') {
            previewElement.src = e.target.result;
        } else {
            // Replace placeholder with image
            previewElement.innerHTML = `<img src="${e.target.result}" alt="Profile Picture Preview" class="profile-image rounded-circle">`;
        }
    };
    
    reader.readAsDataURL(file);
}

// Upload profile image
function uploadProfileImage(file) {
    const formData = new FormData();
    formData.append('profile_image', file);
    formData.append('session_token', sessionToken);
    
    // Show loading state
    const uploadBtn = document.getElementById('uploadImageBtn');
    const originalText = uploadBtn.innerHTML;
    uploadBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Uploading...';
    uploadBtn.disabled = true;
    
    $.ajax({
        url: 'api/update_profile.php',
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            if (response.success) {
                showAlert('Profile image uploaded successfully!', 'success');
                
                // Update the image source if provided
                if (response.profile_image) {
                    const preview = document.getElementById('profileImagePreview');
                    if (preview.tagName === 'IMG') {
                        preview.src = response.profile_image;
                    } else {
                        preview.innerHTML = `<img src="${response.profile_image}" alt="Profile Picture" class="profile-image rounded-circle">`;
                    }
                }
                
                // Show remove button if it doesn't exist
                if (!document.getElementById('removeImageBtn')) {
                    const uploadBtn = document.getElementById('uploadImageBtn');
                    const removeBtn = document.createElement('button');
                    removeBtn.type = 'button';
                    removeBtn.className = 'btn btn-outline-danger ms-2';
                    removeBtn.id = 'removeImageBtn';
                    removeBtn.innerHTML = '<i class="fas fa-trash me-2"></i>Remove Image';
                    removeBtn.addEventListener('click', removeProfileImage);
                    uploadBtn.parentNode.appendChild(removeBtn);
                }
                
                // Clear file input
                document.getElementById('profileImageInput').value = '';
                selectedFile = null;
            } else {
                showAlert('Error: ' + response.message, 'danger');
            }
        },
        error: function(xhr, status, error) {
            console.error('Error uploading image:', error);
            showAlert('Error uploading image. Please try again.', 'danger');
        },
        complete: function() {
            // Reset button state
            uploadBtn.innerHTML = originalText;
            uploadBtn.disabled = false;
        }
    });
}

// Setup image removal functionality
function setupImageRemoval() {
    const removeBtn = document.getElementById('removeImageBtn');
    if (removeBtn) {
        removeBtn.addEventListener('click', removeProfileImage);
    }
}

// Remove profile image
function removeProfileImage() {
    if (!confirm('Are you sure you want to remove your profile image?')) {
        return;
    }
    
    const formData = new FormData();
    formData.append('remove_image', '1');
    formData.append('session_token', sessionToken);
    
    $.ajax({
        url: 'api/update_profile.php',
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            if (response.success) {
                showAlert('Profile image removed successfully!', 'success');
                
                // Reset to placeholder
                const preview = document.getElementById('profileImagePreview');
                preview.innerHTML = '<div class="profile-image-placeholder rounded-circle d-flex align-items-center justify-content-center"><i class="fas fa-user fa-3x text-muted"></i></div>';
                
                // Remove the remove button
                const removeBtn = document.getElementById('removeImageBtn');
                if (removeBtn) {
                    removeBtn.remove();
                }
                
                // Clear file input
                document.getElementById('profileImageInput').value = '';
                selectedFile = null;
            } else {
                showAlert('Error: ' + response.message, 'danger');
            }
        },
        error: function(xhr, status, error) {
            console.error('Error removing image:', error);
            showAlert('Error removing image. Please try again.', 'danger');
        }
    });
}

// Setup profile form submission
function setupProfileFormSubmission() {
    $('#profile-form').on('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        formData.append('session_token', sessionToken);
        
        // Show loading state
        const submitBtn = $(this).find('button[type="submit"]');
        const originalText = submitBtn.html();
        submitBtn.html('<i class="fas fa-spinner fa-spin me-2"></i>Updating...');
        submitBtn.prop('disabled', true);
        
        $.ajax({
            url: 'api/update_profile.php',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    showAlert('Profile updated successfully!', 'success');
                } else {
                    showAlert('Error: ' + response.message, 'danger');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error updating profile:', error);
                showAlert('Error updating profile. Please try again.', 'danger');
            },
            complete: function() {
                // Reset button state
                submitBtn.html(originalText);
                submitBtn.prop('disabled', false);
            }
        });
    });
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

// Copy private code to clipboard
function copyPrivateCode() {
    const privateCodeElement = document.getElementById('privateCodeDisplay');
    if (!privateCodeElement) return;
    
    const privateCode = privateCodeElement.textContent.trim();
    
    // Use the Clipboard API if available
    if (navigator.clipboard && window.isSecureContext) {
        navigator.clipboard.writeText(privateCode).then(function() {
            showAlert('Private code copied to clipboard!', 'success');
        }).catch(function(err) {
            console.error('Failed to copy: ', err);
            fallbackCopyTextToClipboard(privateCode);
        });
    } else {
        // Fallback for older browsers
        fallbackCopyTextToClipboard(privateCode);
    }
}

// Fallback copy function for older browsers
function fallbackCopyTextToClipboard(text) {
    const textArea = document.createElement('textarea');
    textArea.value = text;
    textArea.style.position = 'fixed';
    textArea.style.left = '-999999px';
    textArea.style.top = '-999999px';
    document.body.appendChild(textArea);
    textArea.focus();
    textArea.select();
    
    try {
        const successful = document.execCommand('copy');
        if (successful) {
            showAlert('Private code copied to clipboard!', 'success');
        } else {
            showAlert('Failed to copy private code', 'danger');
        }
    } catch (err) {
        console.error('Fallback: Oops, unable to copy', err);
        showAlert('Failed to copy private code', 'danger');
    }
    
    document.body.removeChild(textArea);
}
