<?php
$pageTitle = 'Promotion Codes';
include 'layout/header.php';

// Get promotion code data
$codeStats = $codeManager->getPartnerCodeStats($currentPartner['id']);
$recentCodes = $codeManager->getPartnerPromotionCodes($currentPartner['id'], null, 10, 0);
$codeManagement = $codeManager->getPartnerCodeManagement($currentPartner['id'], null, 10, 0);
$totalCodesCount = $codeManager->getPartnerPromotionCodesCount($currentPartner['id']);
$totalManagementCount = $codeManager->getPartnerCodeManagementCount($currentPartner['id']);
?>

<!-- Promotion Codes Section -->
<div class="content-section">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-ticket-alt me-2"></i>Promotion Codes</h2>
        <button class="btn btn-primary" onclick="window.location.href='promotion_code_generator.php'">
            <i class="fas fa-magic me-2"></i>Generate New Code
        </button>
    </div>

    <!-- Code Statistics -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card stat-card">
                <div class="card-body text-center">
                    <i class="fas fa-ticket-alt fa-2x mb-2"></i>
                    <div class="stat-number"><?php echo number_format($codeStats['total_generated'] ?? 0); ?></div>
                    <div>Total Generated</div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card stat-card">
                <div class="card-body text-center">
                    <i class="fas fa-clock fa-2x mb-2"></i>
                    <div class="stat-number"><?php echo number_format($codeStats['pending'] ?? 0); ?></div>
                    <div>Pending</div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card stat-card">
                <div class="card-body text-center">
                    <i class="fas fa-percentage fa-2x mb-2"></i>
                    <div class="stat-number"><?php echo $codeStats['usage_rate'] ?? 0; ?>%</div>
                    <div>Usage Rate</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Code Management Table -->
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5><i class="fas fa-cogs me-2"></i>Code Management</h5>
            <div class="btn-group" role="group">
                <button type="button" class="btn btn-sm btn-outline-primary" onclick="filterCodes('all')">All</button>
                <button type="button" class="btn btn-sm btn-outline-warning" onclick="filterCodes('pending')">Pending</button>
                <button type="button" class="btn btn-sm btn-outline-danger" onclick="filterCodes('rejected')">Rejected</button>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover" id="codeManagementTable">
                    <thead>
                        <tr>
                            <th>Code</th>
                            <th>Type</th>
                            <th>Status</th>
                            <th>Created</th>
                            <th>Expires</th>
                            <th>User</th>
                            <th>Phone</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($codeManagement as $code): ?>
                        <tr data-status="<?php echo $code['status']; ?>">
                            <td>
                                <code class="bg-light p-1 rounded"><?php echo htmlspecialchars($code['code']); ?></code>
                            </td>
                            <td>
                                <?php 
                                $codeType = '';
                                if (!empty($code['target_course_id'])) {
                                    $codeType = 'Course';
                                } elseif (!empty($code['target_package_id'])) {
                                    $codeType = 'Package';
                                } else {
                                    $codeType = '-';
                                }
                                ?>
                                <span class="badge bg-info"><?php echo $codeType; ?></span>
                            </td>
                            <td>
                                <span class="badge bg-<?php echo getCodeStatusColor($code['status']); ?>">
                                    <?php echo ucfirst($code['status']); ?>
                                </span>
                            </td>
                            <td><?php echo date('M j, Y H:i', strtotime($code['created_at'])); ?></td>
                            <td><?php echo date('M j, Y H:i', strtotime($code['expired_at'])); ?></td>
                            <td><?php echo htmlspecialchars($code['user_name'] ?? '-'); ?></td>
                            <td><?php echo $code['user_phone'] ?? '-'; ?></td>
                            <td>
                                <?php if ($code['status'] === 'pending'): ?>
                                <div class="btn-group btn-group-sm" role="group">
                                    <button class="btn btn-outline-danger btn-sm" onclick="deleteCode(<?php echo $code['id']; ?>)">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                                <?php else: ?>
                                <span class="text-muted">No actions</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <div class="d-flex justify-content-between align-items-center mt-3">
                <div class="pagination-info">
                    <small class="text-muted">
                        Showing <span id="showingStart">1</span> to <span id="showingEnd">10</span> of <span id="totalCount"><?php echo $totalManagementCount; ?></span> codes
                    </small>
                </div>
                <nav aria-label="Code management pagination">
                    <ul class="pagination pagination-sm mb-0" id="codeManagementPagination">
                        <li class="page-item disabled" id="prevBtn">
                            <a class="page-link" href="#" onclick="loadCodeManagementPage(currentPage - 1)">Previous</a>
                        </li>
                        <li class="page-item active" id="page1">
                            <a class="page-link" href="#" onclick="loadCodeManagementPage(1)">1</a>
                        </li>
                        <li class="page-item" id="nextBtn">
                            <a class="page-link" href="#" onclick="loadCodeManagementPage(currentPage + 1)">Next</a>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
    </div>

    <!-- Recent Codes -->
    <div class="card">
        <div class="card-header">
            <h5><i class="fas fa-history me-2"></i>Recent Promotion Codes</h5>
        </div>
        <div class="card-body">
            <?php if (!empty($recentCodes)): ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Code</th>
                            <th>Type</th>
                            <th>Target</th>
                            <th>Price</th>
                            <th>Commission</th>
                            <th>Status</th>
                            <th>Created</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recentCodes as $code): ?>
                        <tr>
                            <td>
                                <code class="bg-light p-1 rounded"><?php echo htmlspecialchars($code['code']); ?></code>
                            </td>
                            <td>
                                <span class="badge bg-<?php echo $code['target_course_id'] ? 'primary' : 'info'; ?>">
                                    <?php echo $code['target_course_id'] ? 'Course' : 'Package'; ?>
                                </span>
                            </td>
                            <td>
                                <?php 
                                if ($code['target_course_id']) {
                                    echo 'Course #' . $code['target_course_id'];
                                } elseif ($code['target_package_id']) {
                                    echo 'Package #' . $code['target_package_id'];
                                } else {
                                    echo 'Any';
                                }
                                ?>
                            </td>
                            <td>$<?php echo number_format($code['price'], 2); ?></td>
                            <td><?php echo $code['commission_rate']; ?>%</td>
                            <td>
                                <span class="badge bg-<?php echo getCodeStatusColor($code['status']); ?>">
                                    <?php echo ucfirst($code['status']); ?>
                                </span>
                            </td>
                            <td><?php echo date('M j, Y', strtotime($code['created_at'])); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php else: ?>
            <div class="text-center text-muted py-4">
                <i class="fas fa-ticket-alt fa-2x mb-2"></i>
                <p>No promotion codes generated yet.</p>
                <button class="btn btn-primary" onclick="showCreateCodeModal()">
                    <i class="fas fa-plus me-2"></i>Generate Your First Code
                </button>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Create Code Modal -->
