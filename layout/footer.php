            <?php include 'footer_content.php'; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Pass PHP data to JavaScript -->
    <script>
        window.sessionToken = '<?php echo $sessionToken; ?>';
        window.monthlyEarningsData = <?php echo json_encode($dashboardData['monthly_earnings'] ?? []); ?>;
        console.log('Footer: Monthly earnings data loaded:', window.monthlyEarningsData);
    </script>
    
    <!-- Load common app JavaScript -->
    <script src="js/app.js"></script>
</body>
</html>