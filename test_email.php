<?php
require_once 'classes/autoload.php';

echo "<h2>Email Configuration Test</h2>";

// Test email configuration
echo "<h3>Email Configuration:</h3>";
echo "<ul>";
echo "<li><strong>From Address:</strong> " . EMAIL_FROM_ADDRESS . "</li>";
echo "<li><strong>From Name:</strong> " . EMAIL_FROM_NAME . "</li>";
echo "<li><strong>Reply-To:</strong> " . EMAIL_REPLY_TO . "</li>";
echo "<li><strong>Support Address:</strong> " . EMAIL_SUPPORT_ADDRESS . "</li>";
echo "</ul>";

// Test email headers
echo "<h3>Email Headers Test:</h3>";
$headers = getEmailHeaders('test');
echo "<pre>" . htmlspecialchars($headers) . "</pre>";

// Test email sending (uncomment to actually send emails)
echo "<h3>Email Sending Test:</h3>";
echo "<p><strong>Note:</strong> Uncomment the code below to actually send test emails.</p>";

/*
// Test email sending
$testEmail = "test@example.com"; // Change this to your test email
$subject = "Test Email from Affiliate System";
$message = "
<h2>Test Email</h2>
<p>This is a test email from the affiliate system.</p>
<p>If you receive this email, the email configuration is working correctly!</p>
<p>Sent at: " . date('Y-m-d H:i:s') . "</p>
";

$success = sendEmail($testEmail, $subject, $message, 'test');

if ($success) {
    echo "<p style='color: green;'>✅ Test email sent successfully!</p>";
} else {
    echo "<p style='color: red;'>❌ Failed to send test email.</p>";
}
*/

// Check if logs directory exists and is writable
echo "<h3>Logs Directory Check:</h3>";
$logsDir = __DIR__ . '/logs';
if (is_dir($logsDir)) {
    if (is_writable($logsDir)) {
        echo "<p style='color: green;'>✅ Logs directory exists and is writable.</p>";
    } else {
        echo "<p style='color: red;'>❌ Logs directory exists but is not writable.</p>";
    }
} else {
    echo "<p style='color: red;'>❌ Logs directory does not exist.</p>";
}

// Test email logging
echo "<h3>Email Logging Test:</h3>";
logEmailAttempt('test@example.com', 'Test Subject', true, '');
echo "<p style='color: green;'>✅ Email logging test completed. Check logs/email.log file.</p>";

// Display recent log entries
$logFile = $logsDir . '/email.log';
if (file_exists($logFile)) {
    echo "<h3>Recent Email Log Entries:</h3>";
    $logContent = file_get_contents($logFile);
    $logLines = explode("\n", $logContent);
    $recentLines = array_slice($logLines, -5); // Last 5 lines
    
    echo "<pre>";
    foreach ($recentLines as $line) {
        if (!empty(trim($line))) {
            echo htmlspecialchars($line) . "\n";
        }
    }
    echo "</pre>";
}

echo "<h3>PHP Mail Configuration:</h3>";
echo "<ul>";
echo "<li><strong>mail() function:</strong> " . (function_exists('mail') ? 'Available' : 'Not Available') . "</li>";
echo "<li><strong>sendmail_path:</strong> " . ini_get('sendmail_path') . "</li>";
echo "<li><strong>SMTP:</strong> " . ini_get('SMTP') . "</li>";
echo "<li><strong>smtp_port:</strong> " . ini_get('smtp_port') . "</li>";
echo "<li><strong>sendmail_from:</strong> " . ini_get('sendmail_from') . "</li>";
echo "</ul>";

echo "<h3>Recommendations:</h3>";
echo "<ul>";
echo "<li>If using XAMPP, configure SMTP settings in php.ini</li>";
echo "<li>For production, consider using PHPMailer with SMTP</li>";
echo "<li>Update email addresses in email_config.php</li>";
echo "<li>Test with a real email address to verify delivery</li>";
echo "</ul>";

echo "<p><a href='admin_console.php'>← Back to Admin Console</a></p>";
?>
