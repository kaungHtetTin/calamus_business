<?php
$pageTitle = 'Promotion Code Generator';
include 'layout/header.php';

// Get partner commission rate
$partnerCommissionRate = $currentPartner['commission_rate'] ?? 0;
?>

<!-- Promotion Code Generator Section -->
<div class="content-section">
    <!-- Header -->
    <div class="page-header mb-4">
        <div class="d-flex align-items-center justify-content-between">
            <div>
                <h1 class="page-title mb-1">Promotion Code Generator</h1>
                <p class="page-subtitle text-muted">Create targeted promotion codes for your courses and packages</p>
            </div>
            <div class="commission-badge">
                <span class="badge bg-light text-dark"><?php echo $partnerCommissionRate; ?>% Commission</span>
            </div>
        </div>
    </div>

    <!-- Generator Form -->
    <div class="generator-container">
        <div class="generator-card">
            <!-- Step Progress -->
            <div class="step-progress">
                <div class="step-track">
                    <div class="step-item" data-step="1">
                        <div class="step-circle">
                            <span class="step-number">1</span>
                        </div>
                        <div class="step-label">
                            <span class="step-title">Category</span>
                            <span class="step-desc">Language</span>
                        </div>
                    </div>
                    <div class="step-item" data-step="2">
                        <div class="step-circle">
                            <span class="step-number">2</span>
                        </div>
                        <div class="step-label">
                            <span class="step-title">Type</span>
                            <span class="step-desc">Course/Package</span>
                        </div>
                    </div>
                    <div class="step-item" data-step="3">
                        <div class="step-circle">
                            <span class="step-number">3</span>
                        </div>
                        <div class="step-label">
                            <span class="step-title">Target</span>
                            <span class="step-desc">Select Item</span>
                        </div>
                    </div>
                    <div class="step-item" data-step="4">
                        <div class="step-circle">
                            <span class="step-number">4</span>
                        </div>
                        <div class="step-label">
                            <span class="step-title">Generate</span>
                            <span class="step-desc">Create Code</span>
                        </div>
                    </div>
                </div>
                <div class="progress-line">
                    <div class="progress-fill" id="progressBar"></div>
                </div>
            </div>

            <!-- Form Content -->
            <form id="promotionCodeForm">
                <!-- Step 1: Category Selection -->
                <div class="step-content show" id="step1">
                    <div class="step-header">
                        <h3 class="step-title">Select Course Category</h3>
                        <p class="step-description">Choose the language category for your promotion code</p>
                    </div>
                    <div class="category-grid">
                        <div class="category-card" data-category="1">
                            <div class="category-option">
                                <div class="category-icon">
                                    <img src="https://www.calamuseducation.com/appthumbs/eemainicon.png" alt="English">
                                </div>
                                <div class="category-info">
                                    <h4 class="category-name">English Language</h4>
                                    <p class="category-desc">Easy English Courses</p>
                                </div>
                            </div>
                        </div>
                        <div class="category-card" data-category="2">
                            <div class="category-option">
                                <div class="category-icon">
                                    <img src="https://www.calamuseducation.com/appthumbs/kommmainicon.png" alt="Korean">
                                </div>
                                <div class="category-info">
                                    <h4 class="category-name">Korean Language</h4>
                                    <p class="category-desc">Easy Korean Courses</p>
                                </div>
                            </div>
                        </div>
                        <div class="category-card" data-category="3">
                            <div class="category-option">
                                <div class="category-icon">
                                    <img src="https://www.calamuseducation.com/uploads/icons/easyjapanesemainicon.png" alt="Japanese">
                                </div>
                                <div class="category-info">
                                    <h4 class="category-name">Japanese Language</h4>
                                    <p class="category-desc">Easy Japanese Courses</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" id="selectedCategory" name="category_id">
                </div>

                <!-- Step 2: Code Type Selection -->
                <div class="step-content" id="step2" style="display: none;">
                    <div class="step-header">
                        <h3 class="step-title">Select Code Type</h3>
                        <p class="step-description">Choose whether to generate a code for individual courses or packages</p>
                    </div>
                    <div class="type-grid">
                        <div class="type-card" data-type="course_purchase">
                            <div class="type-option">
                                <div class="type-icon">
                                    <i class="fas fa-graduation-cap"></i>
                                </div>
                                <div class="type-info">
                                    <h4 class="type-name">Course Purchase</h4>
                                    <p class="type-desc">Generate code for individual courses</p>
                                </div>
                            </div>
                        </div>
                        <div class="type-card" data-type="package_purchase">
                            <div class="type-option">
                                <div class="type-icon">
                                    <i class="fas fa-box"></i>
                                </div>
                                <div class="type-info">
                                    <h4 class="type-name">Package Purchase</h4>
                                    <p class="type-desc">Generate code for course packages</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" id="selectedType" name="code_type">
                </div>

                <!-- Step 3: Target Selection -->
                <div class="step-content" id="step3" style="display: none;">
                    <div class="step-header">
                        <h3 class="step-title">Select Target</h3>
                        <p class="step-description">Choose the specific course or package for your promotion code</p>
                    </div>
                    <div class="target-selection">
                        <label for="targetSelect" class="form-label">Course/Package</label>
                        <select class="form-select" id="targetSelect" name="target_id">
                            <option value="">Select...</option>
                        </select>
                    </div>
                </div>

                <!-- Step 4: Generate Button -->
                <div class="step-content" id="step4" style="display: none;">
                    <div class="step-header">
                        <h3 class="step-title">Ready to Generate</h3>
                        <p class="step-description">Review your settings and generate the promotion code</p>
                    </div>
                    <div class="generation-summary">
                        <div class="summary-item">
                            <span class="summary-label">Price:</span>
                            <span class="summary-value" id="selectedPrice">Select a target to see price</span>
                        </div>
                        <div class="summary-item">
                            <span class="summary-label">Commission:</span>
                            <span class="summary-value"><?php echo $partnerCommissionRate; ?>%</span>
                        </div>
                        <div class="summary-item">
                            <span class="summary-label">Commission Amount:</span>
                            <span class="summary-value" id="commissionAmount">-</span>
                        </div>
                    </div>
                    <div class="generate-action">
                        <button type="submit" class="btn-generate">
                            <i class="fas fa-magic"></i>
                            <span>Generate Promotion Code</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Generated Code Display -->
    <div class="result-container" id="generatedCodeSection" style="display: none;">
        <div class="result-card">
            <div class="result-header">
                <div class="result-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="result-info">
                    <h3 class="result-title">Code Generated Successfully!</h3>
                    <p class="result-subtitle">Your promotion code is ready to use</p>
                </div>
            </div>
            <div class="result-content">
                <div class="code-display">
                    <div class="code-label">Promotion Code:</div>
                    <div class="code-value" id="generatedCode"></div>
                    <div class="code-security">
                        <i class="fas fa-shield-alt"></i>
                        <span>Base64 encrypted for security</span>
                    </div>
                </div>
                <div class="code-actions">
                    <button class="btn-copy" id="copyCodeBtn">
                        <i class="fas fa-copy"></i>
                        <span>Copy to Clipboard</span>
                    </button>
                </div>
                <div class="code-info">
                    <div class="info-item">
                        <i class="fas fa-clock"></i>
                        <span>Expires: <strong id="expirationDate"></strong></span>
                    </div>
                </div>
                <div class="result-actions">
                    <button class="btn-secondary" onclick="generateNewCode()">
                        <i class="fas fa-plus"></i>
                        <span>Generate Another Code</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Modern Clean Design - Light Gray Theme */