<div class="modal fade" id="createCodeModal" tabindex="-1" aria-labelledby="createCodeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createCodeModalLabel">Generate New Promotion Code</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="createCodeForm">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="categoryId" class="form-label">Course Category</label>
                            <select class="form-select" id="categoryId" name="category_id" required>
                                <option value="">Select Category</option>
                            </select>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="codeType" class="form-label">Code Type</label>
                            <select class="form-select" id="codeType" name="code_type" required>
                                <option value="">Select Type</option>
                                <option value="course_purchase">Course Purchase</option>
                                <option value="package_purchase">Package Purchase</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="mb-3" id="courseSelectionDiv" style="display: none;">
                        <label for="targetCourseId" class="form-label">Select Course</label>
                        <select class="form-select" id="targetCourseId" name="target_course_id">
                            <option value="">Select Course</option>
                        </select>
                    </div>
                    
                    <div class="mb-3" id="packageSelectionDiv" style="display: none;">
                        <label for="targetPackageId" class="form-label">Select Package</label>
                        <select class="form-select" id="targetPackageId" name="target_package_id">
                            <option value="">Select Package</option>
                        </select>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="price" class="form-label">Price ($)</label>
                            <input type="number" class="form-control" id="price" name="price" step="0.01" min="0" required>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="commissionRate" class="form-label">Commission Rate (%)</label>
                            <input type="number" class="form-control" id="commissionRate" name="commission_rate" step="0.01" min="0" max="100" required>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="expiredAt" class="form-label">Expiration Date</label>
                        <input type="datetime-local" class="form-control" id="expiredAt" name="expired_at">
                        <div class="form-text">Leave empty for default 3 days from now</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Generate Code</button>
                </div>
            </form>
        </div>
    </div>
</div>


<script>
// Global variables
let sessionToken = window.sessionToken || localStorage.getItem('partner_session_token') || '';
let categories = [];
let courses = [];
let packages = [];
let currentPage = 1;
let currentStatus = 'all';
let totalPages = 1;
let perPage = 10;

// Initialize page
$(document).ready(function() {
    // Check if session token is available
    if (!sessionToken) {
        console.error('No session token available');
        showAlert('Session expired. Please login again.', 'danger');
        setTimeout(() => {
            window.location.href = 'partner_login.php';
        }, 2000);
        return;
    }
    
    // Update window.sessionToken for other scripts
    window.sessionToken = sessionToken;
    
    loadCategories();
    initializePagination();
    // Load initial data for pagination
    loadCodeManagementPage(1);
});

// Initialize pagination
function initializePagination() {
    // Don't update pagination info until we have data
    updatePaginationButtons();
}

