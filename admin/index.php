<?php
/**
 * Admin Dashboard
 * Main page for managing partners
 */

require_once '../classes/admin_auth.php';

$adminAuth = new AdminAuth();

// Check if admin is logged in
if (!$adminAuth->isLoggedIn()) {
    header('Location: login.php');
    exit();
}

// Get statistics
$stats = $adminAuth->getPartnerStatistics();

// Get current page from URL
$currentPageNum = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 20; // Partners per page

// Get search query from URL
$searchQuery = isset($_GET['search']) ? trim($_GET['search']) : null;

// Get partners with pagination and search
$partnersData = $adminAuth->getAllPartners($currentPageNum, $limit, $searchQuery);

// Get chart year filter
$chartYear = isset($_GET['chart_year']) ? $_GET['chart_year'] : 'current';

// Get chart data
$chartData = $adminAuth->getPaymentHistoryChart($chartYear);

$pageTitle = 'Admin Dashboard';
$isAdmin = true;
$currentPage = 'index';
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
    <link rel="stylesheet" href="css/app.css">
    <link rel="icon" href="../logo.png" type="image/x-icon">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <style>
        .verified-icon {
            color: #137333;
        }
        
        .unverified-icon {
            color: #d93025;
        }
        
        .chart-container {
            background: white;
            border: 1px solid #e8eaed;
            border-radius: 8px;
            padding: 24px;
            margin-bottom: 30px;
        }
        
        .chart-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        
        .chart-title {
            font-size: 18px;
            font-weight: 500;
            color: #202124;
            margin: 0;
        }
        
        .duration-filter {
            display: flex;
            gap: 8px;
        }
        
        .duration-btn {
            padding: 6px 12px;
            border: 1px solid #dadce0;
            background: white;
            color: #5f6368;
            border-radius: 4px;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.2s;
        }
        
        .duration-btn:hover {
            background: #f8f9fa;
            border-color: #1a73e8;
        }
        
        .duration-btn.active {
            background: #1a73e8;
            color: white;
            border-color: #1a73e8;
        }
    </style>
