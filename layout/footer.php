                <?php include 'footer_content.php'; ?>
            </div>
            <div class="portal-footer">Calamus Education Partner Portal</div>
        </div>
    </div>
</div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Pass PHP data to JavaScript -->
    <script>
        window.sessionToken = '<?php echo $sessionToken; ?>';
        window.monthlyEarningsData = <?php echo json_encode($dashboardData['monthly_earnings'] ?? []); ?>;
    </script>
    
    <script src="js/theme.js"></script>
    <script src="js/app.js"></script>
</body>
</html>