// Load code management page
function loadCodeManagementPage(page) {
    if (page < 1 || page > totalPages) return;
    
    currentPage = page;
    
    // Show loading state
    const tbody = $('#codeManagementTable tbody');
    tbody.html('<tr><td colspan="8" class="text-center"><i class="fas fa-spinner fa-spin"></i> Loading...</td></tr>');
    
    // Build API URL
    const params = new URLSearchParams({
        session_token: sessionToken,
        status: currentStatus === 'all' ? '' : currentStatus,
        limit: perPage,
        page: page
    });
    
    fetch(`api/promotion_codes.php?endpoint=get_code_management&${params}`)
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            updateCodeManagementTable(data.data);
            updatePaginationInfo(data.pagination);
            updatePaginationButtons(data.pagination);
        } else {
            showAlert(data.message || 'Failed to load codes', 'danger');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('An error occurred while loading codes', 'danger');
    });
}

// Update code management table
function updateCodeManagementTable(codes) {
    const tbody = $('#codeManagementTable tbody');
    tbody.empty();
    
    if (codes.length === 0) {
        tbody.html('<tr><td colspan="8" class="text-center text-muted">No codes found</td></tr>');
        return;
    }
    
    codes.forEach(code => {
        const statusBadge = getStatusBadge(code.status);
        const codeTypeBadge = getCodeTypeBadge(code);
        const actions = getCodeActions(code);
        
        const row = `
            <tr>
                <td><code>${code.code}</code></td>
                <td>${codeTypeBadge}</td>
                <td>${statusBadge}</td>
                <td>${formatDate(code.created_at)}</td>
                <td>${formatDate(code.expired_at)}</td>
                <td>${code.user_name || '-'}</td>
                <td>${code.user_phone || '-'}</td>
                <td>${actions}</td>
            </tr>
        `;
        tbody.append(row);
    });
}

// Get status badge HTML
function getStatusBadge(status) {
    const statusColors = {
        'pending': 'warning',
        'approved': 'success',
        'rejected': 'danger',
        'expired': 'secondary'
    };
    
    const color = statusColors[status] || 'secondary';
    return `<span class="badge bg-${color}">${status.charAt(0).toUpperCase() + status.slice(1)}</span>`;
}

// Get code type badge HTML
function getCodeTypeBadge(code) {
    let codeType = '';
    if (code.target_course_id) {
        codeType = 'Course';
    } else if (code.target_package_id) {
        codeType = 'Package';
    } else {
        codeType = '-';
    }
    
    return `<span class="badge bg-info">${codeType}</span>`;
}

// Get code actions HTML
function getCodeActions(code) {
    if (code.status === 'pending') {
        return `
            <div class="btn-group btn-group-sm" role="group">
                <button class="btn btn-outline-danger btn-sm" onclick="deleteCode(${code.id})">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        `;
    } else {
        return '<span class="text-muted">No actions</span>';
    }
}

// Update pagination info
function updatePaginationInfo(pagination = null) {
    if (pagination) {
        const start = ((pagination.current_page - 1) * pagination.per_page) + 1;
        const end = Math.min(pagination.current_page * pagination.per_page, pagination.total_count);
        
        $('#showingStart').text(start);
        $('#showingEnd').text(end);
        $('#totalCount').text(pagination.total_count);
        
        totalPages = pagination.total_pages;
    }
}

// Update pagination buttons
function updatePaginationButtons(pagination = null) {
    const prevBtn = $('#prevBtn');
    const nextBtn = $('#nextBtn');
    
    if (pagination) {
        prevBtn.toggleClass('disabled', !pagination.has_prev);
        nextBtn.toggleClass('disabled', !pagination.has_next);
    } else {
        prevBtn.toggleClass('disabled', currentPage <= 1);
        nextBtn.toggleClass('disabled', currentPage >= totalPages);
    }
    
    // Update page numbers
    updatePageNumbers();
}

// Update page numbers
function updatePageNumbers() {
    const pagination = $('#codeManagementPagination');
    const pageItems = pagination.find('.page-item').not('#prevBtn, #nextBtn');
    pageItems.remove();
    
    // Add page numbers
    const startPage = Math.max(1, currentPage - 2);
    const endPage = Math.min(totalPages, currentPage + 2);
    
    for (let i = startPage; i <= endPage; i++) {
        const pageItem = $(`
            <li class="page-item ${i === currentPage ? 'active' : ''}">
                <a class="page-link" href="#" onclick="loadCodeManagementPage(${i})">${i}</a>
            </li>
        `);
        $('#prevBtn').after(pageItem);
    }
}

// Format date
function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', {
        month: 'short',
        day: 'numeric',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
}

// Filter codes
function filterCodes(status) {
    currentStatus = status;
    currentPage = 1;
    
    // Update filter buttons
    $('.btn-group .btn').removeClass('active');
    $(`.btn-group .btn[onclick="filterCodes('${status}')"]`).addClass('active');
    
    // Load first page with new filter
    loadCodeManagementPage(1);
}

