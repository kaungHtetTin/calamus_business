<?php
/**
 * File Structure Test Script
 * 
 * This script tests the new file structure and class loading system.
 * Run this to verify that all classes are properly organized and loaded.
 */

echo "<h1>File Structure Reorganization Test</h1>";

// Test 1: Include autoloader
echo "<h2>Test 1: Autoloader</h2>";
try {
    require_once 'classes/autoload.php';
    echo "✅ Autoloader loaded successfully<br>";
} catch (Exception $e) {
    echo "❌ Autoloader failed: " . $e->getMessage() . "<br>";
}

// Test 2: Check if classes are loaded
echo "<h2>Test 2: Class Loading</h2>";
$classes = [
    'Database',
    'PartnerAuth', 
    'PartnerDashboard',
    'AffiliateTracker',
    'CommissionManager',
    'PromotionCodeManager',
    'PackagePlanManager',
    'VipSubscriptionHandler'
];

foreach ($classes as $class) {
    if (class_exists($class)) {
        echo "✅ $class class loaded successfully<br>";
    } else {
        echo "❌ $class class failed to load<br>";
    }
}

// Test 3: Database connection
echo "<h2>Test 3: Database Connection</h2>";
try {
    $db = new Database();
    $connection = $db->connect();
    
    if ($connection) {
        echo "✅ Database connection successful<br>";
        mysqli_close($connection);
    } else {
        echo "❌ Database connection failed<br>";
    }
} catch (Exception $e) {
    echo "❌ Database error: " . $e->getMessage() . "<br>";
}

// Test 4: PartnerAuth instantiation
echo "<h2>Test 4: PartnerAuth Class</h2>";
try {
    $auth = new PartnerAuth();
    echo "✅ PartnerAuth instantiated successfully<br>";
} catch (Exception $e) {
    echo "❌ PartnerAuth error: " . $e->getMessage() . "<br>";
}

// Test 5: Check file structure
echo "<h2>Test 5: File Structure</h2>";
$requiredFiles = [
    'classes/autoload.php',
    'classes/Database.php',
    'classes/PartnerAuth.php',
    'classes/PartnerDashboard.php',
    'classes/AffiliateTracker.php',
    'classes/CommissionManager.php',
    'classes/PromotionCodeManager.php',
    'classes/PackagePlanManager.php',
    'classes/VipSubscriptionHandler.php',
    'connect.php',
    'email_config.php'
];

foreach ($requiredFiles as $file) {
    if (file_exists($file)) {
        echo "✅ $file exists<br>";
    } else {
        echo "❌ $file missing<br>";
    }
}

// Test 6: API files
echo "<h2>Test 6: API Files</h2>";
$apiFiles = [
    'api/login_partner.php',
    'api/register_partner.php',
    'api/code_validation.php',
    'api/promotion_codes.php',
    'api/index.php'
];

foreach ($apiFiles as $file) {
    if (file_exists($file)) {
        echo "✅ $file exists<br>";
    } else {
        echo "❌ $file missing<br>";
    }
}

// Test 7: Available classes function
echo "<h2>Test 7: Available Classes</h2>";
if (function_exists('getAvailableClasses')) {
    $availableClasses = getAvailableClasses();
    echo "Available classes: " . implode(', ', $availableClasses) . "<br>";
} else {
    echo "❌ getAvailableClasses function not available<br>";
}

// Test 8: Email configuration
echo "<h2>Test 8: Email Configuration</h2>";
if (defined('EMAIL_FROM_ADDRESS')) {
    echo "✅ Email configuration loaded<br>";
    echo "From Address: " . EMAIL_FROM_ADDRESS . "<br>";
    echo "From Name: " . EMAIL_FROM_NAME . "<br>";
} else {
    echo "❌ Email configuration not loaded<br>";
}

echo "<h2>Test Summary</h2>";
echo "<p>If all tests show ✅, the file structure reorganization was successful!</p>";
echo "<p><a href='index.php'>← Back to Partner Dashboard</a></p>";
echo "<p><a href='admin_console.php'>← Go to Admin Console</a></p>";
?>
