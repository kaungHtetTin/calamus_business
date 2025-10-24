<?php
$pageTitle = 'Promotion Code Generator';
include 'layout/header.php';

// Get partner commission rate
$partnerCommissionRate = $currentPartner['commission_rate'] ?? 0;
?>

<!-- Promotion Code Generator Section -->
<div class="content-section">
    <div class="text-center mb-5">
        <h1 class="display-4 text-primary mb-3">
            <i class="fas fa-magic me-3"></i>Promotion Code Generator
        </h1>
        <p class="lead text-muted">Create targeted promotion codes for your courses and packages</p>
    </div>

    <!-- Generator Form -->
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-lg border-0">
                <div class="card-header bg-gradient-theme text-white text-center py-4">
                    <h3 class="mb-0">
                        <i class="fas fa-ticket-alt me-2"></i>Generate New Code
                    </h3>
                    <small class="opacity-75">Commission Rate: <?php echo $partnerCommissionRate; ?>%</small>
                </div>
                <div class="card-body p-5">
                    <!-- Horizontal Step Progress -->
                    <div class="step-progress mb-5">
                        <div class="row">
                            <div class="col-3">
                                <div class="step-item text-center" data-step="1">
                                    <div class="step-circle bg-theme-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-2" style="width: 50px; height: 50px;">
                                        <span class="fw-bold">1</span>
                                    </div>
                                    <h6 class="mb-0">Category</h6>
                                    <small class="text-muted">Select Language</small>
                                </div>
                            </div>
                            <div class="col-3">
                                <div class="step-item text-center" data-step="2">
                                    <div class="step-circle bg-secondary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-2" style="width: 50px; height: 50px;">
                                        <span class="fw-bold">2</span>
                                    </div>
                                    <h6 class="mb-0">Type</h6>
                                    <small class="text-muted">Course/Package</small>
                                </div>
                            </div>
                            <div class="col-3">
                                <div class="step-item text-center" data-step="3">
                                    <div class="step-circle bg-secondary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-2" style="width: 50px; height: 50px;">
                                        <span class="fw-bold">3</span>
                                    </div>
                                    <h6 class="mb-0">Target</h6>
                                    <small class="text-muted">Select Item</small>
                                </div>
                            </div>
                            <div class="col-3">
                                <div class="step-item text-center" data-step="4">
                                    <div class="step-circle bg-secondary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-2" style="width: 50px; height: 50px;">
                                        <span class="fw-bold">4</span>
                                    </div>
                                    <h6 class="mb-0">Generate</h6>
                                    <small class="text-muted">Create Code</small>
                                </div>
                            </div>
                        </div>
                        <div class="progress mt-3" style="height: 4px;">
                            <div class="progress-bar bg-theme-primary" role="progressbar" style="width: 25%" id="progressBar"></div>
                        </div>
                    </div>

                    <form id="promotionCodeForm">
                        <!-- Step 1: Category Selection -->
                        <div class="step-content show" id="step1">
                            <h4 class="text-center mb-4">Select Course Category</h4>
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <div class="category-card" data-category="1">
                                        <div class="card h-100 border-2 category-option" style="cursor: pointer;">
                                            <div class="card-body text-center p-4">
                                                <img src="https://www.calamuseducation.com/appthumbs/eemainicon.png" 
                                                     alt="English" class="mb-3" style="width: 60px; height: 60px;">
                                                <h5 class="card-title">English Language</h5>
                                                <p class="card-text text-muted">Easy English Courses</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <div class="category-card" data-category="2">
                                        <div class="card h-100 border-2 category-option" style="cursor: pointer;">
                                            <div class="card-body text-center p-4">
                                                <img src="https://www.calamuseducation.com/appthumbs/kommmainicon.png" 
                                                     alt="Korean" class="mb-3" style="width: 60px; height: 60px;">
                                                <h5 class="card-title">Korean Language</h5>
                                                <p class="card-text text-muted">Easy Korean Courses</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <div class="category-card" data-category="3">
                                        <div class="card h-100 border-2 category-option" style="cursor: pointer;">
                                            <div class="card-body text-center p-4">
                                                <img src="https://www.calamuseducation.com/uploads/icons/easyjapanesemainicon.png" 
                                                     alt="Japanese" class="mb-3" style="width: 60px; height: 60px;">
                                                <h5 class="card-title">Japanese Language</h5>
                                                <p class="card-text text-muted">Easy Japanese Courses</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" id="selectedCategory" name="category_id">
                        </div>

                        <!-- Step 2: Code Type Selection -->
                        <div class="step-content" id="step2" style="display: none;">
                            <h4 class="text-center mb-4">Select Code Type</h4>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <div class="type-card" data-type="course_purchase">
                                        <div class="card h-100 border-2 type-option" style="cursor: pointer;">
                                            <div class="card-body text-center p-4">
                                                <i class="fas fa-graduation-cap fa-3x text-theme-primary mb-3"></i>
                                                <h5 class="card-title">Course Purchase</h5>
                                                <p class="card-text text-muted">Generate code for individual courses</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="type-card" data-type="package_purchase">
                                        <div class="card h-100 border-2 type-option" style="cursor: pointer;">
                                            <div class="card-body text-center p-4">
                                                <i class="fas fa-box fa-3x text-info mb-3"></i>
                                                <h5 class="card-title">Package Purchase</h5>
                                                <p class="card-text text-muted">Generate code for course packages</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" id="selectedType" name="code_type">
                        </div>

                        <!-- Step 3: Target Selection -->
                        <div class="step-content" id="step3" style="display: none;">
                            <h4 class="text-center mb-4">Select Target</h4>
                            <div class="mb-3">
                                <label for="targetSelect" class="form-label fw-bold">Choose Course/Package</label>
                                <select class="form-select form-select-lg" id="targetSelect" name="target_id">
                                    <option value="">Select...</option>
                                </select>
                            </div>
                        </div>

                        <!-- Step 4: Generate Button -->
                        <div class="step-content" id="step4" style="display: none;">
                            <h4 class="text-center mb-4">Ready to Generate</h4>
                            <div class="text-center">
                                <div class="alert alert-info mb-4">
                                    <i class="fas fa-info-circle me-2"></i>
                                    <strong>Price:</strong> $0.00 (Free promotion code)<br>
                                    <strong>Commission:</strong> <?php echo $partnerCommissionRate; ?>%
                                </div>
                                <button type="submit" class="btn btn-theme-primary btn-lg px-5 py-3">
                                    <i class="fas fa-magic me-2"></i>Generate Promotion Code
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Generated Code Display -->
    <div class="row justify-content-center mt-5" id="generatedCodeSection" style="display: none;">
        <div class="col-lg-6">
            <div class="card border-success shadow-lg">
                <div class="card-header bg-success text-white text-center">
                    <h4 class="mb-0">
                        <i class="fas fa-check-circle me-2"></i>Code Generated Successfully!
                    </h4>
                </div>
                <div class="card-body text-center p-5">
                    <div class="mb-4">
                        <h5 class="text-muted mb-3">Your Promotion Code:</h5>
                        <div class="code-display bg-light border rounded p-4 mb-3">
                            <code id="generatedCode" class="fs-3 fw-bold text-primary"></code>
                        </div>
                        <div class="mb-3">
                            <small class="text-muted">
                                <i class="fas fa-shield-alt me-1"></i>
                                This code is base64 encrypted for security
                            </small>
                        </div>
                        <div class="d-flex gap-2 justify-content-center">
                            <button class="btn btn-outline-theme-primary" id="copyCodeBtn">
                                <i class="fas fa-copy me-2"></i>Copy to Clipboard
                            </button>
                        </div>
                    </div>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Expires:</strong> <span id="expirationDate"></span>
                    </div>
                    <button class="btn btn-theme-primary" onclick="generateNewCode()">
                        <i class="fas fa-plus me-2"></i>Generate Another Code
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Theme Colors */
:root {
    --theme-primary: #667eea;
    --theme-secondary: #764ba2;
    --theme-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.bg-theme-primary {
    background-color: var(--theme-primary) !important;
}

.bg-gradient-theme {
    background: var(--theme-gradient) !important;
}

.text-theme-primary {
    color: var(--theme-primary) !important;
}

.btn-theme-primary {
    background-color: var(--theme-primary);
    border-color: var(--theme-primary);
    color: white;
}

.btn-theme-primary:hover {
    background-color: var(--theme-secondary);
    border-color: var(--theme-secondary);
    color: white;
}

.btn-outline-theme-primary {
    color: var(--theme-primary);
    border-color: var(--theme-primary);
}

.btn-outline-theme-primary:hover {
    background-color: var(--theme-primary);
    border-color: var(--theme-primary);
    color: white;
}

.category-option:hover, .type-option:hover {
    border-color: var(--theme-primary) !important;
    transform: translateY(-2px);
    transition: all 0.3s ease;
}

.category-option.selected, .type-option.selected {
    border-color: var(--theme-primary) !important;
    background-color: #f8f9ff;
}

/* Horizontal Step Progress Styles */
.step-progress {
    position: relative;
}

.step-item {
    position: relative;
    transition: all 0.3s ease;
}

.step-circle {
    transition: all 0.3s ease;
    font-size: 1.2rem;
}

.step-item.active .step-circle {
    background-color: var(--theme-primary) !important;
    transform: scale(1.1);
}

.step-item.completed .step-circle {
    background-color: #198754 !important;
}

.step-item.completed .step-circle::after {
    content: "✓";
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    font-size: 1rem;
}

.step-content {
    opacity: 0;
    transform: translateX(20px);
    transition: all 0.5s ease;
    min-height: 300px;
}

.step-content.show {
    opacity: 1;
    transform: translateX(0);
}

.progress {
    background-color: #e9ecef;
}

.progress-bar {
    transition: width 0.5s ease;
}

.code-display {
    font-family: 'Courier New', monospace;
    letter-spacing: 2px;
}

#copyCodeBtn.copied {
    background-color: #198754;
    border-color: #198754;
    color: white;
}

