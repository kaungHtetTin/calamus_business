            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Pass PHP data to JavaScript -->
    <script>
        window.sessionToken = '<?php echo $sessionToken; ?>';
        window.monthlyEarningsData = <?php echo json_encode($dashboardData['monthly_earnings']); ?>;
        
        // Ensure Bootstrap is loaded before initializing dashboard
        document.addEventListener('DOMContentLoaded', function() {
            // Wait for Bootstrap to be available
            function waitForBootstrap() {
                if (typeof bootstrap !== 'undefined') {
                    // Bootstrap is ready, load dashboard script
                    const script = document.createElement('script');
                    script.src = 'js/dashboard.js';
                    document.head.appendChild(script);
                } else {
                    // Retry after a short delay
                    setTimeout(waitForBootstrap, 50);
                }
            }
            waitForBootstrap();
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
