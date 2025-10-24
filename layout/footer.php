            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Pass PHP data to JavaScript -->
    <script>
        window.sessionToken = '<?php echo $sessionToken; ?>';
        window.monthlyEarningsData = <?php echo json_encode($dashboardData['monthly_earnings']); ?>;
    </script>
    
    <!-- Load dashboard JavaScript -->
    <script src="js/dashboard.js"></script>
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
