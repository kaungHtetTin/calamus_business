<?php
require_once __DIR__ . '/autoload.php';
require_once __DIR__ . '/../email_config.php';

class PartnerAuth {
    private $db;
    
    public function __construct() {
        $this->db = new Database();
    }
    
    // Generate unique private code
    private function generateUniquePrivateCode() {
        $maxAttempts = 100; // Prevent infinite loop
        $attempts = 0;
        
        do {
            // Generate 6-character alphanumeric uppercase code
            $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
            $privateCode = '';
            
            for ($i = 0; $i < 6; $i++) {
                $privateCode .= $characters[rand(0, strlen($characters) - 1)];
            }
            
            // Check if code already exists
            $existingCode = $this->db->read("SELECT id FROM partners WHERE private_code = '$privateCode'");
            
            if (!$existingCode) {
                return $privateCode;
            }
            
            $attempts++;
        } while ($attempts < $maxAttempts);
        
        return false; // Failed to generate unique code
    }
    
    // Register new partner (updated method)
    public function registerPartner($partnerData) {
        // Check if email already exists
        $existingPartner = $this->db->read("SELECT id FROM partners WHERE email = '{$partnerData['email']}'");
        if ($existingPartner) {
            return ['success' => false, 'message' => 'Email already registered'];
        }
        
        // Generate unique private code
        $privateCode = $this->generateUniquePrivateCode();
        if (!$privateCode) {
            return ['success' => false, 'message' => 'Failed to generate unique private code'];
        }
        
        // Hash password
        $hashedPassword = password_hash($partnerData['password'], PASSWORD_DEFAULT);
        
        // Generate verification code
        $verificationCode = str_pad(rand(100000, 999999), 6, '0', STR_PAD_LEFT);
        
        // Insert partner
        $query = "INSERT INTO partners 
                 (company_name, contact_name, email, phone, password, website, description, 
                  commission_rate, private_code, status, verification_code, created_at) 
                 VALUES ('{$partnerData['company_name']}', '{$partnerData['contact_name']}', 
                         '{$partnerData['email']}', '{$partnerData['phone']}', '$hashedPassword', 
                         '{$partnerData['website']}', '{$partnerData['description']}', 
                         '{$partnerData['commission_rate']}', '$privateCode', 
                         '{$partnerData['status']}', '$verificationCode', NOW())";
        
        $result = $this->db->save($query);
        if ($result) {
            $query = "SELECT id FROM partners WHERE email = '{$partnerData['email']}' LIMIT 1";
            $partner = $this->db->read($query);
            $partnerId = $partner[0]['id'];
            
            // Send verification email
          //  $this->sendVerificationEmail($partnerData['email'], $verificationCode);
            
            return [
                'success' => true, 
                'message' => 'Registration successful. Please check your email for verification.',
                'partner_id' => $partnerId,
                'private_code' => $privateCode,
                'partner' => $partner
            ];
        }
        
        return ['success' => false, 'message' => 'Registration failed', 'result' => $result];
    }
    
    // Login partner
    public function loginPartner($email, $password) {
        $partner = $this->db->read("SELECT * FROM partners WHERE email = '$email' LIMIT 1");
        
        if (!$partner) {
            return ['success' => false, 'message' => 'Invalid credentials'];
        }
        
        $partner = $partner[0];
        
        // Check if email is verified
        if (!$partner['email_verified']) {
            return ['success' => false, 'message' => 'Please verify your email before logging in'];
        }
        
        // Verify password
        if (!password_verify($password, $partner['password'])) {
            return ['success' => false, 'message' => 'Invalid credentials'];
        }
        
        
        // Create session
        $sessionToken = bin2hex(random_bytes(32));
        $expiresAt = date('Y-m-d H:i:s', strtotime('+7 days'));
        
        $sessionQuery = "INSERT INTO partner_sessions (partner_id, session_token, expired_at) 
                        VALUES ('{$partner['id']}', '$sessionToken', '$expiresAt')";
        
        if ($this->db->save($sessionQuery)) {
            // Update last login
            $this->db->save("UPDATE partners SET last_login = NOW() WHERE id = '{$partner['id']}'");
            
            // Store session token in PHP session
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            $_SESSION['partner_session_token'] = $sessionToken;
            $_SESSION['partner_id'] = $partner['id'];
            

            return [
                'success' => true, 
                'message' => 'Login successful',
                'session_token' => $sessionToken,
                'partner' => [
                    'id' => $partner['id'],
                    'email' => $partner['email'],
                    'contact_name' => $partner['contact_name'],
                    'company_name' => $partner['company_name']
                ]
            ];
        }
        
        return ['success' => false, 'message' => 'Login failed'];
    }
    