:root {
    --primary: #4a5568;
    --primary-dark: #2d3748;
    --primary-light: #718096;
    --secondary: #718096;
    --secondary-dark: #4a5568;
    --secondary-light: #a0aec0;
    --accent: #4a5568;
    --accent-dark: #2d3748;
    --success: #38a169;
    --success-dark: #2f855a;
    --warning: #ed8936;
    --warning-dark: #dd6b20;
    --danger: #e53e3e;
    --danger-dark: #c53030;
    --gray-50: #f9fafb;
    --gray-100: #f3f4f6;
    --gray-200: #e5e7eb;
    --gray-300: #d1d5db;
    --gray-400: #9ca3af;
    --gray-500: #6b7280;
    --gray-600: #4b5563;
    --gray-700: #374151;
    --gray-800: #1f2937;
    --gray-900: #111827;
    --white: #ffffff;
    --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
    --shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
    --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    --radius: 8px;
    --radius-lg: 12px;
}

/* Page Header */
.page-header {
    margin-bottom: 2rem;
}

.page-title {
    font-size: 1.875rem;
    font-weight: 700;
    color: var(--gray-900);
    margin: 0;
}

.page-subtitle {
    font-size: 1rem;
    color: var(--gray-500);
    margin: 0;
}

.commission-badge .badge {
    font-size: 0.875rem;
    font-weight: 500;
    padding: 0.5rem 1rem;
    border-radius: var(--radius);
}

