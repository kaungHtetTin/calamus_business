<?php
/**
 * Course and Package Data API
 * 
 * Handles fetching course categories, courses, and packages
 */

require_once __DIR__ . '/../classes/autoload.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

$db = new Database();

// Get endpoint from URL parameter
$endpoint = $_GET['endpoint'] ?? '';

try {
    switch ($endpoint) {
        case 'get_categories':
            // Get all course categories
            $query = "SELECT * FROM course_categories ORDER BY id ASC";
            $categories = $db->read($query);
            
            echo json_encode([
                'success' => true,
                'categories' => $categories ? $categories : []
            ]);
            break;
            
        case 'get_courses':
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('Method not allowed');
            }
            
            $input = json_decode(file_get_contents('php://input'), true);
            
            if (!isset($input['category_id'])) {
                throw new Exception('Missing category_id');
            }
            
            $categoryId = $input['category_id'];
            
            // Get courses by category
            $query = "SELECT course_id, title, fee, major FROM courses WHERE major = (
                        SELECT keyword FROM course_categories WHERE id = '$categoryId'
                      ) ORDER BY sorting ASC";
            $courses = $db->read($query);
            
            echo json_encode([
                'success' => true,
                'courses' => $courses ? $courses : []
            ]);
            break;
            
        case 'get_packages':
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('Method not allowed');
            }
            
            $input = json_decode(file_get_contents('php://input'), true);
            
            if (!isset($input['category_id'])) {
                throw new Exception('Missing category_id');
            }
            
            $categoryId = $input['category_id'];
            
            // Get packages by category
            $query = "SELECT id, name, description, price FROM package_plans 
                      WHERE course_category_id = '$categoryId' AND status = 'active' 
                      ORDER BY sort_order ASC";
            $packages = $db->read($query);
            
            echo json_encode([
                'success' => true,
                'packages' => $packages ? $packages : []
            ]);
            break;
            
        default:
            throw new Exception('Invalid endpoint');
    }
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>
