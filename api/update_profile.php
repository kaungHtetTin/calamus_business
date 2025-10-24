<?php
require_once __DIR__ . '/../classes/autoload.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'success' => false,
        'message' => 'Method not allowed'
    ]);
    exit();
}

// Initialize authentication
$auth = new PartnerAuth();

// Get session token
$sessionToken = $_POST['session_token'] ?? '';

if (empty($sessionToken)) {
    echo json_encode([
        'success' => false,
        'message' => 'Session token is required'
    ]);
    exit();
}

// Validate session
$sessionResult = $auth->validateSession($sessionToken);
if (!$sessionResult['success']) {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid session'
    ]);
    exit();
}

$partnerId = $sessionResult['partner']['id'];
$partner = $sessionResult['partner'];

// Handle profile image removal
if (isset($_POST['remove_image']) && $_POST['remove_image'] === '1') {
    // Delete current profile image if exists
    if (!empty($partner['profile_image']) && file_exists(__DIR__ . '/../' . $partner['profile_image'])) {
        unlink(__DIR__ . '/../' . $partner['profile_image']);
    }
    
    // Update database to remove profile image
    $result = $auth->updatePartner($partnerId, ['profile_image' => '']);
    echo json_encode($result);
    exit();
}

// Handle profile image upload
$profileImagePath = null;
if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
    $uploadDir = __DIR__ . '/../uploads/profile_images/';
    
    // Create directory if it doesn't exist
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }
    
    $file = $_FILES['profile_image'];
    $fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    
    // Validate file type
    $allowedTypes = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    if (!in_array($fileExtension, $allowedTypes)) {
        echo json_encode([
            'success' => false,
            'message' => 'Invalid file type. Only JPG, PNG, GIF, and WebP are allowed.'
        ]);
        exit();
    }
    
    // Validate file size (max 5MB)
    if ($file['size'] > 5 * 1024 * 1024) {
        echo json_encode([
            'success' => false,
            'message' => 'File size too large. Maximum size is 5MB.'
        ]);
        exit();
    }
    
    // Generate unique filename
    $fileName = 'partner_' . $partnerId . '_' . time() . '.' . $fileExtension;
    $filePath = $uploadDir . $fileName;
    
    // Move uploaded file
    if (move_uploaded_file($file['tmp_name'], $filePath)) {
        $profileImagePath = 'uploads/profile_images/' . $fileName;
        
        // Delete old profile image if exists
        if (!empty($partner['profile_image']) && file_exists(__DIR__ . '/../' . $partner['profile_image'])) {
            unlink(__DIR__ . '/../' . $partner['profile_image']);
        }
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Failed to upload image'
        ]);
        exit();
    }
}

// Prepare update data
$updateData = [];

// Handle regular form fields
$allowedFields = ['contact_name', 'company_name', 'phone', 'website', 'payment_method', 'payment_details'];
foreach ($allowedFields as $field) {
    if (isset($_POST[$field])) {
        $updateData[$field] = trim($_POST[$field]);
    }
}

// Add profile image path if uploaded
if ($profileImagePath) {
    $updateData['profile_image'] = $profileImagePath;
}

// Validate required fields
if (empty($updateData['contact_name'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Contact name is required'
    ]);
    exit();
}

// Update partner profile
$result = $auth->updatePartner($partnerId, $updateData);

if ($result['success']) {
    // If profile image was uploaded, include the new path in response
    if ($profileImagePath) {
        $result['profile_image'] = $profileImagePath;
    }
}

echo json_encode($result);
?>
