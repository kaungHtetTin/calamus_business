<?php
/**
 * Autoloader for PHP Classes
 * 
 * This file automatically loads all PHP classes when they are needed.
 * It follows PSR-4 autoloading standards for better organization.
 */

// Define the base directory for classes
define('CLASSES_DIR', __DIR__);

// Enable debug mode for development
if (!defined('DEBUG')) {
    define('DEBUG', false); // Set to true for development
}

/**
 * Autoload function for classes
 * 
 * @param string $className The name of the class to load
 */
spl_autoload_register(function ($className) {
    // Try PascalCase first (e.g., PartnerAuth.php)
    $filePath = CLASSES_DIR . DIRECTORY_SEPARATOR . $className . '.php';
    
    if (file_exists($filePath)) {
        require_once $filePath;
        return true;
    }
    
    // Try snake_case (e.g., partner_auth.php)
    $snakeCase = strtolower(preg_replace('/([a-z])([A-Z])/', '$1_$2', $className));
    $filePath = CLASSES_DIR . DIRECTORY_SEPARATOR . $snakeCase . '.php';
    
    if (file_exists($filePath)) {
        require_once $filePath;
        return true;
    }
    
    // Debug: Log failed attempts
    if (defined('DEBUG') && DEBUG) {
        error_log("Autoloader failed to find class: $className (tried: $className.php and $snakeCase.php)");
    }
    
    return false;
});

/**
 * Load all required classes
 * This ensures all classes are available when needed
 */
function loadAllClasses() {
    // Get all PHP files in the classes directory
    $files = glob(CLASSES_DIR . DIRECTORY_SEPARATOR . '*.php');
    
    foreach ($files as $file) {
        $fileName = basename($file, '.php');
        
        // Skip autoload.php
        if ($fileName === 'autoload') {
            continue;
        }
        
        // Convert snake_case to PascalCase for class name
        $className = str_replace('_', '', ucwords($fileName, '_'));
        
        // Load the file if class doesn't exist
        if (!class_exists($className)) {
            require_once $file;
        }
    }
}

// Load all classes by default
loadAllClasses();

/**
 * Get class file path
 * 
 * @param string $className The name of the class
 * @return string|false The file path or false if not found
 */
function getClassPath($className) {
    $filePath = CLASSES_DIR . DIRECTORY_SEPARATOR . $className . '.php';
    return file_exists($filePath) ? $filePath : false;
}

/**
 * Check if class exists and is loaded
 * 
 * @param string $className The name of the class
 * @return bool True if class exists and is loaded
 */
function isClassLoaded($className) {
    return class_exists($className);
}

/**
 * Get all available classes in the classes directory
 * 
 * @return array Array of class names
 */
function getAvailableClasses() {
    $classes = [];
    $files = glob(CLASSES_DIR . DIRECTORY_SEPARATOR . '*.php');
    
    foreach ($files as $file) {
        $fileName = basename($file, '.php');
        if ($fileName !== 'autoload') { // Exclude this file
            // Convert snake_case to PascalCase for class name
            $className = str_replace('_', '', ucwords($fileName, '_'));
            $classes[] = $className;
        }
    }
    
    return $classes;
}

// Display loaded classes in development mode
if (defined('DEBUG') && DEBUG) {
    echo "<!-- Loaded Classes: " . implode(', ', getAvailableClasses()) . " -->\n";
}
?>
