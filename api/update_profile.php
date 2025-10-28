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
        'message' => 'Method not allowed',
        'method' => $_SERVER['REQUEST_METHOD'],
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

        $partner['profile_image'] = $profileImagePath;
        $result = $auth->updatePartner($partnerId, ['profile_image' => $profileImagePath]);
        echo json_encode($result);
        exit();

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
$allowedFields = ['contact_name', 'company_name', 'phone', 'website', 'address', 'city', 'state', 'national_id_card_number'];
foreach ($allowedFields as $field) {
    if (isset($_POST[$field])) {
        $updateData[$field] = trim($_POST[$field]);
    }
}

// Add profile image path if uploaded
if ($profileImagePath) {
    $updateData['profile_image'] = $profileImagePath;
}

// Handle National ID card images upload
// Front image
if (isset($_FILES['national_id_card_front_image']) && $_FILES['national_id_card_front_image']['error'] === UPLOAD_ERR_OK) {
    $uploadDir = __DIR__ . '/../uploads/id_cards/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    $file = $_FILES['national_id_card_front_image'];
    $fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $allowedTypes = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    if (!in_array($fileExtension, $allowedTypes)) {
        echo json_encode([
            'success' => false,
            'message' => 'Invalid front ID image type. Only JPG, PNG, GIF, and WebP are allowed.'
        ]);
        exit();
    }
    if ($file['size'] > 5 * 1024 * 1024) {
        echo json_encode([
            'success' => false,
            'message' => 'Front ID image too large. Maximum size is 5MB.'
        ]);
        exit();
    }
    $fileName = 'nid_front_partner_' . $partnerId . '_' . time() . '.' . $fileExtension;
    $filePath = $uploadDir . $fileName;
    if (move_uploaded_file($file['tmp_name'], $filePath)) {
        // Delete old if exists
        if (!empty($partner['national_id_card_front_image']) && file_exists(__DIR__ . '/../' . $partner['national_id_card_front_image'])) {
            unlink(__DIR__ . '/../' . $partner['national_id_card_front_image']);
        }
        $updateData['national_id_card_front_image'] = 'uploads/id_cards/' . $fileName;
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Failed to upload front ID image'
        ]);
        exit();
    }
}

// Back image
if (isset($_FILES['national_id_card_back_image']) && $_FILES['national_id_card_back_image']['error'] === UPLOAD_ERR_OK) {
    $uploadDir = __DIR__ . '/../uploads/id_cards/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    $file = $_FILES['national_id_card_back_image'];
    $fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $allowedTypes = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    if (!in_array($fileExtension, $allowedTypes)) {
        echo json_encode([
            'success' => false,
            'message' => 'Invalid back ID image type. Only JPG, PNG, GIF, and WebP are allowed.'
        ]);
        exit();
    }
    if ($file['size'] > 5 * 1024 * 1024) {
        echo json_encode([
            'success' => false,
            'message' => 'Back ID image too large. Maximum size is 5MB.'
        ]);
        exit();
    }
    $fileName = 'nid_back_partner_' . $partnerId . '_' . time() . '.' . $fileExtension;
    $filePath = $uploadDir . $fileName;
    if (move_uploaded_file($file['tmp_name'], $filePath)) {
        // Delete old if exists
        if (!empty($partner['national_id_card_back_image']) && file_exists(__DIR__ . '/../' . $partner['national_id_card_back_image'])) {
            unlink(__DIR__ . '/../' . $partner['national_id_card_back_image']);
        }
        $updateData['national_id_card_back_image'] = 'uploads/id_cards/' . $fileName;
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Failed to upload back ID image'
        ]);
        exit();
    }
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