    // Verify email with token
    public function verifyEmailWithToken($token) {
        $partner = $this->db->read("SELECT id FROM partners WHERE verification_token = '$token'");
        
        if (!$partner) {
            return ['success' => false, 'message' => 'Invalid verification token'];
        }
        
        $query = "UPDATE partners SET email_verified = TRUE, verification_token = NULL WHERE verification_token = '$token'";
        
        if ($this->db->save($query)) {
            return ['success' => true, 'message' => 'Email verified successfully'];
        }
        
        return ['success' => false, 'message' => 'Verification failed'];
    }
    
    // Forgot password
    public function forgotPassword($email) {
        $partner = $this->db->read("SELECT id FROM partners WHERE email = '$email'");
        
        if (!$partner) {
            return ['success' => false, 'message' => 'Email not found'];
        }
        
        $resetToken = bin2hex(random_bytes(32));
        $expiresAt = date('Y-m-d H:i:s', strtotime('+1 hour'));
        
        $query = "UPDATE partners SET reset_token = '$resetToken', reset_token_expires = '$expiresAt' WHERE email = '$email'";
        
        if ($this->db->save($query)) {
            $this->sendPasswordResetEmail($email, $resetToken);
            return ['success' => true, 'message' => 'Password reset link sent to your email'];
        }
        
        return ['success' => false, 'message' => 'Failed to send reset email'];
    }
    
    // Reset password
    public function resetPassword($token, $newPassword) {
        $partner = $this->db->read("SELECT id FROM partners WHERE reset_token = '$token' AND reset_token_expires > NOW()");
        
        if (!$partner) {
            return ['success' => false, 'message' => 'Invalid or expired reset token'];
        }
        
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $query = "UPDATE partners SET password = '$hashedPassword', reset_token = NULL, reset_token_expires = NULL WHERE reset_token = '$token'";
        
        if ($this->db->save($query)) {
            return ['success' => true, 'message' => 'Password reset successfully'];
        }
        
        return ['success' => false, 'message' => 'Password reset failed'];
    }
    
