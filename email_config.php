<?php
/**
 * Email Configuration
 * 
 * This file contains email settings for the affiliate system.
 * Update these settings according to your email server configuration.
 */

// Include the autoloader
require_once __DIR__ . '/classes/autoload.php';

// Load PHPMailer
require_once __DIR__ . '/vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Email Configuration from env or defaults
define('EMAIL_FROM_ADDRESS', 'business@calamuseducation.com');
define('EMAIL_FROM_NAME', 'Calamus Education');
define('EMAIL_REPLY_TO', 'business@calamuseducation.com');
define('EMAIL_SUPPORT_ADDRESS', 'business@calamuseducation.com');

// SMTP Configuration - Hostinger
define('SMTP_HOST', 'smtp.hostinger.com');
define('SMTP_PORT', 465);
define('SMTP_USERNAME', 'business@calamuseducation.com');
define('SMTP_PASSWORD', '@$Calamus5241$@');
define('SMTP_ENCRYPTION', PHPMailer::ENCRYPTION_SMTPS); // SSL

// Email Templates
define('EMAIL_TEMPLATE_VERIFICATION', 'verification');
define('EMAIL_TEMPLATE_WELCOME', 'welcome');
define('EMAIL_TEMPLATE_PASSWORD_RESET', 'password_reset');
define('EMAIL_TEMPLATE_COMMISSION_EARNED', 'commission_earned');
define('EMAIL_TEMPLATE_PAYOUT_NOTIFICATION', 'payout_notification');

/**
 * Get the base URL dynamically
 * Detects protocol (http/https) and host from server variables
 * 
 * @return string Base URL (e.g., https://example.com/business)
 */
function getBaseUrl() {
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    
    // Get the base path from the script directory
    $scriptPath = dirname($_SERVER['SCRIPT_NAME']);
    
    // Remove 'business' if it's at the end of the path (for backward compatibility)
    $basePath = str_replace('/business', '', $scriptPath);
    
    // If we're in the business directory, add it to the path
    if (strpos($_SERVER['SCRIPT_NAME'], '/business/') !== false || strpos($_SERVER['DOCUMENT_ROOT'], '/business') !== false) {
        $basePath = '/business';
    } else {
        $basePath = '';
    }
    
    return $protocol . '://' . $host;
}

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
 * Send email using PHPMailer with SMTP (default method)
 * 
 * @param string $to Recipient email address
 * @param string $subject Email subject
 * @param string $message Email message (HTML)
 * @param string $type Email type
 * @return bool Success status
 */
function sendEmail($to, $subject, $message, $type = 'default') {
    // Use SMTP by default for better deliverability
    return sendEmailSMTP($to, $subject, $message, $type);
}

/**
 * Send email using PHPMailer with SMTP
 * 
 * @param string $to Recipient email address
 * @param string $subject Email subject
 * @param string $message Email message (HTML)
 * @param string $type Email type
 * @return bool Success status
 */
function sendEmailSMTP($to, $subject, $message, $type = 'default') {
    try {
        $mail = new PHPMailer(true);
        
        // Server settings
        $mail->isSMTP();
        $mail->Host = SMTP_HOST;
        $mail->SMTPAuth = true;
        $mail->Username = SMTP_USERNAME;
        $mail->Password = SMTP_PASSWORD;
        $mail->SMTPSecure = SMTP_ENCRYPTION;
        $mail->Port = SMTP_PORT;
        $mail->CharSet = 'UTF-8';
        
        // Recipients
        $mail->setFrom(EMAIL_FROM_ADDRESS, EMAIL_FROM_NAME);
        $mail->addAddress($to);
        $mail->addReplyTo(EMAIL_REPLY_TO, EMAIL_FROM_NAME);
        
        // Content
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $message;
        $mail->AltBody = strip_tags($message); // Plain text version
        
        // Send email
        $mail->send();
        
        // Log success
        logEmailAttempt($to, $subject, true);
        return true;
        
    } catch (Exception $e) {
        // Log error
        logEmailAttempt($to, $subject, false, $mail->ErrorInfo);
        error_log("Email sending failed: " . $mail->ErrorInfo);
        return false;
    }
}

/**
 * Get email template and replace variables
 * 
 * @param string $templateName Template name
 * @param array $variables Variables to replace in template
 * @return string|false Processed template or false on error
 */
function getEmailTemplate($templateName, $variables = []) {
    $templatePath = __DIR__ . '/email_templates/' . $templateName . '.html';
    
    if (!file_exists($templatePath)) {
        error_log("Email template not found: $templatePath");
        return false;
    }
    
    $template = file_get_contents($templatePath);
    
    if ($template === false) {
        error_log("Failed to read email template: $templatePath");
        return false;
    }
    
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
