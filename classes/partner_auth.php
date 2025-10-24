<?php
require_once __DIR__ . '/autoload.php';
require_once __DIR__ . '/../email_config.php';

class PartnerAuth {
    private $db;
    
    public function __construct() {
        $this->db = new Database();
    }
    
    // Register new partner (updated method)
    public function registerPartner($partnerData) {
        // Check if email already exists
        $existingPartner = $this->db->read("SELECT id FROM partners WHERE email = '{$partnerData['email']}'");
        if ($existingPartner) {
            return ['success' => false, 'message' => 'Email already registered'];
        }
        
        // Check if code prefix already exists
        if (isset($partnerData['code_prefix'])) {
            $existingPrefix = $this->db->read("SELECT id FROM partners WHERE code_prefix = '{$partnerData['code_prefix']}'");
            if ($existingPrefix) {
                return ['success' => false, 'message' => 'Code prefix already taken'];
            }
        }

      
        
        // Hash password
        $hashedPassword = password_hash($partnerData['password'], PASSWORD_DEFAULT);
        
        // Generate verification code
        $verificationCode = str_pad(rand(100000, 999999), 6, '0', STR_PAD_LEFT);
        
        // Insert partner
        $query = "INSERT INTO partners 
                 (company_name, contact_name, email, phone, password, website, description, 
                  commission_rate, code_prefix, payment_method, payment_details, status, 
                  verification_code, created_at) 
                 VALUES ('{$partnerData['company_name']}', '{$partnerData['contact_name']}', 
                         '{$partnerData['email']}', '{$partnerData['phone']}', '$hashedPassword', 
                         '{$partnerData['website']}', '{$partnerData['description']}', 
                         '{$partnerData['commission_rate']}', '{$partnerData['code_prefix']}', 
                         '{$partnerData['payment_method']}', '{$partnerData['payment_details']}', 
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
                'partner' => $partner
            ];
        }
        
        return ['success' => false, 'message' => 'Registration failed', 'result' => $result];
    }
    
    // Login partner
    public function loginPartner($email, $password) {
        $partner = $this->db->read("SELECT * FROM partners WHERE email = '$email' AND status = 'active'");
        
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
        
        $sessionQuery = "INSERT INTO partner_sessions (partner_id, session_token, expires_at) 
                        VALUES ('{$partner['id']}', '$sessionToken', '$expiresAt')";
        
        if ($this->db->save($sessionQuery)) {
            // Update last login
            $this->db->save("UPDATE partners SET last_login = NOW() WHERE id = '{$partner['id']}'");
            
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
        $session = $this->db->read("SELECT p.*, ps.expires_at FROM partners p 
                                  JOIN partner_sessions ps ON p.id = ps.partner_id 
                                  WHERE ps.session_token = '$sessionToken' AND ps.expires_at > NOW()");
        
        if (!$session) {
            return ['success' => false, 'message' => 'Invalid or expired session'];
        }
        
        return ['success' => true, 'partner' => $session[0]];
    }
    
    // Logout
    public function logout($sessionToken) {
        $query = "DELETE FROM partner_sessions WHERE session_token = '$sessionToken'";
        return $this->db->save($query);
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
    
    // Get partner by code prefix
    public function getPartnerByCodePrefix($codePrefix) {
        $partner = $this->db->read("SELECT * FROM partners WHERE code_prefix = '$codePrefix'");
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
        // Get current partner data
        $partner = $this->db->read("SELECT password FROM partners WHERE id = '$partnerId'");
        
        if (!$partner) {
            return ['success' => false, 'message' => 'Partner not found'];
        }
        
        $partner = $partner[0];
        
        // Verify current password
        if (!password_verify($currentPassword, $partner['password'])) {
            return ['success' => false, 'message' => 'Current password is incorrect'];
        }
        
        // Hash new password
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        
        // Update password
        $query = "UPDATE partners SET password = '$hashedPassword' WHERE id = '$partnerId'";
        
        if ($this->db->save($query)) {
            return ['success' => true, 'message' => 'Password changed successfully'];
        }
        
        return ['success' => false, 'message' => 'Failed to change password'];
    }
    
    // Send password reset email
    private function sendPasswordResetEmail($email, $token) {
        $resetLink = "http://localhost/business/reset_password.php?token=$token";
        $subject = "Password Reset Request";
        $message = "
        <h2>Password Reset Request</h2>
        <p>Click the link below to reset your password:</p>
        <a href='$resetLink'>Reset Password</a>
        <p>This link will expire in 1 hour.</p>
        ";
        
        $success = sendEmail($email, $subject, $message, 'password_reset');
        logEmailAttempt($email, $subject, $success);
        
        return $success;
    }
    
    // Extend session expiry
    public function extendSession($sessionToken, $newExpiry) {
        $query = "UPDATE partner_sessions SET expires_at = '$newExpiry' WHERE session_token = '$sessionToken'";
        return $this->db->save($query);
    }
    
    // Log login attempts
    public function logLoginAttempt($email, $success, $message = '') {
        $logEntry = date('Y-m-d H:i:s') . " - Login attempt for: $email, Success: " . ($success ? 'Yes' : 'No');
        if ($message) {
            $logEntry .= ", Message: $message";
        }
        $logEntry .= "\n";
        
        file_put_contents(__DIR__ . '/logs/login.log', $logEntry, FILE_APPEND | LOCK_EX);
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