#copyCodeBtn.copied::after {
    content: " ✓";
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .step-item h6 {
        font-size: 0.9rem;
    }
    
    .step-item small {
        font-size: 0.75rem;
    }
    
    .step-circle {
        width: 40px !important;
        height: 40px !important;
        font-size: 1rem;
    }
}
</style>

<script>
// Global variables
//let sessionToken = window.sessionToken || '';
let selectedCategory = null;
let selectedType = null;
let courses = [];
let packages = [];
let currentStep = 1;

// Initialize page
$(document).ready(function() {
    updateStepProgress();
    
    // Category selection
    $('.category-card').on('click', function() {
        $('.category-option').removeClass('selected');
        $(this).find('.category-option').addClass('selected');
        
        selectedCategory = $(this).data('category');
        $('#selectedCategory').val(selectedCategory);
        
        nextStep();
    });

    // Type selection
    $('.type-card').on('click', function() {
        $('.type-option').removeClass('selected');
        $(this).find('.type-option').addClass('selected');
        
        selectedType = $(this).data('type');
        $('#selectedType').val(selectedType);
        
        loadTargets();
        nextStep();
    });

    // Target selection
    $('#targetSelect').on('change', function() {
        if ($(this).val()) {
            nextStep();
        }
    });
});

// Update step progress indicator
function updateStepProgress() {
    // Update step circles
    $('.step-item').each(function(index) {
        const stepNumber = index + 1;
        const $stepItem = $(this);
        const $stepCircle = $stepItem.find('.step-circle');
        
        if (stepNumber < currentStep) {
            $stepItem.addClass('completed').removeClass('active');
            $stepCircle.removeClass('bg-theme-primary bg-secondary').addClass('bg-success');
        } else if (stepNumber === currentStep) {
            $stepItem.addClass('active').removeClass('completed');
            $stepCircle.removeClass('bg-secondary bg-success').addClass('bg-theme-primary');
        } else {
            $stepItem.removeClass('active completed');
            $stepCircle.removeClass('bg-theme-primary bg-success').addClass('bg-secondary');
        }
    });
    
    // Update progress bar
    const progressPercentage = ((currentStep - 1) / 3) * 100;
    $('#progressBar').css('width', progressPercentage + '%');
}