/* Generator Container */
.generator-container {
    max-width: 800px;
    margin: 0 auto;
}

.generator-card {
    background: var(--white);
    border: 1px solid var(--gray-200);
    border-radius: var(--radius-lg);
    padding: 2rem;
    box-shadow: var(--shadow-sm);
}

/* Step Progress */
.step-progress {
    margin-bottom: 3rem;
}

.step-track {
    display: flex;
    justify-content: space-between;
    align-items: center;
    position: relative;
    margin-bottom: 1rem;
}

.step-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    position: relative;
    z-index: 2;
}

.step-circle {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: var(--gray-200);
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 0.5rem;
    transition: all 0.3s ease;
}

.step-number {
    font-size: 0.875rem;
    font-weight: 600;
    color: var(--gray-500);
}

.step-item.active .step-circle {
    background: var(--primary);
}

.step-item.active .step-number {
    color: var(--white);
}

.step-item.completed .step-circle {
    background: var(--success);
}

.step-item.completed .step-number {
    color: var(--white);
}

.step-label {
    text-align: center;
}

.step-title {
    font-size: 0.875rem;
    font-weight: 600;
    color: var(--gray-700);
    margin: 0;
}

.step-desc {
    font-size: 0.75rem;
    color: var(--gray-500);
    margin: 0;
}

.progress-line {
    height: 2px;
    background: var(--gray-200);
    border-radius: 1px;
    position: relative;
}

.progress-fill {
    height: 100%;
    background: var(--primary);
    border-radius: 1px;
    transition: width 0.5s ease;
    width: 25%;
}

/* Step Content */
.step-content {
    opacity: 0;
    transform: translateY(20px);
    transition: all 0.4s ease;
    min-height: 300px;
}

.step-content.show {
    opacity: 1;
    transform: translateY(0);
}

.step-header {
    margin-bottom: 2rem;
}

.step-title {
    font-size: 1.5rem;
    font-weight: 600;
    color: var(--gray-900);
    margin: 0 0 0.5rem 0;
}

.step-description {
    font-size: 1rem;
    color: var(--gray-500);
    margin: 0;
}

/* Category Grid */
.category-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
}

.category-card {
    cursor: pointer;
}

.category-option {
    padding: 1.5rem;
    border: 2px solid var(--gray-200);
    border-radius: var(--radius);
    background: var(--white);
    transition: all 0.2s ease;
    display: flex;
    align-items: center;
    gap: 1rem;
}

.category-option:hover {
    border-color: var(--primary);
    box-shadow: var(--shadow);
}

.category-option.selected {
    border-color: var(--primary);
    background: rgba(102, 126, 234, 0.05);
}

.category-icon {
    width: 48px;
    height: 48px;
    border-radius: var(--radius);
    overflow: hidden;
    flex-shrink: 0;
}

.category-icon img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.category-info {
    flex: 1;
}

.category-name {
    font-size: 1rem;
    font-weight: 600;
    color: var(--gray-900);
    margin: 0 0 0.25rem 0;
}

.category-desc {
    font-size: 0.875rem;
    color: var(--gray-500);
    margin: 0;
}

/* Type Grid */
.type-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1rem;
}

.type-card {
    cursor: pointer;
}

.type-option {
    padding: 1.5rem;
    border: 2px solid var(--gray-200);
    border-radius: var(--radius);
    background: var(--white);
    transition: all 0.2s ease;
    display: flex;
    align-items: center;
    gap: 1rem;
}

.type-option:hover {
    border-color: var(--primary);
    box-shadow: var(--shadow);
}

.type-option.selected {
    border-color: var(--primary);
    background: rgba(102, 126, 234, 0.05);
}

