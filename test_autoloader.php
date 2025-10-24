<?php
/**
 * Autoloader Test Script
 * 
 * This script tests if the autoloader is working correctly with snake_case file names.
 */

echo "<h1>Autoloader Test</h1>";

// Include the autoloader
require_once 'classes/autoload.php';

echo "<h2>Testing Class Loading:</h2>";

// Test each class
$testClasses = [
    'PartnerAuth' => 'partner_auth.php',
    'PartnerDashboard' => 'partner_dashboard.php', 
    'AffiliateTracker' => 'affiliate_tracker.php',
    'CommissionManager' => 'commission_manager.php',
    'PromotionCodeManager' => 'promotion_code_manager.php',
    'PackagePlanManager' => 'package_plan_manager.php',
    'VipSubscriptionHandler' => 'vip_subscription_handler.php',
    'Database' => 'Database.php'
];

foreach ($testClasses as $className => $fileName) {
    echo "<p>Testing $className (from $fileName): ";
    
    if (class_exists($className)) {
        echo "✅ <span style='color: green;'>LOADED</span>";
        
        // Try to instantiate
        try {
            $instance = new $className();
            echo " ✅ <span style='color: green;'>INSTANTIATED</span>";
        } catch (Exception $e) {
            echo " ❌ <span style='color: red;'>INSTANTIATION FAILED: " . $e->getMessage() . "</span>";
        }
    } else {
        echo "❌ <span style='color: red;'>NOT LOADED</span>";
    }
    echo "</p>";
}

echo "<h2>Available Classes:</h2>";
if (function_exists('getAvailableClasses')) {
    $availableClasses = getAvailableClasses();
    echo "<p>Classes found: " . implode(', ', $availableClasses) . "</p>";
} else {
    echo "<p>❌ getAvailableClasses function not available</p>";
}

echo "<h2>File Structure Check:</h2>";
$files = glob('classes/*.php');
foreach ($files as $file) {
    $fileName = basename($file);
    echo "<p>📁 $fileName</p>";
}

echo "<h2>Test Complete</h2>";
echo "<p>If all classes show ✅ LOADED and ✅ INSTANTIATED, the autoloader is working correctly!</p>";
?>