// Move to next step
function nextStep() {
    if (currentStep < 4) {
        currentStep++;
        showStepContent();
        updateStepProgress();
    }
}

// Show current step content
function showStepContent() {
    $('.step-content').removeClass('show').hide();
    $('#step' + currentStep).addClass('show').show();
}

// Load targets based on category and type
function loadTargets() {
    const endpoint = selectedType === 'course_purchase' ? 'get_courses' : 'get_packages';
    
    fetch('api/course_data.php?endpoint=' + endpoint, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ category_id: selectedCategory })
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            const targets = selectedType === 'course_purchase' ? result.courses : result.packages;
            populateTargetDropdown(targets);
        } else {
            showAlert('Failed to load targets', 'danger');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('Error loading targets', 'danger');
    });
}

// Populate target dropdown
function populateTargetDropdown(targets) {
    const targetSelect = document.getElementById('targetSelect');
    targetSelect.innerHTML = '<option value="">Select...</option>';
    
    targets.forEach(target => {
        const option = document.createElement('option');
        if (selectedType === 'course_purchase') {
            option.value = target.course_id;
            option.textContent = `${target.title} - ${target.fee}`;
        } else {
            option.value = target.id;
            option.textContent = `${target.name} - ${target.price}`;
        }
        targetSelect.appendChild(option);
    });
}

