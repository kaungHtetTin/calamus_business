<?php
/**
 * Email Verification Page
 * Partners verify their email address after registration
 */

// Get verification code from URL
$code = $_GET['code'] ?? '';
$verificationMessage = '';
$verificationSuccess = false;
$verificationLink = '';

if (!empty($code)) {
    require_once 'classes/autoload.php';
    
    $auth = new PartnerAuth();
    $result = $auth->verifyEmailByCode($code);
    
    if ($result['success']) {
        $verificationSuccess = true;
        $verificationMessage = 'Your email has been successfully verified! You can now login to your partner portal.';
    } else {
        $verificationMessage = $result['message'] ?? 'Verification failed. Please try again or contact support.';
        
        // If unverified, show the verification link
        if ($code) {
            $baseUrl = 'http://localhost/business';
            if (isset($_SERVER['HTTP_HOST'])) {
                $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
                $baseUrl = $protocol . '://' . $_SERVER['HTTP_HOST'] . '/business';
            }
            $verificationLink = "$baseUrl/verify_email.php?code=$code";
        }
    }
}

$pageTitle = 'Email Verification';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?> - Calamus Education Partner Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: #f5f5f5;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .verification-container {
            max-width: 500px;
            width: 100%;
            padding: 20px;
        }
        .verification-card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            padding: 40px 30px;
        }
        .success-icon {
            width: 80px;
            height: 80px;
            background: #137333;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
        }
        .error-icon {
            width: 80px;
            height: 80px;
            background: #d93025;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
        }
        .btn-primary {
            background: #202124;
            border: none;
            padding: 12px 32px;
        }
        .btn-primary:hover {
            background: #3c4043;
        }
    </style>
</head>
<body>
    <div class="verification-container">
        <div class="verification-card text-center">
            <?php if ($verificationSuccess): ?>
                <div class="success-icon">
                    <i class="fas fa-check fa-2x text-white"></i>
                </div>
                <h2 style="color: #137333; margin-bottom: 20px;">Verified!</h2>
                <p style="color: #5f6368; margin-bottom: 30px;"><?php echo htmlspecialchars($verificationMessage); ?></p>
                <a href="partner_login.php" class="btn btn-primary">
                    <i class="fas fa-sign-in-alt me-2"></i>Go to Login
                </a>
            <?php else: ?>
                <div class="error-icon">
                    <i class="fas fa-times fa-2x text-white"></i>
                </div>
                <h2 style="color: #d93025; margin-bottom: 20px;">Verification Failed</h2>
                <p style="color: #5f6368; margin-bottom: 10px;"><?php echo htmlspecialchars($verificationMessage); ?></p>
                
                <?php if ($verificationLink): ?>
                    <div style="background-color: #f8f9fa; border: 1px solid #e8eaed; border-radius: 4px; padding: 16px; margin: 20px 0;">
                        <p style="color: #202124; font-size: 14px; font-weight: 600; margin: 0 0 8px 0;">Check your email for the verification link</p>
                        <p style="color: #5f6368; font-size: 13px; margin: 0 0 8px 0;">If you lost the email, try logging in again to receive a new verification link.</p>
                    </div>
                <?php endif; ?>
                
                <div class="d-flex gap-2 justify-content-center">
                    <a href="partner_login.php" class="btn btn-primary">
                        <i class="fas fa-sign-in-alt me-2"></i>Try Login
                    </a>
                </div>
            <?php endif; ?>
        </div>
        
        <div class="text-center mt-4">
            <p style="color: #5f6368; font-size: 14px;">
                Having trouble? <a href="contact_us.php" style="color: #202124;">Contact Support</a>
            </p>
        </div>
    </div>
</body>
</html>

