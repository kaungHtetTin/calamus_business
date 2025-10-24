<?php
session_start();
require_once 'classes/autoload.php';

// Initialize authentication
$auth = new PartnerAuth();
$dashboard = new PartnerDashboard();
$codeManager = new PromotionCodeManager();

// Check if user is logged in
$sessionToken = $_SESSION['partner_session_token'] ?? '';

if (empty($sessionToken)) {
    // Check localStorage token via JavaScript
    $needsAuth = true;
} else {
    // Validate session
    $session = $auth->validateSession($sessionToken);
    if (!$session['success']) {
        $needsAuth = true;
        unset($_SESSION['partner_session_token']);
    } else {
        $currentPartner = $session['partner'];
        $needsAuth = false;
    }
}

// If not authenticated, redirect to login
if ($needsAuth) {
    header('Location: partner_login.php');
    exit;
}

// Get dashboard data
$dashboardData = $dashboard->getDashboardData($currentPartner['id']);

// Get promotion code data
$codeStats = $codeManager->getPartnerCodeStats($currentPartner['id']);
$recentCodes = $codeManager->getPartnerPromotionCodes($currentPartner['id'], null, 10);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Partner Dashboard - <?php echo htmlspecialchars($currentPartner['contact_name']); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .sidebar {
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .sidebar .nav-link {
            color: rgba(255,255,255,0.8);
            padding: 12px 20px;
            margin: 5px 0;
            border-radius: 8px;
            transition: all 0.3s;
        }
        .sidebar .nav-link:hover, .sidebar .nav-link.active {
            color: white;
            background: rgba(255,255,255,0.1);
        }
        .card {
            border: none;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            border-radius: 12px;
        }
        .stat-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        .stat-card .card-body {
            padding: 1.5rem;
        }
        .stat-number {
            font-size: 2rem;
            font-weight: bold;
        }
        .affiliate-link {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 15px;
            margin: 10px 0;
        }
        .copy-btn {
            cursor: pointer;
            transition: color 0.3s;
        }
        .copy-btn:hover {
            color: #007bff;
        }
        .welcome-banner {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 1rem;
            border-radius: 10px;
            margin-bottom: 2rem;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 sidebar p-0">
                <div class="p-3">
                    <h4 class="text-white mb-4">
                        <i class="fas fa-handshake me-2"></i>Partner Portal
                    </h4>
                    <div class="text-white-50 mb-3">
                        <small>Welcome, <?php echo htmlspecialchars($currentPartner['contact_name']); ?></small>
                    </div>
                    <nav class="nav flex-column">
                        <a class="nav-link active" href="#dashboard" data-section="dashboard">
                            <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                        </a>
                        <a class="nav-link" href="#links" data-section="links">
                            <i class="fas fa-link me-2"></i>Affiliate Links
                        </a>
                        <a class="nav-link" href="#codes" data-section="codes">
                            <i class="fas fa-ticket-alt me-2"></i>Promotion Codes
                        </a>
                        <a class="nav-link" href="#conversions" data-section="conversions">
                            <i class="fas fa-chart-line me-2"></i>Conversions
                        </a>
                        <a class="nav-link" href="#payments" data-section="payments">
                            <i class="fas fa-money-bill-wave me-2"></i>Payments
                        </a>
                        <a class="nav-link" href="#profile" data-section="profile">
                            <i class="fas fa-user me-2"></i>Profile
                        </a>
                        <a class="nav-link" href="logout.php">
                            <i class="fas fa-sign-out-alt me-2"></i>Logout
                        </a>
                    </nav>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 p-4">
                <!-- Welcome Banner -->
                <div class="welcome-banner">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-1">Welcome back, <?php echo htmlspecialchars($currentPartner['contact_name']); ?>!</h5>
                            <small>Track your affiliate performance and earnings</small>
                        </div>
                        <div class="text-end">
                            <div class="h4 mb-0">$<?php echo number_format($dashboardData['stats']['total_earnings'], 2); ?></div>
                            <small>Total Earnings</small>
                        </div>
                    </div>
                </div>

                <!-- Dashboard Section -->
                <div id="dashboard-section" class="content-section">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h2><i class="fas fa-tachometer-alt me-2"></i>Dashboard</h2>
                        <button class="btn btn-primary" onclick="showCreateLinkModal()">
                            <i class="fas fa-plus me-2"></i>Create New Link
                        </button>
                    </div>

                    <!-- Stats Cards -->
                    <div class="row mb-4">
                        <div class="col-md-3 mb-3">
                            <div class="card stat-card">
                                <div class="card-body text-center">
                                    <i class="fas fa-mouse-pointer fa-2x mb-2"></i>
                                    <div class="stat-number"><?php echo number_format($dashboardData['stats']['total_clicks']); ?></div>
                                    <div>Total Clicks</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="card stat-card">
                                <div class="card-body text-center">
                                    <i class="fas fa-check-circle fa-2x mb-2"></i>
                                    <div class="stat-number"><?php echo number_format($dashboardData['stats']['total_conversions']); ?></div>
                                    <div>Conversions</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="card stat-card">
                                <div class="card-body text-center">
                                    <i class="fas fa-percentage fa-2x mb-2"></i>
                                    <div class="stat-number"><?php echo $dashboardData['stats']['conversion_rate']; ?>%</div>
                                    <div>Conversion Rate</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="card stat-card">
                                <div class="card-body text-center">
                                    <i class="fas fa-dollar-sign fa-2x mb-2"></i>
                                    <div class="stat-number">$<?php echo number_format($dashboardData['stats']['total_earnings'], 2); ?></div>
                                    <div>Total Earnings</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Charts Row -->
                    <div class="row mb-4">
                        <div class="col-md-8">
                            <div class="card">
                                <div class="card-header">
                                    <h5><i class="fas fa-chart-line me-2"></i>Monthly Earnings</h5>
                                </div>
                                <div class="card-body">
                                    <canvas id="earningsChart"></canvas>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-header">
                                    <h5><i class="fas fa-trophy me-2"></i>Top Performing Links</h5>
                                </div>
                                <div class="card-body" id="top-links">
                                    <?php foreach ($dashboardData['top_links'] as $link): ?>
                                    <div class="mb-3 p-3 border rounded">
                                        <div class="d-flex justify-content-between">
                                            <div>
                                                <strong><?php echo htmlspecialchars($link['campaign_name']); ?></strong>
                                                <br>
                                                <small class="text-muted">Code: <?php echo htmlspecialchars($link['link_code']); ?></small>
                                            </div>
                                            <div class="text-end">
                                                <div class="fw-bold">$<?php echo number_format($link['total_commission'] ?? 0, 2); ?></div>
                                                <small class="text-muted"><?php echo $link['conversions'] ?? 0; ?> conversions</small>
                                            </div>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Conversions -->
                    <div class="card">
                        <div class="card-header">
                            <h5><i class="fas fa-clock me-2"></i>Recent Conversions</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Campaign</th>
                                            <th>Type</th>
                                            <th>Value</th>
                                            <th>Commission</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($dashboardData['recent_conversions'] as $conversion): ?>
                                        <tr>
                                            <td><?php echo date('M j, Y', strtotime($conversion['conversion_date'])); ?></td>
                                            <td><?php echo htmlspecialchars($conversion['campaign_name']); ?></td>
                                            <td><span class="badge bg-primary"><?php echo htmlspecialchars($conversion['conversion_type']); ?></span></td>
                                            <td>$<?php echo number_format($conversion['conversion_value'], 2); ?></td>
                                            <td>$<?php echo number_format($conversion['commission_amount'], 2); ?></td>
                                            <td><span class="badge bg-<?php echo getStatusColor($conversion['status']); ?>"><?php echo htmlspecialchars($conversion['status']); ?></span></td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Affiliate Links Section -->
                <div id="links-section" class="content-section" style="display: none;">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h2><i class="fas fa-link me-2"></i>Affiliate Links</h2>
                        <button class="btn btn-primary" onclick="showCreateLinkModal()">
                            <i class="fas fa-plus me-2"></i>Create New Link
                        </button>
                    </div>
                    <div id="affiliate-links-list">
                        <?php 
                        $affiliateLinks = $dashboard->getAffiliateLinks($currentPartner['id']);
                        foreach ($affiliateLinks as $link): 
                        ?>
                        <div class="affiliate-link">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-1"><?php echo htmlspecialchars($link['campaign_name']); ?></h6>
                                    <small class="text-muted">Code: <?php echo htmlspecialchars($link['link_code']); ?></small>
                                    <br>
                                    <small class="text-muted">Clicks: <?php echo $link['clicks']; ?> | Conversions: <?php echo $link['conversions']; ?></small>
                                </div>
                                <div class="text-end">
                                    <div class="input-group mb-2">
                                        <input type="text" class="form-control" value="http://localhost/business/affiliate.php?ref=<?php echo $link['link_code']; ?>" readonly>
                                        <button class="btn btn-outline-secondary copy-btn" onclick="copyToClipboard(this.previousElementSibling)">
                                            <i class="fas fa-copy"></i>
                                        </button>
                                    </div>
                                    <small class="text-success">Earnings: $<?php echo number_format($link['commission_earned'], 2); ?></small>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Promotion Codes Section -->
                <div id="codes-section" class="content-section" style="display: none;">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h2><i class="fas fa-ticket-alt me-2"></i>Promotion Codes</h2>
                        <button class="btn btn-primary" onclick="showCreateCodeModal()">
                            <i class="fas fa-plus me-2"></i>Generate New Code
                        </button>
                    </div>

                    <!-- Code Statistics -->
                    <div class="row mb-4">
                        <div class="col-md-3 mb-3">
                            <div class="card stat-card">
                                <div class="card-body text-center">
                                    <i class="fas fa-ticket-alt fa-2x mb-2"></i>
                                    <div class="stat-number"><?php echo number_format($codeStats['total_generated']); ?></div>
                                    <div>Total Generated</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="card stat-card">
                                <div class="card-body text-center">
                                    <i class="fas fa-check-circle fa-2x mb-2"></i>
                                    <div class="stat-number"><?php echo number_format($codeStats['used_codes']); ?></div>
                                    <div>Used Codes</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="card stat-card">
                                <div class="card-body text-center">
                                    <i class="fas fa-percentage fa-2x mb-2"></i>
                                    <div class="stat-number"><?php echo $codeStats['usage_rate']; ?>%</div>
                                    <div>Usage Rate</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="card stat-card">
                                <div class="card-body text-center">
                                    <i class="fas fa-dollar-sign fa-2x mb-2"></i>
                                    <div class="stat-number">$<?php echo number_format($codeStats['commission_earned'], 2); ?></div>
                                    <div>Code Earnings</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Codes -->
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5><i class="fas fa-clock me-2"></i>Recent Promotion Codes</h5>
                            <div class="btn-group" role="group">
                                <button class="btn btn-sm btn-outline-primary" onclick="filterCodes('all')">All</button>
                                <button class="btn btn-sm btn-outline-success" onclick="filterCodes('active')">Active</button>
                                <button class="btn btn-sm btn-outline-info" onclick="filterCodes('used')">Used</button>
                                <button class="btn btn-sm btn-outline-warning" onclick="filterCodes('expired')">Expired</button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Code</th>
                                            <th>Type</th>
                                            <th>Course</th>
                                            <th>Client</th>
                                            <th>Status</th>
                                            <th>Created</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="codes-list">
                                        <?php foreach ($recentCodes as $code): ?>
                                        <tr data-status="<?php echo $code['status']; ?>">
                                            <td>
                                                <code class="bg-light p-1 rounded"><?php echo htmlspecialchars($code['code']); ?></code>
                                                <button class="btn btn-sm btn-outline-secondary ms-2" onclick="copyCode('<?php echo $code['code']; ?>')">
                                                    <i class="fas fa-copy"></i>
                                                </button>
                                            </td>
                                            <td>
                                                <span class="badge bg-<?php echo $code['code_type'] == 'vip_subscription' ? 'primary' : 'info'; ?>">
                                                    <?php echo ucfirst(str_replace('_', ' ', $code['code_type'])); ?>
                                                </span>
                                            </td>
                                            <td><?php echo $code['course_title'] ? htmlspecialchars($code['course_title']) : 'All Courses'; ?></td>
                                            <td><?php echo $code['generated_for'] ? htmlspecialchars($code['generated_for']) : '-'; ?></td>
                                            <td>
                                                <span class="badge bg-<?php echo getCodeStatusColor($code['status']); ?>">
                                                    <?php echo ucfirst($code['status']); ?>
                                                </span>
                                            </td>
                                            <td><?php echo date('M j, Y', strtotime($code['created_at'])); ?></td>
                                            <td>
                                                <?php if ($code['status'] == 'active'): ?>
                                                <button class="btn btn-sm btn-outline-danger" onclick="cancelCode(<?php echo $code['id']; ?>)">
                                                    <i class="fas fa-times"></i> Cancel
                                                </button>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Conversions Section -->
                <div id="conversions-section" class="content-section" style="display: none;">
                    <h2><i class="fas fa-chart-line me-2"></i>Conversion History</h2>
                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Campaign</th>
                                            <th>Type</th>
                                            <th>Value</th>
                                            <th>Commission</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody id="conversions-list">
                                        <?php 
                                        $conversions = $dashboard->getConversionHistory($currentPartner['id']);
                                        foreach ($conversions as $conversion): 
                                        ?>
                                        <tr>
                                            <td><?php echo date('M j, Y', strtotime($conversion['conversion_date'])); ?></td>
                                            <td><?php echo htmlspecialchars($conversion['campaign_name']); ?></td>
                                            <td><span class="badge bg-primary"><?php echo htmlspecialchars($conversion['conversion_type']); ?></span></td>
                                            <td>$<?php echo number_format($conversion['conversion_value'], 2); ?></td>
                                            <td>$<?php echo number_format($conversion['commission_amount'], 2); ?></td>
                                            <td><span class="badge bg-<?php echo getStatusColor($conversion['status']); ?>"><?php echo htmlspecialchars($conversion['status']); ?></span></td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Payments Section -->
                <div id="payments-section" class="content-section" style="display: none;">
                    <h2><i class="fas fa-money-bill-wave me-2"></i>Payment History</h2>
                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Period</th>
                                            <th>Amount</th>
                                            <th>Method</th>
                                            <th>Status</th>
                                            <th>Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                        $payments = $dashboard->getPaymentHistory($currentPartner['id']);
                                        foreach ($payments as $payment): 
                                        ?>
                                        <tr>
                                            <td><?php echo date('M j', strtotime($payment['payment_period_start'])); ?> - <?php echo date('M j, Y', strtotime($payment['payment_period_end'])); ?></td>
                                            <td>$<?php echo number_format($payment['total_commission'], 2); ?></td>
                                            <td><?php echo ucfirst(str_replace('_', ' ', $payment['payment_method'])); ?></td>
                                            <td><span class="badge bg-<?php echo getPaymentStatusColor($payment['payment_status']); ?>"><?php echo ucfirst($payment['payment_status']); ?></span></td>
                                            <td><?php echo date('M j, Y', strtotime($payment['created_at'])); ?></td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Profile Section -->
                <div id="profile-section" class="content-section" style="display: none;">
                    <h2><i class="fas fa-user me-2"></i>Profile Settings</h2>
                    <div class="card">
                        <div class="card-body">
                            <form id="profile-form" method="POST" action="api/update_profile.php">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Contact Name</label>
                                            <input type="text" class="form-control" id="contact_name" name="contact_name" value="<?php echo htmlspecialchars($currentPartner['contact_name']); ?>" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Company Name</label>
                                            <input type="text" class="form-control" id="company_name" name="company_name" value="<?php echo htmlspecialchars($currentPartner['company_name']); ?>">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Phone</label>
                                            <input type="tel" class="form-control" id="phone" name="phone" value="<?php echo htmlspecialchars($currentPartner['phone']); ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Website</label>
                                            <input type="url" class="form-control" id="website" name="website" value="<?php echo htmlspecialchars($currentPartner['website']); ?>">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Payment Method</label>
                                            <select class="form-control" id="payment_method" name="payment_method">
                                                <option value="bank_transfer" <?php echo $currentPartner['payment_method'] == 'bank_transfer' ? 'selected' : ''; ?>>Bank Transfer</option>
                                                <option value="paypal" <?php echo $currentPartner['payment_method'] == 'paypal' ? 'selected' : ''; ?>>PayPal</option>
                                                <option value="stripe" <?php echo $currentPartner['payment_method'] == 'stripe' ? 'selected' : ''; ?>>Stripe</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Payment Details</label>
                                            <textarea class="form-control" id="payment_details" name="payment_details" rows="3"><?php echo htmlspecialchars($currentPartner['payment_details']); ?></textarea>
                                        </div>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>Update Profile
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Create Link Modal -->
    <div class="modal fade" id="createLinkModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Create New Affiliate Link</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="create-link-form">
                        <div class="mb-3">
                            <label class="form-label">Campaign Name</label>
                            <input type="text" class="form-control" id="campaign_name" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Target Course (Optional)</label>
                            <select class="form-control" id="target_course">
                                <option value="">All Courses</option>
                                <?php
                                $courses = $dashboard->db->read("SELECT course_id, title FROM courses ORDER BY title");
                                foreach ($courses as $course):
                                ?>
                                <option value="<?php echo $course['course_id']; ?>"><?php echo htmlspecialchars($course['title']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Target Language</label>
                            <select class="form-control" id="target_major">
                                <option value="">All Languages</option>
                                <option value="english">English</option>
                                <option value="chinese">Chinese</option>
                                <option value="japanese">Japanese</option>
                                <option value="korean">Korean</option>
                                <option value="russian">Russian</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Custom Landing Page (Optional)</label>
                            <input type="url" class="form-control" id="custom_url">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="createAffiliateLink()">Create Link</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Create Code Modal -->
    <div class="modal fade" id="createCodeModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Generate New Promotion Code</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="create-code-form">
                        <div class="mb-3">
                            <label class="form-label">Code Type *</label>
                            <select class="form-control" id="code_type" required>
                                <option value="">Select Code Type</option>
                                <option value="vip_subscription">VIP Subscription</option>
                                <option value="course_purchase">Course Purchase</option>
                                <option value="package_purchase">Package Purchase</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Target Course (Optional)</label>
                            <select class="form-control" id="code_target_course">
                                <option value="">All Courses</option>
                                <?php
                                $courses = $dashboard->db->read("SELECT course_id, title FROM courses ORDER BY title");
                                foreach ($courses as $course):
                                ?>
                                <option value="<?php echo $course['course_id']; ?>"><?php echo htmlspecialchars($course['title']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Target Package (Optional)</label>
                            <select class="form-control" id="code_target_package">
                                <option value="">All Packages</option>
                                <?php
                                $packages = $dashboard->db->read("SELECT id, name FROM package_plans WHERE status = 'active' ORDER BY name");
                                foreach ($packages as $package):
                                ?>
                                <option value="<?php echo $package['id']; ?>"><?php echo htmlspecialchars($package['name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Target Language</label>
                            <select class="form-control" id="code_target_major">
                                <option value="">All Languages</option>
                                <option value="english">English</option>
                                <option value="chinese">Chinese</option>
                                <option value="japanese">Japanese</option>
                                <option value="korean">Korean</option>
                                <option value="russian">Russian</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Client Name/Description</label>
                            <input type="text" class="form-control" id="client_name" placeholder="e.g., John Smith, Facebook Ad Campaign">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Expiration Date (Optional)</label>
                            <input type="datetime-local" class="form-control" id="expires_at">
                        </div>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Code Format:</strong> <?php echo htmlspecialchars($currentPartner['code_prefix'] ?? 'PART'); ?>-TYPE-COURSE-XXXX
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="generatePromotionCode()">Generate Code</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Global variables
        let earningsChart = null;
        const sessionToken = '<?php echo $sessionToken; ?>';

        // Initialize dashboard
        document.addEventListener('DOMContentLoaded', function() {
            // Setup navigation
            setupNavigation();
            
            // Load earnings chart
            loadEarningsChart();
        });

        // Setup navigation
        function setupNavigation() {
            document.querySelectorAll('.nav-link[data-section]').forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    const section = this.getAttribute('data-section');
                    showSection(section);
                    
                    // Update active state
                    document.querySelectorAll('.nav-link').forEach(l => l.classList.remove('active'));
                    this.classList.add('active');
                });
            });
        }

        // Show section
        function showSection(sectionName) {
            document.querySelectorAll('.content-section').forEach(section => {
                section.style.display = 'none';
            });
            
            const targetSection = document.getElementById(sectionName + '-section');
            if (targetSection) {
                targetSection.style.display = 'block';
            }
        }

        // Load earnings chart
        function loadEarningsChart() {
            const ctx = document.getElementById('earningsChart').getContext('2d');
            const monthlyData = <?php echo json_encode($dashboardData['monthly_earnings']); ?>;
            
            const labels = monthlyData.map(item => item.month);
            const earnings = monthlyData.map(item => parseFloat(item.earnings));
            
            earningsChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Monthly Earnings',
                        data: earnings,
                        borderColor: '#667eea',
                        backgroundColor: 'rgba(102, 126, 234, 0.1)',
                        tension: 0.4,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return '$' + value.toFixed(2);
                                }
                            }
                        }
                    }
                }
            });
        }

        // Show create link modal
        function showCreateLinkModal() {
            const modal = new bootstrap.Modal(document.getElementById('createLinkModal'));
            modal.show();
        }

        // Show create code modal
        function showCreateCodeModal() {
            const modal = new bootstrap.Modal(document.getElementById('createCodeModal'));
            modal.show();
        }

        // Create affiliate link
        function createAffiliateLink() {
            const formData = {
                campaign_name: document.getElementById('campaign_name').value,
                target_course_id: document.getElementById('target_course').value || null,
                target_major: document.getElementById('target_major').value || null,
                custom_url: document.getElementById('custom_url').value || ''
            };
            
            fetch('api/create_affiliate_link.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    session_token: sessionToken,
                    ...formData
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Affiliate link created successfully!');
                    bootstrap.Modal.getInstance(document.getElementById('createLinkModal')).hide();
                    location.reload(); // Reload to show new link
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error creating link:', error);
                alert('Error creating affiliate link');
            });
        }

        // Generate promotion code
        function generatePromotionCode() {
            const formData = {
                code_type: document.getElementById('code_type').value,
                target_course_id: document.getElementById('code_target_course').value || null,
                target_major: document.getElementById('code_target_major').value || null,
                package_id: document.getElementById('code_target_package').value || null,
                client_name: document.getElementById('client_name').value || '',
                expires_at: document.getElementById('expires_at').value || null
            };
            
            if (!formData.code_type) {
                alert('Please select a code type');
                return;
            }
            
            fetch('api/generate_promotion_code.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    session_token: sessionToken,
                    ...formData
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Promotion code generated successfully!\nCode: ' + data.code);
                    bootstrap.Modal.getInstance(document.getElementById('createCodeModal')).hide();
                    location.reload(); // Reload to show new code
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error generating code:', error);
                alert('Error generating promotion code');
            });
        }

        // Copy code to clipboard
        function copyCode(code) {
            navigator.clipboard.writeText(code).then(() => {
                // Visual feedback
                const button = event.target.closest('button');
                const originalHTML = button.innerHTML;
                button.innerHTML = '<i class="fas fa-check"></i>';
                button.classList.remove('btn-outline-secondary');
                button.classList.add('btn-success');
                
                setTimeout(() => {
                    button.innerHTML = originalHTML;
                    button.classList.remove('btn-success');
                    button.classList.add('btn-outline-secondary');
                }, 2000);
            }).catch(err => {
                console.error('Failed to copy code: ', err);
                alert('Failed to copy code');
            });
        }

        // Filter codes by status
        function filterCodes(status) {
            const rows = document.querySelectorAll('#codes-list tr');
            rows.forEach(row => {
                if (status === 'all' || row.getAttribute('data-status') === status) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
            
            // Update button states
            document.querySelectorAll('.btn-group button').forEach(btn => {
                btn.classList.remove('active');
            });
            event.target.classList.add('active');
        }

        // Cancel promotion code
        function cancelCode(codeId) {
            if (!confirm('Are you sure you want to cancel this promotion code?')) {
                return;
            }
            
            fetch('api/cancel_promotion_code.php', {
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
            .then(data => {
                if (data.success) {
                    alert('Promotion code cancelled successfully');
                    location.reload();
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error cancelling code:', error);
                alert('Error cancelling promotion code');
            });
        }

        // Profile form submission
        document.getElementById('profile-form').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            formData.append('session_token', sessionToken);
            
            fetch('api/update_profile.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Profile updated successfully!');
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error updating profile:', error);
                alert('Error updating profile');
            });
        });
    </script>
</body>
</html>

<?php
// Helper functions
function getStatusColor($status) {
    $colors = [
        'pending' => 'warning',
        'approved' => 'success',
        'paid' => 'info',
        'cancelled' => 'danger'
    ];
    return $colors[$status] ?? 'secondary';
}

function getPaymentStatusColor($status) {
    $colors = [
        'pending' => 'warning',
        'processing' => 'info',
        'completed' => 'success',
        'failed' => 'danger'
    ];
    return $colors[$status] ?? 'secondary';
}

function getCodeStatusColor($status) {
    $colors = [
        'active' => 'success',
        'used' => 'info',
        'expired' => 'warning',
        'cancelled' => 'danger'
    ];
    return $colors[$status] ?? 'secondary';
}
?>
