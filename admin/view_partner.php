<?php
/**
 * View Partner Details Page
 */

require_once '../classes/admin_auth.php';

$adminAuth = new AdminAuth();

// Check if admin is logged in
if (!$adminAuth->isLoggedIn()) {
    header('Location: login.php');
    exit();
}

// Get partner ID from URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: index.php?error=Partner ID required');
    exit();
}

$partnerId = $_GET['id'];

// Get partner details
$partner = $adminAuth->getPartnerById($partnerId);

if (!$partner) {
    header('Location: index.php?error=Partner not found');
    exit();
}

$pageTitle = 'Partner Details - ' . htmlspecialchars($partner['contact_name']);
$currentPage = 'partners';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?> - Calamus Education Partner Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../css/app.css">
    <style>
        .partner-header {
            background: white;
            border: 1px solid #e8eaed;
            border-radius: 8px;
            padding: 24px;
            margin-bottom: 24px;
        }
        
        .info-card {
            background: white;
            border: 1px solid #e8eaed;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
        }
        
        .info-row {
            padding: 12px 0;
            border-bottom: 1px solid #f1f3f4;
        }
        
        .info-row:last-child {
            border-bottom: none;
        }
        
        .info-label {
            color: #5f6368;
            font-size: 14px;
            font-weight: 500;
            margin-bottom: 4px;
        }
        
        .info-value {
            color: #202124;
            font-size: 14px;
        }
        
        .status-badge {
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 13px;
            font-weight: 500;
        }
        
        .status-active {
            background: #e6f4ea;
            color: #137333;
        }
        
        .status-inactive {
            background: #fce8e6;
            color: #d93025;
        }
        
        .status-suspended {
            background: #fef7e0;
            color: #ea8600;
        }
        
        .partner-avatar {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #e8eaed;
        }
        
        .profile-placeholder {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: #f1f3f4;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 32px;
            color: #5f6368;
        }
        
        .action-buttons {
            display: flex;
            gap: 12px;
            margin-top: 24px;
        }
    </style>
