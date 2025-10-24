<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Console - Affiliate Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #4285f4;
            --secondary-color: #34a853;
            --warning-color: #fbbc04;
            --danger-color: #ea4335;
            --dark-color: #202124;
            --light-gray: #f8f9fa;
            --border-color: #dadce0;
            --text-primary: #202124;
            --text-secondary: #5f6368;
            --sidebar-width: 280px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--light-gray);
            color: var(--text-primary);
            line-height: 1.6;
        }

        /* Sidebar */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: var(--sidebar-width);
            height: 100vh;
            background: white;
            border-right: 1px solid var(--border-color);
            z-index: 1000;
            overflow-y: auto;
        }

        .sidebar-header {
            padding: 20px;
            border-bottom: 1px solid var(--border-color);
            background: var(--primary-color);
            color: white;
        }

        .sidebar-header h4 {
            font-weight: 600;
            margin: 0;
        }

        .sidebar-nav {
            padding: 20px 0;
        }

        .nav-item {
            margin: 0;
        }

        .nav-link {
            display: flex;
            align-items: center;
            padding: 12px 24px;
            color: var(--text-secondary);
            text-decoration: none;
            transition: all 0.2s ease;
            border: none;
            background: none;
            width: 100%;
            text-align: left;
        }

        .nav-link:hover {
            background-color: var(--light-gray);
            color: var(--text-primary);
        }

        .nav-link.active {
            background-color: #e8f0fe;
            color: var(--primary-color);
            border-right: 3px solid var(--primary-color);
        }

        .nav-link i {
            width: 20px;
            margin-right: 12px;
            font-size: 16px;
        }

        /* Main Content */
        .main-content {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
        }

        .top-navbar {
            background: white;
            border-bottom: 1px solid var(--border-color);
            padding: 16px 24px;
            display: flex;
            justify-content: between;
            align-items: center;
        }

        .content-area {
            padding: 24px;
        }

        /* Cards */
        .card {
            border: 1px solid var(--border-color);
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            background: white;
        }

        .card-header {
            background: white;
            border-bottom: 1px solid var(--border-color);
            padding: 16px 20px;
            font-weight: 600;
        }

        .card-body {
            padding: 20px;
        }

        /* Stats Cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            padding: 20px;
            transition: transform 0.2s ease;
        }

        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }

        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            color: white;
            margin-bottom: 12px;
        }

        .stat-icon.primary { background: var(--primary-color); }
        .stat-icon.success { background: var(--secondary-color); }
        .stat-icon.warning { background: var(--warning-color); }
        .stat-icon.danger { background: var(--danger-color); }

        .stat-value {
            font-size: 28px;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 4px;
        }

        .stat-label {
            color: var(--text-secondary);
            font-size: 14px;
        }

        /* Tables */
        .table-container {
            background: white;
            border-radius: 8px;
            overflow: hidden;
        }

        .table {
            margin: 0;
        }

        .table th {
            background: var(--light-gray);
            border: none;
            font-weight: 600;
            color: var(--text-primary);
            padding: 16px;
        }

        .table td {
            border: none;
            padding: 16px;
            vertical-align: middle;
        }

        .table tbody tr {
            border-bottom: 1px solid var(--border-color);
        }

        .table tbody tr:hover {
            background-color: #f8f9fa;
        }

        /* Badges */
        .badge {
            font-size: 12px;
            font-weight: 500;
            padding: 6px 12px;
            border-radius: 16px;
        }

        .badge-success { background: var(--secondary-color); }
        .badge-warning { background: var(--warning-color); color: #000; }
        .badge-danger { background: var(--danger-color); }
        .badge-primary { background: var(--primary-color); }
        .badge-secondary { background: var(--text-secondary); }

        /* Buttons */
        .btn {
            border-radius: 6px;
            font-weight: 500;
            padding: 8px 16px;
            border: none;
            transition: all 0.2s ease;
        }

        .btn-primary {
            background: var(--primary-color);
        }

        .btn-primary:hover {
            background: #3367d6;
            transform: translateY(-1px);
        }

        .btn-success {
            background: var(--secondary-color);
        }

        .btn-danger {
            background: var(--danger-color);
        }

        .btn-sm {
            padding: 6px 12px;
            font-size: 12px;
        }

        /* Forms */
        .form-control {
            border: 1px solid var(--border-color);
            border-radius: 6px;
            padding: 10px 12px;
            font-size: 14px;
        }

        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 2px rgba(66, 133, 244, 0.2);
        }

        /* Search and Filters */
        .search-container {
            display: flex;
            gap: 12px;
            margin-bottom: 20px;
            align-items: center;
        }

        .search-input {
            flex: 1;
            max-width: 400px;
        }

        /* Modal */
        .modal-content {
            border: none;
            border-radius: 8px;
        }

        .modal-header {
            border-bottom: 1px solid var(--border-color);
            padding: 20px 24px;
        }

        .modal-body {
            padding: 24px;
        }

        .modal-footer {
            border-top: 1px solid var(--border-color);
            padding: 16px 24px;
        }

        /* Loading */
        .loading {
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 40px;
        }

        .spinner {
            width: 40px;
            height: 40px;
            border: 4px solid var(--border-color);
            border-top: 4px solid var(--primary-color);
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s ease;
            }

            .sidebar.show {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
        }

        ::-webkit-scrollbar-track {
            background: var(--light-gray);
        }

        ::-webkit-scrollbar-thumb {
            background: var(--border-color);
            border-radius: 3px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: var(--text-secondary);
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-header">
            <h4><i class="fas fa-fire me-2"></i>Admin Console</h4>
        </div>
        <nav class="sidebar-nav">
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link active" href="#" data-section="dashboard">
                        <i class="fas fa-tachometer-alt"></i>
                        Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#" data-section="partners">
                        <i class="fas fa-users"></i>
                        Partners
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#" data-section="packages">
                        <i class="fas fa-box"></i>
                        Packages
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#" data-section="promotion-codes">
                        <i class="fas fa-ticket-alt"></i>
                        Promotion Codes
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#" data-section="conversions">
                        <i class="fas fa-chart-line"></i>
                        Conversions
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#" data-section="payments">
                        <i class="fas fa-credit-card"></i>
                        Payments
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#" data-section="analytics">
                        <i class="fas fa-chart-bar"></i>
                        Analytics
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#" data-section="settings">
                        <i class="fas fa-cog"></i>
                        Settings
                    </a>
                </li>
            </ul>
        </nav>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Top Navbar -->
        <div class="top-navbar">
            <div class="d-flex align-items-center">
                <button class="btn btn-outline-secondary d-md-none me-3" id="sidebarToggle">
                    <i class="fas fa-bars"></i>
                </button>
                <h5 class="mb-0" id="pageTitle">Dashboard</h5>
            </div>
            <div class="ms-auto d-flex align-items-center">
                <div class="dropdown">
                    <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        <i class="fas fa-user-circle me-2"></i>Admin
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="#"><i class="fas fa-user me-2"></i>Profile</a></li>
                        <li><a class="dropdown-item" href="#"><i class="fas fa-cog me-2"></i>Settings</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="#"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Content Area -->
        <div class="content-area">
            <!-- Dashboard Section -->
            <div id="dashboard-section" class="content-section">
                <!-- Stats Grid -->
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon primary">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="stat-value" id="totalPartners">0</div>
                        <div class="stat-label">Total Partners</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon success">
                            <i class="fas fa-dollar-sign"></i>
                        </div>
                        <div class="stat-value" id="totalRevenue">$0</div>
                        <div class="stat-label">Total Revenue</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon warning">
                            <i class="fas fa-ticket-alt"></i>
                        </div>
                        <div class="stat-value" id="totalCodes">0</div>
                        <div class="stat-label">Promotion Codes</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon danger">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <div class="stat-value" id="totalConversions">0</div>
                        <div class="stat-label">Conversions</div>
                    </div>
                </div>

                <!-- Recent Activity -->
                <div class="row">
                    <div class="col-lg-8">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="mb-0">Recent Conversions</h6>
                            </div>
                            <div class="card-body">
                                <div class="table-container">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>Partner</th>
                                                <th>Type</th>
                                                <th>Amount</th>
                                                <th>Commission</th>
                                                <th>Date</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody id="recentConversions">
                                            <tr>
                                                <td colspan="6" class="text-center">
                                                    <div class="loading">
                                                        <div class="spinner"></div>
                                                    </div>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="mb-0">Top Partners</h6>
                            </div>
                            <div class="card-body">
                                <div id="topPartners">
                                    <div class="loading">
                                        <div class="spinner"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Partners Section -->
            <div id="partners-section" class="content-section" style="display: none;">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4>Partners Management</h4>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addPartnerModal">
                        <i class="fas fa-plus me-2"></i>Add Partner
                    </button>
                </div>

                <!-- Search and Filters -->
                <div class="search-container">
                    <div class="search-input">
                        <input type="text" class="form-control" id="partnerSearch" placeholder="Search partners...">
                    </div>
                    <select class="form-control" id="partnerStatusFilter" style="width: 150px;">
                        <option value="">All Status</option>
                        <option value="active">Active</option>
                        <option value="pending">Pending</option>
                        <option value="suspended">Suspended</option>
                    </select>
                </div>

                <!-- Partners Table -->
                <div class="table-container">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Company</th>
                                <th>Contact</th>
                                <th>Email</th>
                                <th>Commission</th>
                                <th>Earnings</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="partnersTable">
                            <tr>
                                <td colspan="7" class="text-center">
                                    <div class="loading">
                                        <div class="spinner"></div>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Packages Section -->
            <div id="packages-section" class="content-section" style="display: none;">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4>Package Plans</h4>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addPackageModal">
                        <i class="fas fa-plus me-2"></i>Add Package
                    </button>
                </div>

                <!-- Packages Grid -->
                <div class="row" id="packagesGrid">
                    <div class="col-12 text-center">
                        <div class="loading">
                            <div class="spinner"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Promotion Codes Section -->
            <div id="promotion-codes-section" class="content-section" style="display: none;">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4>Promotion Codes</h4>
                    <div class="btn-group">
                        <button class="btn btn-outline-primary" id="exportCodes">
                            <i class="fas fa-download me-2"></i>Export
                        </button>
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#generateCodeModal">
                            <i class="fas fa-plus me-2"></i>Generate Code
                        </button>
                    </div>
                </div>

                <!-- Codes Table -->
                <div class="table-container">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Code</th>
                                <th>Partner</th>
                                <th>Type</th>
                                <th>Target</th>
                                <th>Status</th>
                                <th>Created</th>
                                <th>Used</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="codesTable">
                            <tr>
                                <td colspan="8" class="text-center">
                                    <div class="loading">
                                        <div class="spinner"></div>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Conversions Section -->
            <div id="conversions-section" class="content-section" style="display: none;">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4>Conversions</h4>
                    <div class="btn-group">
                        <button class="btn btn-outline-success" id="approveSelected">
                            <i class="fas fa-check me-2"></i>Approve Selected
                        </button>
                        <button class="btn btn-outline-danger" id="rejectSelected">
                            <i class="fas fa-times me-2"></i>Reject Selected
                        </button>
                    </div>
                </div>

                <!-- Conversions Table -->
                <div class="table-container">
                    <table class="table">
                        <thead>
                            <tr>
                                <th><input type="checkbox" id="selectAll"></th>
                                <th>Partner</th>
                                <th>Code</th>
                                <th>Type</th>
                                <th>Amount</th>
                                <th>Commission</th>
                                <th>Date</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="conversionsTable">
                            <tr>
                                <td colspan="9" class="text-center">
                                    <div class="loading">
                                        <div class="spinner"></div>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Payments Section -->
            <div id="payments-section" class="content-section" style="display: none;">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4>Payment Management</h4>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#processPaymentModal">
                        <i class="fas fa-money-bill-wave me-2"></i>Process Payment
                    </button>
                </div>

                <!-- Pending Payments -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h6 class="mb-0">Pending Payments</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-container">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Partner</th>
                                        <th>Amount</th>
                                        <th>Method</th>
                                        <th>Period</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="pendingPayments">
                                    <tr>
                                        <td colspan="5" class="text-center">
                                            <div class="loading">
                                                <div class="spinner"></div>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Analytics Section -->
            <div id="analytics-section" class="content-section" style="display: none;">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4>Analytics</h4>
                    <div class="btn-group">
                        <button class="btn btn-outline-primary" id="exportAnalytics">
                            <i class="fas fa-download me-2"></i>Export Report
                        </button>
                    </div>
                </div>

                <!-- Analytics Charts -->
                <div class="row">
                    <div class="col-lg-6">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="mb-0">Revenue Trend</h6>
                            </div>
                            <div class="card-body">
                                <canvas id="revenueChart" height="300"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="mb-0">Partner Performance</h6>
                            </div>
                            <div class="card-body">
                                <canvas id="partnerChart" height="300"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Settings Section -->
            <div id="settings-section" class="content-section" style="display: none;">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4>System Settings</h4>
                </div>

                <!-- Settings Form -->
                <div class="row">
                    <div class="col-lg-8">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="mb-0">General Settings</h6>
                            </div>
                            <div class="card-body">
                                <form id="settingsForm">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Default Commission Rate (%)</label>
                                                <input type="number" class="form-control" id="defaultCommission" value="10" min="0" max="50" step="0.1">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Minimum Payout Amount ($)</label>
                                                <input type="number" class="form-control" id="minPayout" value="50" min="0">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Code Expiry Days</label>
                                                <input type="number" class="form-control" id="codeExpiry" value="30" min="1">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Email Notifications</label>
                                                <select class="form-control" id="emailNotifications">
                                                    <option value="enabled">Enabled</option>
                                                    <option value="disabled">Disabled</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-2"></i>Save Settings
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="mb-0">System Info</h6>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <strong>Version:</strong> 1.0.0
                                </div>
                                <div class="mb-3">
                                    <strong>Last Updated:</strong> <span id="lastUpdated">-</span>
                                </div>
                                <div class="mb-3">
                                    <strong>Database:</strong> Connected
                                </div>
                                <div class="mb-3">
                                    <strong>Email Service:</strong> <span class="badge badge-success">Active</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modals -->
    <!-- Add Partner Modal -->
    <div class="modal fade" id="addPartnerModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Partner</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="addPartnerForm">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Company Name *</label>
                                    <input type="text" class="form-control" id="addCompanyName" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Contact Name *</label>
                                    <input type="text" class="form-control" id="addContactName" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Email *</label>
                                    <input type="email" class="form-control" id="addEmail" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Phone *</label>
                                    <input type="tel" class="form-control" id="addPhone" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Commission Rate (%)</label>
                                    <input type="number" class="form-control" id="addCommission" value="10" min="0" max="50" step="0.1">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Code Prefix</label>
                                    <input type="text" class="form-control" id="addCodePrefix" maxlength="4" placeholder="ABC">
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" id="addDescription" rows="3"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="savePartner">Add Partner</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Package Modal -->
    <div class="modal fade" id="addPackageModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Package</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="addPackageForm">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Package Name *</label>
                                    <input type="text" class="form-control" id="addPackageName" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Language</label>
                                    <select class="form-control" id="addPackageMajor">
                                        <option value="english">English</option>
                                        <option value="chinese">Chinese</option>
                                        <option value="japanese">Japanese</option>
                                        <option value="korean">Korean</option>
                                        <option value="russian">Russian</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Price ($) *</label>
                                    <input type="number" class="form-control" id="addPackagePrice" required step="0.01">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Original Price ($)</label>
                                    <input type="number" class="form-control" id="addOriginalPrice" step="0.01">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Duration (Days) *</label>
                                    <input type="number" class="form-control" id="addDuration" required min="1">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Max Courses</label>
                                    <input type="number" class="form-control" id="addMaxCourses" min="1">
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" id="addPackageDescription" rows="3"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="savePackage">Add Package</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Generate Code Modal -->
    <div class="modal fade" id="generateCodeModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Generate Promotion Code</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="generateCodeForm">
                        <div class="mb-3">
                            <label class="form-label">Partner *</label>
                            <select class="form-control" id="codePartner" required>
                                <option value="">Select Partner</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Code Type *</label>
                            <select class="form-control" id="codeType" required>
                                <option value="">Select Type</option>
                                <option value="vip_subscription">VIP Subscription</option>
                                <option value="course_purchase">Course Purchase</option>
                                <option value="package_purchase">Package Purchase</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Target Course/Package</label>
                            <select class="form-control" id="codeTarget">
                                <option value="">All</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Client Name</label>
                            <input type="text" class="form-control" id="codeClient">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Expiry Date</label>
                            <input type="datetime-local" class="form-control" id="codeExpiry">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="generateCode">Generate Code</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        $(document).ready(function() {
            // Initialize the admin console
            initializeAdminConsole();
            
            // Load dashboard data
            loadDashboardData();
            
            // Setup event listeners
            setupEventListeners();
        });

        function initializeAdminConsole() {
            // Set current date
            $('#lastUpdated').text(new Date().toLocaleDateString());
            
            // Initialize charts
            initializeCharts();
        }

        function setupEventListeners() {
            // Sidebar navigation
            $('.nav-link').on('click', function(e) {
                e.preventDefault();
                const section = $(this).data('section');
                showSection(section);
                
                // Update active nav
                $('.nav-link').removeClass('active');
                $(this).addClass('active');
                
                // Update page title
                const title = $(this).text().trim();
                $('#pageTitle').text(title);
            });

            // Sidebar toggle for mobile
            $('#sidebarToggle').on('click', function() {
                $('.sidebar').toggleClass('show');
            });

            // Search functionality
            $('#partnerSearch').on('input', function() {
                filterPartners();
            });

            $('#partnerStatusFilter').on('change', function() {
                filterPartners();
            });

            // Modal form submissions
            $('#savePartner').on('click', function() {
                savePartner();
            });

            $('#savePackage').on('click', function() {
                savePackage();
            });

            $('#generateCode').on('click', function() {
                generatePromotionCode();
            });

            // Settings form
            $('#settingsForm').on('submit', function(e) {
                e.preventDefault();
                saveSettings();
            });

            // Export functions
            $('#exportCodes').on('click', function() {
                exportPromotionCodes();
            });

            $('#exportAnalytics').on('click', function() {
                exportAnalytics();
            });
        }

        function showSection(section) {
            $('.content-section').hide();
            $(`#${section}-section`).show();
            
            // Load section-specific data
            switch(section) {
                case 'partners':
                    loadPartners();
                    break;
                case 'packages':
                    loadPackages();
                    break;
                case 'promotion-codes':
                    loadPromotionCodes();
                    break;
                case 'conversions':
                    loadConversions();
                    break;
                case 'payments':
                    loadPayments();
                    break;
                case 'analytics':
                    loadAnalytics();
                    break;
            }
        }

        function loadDashboardData() {
            // Simulate API calls with mock data
            setTimeout(() => {
                $('#totalPartners').text('127');
                $('#totalRevenue').text('$45,230');
                $('#totalCodes').text('1,234');
                $('#totalConversions').text('456');
                
                loadRecentConversions();
                loadTopPartners();
            }, 1000);
        }

        function loadRecentConversions() {
            const mockData = [
                { partner: 'ABC Corp', type: 'VIP', amount: '$99', commission: '$9.90', date: '2024-01-15', status: 'approved' },
                { partner: 'XYZ Ltd', type: 'Package', amount: '$299', commission: '$29.90', date: '2024-01-15', status: 'pending' },
                { partner: 'Tech Solutions', type: 'VIP', amount: '$99', commission: '$9.90', date: '2024-01-14', status: 'approved' },
                { partner: 'Digital Agency', type: 'Package', amount: '$199', commission: '$19.90', date: '2024-01-14', status: 'approved' }
            ];

            let html = '';
            mockData.forEach(item => {
                const statusBadge = item.status === 'approved' ? 'badge-success' : 'badge-warning';
                html += `
                    <tr>
                        <td>${item.partner}</td>
                        <td><span class="badge badge-primary">${item.type}</span></td>
                        <td>${item.amount}</td>
                        <td>${item.commission}</td>
                        <td>${item.date}</td>
                        <td><span class="badge ${statusBadge}">${item.status}</span></td>
                    </tr>
                `;
            });
            $('#recentConversions').html(html);
        }

        function loadTopPartners() {
            const mockData = [
                { name: 'ABC Corp', earnings: '$2,450', conversions: 25 },
                { name: 'XYZ Ltd', earnings: '$1,890', conversions: 19 },
                { name: 'Tech Solutions', earnings: '$1,650', conversions: 17 },
                { name: 'Digital Agency', earnings: '$1,320', conversions: 13 }
            ];

            let html = '';
            mockData.forEach((item, index) => {
                html += `
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <div class="fw-bold">${index + 1}. ${item.name}</div>
                            <small class="text-muted">${item.conversions} conversions</small>
                        </div>
                        <div class="text-end">
                            <div class="fw-bold text-success">${item.earnings}</div>
                        </div>
                    </div>
                `;
            });
            $('#topPartners').html(html);
        }

        function loadPartners() {
            // Simulate loading partners data
            setTimeout(() => {
                const mockData = [
                    { company: 'ABC Corp', contact: 'John Doe', email: 'john@abc.com', commission: '10%', earnings: '$2,450', status: 'active' },
                    { company: 'XYZ Ltd', contact: 'Jane Smith', email: 'jane@xyz.com', commission: '12%', earnings: '$1,890', status: 'active' },
                    { company: 'Tech Solutions', contact: 'Bob Johnson', email: 'bob@tech.com', commission: '8%', earnings: '$1,650', status: 'pending' },
                    { company: 'Digital Agency', contact: 'Alice Brown', email: 'alice@digital.com', commission: '15%', earnings: '$1,320', status: 'active' }
                ];

                let html = '';
                mockData.forEach(item => {
                    const statusBadge = item.status === 'active' ? 'badge-success' : 'badge-warning';
                    html += `
                        <tr>
                            <td>${item.company}</td>
                            <td>${item.contact}</td>
                            <td>${item.email}</td>
                            <td>${item.commission}</td>
                            <td>${item.earnings}</td>
                            <td><span class="badge ${statusBadge}">${item.status}</span></td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary me-1" onclick="editPartner('${item.company}')">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-danger" onclick="deletePartner('${item.company}')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    `;
                });
                $('#partnersTable').html(html);
            }, 1000);
        }

        function loadPackages() {
            // Simulate loading packages data
            setTimeout(() => {
                const mockData = [
                    { name: 'English Complete Package', price: '$299.99', original: '$399.99', duration: '365 days', courses: 10, status: 'active' },
                    { name: 'Chinese Starter Package', price: '$199.99', original: '$249.99', duration: '180 days', courses: 5, status: 'active' },
                    { name: 'Japanese Pro Package', price: '$399.99', original: '$499.99', duration: '365 days', courses: 15, status: 'active' },
                    { name: 'Korean Essentials Package', price: '$179.99', original: '$229.99', duration: '120 days', courses: 4, status: 'inactive' }
                ];

                let html = '';
                mockData.forEach(item => {
                    const statusBadge = item.status === 'active' ? 'badge-success' : 'badge-secondary';
                    const discount = Math.round(((parseFloat(item.original.replace('$', '')) - parseFloat(item.price.replace('$', ''))) / parseFloat(item.original.replace('$', ''))) * 100);
                    
                    html += `
                        <div class="col-lg-6 mb-4">
                            <div class="card">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start mb-3">
                                        <h6 class="card-title mb-0">${item.name}</h6>
                                        <span class="badge ${statusBadge}">${item.status}</span>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-6">
                                            <div class="text-muted small">Price</div>
                                            <div class="fw-bold">${item.price}</div>
                                        </div>
                                        <div class="col-6">
                                            <div class="text-muted small">Original</div>
                                            <div class="fw-bold text-decoration-line-through">${item.original}</div>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-6">
                                            <div class="text-muted small">Duration</div>
                                            <div class="fw-bold">${item.duration}</div>
                                        </div>
                                        <div class="col-6">
                                            <div class="text-muted small">Courses</div>
                                            <div class="fw-bold">${item.courses}</div>
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <span class="badge badge-warning">${discount}% OFF</span>
                                        <div>
                                            <button class="btn btn-sm btn-outline-primary me-1">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="btn btn-sm btn-outline-danger">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                });
                $('#packagesGrid').html(html);
            }, 1000);
        }

        function loadPromotionCodes() {
            // Simulate loading promotion codes data
            setTimeout(() => {
                const mockData = [
                    { code: 'ABC-VIP-001-1234', partner: 'ABC Corp', type: 'VIP Subscription', target: 'Course 1', status: 'used', created: '2024-01-10', used: '2024-01-12' },
                    { code: 'XYZ-PKG-002-5678', partner: 'XYZ Ltd', type: 'Package Purchase', target: 'English Package', status: 'active', created: '2024-01-11', used: '-' },
                    { code: 'TECH-CRS-003-9012', partner: 'Tech Solutions', type: 'Course Purchase', target: 'Course 5', status: 'expired', created: '2024-01-05', used: '-' },
                    { code: 'DIG-PKG-004-3456', partner: 'Digital Agency', type: 'Package Purchase', target: 'Chinese Package', status: 'active', created: '2024-01-13', used: '-' }
                ];

                let html = '';
                mockData.forEach(item => {
                    let statusBadge = '';
                    switch(item.status) {
                        case 'active': statusBadge = 'badge-success'; break;
                        case 'used': statusBadge = 'badge-primary'; break;
                        case 'expired': statusBadge = 'badge-warning'; break;
                        case 'cancelled': statusBadge = 'badge-danger'; break;
                    }
                    
                    html += `
                        <tr>
                            <td><code>${item.code}</code></td>
                            <td>${item.partner}</td>
                            <td><span class="badge badge-primary">${item.type}</span></td>
                            <td>${item.target}</td>
                            <td><span class="badge ${statusBadge}">${item.status}</span></td>
                            <td>${item.created}</td>
                            <td>${item.used}</td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary me-1" onclick="viewCode('${item.code}')">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-danger" onclick="cancelCode('${item.code}')">
                                    <i class="fas fa-times"></i>
                                </button>
                            </td>
                        </tr>
                    `;
                });
                $('#codesTable').html(html);
            }, 1000);
        }

        function loadConversions() {
            // Simulate loading conversions data
            setTimeout(() => {
                const mockData = [
                    { partner: 'ABC Corp', code: 'ABC-VIP-001-1234', type: 'VIP', amount: '$99', commission: '$9.90', date: '2024-01-15', status: 'approved' },
                    { partner: 'XYZ Ltd', code: 'XYZ-PKG-002-5678', type: 'Package', amount: '$299', commission: '$29.90', date: '2024-01-15', status: 'pending' },
                    { partner: 'Tech Solutions', code: 'TECH-CRS-003-9012', type: 'Course', amount: '$49', commission: '$4.90', date: '2024-01-14', status: 'approved' },
                    { partner: 'Digital Agency', code: 'DIG-PKG-004-3456', type: 'Package', amount: '$199', commission: '$19.90', date: '2024-01-14', status: 'rejected' }
                ];

                let html = '';
                mockData.forEach(item => {
                    let statusBadge = '';
                    switch(item.status) {
                        case 'approved': statusBadge = 'badge-success'; break;
                        case 'pending': statusBadge = 'badge-warning'; break;
                        case 'rejected': statusBadge = 'badge-danger'; break;
                    }
                    
                    html += `
                        <tr>
                            <td><input type="checkbox" class="conversion-checkbox"></td>
                            <td>${item.partner}</td>
                            <td><code>${item.code}</code></td>
                            <td><span class="badge badge-primary">${item.type}</span></td>
                            <td>${item.amount}</td>
                            <td>${item.commission}</td>
                            <td>${item.date}</td>
                            <td><span class="badge ${statusBadge}">${item.status}</span></td>
                            <td>
                                <button class="btn btn-sm btn-outline-success me-1" onclick="approveConversion('${item.code}')">
                                    <i class="fas fa-check"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-danger" onclick="rejectConversion('${item.code}')">
                                    <i class="fas fa-times"></i>
                                </button>
                            </td>
                        </tr>
                    `;
                });
                $('#conversionsTable').html(html);
            }, 1000);
        }

        function loadPayments() {
            // Simulate loading payments data
            setTimeout(() => {
                const mockData = [
                    { partner: 'ABC Corp', amount: '$450.00', method: 'Bank Transfer', period: 'Dec 2023' },
                    { partner: 'XYZ Ltd', amount: '$320.00', method: 'PayPal', period: 'Dec 2023' },
                    { partner: 'Tech Solutions', amount: '$280.00', method: 'Bank Transfer', period: 'Dec 2023' },
                    { partner: 'Digital Agency', amount: '$190.00', method: 'Stripe', period: 'Dec 2023' }
                ];

                let html = '';
                mockData.forEach(item => {
                    html += `
                        <tr>
                            <td>${item.partner}</td>
                            <td>${item.amount}</td>
                            <td><span class="badge badge-primary">${item.method}</span></td>
                            <td>${item.period}</td>
                            <td>
                                <button class="btn btn-sm btn-success me-1" onclick="processPayment('${item.partner}')">
                                    <i class="fas fa-check"></i> Process
                                </button>
                                <button class="btn btn-sm btn-outline-primary" onclick="viewPayment('${item.partner}')">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </td>
                        </tr>
                    `;
                });
                $('#pendingPayments').html(html);
            }, 1000);
        }

        function loadAnalytics() {
            // Initialize charts when analytics section is loaded
            initializeCharts();
        }

        function initializeCharts() {
            // Revenue Chart
            const revenueCtx = document.getElementById('revenueChart');
            if (revenueCtx) {
                new Chart(revenueCtx, {
                    type: 'line',
                    data: {
                        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                        datasets: [{
                            label: 'Revenue',
                            data: [12000, 19000, 15000, 25000, 22000, 30000],
                            borderColor: '#4285f4',
                            backgroundColor: 'rgba(66, 133, 244, 0.1)',
                            tension: 0.4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    callback: function(value) {
                                        return '$' + value.toLocaleString();
                                    }
                                }
                            }
                        }
                    }
                });
            }

            // Partner Performance Chart
            const partnerCtx = document.getElementById('partnerChart');
            if (partnerCtx) {
                new Chart(partnerCtx, {
                    type: 'doughnut',
                    data: {
                        labels: ['ABC Corp', 'XYZ Ltd', 'Tech Solutions', 'Digital Agency', 'Others'],
                        datasets: [{
                            data: [30, 25, 20, 15, 10],
                            backgroundColor: [
                                '#4285f4',
                                '#34a853',
                                '#fbbc04',
                                '#ea4335',
                                '#9aa0a6'
                            ]
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom'
                            }
                        }
                    }
                });
            }
        }

        // Form submission functions
        function savePartner() {
            const formData = {
                company_name: $('#addCompanyName').val(),
                contact_name: $('#addContactName').val(),
                email: $('#addEmail').val(),
                phone: $('#addPhone').val(),
                commission_rate: $('#addCommission').val(),
                code_prefix: $('#addCodePrefix').val(),
                description: $('#addDescription').val()
            };

            // Simulate API call
            console.log('Saving partner:', formData);
            
            // Show success message
            showNotification('Partner added successfully!', 'success');
            
            // Close modal and reset form
            $('#addPartnerModal').modal('hide');
            $('#addPartnerForm')[0].reset();
            
            // Reload partners if on partners section
            if ($('#partners-section').is(':visible')) {
                loadPartners();
            }
        }

        function savePackage() {
            const formData = {
                name: $('#addPackageName').val(),
                major: $('#addPackageMajor').val(),
                price: $('#addPackagePrice').val(),
                original_price: $('#addOriginalPrice').val(),
                duration_days: $('#addDuration').val(),
                max_courses: $('#addMaxCourses').val(),
                description: $('#addPackageDescription').val()
            };

            // Simulate API call
            console.log('Saving package:', formData);
            
            // Show success message
            showNotification('Package added successfully!', 'success');
            
            // Close modal and reset form
            $('#addPackageModal').modal('hide');
            $('#addPackageForm')[0].reset();
            
            // Reload packages if on packages section
            if ($('#packages-section').is(':visible')) {
                loadPackages();
            }
        }

        function generatePromotionCode() {
            const formData = {
                partner_id: $('#codePartner').val(),
                code_type: $('#codeType').val(),
                target: $('#codeTarget').val(),
                client_name: $('#codeClient').val(),
                expires_at: $('#codeExpiry').val()
            };

            // Simulate API call
            console.log('Generating code:', formData);
            
            // Show success message
            showNotification('Promotion code generated successfully!', 'success');
            
            // Close modal and reset form
            $('#generateCodeModal').modal('hide');
            $('#generateCodeForm')[0].reset();
            
            // Reload codes if on codes section
            if ($('#promotion-codes-section').is(':visible')) {
                loadPromotionCodes();
            }
        }

        function saveSettings() {
            const formData = {
                default_commission: $('#defaultCommission').val(),
                min_payout: $('#minPayout').val(),
                code_expiry: $('#codeExpiry').val(),
                email_notifications: $('#emailNotifications').val()
            };

            // Simulate API call
            console.log('Saving settings:', formData);
            
            // Show success message
            showNotification('Settings saved successfully!', 'success');
        }

        // Utility functions
        function showNotification(message, type) {
            const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
            const alertHtml = `
                <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
                    ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            `;
            
            // Remove existing alerts
            $('.alert').remove();
            
            // Add new alert
            $('.content-area').prepend(alertHtml);
            
            // Auto-dismiss after 5 seconds
            setTimeout(() => {
                $('.alert').alert('close');
            }, 5000);
        }

        function filterPartners() {
            const searchTerm = $('#partnerSearch').val().toLowerCase();
            const statusFilter = $('#partnerStatusFilter').val();
            
            // Filter logic would go here
            console.log('Filtering partners:', { searchTerm, statusFilter });
        }

        function exportPromotionCodes() {
            // Simulate export
            showNotification('Promotion codes exported successfully!', 'success');
        }

        function exportAnalytics() {
            // Simulate export
            showNotification('Analytics report exported successfully!', 'success');
        }

        // Action functions (placeholder implementations)
        function editPartner(company) {
            console.log('Editing partner:', company);
            showNotification('Edit partner functionality coming soon!', 'info');
        }

        function deletePartner(company) {
            if (confirm(`Are you sure you want to delete ${company}?`)) {
                console.log('Deleting partner:', company);
                showNotification('Partner deleted successfully!', 'success');
            }
        }

        function viewCode(code) {
            console.log('Viewing code:', code);
            showNotification('Code details functionality coming soon!', 'info');
        }

        function cancelCode(code) {
            if (confirm(`Are you sure you want to cancel code ${code}?`)) {
                console.log('Cancelling code:', code);
                showNotification('Code cancelled successfully!', 'success');
            }
        }

        function approveConversion(code) {
            console.log('Approving conversion:', code);
            showNotification('Conversion approved successfully!', 'success');
        }

        function rejectConversion(code) {
            if (confirm(`Are you sure you want to reject conversion ${code}?`)) {
                console.log('Rejecting conversion:', code);
                showNotification('Conversion rejected successfully!', 'success');
            }
        }

        function processPayment(partner) {
            console.log('Processing payment for:', partner);
            showNotification('Payment processed successfully!', 'success');
        }

        function viewPayment(partner) {
            console.log('Viewing payment for:', partner);
            showNotification('Payment details functionality coming soon!', 'info');
        }
    </script>
</body>
</html>