// Initialize page

// Load course categories
function loadCategories() {
    fetch('api/course_data.php?endpoint=get_categories')
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                categories = result.categories;
                populateCategoryDropdown();
            } else {
                showAlert('Failed to load categories', 'danger');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('Error loading categories', 'danger');
        });
}

// Populate category dropdown
function populateCategoryDropdown() {
    const categorySelect = document.getElementById('categoryId');
    categorySelect.innerHTML = '<option value="">Select Category</option>';
    
    categories.forEach(category => {
        const option = document.createElement('option');
        option.value = category.id;
        option.textContent = category.title;
        categorySelect.appendChild(option);
    });
}

// Load courses by category
function loadCourses(categoryId) {
    fetch('api/course_data.php?endpoint=get_courses', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ category_id: categoryId })
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            courses = result.courses;
            populateCourseDropdown();
        } else {
            showAlert('Failed to load courses', 'danger');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('Error loading courses', 'danger');
    });
}

// Load packages by category
function loadPackages(categoryId) {
    fetch('api/course_data.php?endpoint=get_packages', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ category_id: categoryId })
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            packages = result.packages;
            populatePackageDropdown();
        } else {
            showAlert('Failed to load packages', 'danger');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('Error loading packages', 'danger');
    });
}

// Populate course dropdown
function populateCourseDropdown() {
    const courseSelect = document.getElementById('targetCourseId');
    courseSelect.innerHTML = '<option value="">Select Course</option>';
    
    courses.forEach(course => {
        const option = document.createElement('option');
        option.value = course.course_id;
        option.textContent = `${course.title} - $${course.fee}`;
        courseSelect.appendChild(option);
    });
}

// Populate package dropdown
function populatePackageDropdown() {
    const packageSelect = document.getElementById('targetPackageId');
    packageSelect.innerHTML = '<option value="">Select Package</option>';
    
    packages.forEach(package => {
        const option = document.createElement('option');
        option.value = package.id;
        option.textContent = `${package.name} - $${package.price}`;
        packageSelect.appendChild(option);
    });
}

// Show create code modal
function showCreateCodeModal() {
    const modal = new bootstrap.Modal(document.getElementById('createCodeModal'));
    modal.show();
}

// Handle category change
document.getElementById('categoryId').addEventListener('change', function() {
    const categoryId = this.value;
    const codeType = document.getElementById('codeType').value;
    
    if (categoryId && codeType) {
        if (codeType === 'course_purchase') {
            loadCourses(categoryId);
        } else if (codeType === 'package_purchase') {
            loadPackages(categoryId);
        }
    }
});

// Handle code type change
document.getElementById('codeType').addEventListener('change', function() {
    const codeType = this.value;
    const categoryId = document.getElementById('categoryId').value;
    const courseDiv = document.getElementById('courseSelectionDiv');
    const packageDiv = document.getElementById('packageSelectionDiv');
    
    // Hide both divs first
    courseDiv.style.display = 'none';
    packageDiv.style.display = 'none';
    
    if (codeType === 'course_purchase') {
        courseDiv.style.display = 'block';
        if (categoryId) {
            loadCourses(categoryId);
        }
    } else if (codeType === 'package_purchase') {
        packageDiv.style.display = 'block';
        if (categoryId) {
            loadPackages(categoryId);
        }
    }
});

// Handle create code form submission
document.getElementById('createCodeForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const data = Object.fromEntries(formData);
    data.session_token = sessionToken;
    
    // Set default expiration if not provided
    if (!data.expired_at) {
        const now = new Date();
        now.setDate(now.getDate() + 3);
        data.expired_at = now.toISOString().slice(0, 16);
    }
    
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
            showAlert('Code generated successfully: ' + result.code, 'success');
            bootstrap.Modal.getInstance(document.getElementById('createCodeModal')).hide();
            location.reload();
        } else {
            showAlert(result.message, 'danger');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('An error occurred while generating the code', 'danger');
    });
});



// Delete code
function deleteCode(codeId) {
    if (confirm('Are you sure you want to delete this code? This action cannot be undone.')) {
        fetch('api/promotion_codes.php?endpoint=delete_code', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                session_token: sessionToken,
                code_id: codeId
            })
        })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                showAlert('Code deleted successfully', 'success');
                location.reload();
            } else {
                showAlert(result.message, 'danger');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('An error occurred while deleting the code', 'danger');
        });
    }
}

// Filter codes by status
function filterCodes(status) {
    const rows = document.querySelectorAll('#codeManagementTable tbody tr');
    
    rows.forEach(row => {
        if (status === 'all' || row.dataset.status === status) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
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
</script>

<?php include 'layout/footer.php'; ?>