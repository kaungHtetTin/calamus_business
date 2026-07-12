<?php
/**
 * Admin Authentication Class
 * Handles admin login with fixed credentials
 */

require_once __DIR__ . '/Database.php';

class AdminAuth {
    private $db;
    private $admin_username = 'calamuseducation@gmail.com';
    private $admin_password = '@$calamus5241$@';
    
    public function __construct() {
        $this->db = new Database();
    }
    
    /**
     * Admin login
     */
    public function loginAdmin($username, $password) {
        // Validate credentials
        if ($username === $this->admin_username && $password === $this->admin_password) {
            // Start session
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            
            // Set admin session
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_username'] = $username;
            $_SESSION['admin_login_time'] = time();
            
            return [
                'success' => true,
                'message' => 'Login successful'
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Invalid credentials'
            ];
        }
    }
    
    /**
     * Check if admin is logged in
     */
    public function isLoggedIn() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
    }
    
    /**
     * Get current admin username
     */
    public function getAdminUsername() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        return $_SESSION['admin_username'] ?? null;
    }
    
    /**
     * Logout admin
     */
    public function logout() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        session_unset();
        session_destroy();
        
        return ['success' => true, 'message' => 'Logout successful'];
    }
    
    /**
     * Get all partners with pagination and search
     */
    public function getAllPartners($page = 1, $limit = 20, $search = null) {
        $offset = ($page - 1) * $limit;
        
        // Build search condition
        $searchCondition = '';
        if ($search && !empty(trim($search))) {
            $sanitizedSearch = trim($search);
            $searchCondition = " WHERE email LIKE '%$sanitizedSearch%' OR contact_name LIKE '%$sanitizedSearch%' OR company_name LIKE '%$sanitizedSearch%'";
        }
        
        // Get total count
        $totalQuery = "SELECT COUNT(*) as total FROM partners $searchCondition";
        $totalResult = $this->db->read($totalQuery);
        $total = $totalResult[0]['total'];
        
        // Get partners with pagination
        $partnersQuery = "SELECT * FROM partners $searchCondition ORDER BY created_at DESC LIMIT $limit OFFSET $offset";
        $partners = $this->db->read($partnersQuery);
        
        return [
            'partners' => $partners,
            'total' => $total,
            'page' => $page,
            'limit' => $limit,
            'total_pages' => ceil($total / $limit),
            'search' => $search
        ];
    }
    
    /**
     * Get partner by ID
     */
    public function getPartnerById($partnerId) {
        $query = "SELECT * FROM partners WHERE id = '$partnerId' LIMIT 1";
        $result = $this->db->read($query);
        
        if (count($result) > 0) {
            return $result[0];
        }
        
        return null;
    }
    
    /**
     * Update partner status
     */
    public function updatePartnerStatus($partnerId, $status) {
        $validStatuses = ['active', 'inactive', 'suspended'];
        
        if (!in_array($status, $validStatuses)) {
            return ['success' => false, 'message' => 'Invalid status'];
        }
        
        $query = "UPDATE partners SET status = '$status' WHERE id = '$partnerId'";
        
        if ($this->db->save($query)) {
            return ['success' => true, 'message' => 'Partner status updated successfully'];
        }
        
        return ['success' => false, 'message' => 'Failed to update partner status'];
    }
    
    /**
     * Delete partner
     */
    public function deletePartner($partnerId) {
        // Check if partner exists
        $partner = $this->getPartnerById($partnerId);
        
        if (!$partner) {
            return ['success' => false, 'message' => 'Partner not found'];
        }
        
        // Delete partner's profile image if exists
        if ($partner['profile_image'] && file_exists($partner['profile_image'])) {
            unlink($partner['profile_image']);
        }
        
        // Delete partner's sessions
        $this->db->save("DELETE FROM partner_sessions WHERE partner_id = '$partnerId'");
        
        // Delete partner
        $query = "DELETE FROM partners WHERE id = '$partnerId'";
        
        if ($this->db->save($query)) {
            return ['success' => true, 'message' => 'Partner deleted successfully'];
        }
        
        return ['success' => false, 'message' => 'Failed to delete partner'];
    }
    
    /**
     * Get partners statistics
     */
    public function getPartnerStatistics() {
        $query = "SELECT
                    COUNT(*) AS total,
                    SUM(status = 'active') AS active,
                    SUM(status = 'inactive') AS inactive,
                    SUM(status = 'suspended') AS suspended,
                    SUM(email_verified = 1) AS verified,
                    SUM(email_verified = 0) AS unverified,
                    SUM(created_at >= DATE_FORMAT(CURDATE(), '%Y-%m-01')) AS new_this_month
                  FROM partners";
        $result = $this->db->read($query);

        if (!$result) {
            return [
                'total' => 0, 'active' => 0, 'inactive' => 0,
                'suspended' => 0, 'verified' => 0,
                'unverified' => 0, 'new_this_month' => 0
            ];
        }

        return array_map('intval', $result[0]);
    }

    /**
     * Get partners eligible for account status check
     * Conditions:
     *  - email_verified = 1
     *  - has at least one payment method
     *  - personal information filled (address, city, state, national_id_card_number)
     */
    public function getPartnersEligibleForStatusCheck() {
        $query = "SELECT p.*
                  FROM partners p
                  WHERE p.email_verified = 1
                    AND COALESCE(p.address, '') <> ''
                    AND COALESCE(p.city, '') <> ''
                    AND COALESCE(p.state, '') <> ''
                    AND COALESCE(p.national_id_card_number, '') <> ''
                    AND EXISTS (
                        SELECT 1 FROM partner_payment_methods pm
                        WHERE pm.partner_id = p.id
                    )
                    AND account_verified = 0
                  ORDER BY p.created_at DESC";
        $result = $this->db->read($query);
        return $result ? $result : [];
    }
    
    /**
     * Get all earning logs with pagination and filtering
     */
    public function getEarningLogs($page = 1, $limit = 20, $status = null, $startDate = null, $endDate = null) {
        $offset = ($page - 1) * $limit;
        
        // Build WHERE clause
        $whereClause = "WHERE 1=1";
        
        if ($status) {
            $whereClause .= " AND pe.status = '$status'";
        }
        
        if ($startDate) {
            $whereClause .= " AND pe.created_at >= '$startDate 00:00:00'";
        }
        
        if ($endDate) {
            $whereClause .= " AND pe.created_at < DATE_ADD('$endDate', INTERVAL 1 DAY)";
        }
        
        // Get total count
        $countQuery = "SELECT COUNT(*) as total FROM partner_earnings pe $whereClause";
        $countResult = $this->db->read($countQuery);
        $total = $countResult[0]['total'];
        
        // Get earning logs with partner details
        $query = "SELECT pe.*, p.contact_name, p.company_name, p.email 
                 FROM partner_earnings pe 
                 LEFT JOIN partners p ON pe.partner_id = p.id 
                 $whereClause 
                 ORDER BY pe.created_at DESC 
                 LIMIT $limit OFFSET $offset";
        
        $logs = $this->db->read($query);
        
        return [
            'logs' => $logs ? $logs : [],
            'total' => $total,
            'page' => $page,
            'limit' => $limit,
            'total_pages' => ceil($total / $limit)
        ];
    }
    
    /**
     * Get earning logs statistics
     */
    public function getEarningLogsStatistics($status = null, $startDate = null, $endDate = null) {
        $whereClause = "WHERE 1=1";
        
        if ($status) {
            $whereClause .= " AND status = '$status'";
        }
        
        if ($startDate) {
            $whereClause .= " AND created_at >= '$startDate 00:00:00'";
        }
        
        if ($endDate) {
            $whereClause .= " AND created_at < DATE_ADD('$endDate', INTERVAL 1 DAY)";
        }

        $query = "SELECT
                    COALESCE(SUM(amount_received), 0) AS total_amount,
                    COUNT(*) AS total_transactions,
                    COALESCE(SUM(CASE WHEN status = 'pending' THEN amount_received ELSE 0 END), 0) AS pending_amount,
                    COALESCE(SUM(CASE WHEN status = 'paid' THEN amount_received ELSE 0 END), 0) AS paid_amount
                  FROM partner_earnings $whereClause";
        $result = $this->db->read($query);

        return [
            'total_amount' => $result ? (float) $result[0]['total_amount'] : 0.00,
            'total_transactions' => $result ? (int) $result[0]['total_transactions'] : 0,
            'pending_amount' => $result ? (float) $result[0]['pending_amount'] : 0.00,
            'paid_amount' => $result ? (float) $result[0]['paid_amount'] : 0.00
        ];
    }
    
    /**
     * Get payout logs (grouped by partner)
     */
    public function getPayoutLogs($page = 1, $limit = 20, $startDate = null, $endDate = null) {
        $offset = ($page - 1) * $limit;
        
        // Build WHERE clause
        $whereClause = "WHERE pe.status = 'pending'";
        
        if ($startDate) {
            $whereClause .= " AND pe.created_at >= '$startDate 00:00:00'";
        }
        
        if ($endDate) {
            $whereClause .= " AND pe.created_at < DATE_ADD('$endDate', INTERVAL 1 DAY)";
        }

        // Get total count
        $countQuery = "SELECT COUNT(DISTINCT pe.partner_id) as total FROM partner_earnings pe $whereClause";
        $countResult = $this->db->read($countQuery);
        $total = $countResult[0]['total'];

        // Get payout logs grouped by partner
        $query = "SELECT 
                    pe.partner_id,
                    SUM(pe.amount_received) as total_amount,
                    COUNT(pe.id) as transaction_count,
                    p.contact_name,
                    p.company_name,
                    p.email,
                    p.phone
                 FROM partner_earnings pe 
                 LEFT JOIN partners p ON pe.partner_id = p.id 
                 $whereClause 
                 GROUP BY pe.partner_id, p.contact_name, p.company_name, p.email, p.phone
                 ORDER BY total_amount DESC
                 LIMIT $limit OFFSET $offset";
        
        $logs = $this->db->read($query);
        
        return [
            'logs' => $logs ? $logs : [],
            'query' => $query,
            'total' => $total,
            'page' => $page,
            'limit' => $limit,
            'total_pages' => ceil($total / $limit)
        ];
    }
    
    /**
     * Get payout logs statistics
     */
    public function getPayoutLogsStatistics($startDate = null, $endDate = null) {
        $whereClause = "WHERE status = 'pending'";
        
        if ($startDate) {
            $whereClause .= " AND created_at >= '$startDate 00:00:00'";
        }
        
        if ($endDate) {
            $whereClause .= " AND created_at < DATE_ADD('$endDate', INTERVAL 1 DAY)";
        }

        $query = "SELECT
                    COALESCE(SUM(amount_received), 0) AS total_amount,
                    COUNT(DISTINCT partner_id) AS total_partners
                  FROM partner_earnings $whereClause";
        $result = $this->db->read($query);
        $amount = $result ? (float) $result[0]['total_amount'] : 0.00;

        return [
            'total_amount' => $amount,
            'total_partners' => $result ? (int) $result[0]['total_partners'] : 0,
            'pending_amount' => $amount,
            'paid_amount' => 0.00
        ];
    }
    
    /**
     * Get partner payment methods
     */
    public function getPartnerPaymentMethods($partnerId) {
        $query = "SELECT * FROM partner_payment_methods WHERE partner_id = '$partnerId' ORDER BY created_at DESC";
        $result = $this->db->read($query);
        return $result ? $result : [];
    }
    
    /**
     * Get pending payout amount for a partner
     */
    public function getPendingPayoutAmount($partnerId) {
        $query = "SELECT SUM(amount_received) as total FROM partner_earnings WHERE partner_id = '$partnerId' AND status = 'pending'";
        $result = $this->db->read($query);
        return $result && $result[0]['total'] ? (float)$result[0]['total'] : 0.00;
    }
    
    /**
     * Get payment history chart data grouped by month
     */
    public function getPaymentHistoryChart($year = 'current') {
        // Year filter
        $yearFilter = '';
        if ($year === 'current') {
            $yearFilter = " AND pph.created_at >= MAKEDATE(YEAR(CURDATE()), 1)
                            AND pph.created_at < MAKEDATE(YEAR(CURDATE()) + 1, 1)";
        } elseif ($year === '2024') {
            $yearFilter = " AND pph.created_at >= '2024-01-01' AND pph.created_at < '2025-01-01'";
        } elseif ($year === '2023') {
            $yearFilter = " AND pph.created_at >= '2023-01-01' AND pph.created_at < '2024-01-01'";
        } elseif ($year === '2022') {
            $yearFilter = " AND pph.created_at >= '2022-01-01' AND pph.created_at < '2023-01-01'";
        }
        // 'all' = no filter
        
        // Query to get payment amounts grouped by month
        $query = "SELECT 
                    DATE_FORMAT(pph.created_at, '%Y-%m') as payment_month,
                    SUM(pph.amount) as total_amount
                  FROM partner_payment_histories pph
                  WHERE 1=1 $yearFilter
                  GROUP BY DATE_FORMAT(pph.created_at, '%Y-%m')
                  ORDER BY payment_month ASC";
        
        $result = $this->db->read($query);
        
        return $result ? $result : [];
    }
    
    /**
     * Get payout histories with pagination and filtering
     */
    public function getPayoutHistories($page = 1, $limit = 20, $status = null, $startDate = null, $endDate = null) {
        $offset = ($page - 1) * $limit;
        $whereClause = "WHERE 1=1";
        
        if ($status) {
            $whereClause .= " AND pph.status = '$status'";
        }
        
        if ($startDate) {
            $whereClause .= " AND pph.created_at >= '$startDate 00:00:00'";
        }
        
        if ($endDate) {
            $whereClause .= " AND pph.created_at < DATE_ADD('$endDate', INTERVAL 1 DAY)";
        }
        
        // Get total count
        $countQuery = "SELECT COUNT(*) as total FROM partner_payment_histories pph $whereClause";
        $countResult = $this->db->read($countQuery);
        $total = $countResult[0]['total'];
        
        // Get payout histories with partner info
        $query = "SELECT 
                    pph.*,
                    p.contact_name,
                    p.company_name,
                    p.email
                  FROM partner_payment_histories pph
                  LEFT JOIN partners p ON pph.partner_id = p.id
                  $whereClause
                  ORDER BY pph.created_at DESC
                  LIMIT $limit OFFSET $offset";
        
        $histories = $this->db->read($query);
        
        return [
            'histories' => $histories ? $histories : [],
            'total' => $total,
            'page' => $page,
            'limit' => $limit,
            'total_pages' => ceil($total / $limit)
        ];
    }
    
    /**
     * Get payout history statistics
     */
    public function getPayoutHistoryStatistics($status = null, $startDate = null, $endDate = null) {
        $whereClause = "WHERE 1=1";
        
        if ($status) {
            $whereClause .= " AND status = '$status'";
        }
        
        if ($startDate) {
            $whereClause .= " AND created_at >= '$startDate 00:00:00'";
        }
        
        if ($endDate) {
            $whereClause .= " AND created_at < DATE_ADD('$endDate', INTERVAL 1 DAY)";
        }

        $query = "SELECT
                    COALESCE(SUM(amount), 0) AS total_amount,
                    COUNT(*) AS total_transactions,
                    COALESCE(SUM(CASE WHEN status = 'pending' THEN amount ELSE 0 END), 0) AS pending_amount,
                    COALESCE(SUM(CASE WHEN status IN ('received', 'completed') THEN amount ELSE 0 END), 0) AS received_amount
                  FROM partner_payment_histories $whereClause";
        $result = $this->db->read($query);

        return [
            'total_amount' => $result ? (float) $result[0]['total_amount'] : 0.00,
            'total_transactions' => $result ? (int) $result[0]['total_transactions'] : 0,
            'pending_amount' => $result ? (float) $result[0]['pending_amount'] : 0.00,
            'received_amount' => $result ? (float) $result[0]['received_amount'] : 0.00
        ];
    }
    
    /**
     * Generate unique private code
     */
    private function generateUniquePrivateCode() {
        $maxAttempts = 100;
        $attempts = 0;
        
        do {
            $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
            $privateCode = '';
            
            for ($i = 0; $i < 6; $i++) {
                $privateCode .= $characters[rand(0, strlen($characters) - 1)];
            }
            
            $existingCode = $this->db->read("SELECT id FROM partners WHERE private_code = '$privateCode'");
            
            if (!$existingCode) {
                return $privateCode;
            }
            
            $attempts++;
        } while ($attempts < $maxAttempts);
        
        return false;
    }
    
    /**
     * Create new partner
     */
    public function createPartner($partnerData) {
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
        
        // Set default values
        $status = $partnerData['status'] ?? 'active';
        $commissionRate = $partnerData['commission_rate'] ?? '10';
        $emailVerified = isset($partnerData['email_verified']) && $partnerData['email_verified'] ? 1 : 0;
        
        // Insert partner
        $query = "INSERT INTO partners 
                 (company_name, contact_name, email, phone, password, website, description, 
                  commission_rate, private_code, status, verification_code, email_verified, created_at, updated_at) 
                 VALUES ('{$partnerData['company_name']}', '{$partnerData['contact_name']}', 
                         '{$partnerData['email']}', '{$partnerData['phone']}', '$hashedPassword', 
                         '{$partnerData['website']}', '{$partnerData['description']}', 
                         '$commissionRate', '$privateCode', '$status', '$verificationCode', '$emailVerified', NOW(), NOW())";
        
        $result = $this->db->save($query);
        if ($result) {
            $query = "SELECT id FROM partners WHERE email = '{$partnerData['email']}' LIMIT 1";
            $partner = $this->db->read($query);
            $partnerId = $partner[0]['id'];
            
            return [
                'success' => true, 
                'message' => 'Partner created successfully',
                'partner_id' => $partnerId,
                'private_code' => $privateCode
            ];
        }
        
        return ['success' => false, 'message' => 'Failed to create partner'];
    }
    
    /**
     * Get staff by ranking
     */
    public function getStaffByRanking($ranking) {
        $query = "SELECT id, name, ranking FROM staffs WHERE ranking = '$ranking' ORDER BY name ASC";
        return $this->db->read($query);
    }
    
    /**
     * Get staff by ID
     */
    public function getStaffById($staffId) {
        $query = "SELECT id, name, ranking FROM staffs WHERE id = '$staffId'";
        $result = $this->db->read($query);
        return $result ? $result[0] : null;
    }
    
    /**
     * Get payout history detail
     */
    public function getPayoutHistoryDetail($paymentHistoryId) {
        $query = "SELECT 
                    pph.*,
                    p.company_name,
                    p.contact_name,
                    p.email,
                    p.phone,
                    p.website,
                    s.name as staff_name,
                    s.ranking as staff_ranking
                  FROM partner_payment_histories pph
                  LEFT JOIN partners p ON pph.partner_id = p.id
                  LEFT JOIN staffs s ON pph.staff_id = s.id
                  WHERE pph.id = '$paymentHistoryId'";
        
        $result = $this->db->read($query);
        return $result ? $result[0] : null;
    }
    
    /**
     * Send payout notification email
     */
    public function sendPayoutNotificationEmail($paymentHistoryId) {
        require_once __DIR__ . '/../email_config.php';
        
        // Get payment history details
        $history = $this->getPayoutHistoryDetail($paymentHistoryId);
        
        if (!$history) {
            return ['success' => false, 'message' => 'Payment history not found'];
        }
        
        // Prepare email variables
        $baseUrl = getBaseUrl();
        $variables = [
            'partner_name' => $history['contact_name'] ?? 'Partner',
            'amount' => number_format($history['amount'], 2),
            'transaction_date' => date('F d, Y', strtotime($history['created_at'])),
            'payment_method' => $history['payment_method'],
            'account_name' => $history['account_name'],
            'account_number' => $history['account_number'],
            'dashboard_link' => $baseUrl . '/dashboard.php'
        ];
        
        // Get email template
        $template = getEmailTemplate('payout_notification', $variables);
        
        // Fallback if template fails
        if (!$template) {
            $template = "
            <div style='font-family: Arial, sans-serif; padding: 20px;'>
                <h2>Payout Processed</h2>
                <p>Hi {$variables['partner_name']},</p>
                <p>Your payout of {$variables['amount']} MMK has been successfully processed.</p>
                <p>Payment Method: {$variables['payment_method']}</p>
                <p>Account: {$variables['account_name']} ({$variables['account_number']})</p>
                <p>Date: {$variables['transaction_date']}</p>
                <p><a href='{$variables['dashboard_link']}'>View Dashboard</a></p>
            </div>
            ";
        }
        
        $subject = "Payout Processed - Calamus Education";
        $success = sendEmail($history['email'], $subject, $template, 'payout_notification');
        
        return [
            'success' => $success,
            'message' => $success ? 'Payout notification email sent' : 'Failed to send payout notification email'
        ];
    }
    
    /**
     * Process partner payout
     */
    public function processPartnerPayout($partnerId, $paymentMethodId, $staffId, $amount, $screenshotPath) {
        // Start transaction by getting all data first
        $partner = $this->getPartnerById($partnerId);
        if (!$partner) {
            return ['success' => false, 'message' => 'Partner not found'];
        }
        
        // Get payment method details
        $paymentMethods = $this->getPartnerPaymentMethods($partnerId);
        $selectedMethod = null;
        foreach ($paymentMethods as $method) {
            if ($method['id'] == $paymentMethodId) {
                $selectedMethod = $method;
                break;
            }
        }
        
        if (!$selectedMethod) {
            return ['success' => false, 'message' => 'Payment method not found'];
        }
        
        // Get staff details
        $staff = $this->db->read("SELECT id, name FROM staffs WHERE id = '$staffId'");
        if (!$staff) {
            return ['success' => false, 'message' => 'Staff not found'];
        }
        $staff = $staff[0];
        
        // Insert into partner_payment_histories
        $query = "INSERT INTO partner_payment_histories 
                  (partner_id, payment_method, account_number, account_name, amount, status, transaction_screenshot, staff_id, created_at) 
                  VALUES ('$partnerId', '{$selectedMethod['payment_method']}', '{$selectedMethod['account_number']}', 
                         '{$selectedMethod['account_name']}', '$amount', 'completed', '$screenshotPath', '$staffId', NOW())";
        
        $result = $this->db->save($query);
        if (!$result) {
            return ['success' => false, 'message' => 'Failed to insert payment history'];
        }
        
        // Get the inserted payment history ID
        $paymentHistory = $this->db->read("SELECT id FROM partner_payment_histories WHERE partner_id = '$partnerId' AND staff_id = '$staffId' ORDER BY id DESC LIMIT 1");
        $paymentHistoryId = $paymentHistory[0]['id'];
        
        // Update partner_earnings status to 'paid'
        $updateQuery = "UPDATE partner_earnings SET status = 'paid', updated_at = NOW() WHERE partner_id = '$partnerId' AND status = 'pending'";
        if (!$this->db->save($updateQuery)) {
            return ['success' => false, 'message' => 'Failed to update earnings status'];
        }
        
        // Add record to funds table
        $title = "Payment to partner {$partner['contact_name']}";
        
        // Get last transaction for the staff
        $lastTrans = $this->db->read("SELECT * FROM funds WHERE staff_id = '$staffId' ORDER BY id DESC LIMIT 1");
        
        $currentBalance = 0;
        if ($lastTrans && isset($lastTrans[0])) {
            $currentBalance = $lastTrans[0]['current_balance'];
        }
        
        // Subtract amount (type = 1 means outgoing payment)
        $currentBalance = $currentBalance - $amount;
        
        // Insert into funds
        $fundsQuery = "INSERT INTO funds (title, amount, current_balance, type, staff_id, transfer_id) 
                       VALUES ('$title', '$amount', '$currentBalance', '1', '$staffId', '$paymentHistoryId')";
        
        if (!$this->db->save($fundsQuery)) {
            return ['success' => false, 'message' => 'Failed to update funds'];
        }
        
        // Send payout notification email to partner
        $emailResult = $this->sendPayoutNotificationEmail($paymentHistoryId);
        if (!$emailResult['success']) {
            // Log email failure but don't fail the entire payout process
            error_log("Failed to send payout notification email: " . $emailResult['message']);
        }
        
        return [
            'success' => true, 
            'message' => 'Payout processed successfully',
            'payment_history_id' => $paymentHistoryId,
            'email_sent' => $emailResult['success']
        ];
    }
    
    /**
     * Reset partner password
     */
    public function resetPartnerPassword($partnerId, $newPassword) {
        // Validate password length
        if (strlen($newPassword) < 8) {
            return ['success' => false, 'message' => 'Password must be at least 8 characters long'];
        }
        
        // Hash password
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        
        // Update password
        $query = "UPDATE partners SET password = '$hashedPassword', updated_at = NOW() WHERE id = '$partnerId'";
        
        if ($this->db->save($query)) {
            // Invalidate all existing sessions for this partner
            $this->db->save("DELETE FROM partner_sessions WHERE partner_id = '$partnerId'");
            
            return ['success' => true, 'message' => 'Password reset successfully. All sessions have been terminated.'];
        }
        
        return ['success' => false, 'message' => 'Failed to reset password'];
    }
    
    /**
     * Process payout for a partner
     */
    public function processPayout($partnerId) {
        // Check if partner has pending earnings
        $pendingQuery = "SELECT COUNT(*) as count FROM partner_earnings WHERE partner_id = '$partnerId' AND status = 'pending'";
        $result = $this->db->read($pendingQuery);
        
        if ($result[0]['count'] == 0) {
            return ['success' => false, 'message' => 'No pending earnings found for this partner'];
        }
        
        // Update all pending earnings to paid
        $updateQuery = "UPDATE partner_earnings SET status = 'paid', updated_at = NOW() WHERE partner_id = '$partnerId' AND status = 'pending'";
        
        if ($this->db->save($updateQuery)) {
            return ['success' => true, 'message' => 'Payout processed successfully'];
        }
        
        return ['success' => false, 'message' => 'Failed to process payout'];
    }
}