    // Validate session
    public function validateSession($sessionToken) {
        $session = $this->db->read("SELECT p.*, ps.expired_at FROM partners p 
                                  JOIN partner_sessions ps ON p.id = ps.partner_id 
                                  WHERE ps.session_token = '$sessionToken' AND ps.expired_at > NOW()");
        
        if (!$session) {
            return ['success' => false, 'message' => 'Invalid or expired session'];
        }
        
        return ['success' => true, 'partner' => $session[0]];
    }
    
    // Logout
    public function logout($sessionToken) {
        $query = "DELETE FROM partner_sessions WHERE session_token = '$sessionToken'";
        $result = $this->db->save($query);
        
        // Clear PHP session
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        unset($_SESSION['partner_session_token']);
        unset($_SESSION['partner_id']);
        
        return $result;
    }
    
    // Send verification email
    private function sendVerificationEmail($email, $verificationCode) {
        $subject = "Verify Your Partner Account";
        $message = "
        <h2>Welcome to Our Partner Program!</h2>
        <p>Your verification code is: <strong>$verificationCode</strong></p>
        <p>Please use this code to verify your email address.</p>
        <p>If you didn't create an account, please ignore this email.</p>
        ";
        
        $success = sendEmail($email, $subject, $message, 'verification');
        logEmailAttempt($email, $subject, $success);
        
        return $success;
    }
    
    // Get partner by email
    public function getPartnerByEmail($email) {
        $partner = $this->db->read("SELECT * FROM partners WHERE email = '$email'");
        return $partner ? $partner[0] : null;
    }
    
    // Verify email with verification code
    public function verifyEmail($email, $verificationCode) {
        $partner = $this->db->read("SELECT * FROM partners WHERE email = '$email' AND verification_code = '$verificationCode'");
        
        if (!$partner) {
            return ['success' => false, 'message' => 'Invalid verification code'];
        }
        
        $partner = $partner[0];
        
        // Update partner status to active and clear verification code
        $query = "UPDATE partners SET status = 'active', email_verified = 1, verification_code = NULL WHERE id = '{$partner['id']}'";
        
        if ($this->db->save($query)) {
            return ['success' => true, 'message' => 'Email verified successfully'];
        }
        
        return ['success' => false, 'message' => 'Failed to verify email'];
    }
    
    // Resend verification email
    public function resendVerificationEmail($email) {
        $partner = $this->db->read("SELECT * FROM partners WHERE email = '$email'");
        
        if (!$partner) {
            return ['success' => false, 'message' => 'Email not found'];
        }
        
        $partner = $partner[0];
        
        if ($partner['status'] === 'active') {
            return ['success' => false, 'message' => 'Email already verified'];
        }
        
        // Generate new verification code
        $verificationCode = str_pad(rand(100000, 999999), 6, '0', STR_PAD_LEFT);
        
        // Update verification code
        $query = "UPDATE partners SET verification_code = '$verificationCode' WHERE id = '{$partner['id']}'";
        
        if ($this->db->save($query)) {
            // Send verification email
            $this->sendVerificationEmail($email, $verificationCode);
            return ['success' => true, 'message' => 'Verification email sent'];
        }
        
        return ['success' => false, 'message' => 'Failed to send verification email'];
    }
    
    // Send welcome email
    public function sendWelcomeEmail($email, $contactName, $companyName) {
        $subject = "Welcome to Our Partner Program!";
        $message = "
        <h2>Welcome to Our Partner Program!</h2>
        <p>Dear $contactName,</p>
        <p>Thank you for registering $companyName as a partner!</p>
        <p>Your account is currently pending approval. You will receive an email once your account is activated.</p>
        <p>Once approved, you will be able to:</p>
        <ul>
            <li>Generate promotion codes for your clients</li>
            <li>Track your earnings and commissions</li>
            <li>Access detailed analytics</li>
            <li>Manage your payment settings</li>
        </ul>
        <p>If you have any questions, please don't hesitate to contact us.</p>
        <p>Best regards,<br>The Partner Team</p>
        ";
        
        $success = sendEmail($email, $subject, $message, 'welcome');
        logEmailAttempt($email, $subject, $success);
        
        return $success;
    }
    
    // Update partner information
    public function updatePartner($partnerId, $updateData) {
        $setClause = [];
        foreach ($updateData as $field => $value) {
            $setClause[] = "$field = '$value'";
        }
        
        $query = "UPDATE partners SET " . implode(', ', $setClause) . " WHERE id = '$partnerId'";
        
        if ($this->db->save($query)) {
            return ['success' => true, 'message' => 'Partner information updated successfully'];
        }
        
        return ['success' => false, 'message' => 'Failed to update partner information'];
    }
    
    // Change password
    public function changePassword($partnerId, $currentPassword, $newPassword) {
        // Validate inputs
        if (empty($currentPassword)) {
            return ['success' => false, 'message' => 'Current password is required', 'field' => 'currentPassword'];
        }
        
        if (empty($newPassword)) {
            return ['success' => false, 'message' => 'New password is required', 'field' => 'newPassword'];
        }
        
        if (strlen($newPassword) < 8) {
            return ['success' => false, 'message' => 'Password must be at least 8 characters long', 'field' => 'newPassword'];
        }
        
        // Check for weak passwords
        $weakPasswords = ['password', '12345678', 'qwerty123', 'abc12345', 'password123'];
        if (in_array(strtolower($newPassword), $weakPasswords)) {
            return ['success' => false, 'message' => 'Password is too weak. Please choose a stronger password.', 'field' => 'newPassword'];
        }
        
        // Get current partner data
        $partner = $this->db->read("SELECT password FROM partners WHERE id = '$partnerId'");
        
        if (!$partner) {
            return ['success' => false, 'message' => 'Partner not found'];
        }
        
        $partner = $partner[0];
        
        // Verify current password
        if (!password_verify($currentPassword, $partner['password'])) {
            return ['success' => false, 'message' => 'Current password is incorrect', 'field' => 'currentPassword'];
        }
        
        // Check if new password is same as current password
        if (password_verify($newPassword, $partner['password'])) {
            return ['success' => false, 'message' => 'New password must be different from current password', 'field' => 'newPassword'];
        }
        
        // Hash new password
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        
        // Update password
        $query = "UPDATE partners SET password = '$hashedPassword', updated_at = NOW() WHERE id = '$partnerId'";
        
        if ($this->db->save($query)) {
            // Invalidate all existing sessions for security (except current session)
            $this->invalidateOtherSessions($partnerId);
            
            return ['success' => true, 'message' => 'Password changed successfully'];
        }
        
        return ['success' => false, 'message' => 'Failed to change password'];
    }
    
    // Invalidate other sessions (keep current session active)
    private function invalidateOtherSessions($partnerId) {
        // Get current session token from session
        $currentSessionToken = $_SESSION['session_token'] ?? '';
        
        if ($currentSessionToken) {
            // Invalidate all sessions except current one
            $query = "UPDATE partner_sessions SET expired_at = NOW() 
                      WHERE partner_id = '$partnerId' AND session_token != '$currentSessionToken'";
        } else {
            // If no current session token, invalidate all sessions
            $query = "UPDATE partner_sessions SET expired_at = NOW() WHERE partner_id = '$partnerId'";
        }
        
        $this->db->save($query);
    }
    
    // Send password reset email
    private function sendPasswordResetEmail($email, $token) {
        $resetLink = "http://localhost/business/reset_password.php?token=$token";
        $subject = "Password Reset Request - Calamus Education Partner Portal";
        $message = "
        <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
            <div style='background: linear-gradient(135deg, #4a5568 0%, #718096 100%); color: white; padding: 20px; text-align: center;'>
                <h2 style='margin: 0;'>Calamus Education</h2>
                <p style='margin: 10px 0 0 0;'>Partner Portal</p>
            </div>
            <div style='padding: 30px; background: #f8f9fa;'>
                <h3 style='color: #4a5568; margin-top: 0;'>Password Reset Request</h3>
                <p>Hello,</p>
                <p>We received a request to reset your password for your Calamus Education Partner Portal account.</p>
                <p>Click the button below to reset your password:</p>
                <div style='text-align: center; margin: 30px 0;'>
                    <a href='$resetLink' style='background: #4a5568; color: white; padding: 12px 30px; text-decoration: none; border-radius: 5px; display: inline-block; font-weight: bold;'>Reset Password</a>
                </div>
                <p><strong>Important:</strong></p>
                <ul>
                    <li>This link will expire in 1 hour for security reasons</li>
                    <li>If you didn't request this password reset, please ignore this email</li>
                    <li>Your password will remain unchanged until you create a new one</li>
                </ul>
                <p>If the button doesn't work, copy and paste this link into your browser:</p>
                <p style='word-break: break-all; color: #4a5568; background: #e9ecef; padding: 10px; border-radius: 3px;'>$resetLink</p>
                <hr style='border: none; border-top: 1px solid #dee2e6; margin: 30px 0;'>
                <p style='color: #6c757d; font-size: 14px; margin-bottom: 0;'>
                    This email was sent from Calamus Education Partner Portal.<br>
                    If you have any questions, please contact our support team.
                </p>
            </div>
        </div>
        ";
        
        $success = sendEmail($email, $subject, $message, 'password_reset');
        logEmailAttempt($email, $subject, $success);
        
        return $success;
    }
    
    // Request password reset
    public function requestPasswordReset($email) {
        // Check if email exists
        $partner = $this->db->read("SELECT id, contact_name FROM partners WHERE email = '$email'");
        if (!$partner) {
            return ['success' => false, 'message' => 'Email address not found'];
        }
        
        $partner = $partner[0];
        
        // Generate reset token
        $token = bin2hex(random_bytes(32));
        $expiresAt = date('Y-m-d H:i:s', strtotime('+1 hour'));
        
        // Store reset token
        $query = "INSERT INTO password_reset_tokens (partner_id, token, expires_at, created_at) 
                  VALUES ('{$partner['id']}', '$token', '$expiresAt', NOW())";
        
        if (!$this->db->save($query)) {
            return ['success' => false, 'message' => 'Failed to create reset token'];
        }
        
        // Send email
        if ($this->sendPasswordResetEmail($email, $token)) {
            return ['success' => true, 'message' => 'Password reset link sent to your email'];
        } else {
            // Clean up token if email failed
            $this->db->save("DELETE FROM password_reset_tokens WHERE token = '$token'");
            return ['success' => false, 'message' => 'Failed to send reset email'];
        }
    }
    
    // Validate password reset token
    public function validatePasswordResetToken($token) {
        $query = "SELECT prt.*, p.email, p.contact_name 
                  FROM password_reset_tokens prt 
                  JOIN partners p ON prt.partner_id = p.id 
                  WHERE prt.token = '$token' AND prt.expires_at > NOW() AND prt.used = 0";
        
        $result = $this->db->read($query);
        
        if (!$result) {
            return ['success' => false, 'message' => 'Invalid or expired reset token'];
        }
        
        return ['success' => true, 'partner' => $result[0]];
    }
    
    // Reset password using token
    public function resetPasswordWithToken($token, $newPassword) {
        // Validate token
        $tokenValidation = $this->validatePasswordResetToken($token);
        if (!$tokenValidation['success']) {
            return $tokenValidation;
        }
        
        $partnerId = $tokenValidation['partner']['partner_id'];
        
        // Hash new password
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        
        // Update password
        $updateQuery = "UPDATE partners SET password = '$hashedPassword' WHERE id = '$partnerId'";
        if (!$this->db->save($updateQuery)) {
            return ['success' => false, 'message' => 'Failed to update password'];
        }
        
        // Mark token as used
        $markUsedQuery = "UPDATE password_reset_tokens SET used = 1 WHERE token = '$token'";
        $this->db->save($markUsedQuery);
        
        // Invalidate all existing sessions for security
        $this->db->save("UPDATE partner_sessions SET expired_at = NOW() WHERE partner_id = '$partnerId'");
        
        return ['success' => true, 'message' => 'Password reset successfully'];
    }
    
    // Extend session expiry
    public function extendSession($sessionToken, $newExpiry) {
        $query = "UPDATE partner_sessions SET expired_at = '$newExpiry' WHERE session_token = '$sessionToken'";
        return $this->db->save($query);
    }

    
    // Get partner by ID
    public function getPartnerById($partnerId) {
        $partner = $this->db->read("SELECT * FROM partners WHERE id = '$partnerId'");
        return $partner ? $partner[0] : null;
    }
    
    // Check if user is logged in (for session management)
    public function isLoggedIn() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!isset($_SESSION['partner_id']) || !isset($_SESSION['session_token'])) {
            return false;
        }
        
        $sessionResult = $this->validateSession($_SESSION['session_token']);
        return $sessionResult['success'];
    }
}
?>
