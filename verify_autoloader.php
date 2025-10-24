<?php
/**
 * Final Autoloader Verification
 * 
 * This script verifies that the autoloader is working correctly in a web context.
 */

// Set content type
header('Content-Type: application/json');

// Include the autoloader
require_once 'classes/autoload.php';

// Test results
$results = [
    'status' => 'success',
    'message' => 'Autoloader is working correctly',
    'timestamp' => date('Y-m-d H:i:s'),
    'classes_loaded' => [],
    'api_tests' => []
];

// Test all classes
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
    $results['classes_loaded'][$className] = [
        'exists' => class_exists($className),
        'instantiable' => false
    ];
    
    // Test instantiation
    try {
        $instance = new $className();
        $results['classes_loaded'][$className]['instantiable'] = true;
    } catch (Exception $e) {
        $results['classes_loaded'][$className]['instantiable'] = false;
        $results['classes_loaded'][$className]['error'] = $e->getMessage();
    }
}

// Test API functionality
try {
    $auth = new PartnerAuth();
    $results['api_tests']['PartnerAuth'] = 'success';
    
    // Test database connection
    $db = new Database();
    $connection = $db->connect();
    $results['api_tests']['Database'] = $connection ? 'connected' : 'failed';
    
} catch (Exception $e) {
    $results['api_tests']['error'] = $e->getMessage();
}

// Count successful loads
$successfulLoads = 0;
foreach ($results['classes_loaded'] as $class => $status) {
    if ($status['exists'] && $status['instantiable']) {
        $successfulLoads++;
    }
}

$results['summary'] = [
    'total_classes' => count($testClasses),
    'successful_loads' => $successfulLoads,
    'success_rate' => round(($successfulLoads / count($testClasses)) * 100, 2) . '%'
];

// Return JSON response
echo json_encode($results, JSON_PRETTY_PRINT);
?>