// Handle form submission
document.getElementById('promotionCodeForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const data = Object.fromEntries(formData);
    data.session_token = sessionToken;
    
    // Set target based on type
    if (selectedType === 'course_purchase') {
        data.target_course_id = data.target_id;
        data.target_package_id = null;
    } else {
        data.target_package_id = data.target_id;
        data.target_course_id = null;
    }
    
    // Remove unnecessary fields
    delete data.target_id;
    
    // Set price to 0 (free promotion code)
    data.price = 0;
    
    // Set commission rate from partner
    data.commission_rate = <?php echo $partnerCommissionRate; ?>;
    
    // Set amount_received to 0
    data.amount_received = 0;
    
    // Generate code
    fetch('api/promotion_codes.php?endpoint=generate_code', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            displayGeneratedCode(result.code, result.expired_at);
        } else {
            showAlert(result.message, 'danger');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('An error occurred while generating the code', 'danger');
    });
});

// Display generated code
function displayGeneratedCode(code, expirationDate) {
    document.getElementById('generatedCode').textContent = code;
    document.getElementById('expirationDate').textContent = new Date(expirationDate).toLocaleString();
    
    // Hide form and show result
    document.querySelector('.card.shadow-lg').style.display = 'none';
    document.getElementById('generatedCodeSection').style.display = 'block';
    
    // Scroll to result using jQuery animate
    setTimeout(() => {
        $('html, body').animate({
            scrollTop: $('#generatedCodeSection').offset().top - 50
        }, 800);
    }, 300);
}

// Copy code to clipboard
document.getElementById('copyCodeBtn').addEventListener('click', function() {
    const code = document.getElementById('generatedCode').textContent;
    
    navigator.clipboard.writeText(code).then(() => {
        this.classList.add('copied');
        this.innerHTML = '<i class="fas fa-check me-2"></i>Copied!';
        
        setTimeout(() => {
            this.classList.remove('copied');
            this.innerHTML = '<i class="fas fa-copy me-2"></i>Copy to Clipboard';
        }, 2000);
    }).catch(err => {
        console.error('Failed to copy: ', err);
        showAlert('Failed to copy to clipboard', 'danger');
    });
});

// Generate new code
function generateNewCode() {
    // Reset form
    document.querySelector('.card.shadow-lg').style.display = 'block';
    document.getElementById('generatedCodeSection').style.display = 'none';
    
    // Reset selections
    $('.category-option, .type-option').removeClass('selected');
    $('#selectedCategory, #selectedType').val('');
    $('#targetSelect').html('<option value="">Select...</option>');
    
    // Reset step progress
    currentStep = 1;
    updateStepProgress();
    showStepContent();
    
    // Scroll to top using jQuery animate
    $('html, body').animate({
        scrollTop: 0
    }, 600);
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
</script>

<?php include 'layout/footer.php'; ?>