</head>
<body>
    <!-- Admin Header -->
    <?php include 'layout/admin_header.php'; ?>
    
    
    <?php include 'layout/admin_sidebar.php'; ?>

    <div class="container-fluid" style="padding: 24px;">
        <!-- Alert Messages -->
        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php echo htmlspecialchars($_GET['success']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        
        <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php echo htmlspecialchars($_GET['error']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        
        <!-- Statistics -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-label">Total Partners</div>
                <div class="stat-value"><?php echo number_format($stats['total']); ?></div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Active Partners</div>
                <div class="stat-value"><?php echo number_format($stats['active']); ?></div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Verified Partners</div>
                <div class="stat-value"><?php echo number_format($stats['verified']); ?></div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Unverified Partners</div>
                <div class="stat-value"><?php echo number_format($stats['unverified']); ?></div>
            </div>
        </div>
        
        <!-- Payment History Chart -->
        <div class="chart-container">
            <div class="chart-header">
                <h5 class="chart-title">
                    <i class="fas fa-chart-line me-2"></i>Payout History
                </h5>
                <div class="duration-filter">
                    <a href="?chart_year=current<?php echo $searchQuery ? '&search=' . urlencode($searchQuery) : ''; ?>" class="duration-btn <?php echo $chartYear === 'current' ? 'active' : ''; ?>"><?php echo date('Y'); ?></a>
                    <a href="?chart_year=2024<?php echo $searchQuery ? '&search=' . urlencode($searchQuery) : ''; ?>" class="duration-btn <?php echo $chartYear === '2024' ? 'active' : ''; ?>">2024</a>
                    <a href="?chart_year=2023<?php echo $searchQuery ? '&search=' . urlencode($searchQuery) : ''; ?>" class="duration-btn <?php echo $chartYear === '2023' ? 'active' : ''; ?>">2023</a>
                    <a href="?chart_year=2022<?php echo $searchQuery ? '&search=' . urlencode($searchQuery) : ''; ?>" class="duration-btn <?php echo $chartYear === '2022' ? 'active' : ''; ?>">2022</a>
                    <a href="?chart_year=all<?php echo $searchQuery ? '&search=' . urlencode($searchQuery) : ''; ?>" class="duration-btn <?php echo $chartYear === 'all' ? 'active' : ''; ?>">All</a>
                </div>
            </div>
            <canvas id="payoutChart" height="80"></canvas>
        </div>
        
        <!-- Search Form -->
        <div class="filter-card">
            <h5 class="mb-3" style="color: #202124;">
                <i class="fas fa-search me-2"></i>Search Partners
            </h5>
            <form method="GET" class="row g-3">
                <div class="col-md-10">
                    <input type="text" class="form-control" name="search" placeholder="Search by email, contact name, or company name..." value="<?php echo htmlspecialchars($searchQuery ?? ''); ?>">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search me-2"></i>Search
                    </button>
                </div>
                <?php if ($searchQuery): ?>
                    <div class="col-12">
                        <a href="index.php" class="btn btn-sm btn-outline-secondary">
                            <i class="fas fa-times me-2"></i>Clear Search
                        </a>
                        <span class="ms-2 text-muted">
                            Showing results for: "<strong><?php echo htmlspecialchars($searchQuery); ?></strong>"
                        </span>
                    </div>
                <?php endif; ?>
            </form>
        </div>
        
        <!-- Partners Table -->
        <div class="partners-table">
            <div class="table-header">
                <h2 class="table-title">All Partners (<?php echo number_format($partnersData['total']); ?>)</h2>
                <a href="index.php<?php echo $searchQuery ? '?search=' . urlencode($searchQuery) : ''; ?>" class="btn btn-sm btn-primary">
                    <i class="fas fa-sync-alt me-2"></i>Refresh
                </a>
            </div>
            
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Partner ID</th>
                            <th>Company Name</th>
                            <th>Contact Name</th>
                            <th>Email</th>
                            <th>Status</th>
                            <th>Verified</th>
                            <th>Joined</th>
                            <th>Last Login</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($partnersData['partners'] as $partner): ?>
                        <tr data-partner-id="<?php echo htmlspecialchars($partner['id']); ?>">
                            <td>#<?php echo htmlspecialchars($partner['id']); ?></td>
                            <td><?php echo htmlspecialchars($partner['company_name'] ?? 'N/A'); ?></td>
                            <td><?php echo htmlspecialchars($partner['contact_name'] ?? 'N/A'); ?></td>
                            <td><?php echo htmlspecialchars($partner['email']); ?></td>
                            <td>
                                <?php
                                $status = $partner['status'] ?? 'active';
                                $statusClass = 'status-active';
                                if ($status === 'inactive') $statusClass = 'status-inactive';
                                if ($status === 'suspended') $statusClass = 'status-suspended';
                                ?>
                                <span class="status-badge <?php echo $statusClass; ?>"><?php echo ucfirst($status); ?></span>
                            </td>
                            <td>
                                <?php if ($partner['email_verified']): ?>
                                    <i class="fas fa-check-circle verified-icon"></i>
                                <?php else: ?>
                                    <i class="fas fa-times-circle unverified-icon"></i>
                                <?php endif; ?>
                            </td>
                            <td><?php echo date('M d, Y', strtotime($partner['created_at'])); ?></td>
                            <td><?php echo $partner['last_login'] ? date('M d, Y', strtotime($partner['last_login'])) : 'Never'; ?></td>
                            <td>
                                <div class="btn-group" role="group">
                                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="window.location.href='view_partner.php?id=<?php echo $partner['id']; ?>'">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <?php if ($partnersData['total_pages'] > 1): ?>
            <div class="d-flex justify-content-between align-items-center p-3 border-top">
                <div class="text-muted">
                    Showing <?php echo (($currentPageNum - 1) * $limit) + 1; ?> to <?php echo min($currentPageNum * $limit, $partnersData['total']); ?> of <?php echo number_format($partnersData['total']); ?> partners
                </div>
                <nav>
                    <ul class="pagination mb-0">
                        <!-- Previous Button -->
                        <?php $searchParam = $searchQuery ? '&search=' . urlencode($searchQuery) : ''; ?>
                        <li class="page-item <?php echo $currentPageNum <= 1 ? 'disabled' : ''; ?>">
                            <a class="page-link" href="index.php?page=<?php echo $currentPageNum - 1; ?><?php echo $searchParam; ?>">Previous</a>
                        </li>
                        
                        <?php
                        $totalPages = $partnersData['total_pages'];
                        $startPage = max(1, $currentPageNum - 2);
                        $endPage = min($totalPages, $currentPageNum + 2);
                        
                        // Show first page if not in range
                        if ($startPage > 1): ?>
                            <li class="page-item">
                                <a class="page-link" href="index.php?page=1<?php echo $searchParam; ?>">1</a>
                            </li>
                            <?php if ($startPage > 2): ?>
                                <li class="page-item disabled">
                                    <span class="page-link">...</span>
                                </li>
                            <?php endif; ?>
                        <?php endif; ?>
                        
                        <?php for ($i = $startPage; $i <= $endPage; $i++): ?>
                            <li class="page-item <?php echo $i == $currentPageNum ? 'active' : ''; ?>">
                                <a class="page-link" href="index.php?page=<?php echo $i; ?><?php echo $searchParam; ?>"><?php echo $i; ?></a>
                            </li>
                        <?php endfor; ?>
                        
                        <?php if ($endPage < $totalPages): ?>
                            <?php if ($endPage < $totalPages - 1): ?>
                                <li class="page-item disabled">
                                    <span class="page-link">...</span>
                                </li>
                            <?php endif; ?>
                            <li class="page-item">
                                <a class="page-link" href="index.php?page=<?php echo $totalPages; ?><?php echo $searchParam; ?>"><?php echo $totalPages; ?></a>
                            </li>
                        <?php endif; ?>
                        
                        <!-- Next Button -->
                        <li class="page-item <?php echo $currentPageNum >= $totalPages ? 'disabled' : ''; ?>">
                            <a class="page-link" href="index.php?page=<?php echo $currentPageNum + 1; ?><?php echo $searchParam; ?>">Next</a>
                        </li>
                    </ul>
                </nav>
            </div>
            <?php endif; ?>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Chart data from PHP
        const chartData = <?php echo json_encode($chartData); ?>;
        
        // Prepare labels and data for monthly display
        const labels = chartData.map(item => {
            const date = new Date(item.payment_month + '-01');
            return date.toLocaleDateString('en-US', { month: 'short' });
        });
        
        const amounts = chartData.map(item => parseFloat(item.total_amount));
        
        // Create chart
        const ctx = document.getElementById('payoutChart');
        const payoutChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Payout Amount (MMK)',
                    data: amounts,
                    borderColor: '#1a73e8',
                    backgroundColor: 'rgba(26, 115, 232, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    pointBackgroundColor: '#1a73e8',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                        labels: {
                            usePointStyle: true,
                            padding: 15
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        padding: 12,
                        titleFont: {
                            size: 14,
                            weight: 'bold'
                        },
                        bodyFont: {
                            size: 13
                        },
                        callbacks: {
                            label: function(context) {
                                return 'Amount: ' + new Intl.NumberFormat('en-US').format(context.parsed.y) + ' MMK';
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)'
                        },
                        ticks: {
                            callback: function(value) {
                                return new Intl.NumberFormat('en-US').format(value);
                            }
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
    </script>
</body>
</html>
