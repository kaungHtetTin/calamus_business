<nav class="navbar navbar-expand-lg admin-navbar" style="background: white; border-bottom: 1px solid #e8eaed; padding: 12px 24px;">
    <div class="container-fluid">
        <button class="btn btn-sm me-2" type="button" data-bs-toggle="offcanvas" data-bs-target="#adminSidebar" aria-controls="adminSidebar" style="border: 1px solid #dadce0;">
            <i class="fas fa-bars" style="color: #e8eaed;"></i>
        </button>
        <h1 class="admin-title mb-0" style="font-size: 22px; font-weight: 400; color: #e8eaed;">
            <i class="fas fa-shield-alt me-2"></i>Admin Dashboard
        </h1>
        <div class="ms-auto d-flex align-items-center">
            <span class="text-muted me-3" style="color: #e8eaed;">Welcome, <?php echo htmlspecialchars($adminAuth->getAdminUsername()); ?></span>
            <button class="btn btn-sm btn-outline-secondary" style="color: #e8eaed;" onclick="window.location.href='logout.php'">
                <i class="fas fa-sign-out-alt me-2" ></i>Logout
            </button>
        </div>
    </div>
</nav>