.type-icon {
    width: 48px;
    height: 48px;
    border-radius: var(--radius);
    background: var(--gray-100);
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.type-icon i {
    font-size: 1.5rem;
    color: var(--primary);
}

.type-info {
    flex: 1;
}

.type-name {
    font-size: 1rem;
    font-weight: 600;
    color: var(--gray-900);
    margin: 0 0 0.25rem 0;
}

.type-desc {
    font-size: 0.875rem;
    color: var(--gray-500);
    margin: 0;
}

/* Target Selection */
.target-selection {
    max-width: 400px;
}

.form-label {
    font-size: 0.875rem;
    font-weight: 600;
    color: var(--gray-700);
    margin-bottom: 0.5rem;
}

.form-select {
    width: 100%;
    padding: 0.75rem 1rem;
    border: 1px solid var(--gray-300);
    border-radius: var(--radius);
    background: var(--white);
    font-size: 1rem;
    transition: border-color 0.2s ease;
}

.form-select:focus {
    outline: none;
    border-color: var(--primary);
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

/* Generation Summary */
.generation-summary {
    background: var(--gray-50);
    border-radius: var(--radius);
    padding: 1.5rem;
    margin-bottom: 2rem;
}

.summary-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 0.5rem;
}

.summary-item:last-child {
    margin-bottom: 0;
}

.summary-label {
    font-size: 0.875rem;
    color: var(--gray-600);
}

.summary-value {
    font-size: 0.875rem;
    font-weight: 600;
    color: var(--gray-900);
}

/* Generate Button */
.generate-action {
    text-align: center;
}

.btn-generate {
    background: var(--primary);
    color: var(--white);
    border: none;
    border-radius: var(--radius);
    padding: 1rem 2rem;
    font-size: 1rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s ease;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
}

.btn-generate:hover {
    background: var(--primary-dark);
    transform: translateY(-1px);
    box-shadow: var(--shadow-md);
}

/* Result Container */
.result-container {
    max-width: 600px;
    margin: 2rem auto 0;
}

.result-card {
    background: var(--white);
    border: 1px solid var(--gray-200);
    border-radius: var(--radius-lg);
    padding: 2rem;
    box-shadow: var(--shadow-sm);
}

.result-header {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin-bottom: 2rem;
}

.result-icon {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    background: var(--success);
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.result-icon i {
    font-size: 1.5rem;
    color: var(--white);
}

.result-info {
    flex: 1;
}

.result-title {
    font-size: 1.25rem;
    font-weight: 600;
    color: var(--gray-900);
    margin: 0 0 0.25rem 0;
}

.result-subtitle {
    font-size: 0.875rem;
    color: var(--gray-500);
    margin: 0;
}

/* Code Display */
.code-display {
    background: var(--gray-50);
    border: 1px solid var(--gray-200);
    border-radius: var(--radius);
    padding: 1.5rem;
    margin-bottom: 1.5rem;
    text-align: center;
}

.code-label {
    font-size: 0.875rem;
    color: var(--gray-600);
    margin-bottom: 0.5rem;
}

.code-value {
    font-family: 'Monaco', 'Menlo', 'Ubuntu Mono', monospace;
    font-size: 1.25rem;
    font-weight: 600;
    color: var(--primary);
    letter-spacing: 1px;
    margin-bottom: 0.5rem;
    word-break: break-all;
}

.code-security {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    font-size: 0.75rem;
    color: var(--gray-500);
}

.code-security i {
    color: var(--success);
}

/* Code Actions */
.code-actions {
    text-align: center;
    margin-bottom: 1.5rem;
}

.btn-copy {
    background: var(--white);
    color: var(--primary);
    border: 1px solid var(--primary);
    border-radius: var(--radius);
    padding: 0.75rem 1.5rem;
    font-size: 0.875rem;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s ease;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
}

.btn-copy:hover {
    background: var(--primary);
    color: var(--white);
}

.btn-copy.copied {
    background: var(--success);
    border-color: var(--success);
    color: var(--white);
}

/* Code Info */
.code-info {
    margin-bottom: 2rem;
}

.info-item {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    font-size: 0.875rem;
    color: var(--gray-600);
}

.info-item i {
    color: var(--warning);
}

/* Result Actions */
.result-actions {
    text-align: center;
}

.btn-secondary {
    background: var(--gray-100);
    color: var(--gray-700);
    border: 1px solid var(--gray-300);
    border-radius: var(--radius);
    padding: 0.75rem 1.5rem;
    font-size: 0.875rem;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s ease;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
}

.btn-secondary:hover {
    background: var(--gray-200);
    border-color: var(--gray-400);
}

/* Responsive Design */
@media (max-width: 768px) {
    .generator-card {
        padding: 1.5rem;
        margin: 0 1rem;
    }
    
    .page-header {
        padding: 0 1rem;
    }
    
    .step-track {
        flex-direction: column;
        gap: 1rem;
    }
    
    .step-item {
        flex-direction: row;
        gap: 0.75rem;
    }
    
    .step-circle {
        width: 32px;
        height: 32px;
    }
    
    .step-number {
        font-size: 0.75rem;
    }
    
    .step-title {
        font-size: 0.75rem;
    }
    
    .step-desc {
        font-size: 0.625rem;
    }
    
    .category-grid,
    .type-grid {
        grid-template-columns: 1fr;
    }
    
    .category-option,
    .type-option {
        padding: 1rem;
    }
    
    .result-card {
        margin: 1rem;
        padding: 1.5rem;
    }
    
    .code-value {
        font-size: 1rem;
    }
}

@media (max-width: 480px) {
    .generator-card {
        padding: 1rem;
        margin: 0 0.5rem;
    }
    
    .page-title {
        font-size: 1.5rem;
    }
    
    .step-title {
        font-size: 1.25rem;
    }
    
    .result-card {
        margin: 0.5rem;
        padding: 1rem;
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
let selectedTarget = null;
let selectedPrice = 0;

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
        const targetId = $(this).val();
        if (targetId) {
            // Find the selected target from the loaded data
            const targets = selectedType === 'course_purchase' ? courses : packages;
            selectedTarget = targets.find(target => {
                const id = selectedType === 'course_purchase' ? target.course_id : target.id;
                return id == targetId;
            });
            
            if (selectedTarget) {
                // Extract price based on type
                selectedPrice = selectedType === 'course_purchase' ? 
                    parseFloat(selectedTarget.fee) : 
                    parseFloat(selectedTarget.price);
                
                // Update price display
                updatePriceDisplay();
            }
            
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
        } else if (stepNumber === currentStep) {
            $stepItem.addClass('active').removeClass('completed');
        } else {
            $stepItem.removeClass('active completed');
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
    
    // Update price display when showing step 4
    if (currentStep === 4) {
        updatePriceDisplay();
    }
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
    
    // Store targets globally for later use
    if (selectedType === 'course_purchase') {
        courses = targets;
    } else {
        packages = targets;
    }
    
    targets.forEach(target => {
        const option = document.createElement('option');
        if (selectedType === 'course_purchase') {
            option.value = target.course_id;
            option.textContent = `${target.title} - $${target.fee}`;
        } else {
            option.value = target.id;
            option.textContent = `${target.name} - $${target.price}`;
        }
        targetSelect.appendChild(option);
    });
}

// Update price display in step 4
function updatePriceDisplay() {
    const priceElement = document.getElementById('selectedPrice');
    const commissionElement = document.getElementById('commissionAmount');
    
    if (selectedPrice > 0) {
        priceElement.textContent = `$${selectedPrice.toFixed(2)}`;
        
        // Calculate commission amount
        const commissionRate = <?php echo $partnerCommissionRate; ?>;
        const commissionAmount = (selectedPrice * commissionRate) / 100;
        commissionElement.textContent = `$${commissionAmount.toFixed(2)}`;
    } else {
        priceElement.textContent = 'Select a target to see price';
        commissionElement.textContent = '-';
    }
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
    
    // Set price from selected target
    data.price = selectedPrice;
    
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
    document.querySelector('.generator-container').style.display = 'none';
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
    document.querySelector('.generator-container').style.display = 'block';
    document.getElementById('generatedCodeSection').style.display = 'none';
    
    // Reset selections
    $('.category-option, .type-option').removeClass('selected');
    $('#selectedCategory, #selectedType').val('');
    $('#targetSelect').html('<option value="">Select...</option>');
    
    // Reset global variables
    selectedCategory = null;
    selectedType = null;
    selectedTarget = null;
    selectedPrice = 0;
    courses = [];
    packages = [];
    
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
