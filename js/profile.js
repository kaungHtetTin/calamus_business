/**
 * Profile Management JavaScript
 * 
 * This file contains all JavaScript functionality for profile management,
 * including profile image upload, preview, and form handling.
 */

// Global variables
let selectedFile = null;

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
    
    // Setup password change form
    setupPasswordChangeForm();
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
    $('.alert-google').remove();
    
    // Map type names for Google style
    let alertType = 'info';
    if (type === 'success') alertType = 'success';
    else if (type === 'danger' || type === 'error') alertType = 'error';
    else alertType = 'info';
    
    // Create new alert with Google styling
    const alertHtml = `
        <div class="alert alert-google alert-${alertType}" role="alert">
            ${message}
        </div>
    `;
    
    // Insert alert at the top of the content wrapper
    if ($('.content-wrapper').length > 0) {
        $('.content-wrapper').prepend(alertHtml);
    } else {
        $('.profile-container').prepend(alertHtml);
    }
    
    // Auto-dismiss after 2 seconds
    setTimeout(function() {
        $('.alert-google').fadeOut(300, function() {
            $(this).remove();
        });
    }, 10000);
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

// Setup password change form functionality
function setupPasswordChangeForm() {
    const passwordForm = document.getElementById('password-change-form');
    if (!passwordForm) return;
    
    $('#changePasswordBtn').click(function() {
          handlePasswordChange();
    });
    
    // Setup password strength indicator
    const newPasswordInput = document.getElementById('newPassword');
    if (newPasswordInput) {
        newPasswordInput.addEventListener('input', function() {
            checkPasswordStrength(this.value);
        });
    }
    
    // Setup password confirmation validation
    const confirmPasswordInput = document.getElementById('confirmPassword');
    if (confirmPasswordInput) {
        confirmPasswordInput.addEventListener('input', function() {
            validatePasswordConfirmation();
        });
    }
}

// Handle password change form submission
function handlePasswordChange() {
    const currentPassword = document.getElementById('currentPassword').value;
    const newPassword = document.getElementById('newPassword').value;
    const confirmPassword = document.getElementById('confirmPassword').value;
    const submitBtn = document.getElementById('changePasswordBtn');

   
    // Clear previous errors
    clearPasswordErrors();
    // Validate inputs
    if (!currentPassword) {
        showPasswordError('currentPassword', 'Current password is required');
        return;
    }
    
    if (!newPassword) {
        showPasswordError('newPassword', 'New password is required');
        return;
    }
    
    if (newPassword.length < 8) {
        showPasswordError('newPassword', 'Password must be at least 8 characters long');
        return;
    }
    
    if (newPassword !== confirmPassword) {
        showPasswordError('confirmPassword', 'Passwords do not match');
        return;
    }
    
    // Check for weak passwords
    const weakPasswords = ['password', '12345678', 'qwerty123', 'abc12345', 'password123'];
    if (weakPasswords.includes(newPassword.toLowerCase())) {
        showPasswordError('newPassword', 'Password is too weak. Please choose a stronger password.');
        return;
    }
    
    // Show loading state
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Changing Password...';
    submitBtn.disabled = true;
    
    // Send request
    fetch('api/change_password.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            currentPassword: currentPassword,
            newPassword: newPassword,
            session_token: sessionToken
        })
    })
    .then(response => response.json())
    .then(data => {
        console.log(data);
        if (data.success) {
            showAlert('Password changed successfully!', 'success');
            // Clear form
            document.getElementById('password-change-form').reset();
            document.getElementById('passwordStrength').style.display = 'none';
        } else {
            if (data.field) {
                showPasswordError(data.field, data.message);
            } else {
                showAlert('Error: ' + data.message, 'danger');
            }
        }
    })
    .catch(error => {
        console.error('Error changing password:', error);
        showAlert('Error changing password. Please try again.', 'danger');
    })
    .finally(() => {
        // Reset button state
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    });
}

// Show password error for specific field
function showPasswordError(fieldId, message) {
    const errorElement = document.getElementById(fieldId + 'Error');
    
    if (errorElement) {
        errorElement.textContent = message;
        errorElement.style.display = 'block';
        
        // Add visual feedback to the input
        const field = document.getElementById(fieldId);
        if (field) {
            field.style.borderColor = '#d93025';
        }
    }
    
    console.log('Error for ' + fieldId + ':', message);
}

// Clear all password errors
function clearPasswordErrors() {
    const fields = ['currentPassword', 'newPassword', 'confirmPassword'];
    fields.forEach(fieldId => {
        const field = document.getElementById(fieldId);
        const errorElement = document.getElementById(fieldId + 'Error');
        
        if (field && errorElement) {
            // Reset input border
            field.style.borderColor = '';
            field.classList.remove('is-invalid');
            
            // Hide error message
            errorElement.textContent = '';
            errorElement.style.display = 'none';
        }
    });
}

// Check password strength
function checkPasswordStrength(password) {
    const strengthContainer = document.getElementById('passwordStrength');
    const strengthBar = document.getElementById('strengthBar');
    const strengthText = document.getElementById('strengthText');
    
    if (!password) {
        strengthContainer.style.display = 'none';
        return;
    }
    
    strengthContainer.style.display = 'block';
    
    let strength = 0;
    let strengthLabel = '';
    let strengthClass = '';
    
    // Length check
    if (password.length >= 8) strength += 20;
    if (password.length >= 12) strength += 10;
    
    // Character variety checks
    if (/[a-z]/.test(password)) strength += 10;
    if (/[A-Z]/.test(password)) strength += 10;
    if (/[0-9]/.test(password)) strength += 10;
    if (/[^A-Za-z0-9]/.test(password)) strength += 10;
    
    // Additional checks
    if (password.length >= 16) strength += 10;
    if (/(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[^A-Za-z0-9])/.test(password)) strength += 10;
    
    // Determine strength level
    if (strength < 30) {
        strengthLabel = 'Weak';
        strengthClass = 'strength-weak';
    } else if (strength < 60) {
        strengthLabel = 'Fair';
        strengthClass = 'strength-fair';
    } else if (strength < 80) {
        strengthLabel = 'Good';
        strengthClass = 'strength-good';
    } else {
        strengthLabel = 'Strong';
        strengthClass = 'strength-strong';
    }
    
    // Update UI
    strengthBar.style.width = strength + '%';
    strengthBar.className = 'strength-fill ' + strengthClass;
    strengthText.textContent = `Password strength: ${strengthLabel}`;
}

// Validate password confirmation
function validatePasswordConfirmation() {
    const newPassword = document.getElementById('newPassword').value;
    const confirmPassword = document.getElementById('confirmPassword').value;
    
    if (confirmPassword && newPassword !== confirmPassword) {
        showPasswordError('confirmPassword', 'Passwords do not match');
    } else {
        const confirmField = document.getElementById('confirmPassword');
        const confirmError = document.getElementById('confirmPasswordError');
        if (confirmField && confirmError) {
            confirmField.classList.remove('is-invalid');
            confirmError.textContent = '';
        }
    }
}

// Toggle password visibility
function togglePassword(fieldId) {
    const field = document.getElementById(fieldId);
    const icon = document.getElementById(fieldId + 'Icon');
    
    if (field && icon) {
        if (field.type === 'password') {
            field.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            field.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }
}
