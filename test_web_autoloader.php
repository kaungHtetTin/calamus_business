<?php
/**
 * Final Autoloader Test
 * 
 * This script tests the autoloader in a web context to ensure it works properly.
 */

// Set content type
header('Content-Type: application/json');

// Include the autoloader
require_once 'classes/autoload.php';

// Test results
$results = [
    'autoloader_loaded' => true,
    'classes_available' => [],
    'instantiation_tests' => [],
    'timestamp' => date('Y-m-d H:i:s')
];

// Test class availability
$testClasses = [
    'PartnerAuth',
    'PartnerDashboard', 
    'AffiliateTracker',
    'CommissionManager',
    'PromotionCodeManager',
    'PackagePlanManager',
    'VipSubscriptionHandler',
    'Database'
];

foreach ($testClasses as $className) {
    $results['classes_available'][$className] = class_exists($className);
    
    // Test instantiation
    try {
        $instance = new $className();
        $results['instantiation_tests'][$className] = 'success';
    } catch (Exception $e) {
        $results['instantiation_tests'][$className] = 'failed: ' . $e->getMessage();
    }
}

// Test API functionality
try {
    $auth = new PartnerAuth();
    $results['api_test'] = 'PartnerAuth instantiated successfully';
} catch (Exception $e) {
    $results['api_test'] = 'PartnerAuth failed: ' . $e->getMessage();
}

// Return JSON response
echo json_encode($results, JSON_PRETTY_PRINT);
?>
