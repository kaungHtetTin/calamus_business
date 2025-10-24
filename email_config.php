<?php
/**
 * Email Configuration
 * 
 * This file contains email settings for the affiliate system.
 * Update these settings according to your email server configuration.
 */

// Include the autoloader
require_once __DIR__ . '/classes/autoload.php';

// Email Configuration
define('EMAIL_FROM_ADDRESS', 'noreply@yourcompany.com');
define('EMAIL_FROM_NAME', 'Your Company Name');
define('EMAIL_REPLY_TO', 'support@yourcompany.com');
define('EMAIL_SUPPORT_ADDRESS', 'support@yourcompany.com');

// SMTP Configuration (if using SMTP instead of mail())
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USERNAME', 'your-email@gmail.com');
define('SMTP_PASSWORD', 'your-app-password');
define('SMTP_ENCRYPTION', 'tls');

// Email Templates
define('EMAIL_TEMPLATE_VERIFICATION', 'verification');
define('EMAIL_TEMPLATE_WELCOME', 'welcome');
define('EMAIL_TEMPLATE_PASSWORD_RESET', 'password_reset');
define('EMAIL_TEMPLATE_COMMISSION_EARNED', 'commission_earned');

/**
 * Get email headers for sending emails
 * 
 * @param string $type Type of email (verification, welcome, etc.)
 * @return string Formatted email headers
 */
function getEmailHeaders($type = 'default') {
    $headers = "From: " . EMAIL_FROM_NAME . " <" . EMAIL_FROM_ADDRESS . ">\r\n";
    $headers .= "Reply-To: " . EMAIL_REPLY_TO . "\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
    $headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    
    return $headers;
}

/**
 * Send email using PHP mail() function
 * 
 * @param string $to Recipient email address
 * @param string $subject Email subject
 * @param string $message Email message (HTML)
 * @param string $type Email type for headers
 * @return bool Success status
 */
function sendEmail($to, $subject, $message, $type = 'default') {
    $headers = getEmailHeaders($type);
    
    // Add additional headers based on type
    switch ($type) {
        case 'verification':
            $headers .= "X-Priority: 3\r\n";
            break;
        case 'welcome':
            $headers .= "X-Priority: 3\r\n";
            break;
        case 'password_reset':
            $headers .= "X-Priority: 1\r\n";
            break;
    }
    
    return mail($to, $subject, $message, $headers);
}

/**
 * Send email using SMTP (requires PHPMailer or similar)
 * This is a placeholder function - implement if you want to use SMTP
 * 
 * @param string $to Recipient email address
 * @param string $subject Email subject
 * @param string $message Email message (HTML)
 * @param string $type Email type
 * @return bool Success status
 */
function sendEmailSMTP($to, $subject, $message, $type = 'default') {
    // This would require PHPMailer or similar library
    // For now, fall back to regular mail()
    return sendEmail($to, $subject, $message, $type);
}

/**
 * Get email template
 * 
 * @param string $templateName Template name
 * @param array $variables Variables to replace in template
 * @return string Processed template
 */
function getEmailTemplate($templateName, $variables = []) {
    $templatePath = __DIR__ . '/email_templates/' . $templateName . '.html';
    
    if (!file_exists($templatePath)) {
        return false;
    }
    
    $template = file_get_contents($templatePath);
    
    // Replace variables in template
    foreach ($variables as $key => $value) {
        $template = str_replace('{{' . $key . '}}', $value, $template);
    }
    
    return $template;
}

/**
 * Log email sending attempts
 * 
 * @param string $to Recipient email
 * @param string $subject Email subject
 * @param bool $success Success status
 * @param string $error Error message if failed
 */
function logEmailAttempt($to, $subject, $success, $error = '') {
    $logEntry = date('Y-m-d H:i:s') . " - Email to: $to, Subject: $subject, Success: " . ($success ? 'Yes' : 'No');
    if (!$success && $error) {
        $logEntry .= ", Error: $error";
    }
    $logEntry .= "\n";
    
    file_put_contents(__DIR__ . '/logs/email.log', $logEntry, FILE_APPEND | LOCK_EX);
}
?>