</head>
<body>
    <!-- Admin Header -->
    <nav class="navbar navbar-expand-lg admin-navbar" style="background: white; border-bottom: 1px solid #e8eaed; padding: 12px 24px;">
        <div class="container-fluid">
            <button class="btn btn-sm me-2" type="button" data-bs-toggle="offcanvas" data-bs-target="#adminSidebar" aria-controls="adminSidebar" style="border: 1px solid #dadce0;">
                <i class="fas fa-bars"></i>
            </button>
            <h1 class="admin-title mb-0" style="font-size: 22px; font-weight: 400; color: #202124;">
                <i class="fas fa-users me-2"></i>Partner Details
            </h1>
            <div class="ms-auto d-flex align-items-center">
                <span class="text-muted me-3">Welcome, <?php echo htmlspecialchars($adminAuth->getAdminUsername()); ?></span>
                <button class="btn btn-sm btn-outline-secondary" onclick="window.location.href='logout.php'">
                    <i class="fas fa-sign-out-alt me-2"></i>Logout
                </button>
            </div>
        </div>
    </nav>

    <?php include 'layout/admin_sidebar.php'; ?>
    <div class="container-fluid p-0">
        <div class="row g-0">
            <!-- Main Content -->
            <div class="col-md-12 col-lg-12" style="padding: 24px;">
                
                <!-- Back Button -->
                <div class="mb-3">
                    <a href="partners.php" class="btn btn-sm btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Back to Partners
                    </a>
                </div>
                
                <!-- Partner Header -->
                <div class="partner-header">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <?php if ($partner['profile_image']): ?>
                                <img src="../<?php echo htmlspecialchars($partner['profile_image']); ?>" alt="Profile" class="partner-avatar">
                            <?php else: ?>
                                <div class="profile-placeholder">
                                    <i class="fas fa-user"></i>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="flex-grow-1 ms-4">
                            <h2 class="mb-2" style="color: #202124;"><?php echo htmlspecialchars($partner['contact_name']); ?></h2>
                            <p class="mb-1" style="color: #5f6368;"><?php echo htmlspecialchars($partner['company_name'] ?? 'N/A'); ?></p>
                            <p class="mb-0" style="color: #5f6368; font-size: 14px;">
                                <i class="fas fa-envelope me-2"></i><?php echo htmlspecialchars($partner['email']); ?>
                            </p>
                        </div>
                        <div class="flex-shrink-0">
                            <?php
                            $status = $partner['status'] ?? 'active';
                            $statusClass = 'status-active';
                            if ($status === 'inactive') $statusClass = 'status-inactive';
                            if ($status === 'suspended') $statusClass = 'status-suspended';
                            ?>
                            <span class="status-badge <?php echo $statusClass; ?>"><?php echo ucfirst($status); ?></span>
                        </div>
                    </div>
                </div>
                
                <!-- Partner Information -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="info-card">
                            <h5 class="mb-3" style="color: #202124;">
                                <i class="fas fa-info-circle me-2"></i>Basic Information
                            </h5>
                            <div class="info-row">
                                <div class="info-label">Partner ID</div>
                                <div class="info-value">#<?php echo htmlspecialchars($partner['id']); ?></div>
                            </div>
                            <div class="info-row">
                                <div class="info-label">Company Name</div>
                                <div class="info-value"><?php echo htmlspecialchars($partner['company_name'] ?? 'N/A'); ?></div>
                            </div>
                            <div class="info-row">
                                <div class="info-label">Contact Name</div>
                                <div class="info-value"><?php echo htmlspecialchars($partner['contact_name'] ?? 'N/A'); ?></div>
                            </div>
                            <div class="info-row">
                                <div class="info-label">Email</div>
                                <div class="info-value">
                                    <i class="fas fa-envelope me-2"></i><?php echo htmlspecialchars($partner['email']); ?>
                                </div>
                            </div>
                            <div class="info-row">
                                <div class="info-label">Phone</div>
                                <div class="info-value">
                                    <i class="fas fa-phone me-2"></i><?php echo htmlspecialchars($partner['phone'] ?? 'N/A'); ?>
                                </div>
                            </div>
                            <div class="info-row">
                                <div class="info-label">Website</div>
                                <div class="info-value">
                                    <?php if ($partner['website']): ?>
                                        <a href="<?php echo htmlspecialchars($partner['website']); ?>" target="_blank" style="color: #1a73e8;">
                                            <i class="fas fa-globe me-2"></i><?php echo htmlspecialchars($partner['website']); ?>
                                        </a>
                                    <?php else: ?>
                                        N/A
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="info-card">
                            <h5 class="mb-3" style="color: #202124;">
                                <i class="fas fa-cog me-2"></i>Account Information
                            </h5>
                            <div class="info-row">
                                <div class="info-label">Status</div>
                                <div class="info-value">
                                    <span class="status-badge <?php echo $statusClass; ?>"><?php echo ucfirst($status); ?></span>
                                </div>
                            </div>
                            <div class="info-row">
                                <div class="info-label">Email Verified</div>
                                <div class="info-value">
                                    <?php if ($partner['email_verified']): ?>
                                        <i class="fas fa-check-circle text-success"></i> Yes
                                    <?php else: ?>
                                        <i class="fas fa-times-circle text-danger"></i> No
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="info-row">
                                <div class="info-label">Private Code</div>
                                <div class="info-value">
                                    <code><?php echo htmlspecialchars($partner['private_code'] ?? 'N/A'); ?></code>
                                </div>
                            </div>
                            <div class="info-row">
                                <div class="info-label">Commission Rate</div>
                                <div class="info-value"><?php echo htmlspecialchars($partner['commission_rate'] ?? 'N/A'); ?>%</div>
                            </div>
                            <div class="info-row">
                                <div class="info-label">Account Created</div>
                                <div class="info-value">
                                    <i class="fas fa-calendar me-2"></i><?php echo date('M d, Y', strtotime($partner['created_at'])); ?>
                                </div>
                            </div>
                            <div class="info-row">
                                <div class="info-label">Last Login</div>
                                <div class="info-value">
                                    <i class="fas fa-clock me-2"></i><?php echo $partner['last_login'] ? date('M d, Y H:i', strtotime($partner['last_login'])) : 'Never'; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Description -->
                <?php if ($partner['description']): ?>
                <div class="info-card">
                    <h5 class="mb-3" style="color: #202124;">
                        <i class="fas fa-align-left me-2"></i>Description
                    </h5>
                    <p style="color: #202124; line-height: 1.6;"><?php echo nl2br(htmlspecialchars($partner['description'])); ?></p>
                </div>
                <?php endif; ?>
                
                <!-- Action Buttons -->
                <div class="action-buttons">
                    <a href="partners.php" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Back to List
                    </a>
                    <a href="edit_partner.php?id=<?php echo htmlspecialchars($partner['id']); ?>" class="btn btn-primary">
                        <i class="fas fa-edit me-2"></i>Edit Partner
                    </a>
                    <form method="POST" action="delete_partner.php" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this partner? This action cannot be undone.');">
                        <input type="hidden" name="partner_id" value="<?php echo htmlspecialchars($partner['id']); ?>">
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-trash me-2"></i>Delete Partner
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
