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
        $stats = [];
        
        // Total partners
        $totalQuery = "SELECT COUNT(*) as total FROM partners";
        $result = $this->db->read($totalQuery);
        $stats['total'] = $result[0]['total'];
        
        // Active partners
        $activeQuery = "SELECT COUNT(*) as total FROM partners WHERE status = 'active'";
        $result = $this->db->read($activeQuery);
        $stats['active'] = $result[0]['total'];
        
        // Inactive partners
        $inactiveQuery = "SELECT COUNT(*) as total FROM partners WHERE status = 'inactive'";
        $result = $this->db->read($inactiveQuery);
        $stats['inactive'] = $result[0]['total'];
        
        // Suspended partners
        $suspendedQuery = "SELECT COUNT(*) as total FROM partners WHERE status = 'suspended'";
        $result = $this->db->read($suspendedQuery);
        $stats['suspended'] = $result[0]['total'];
        
        // Verified partners
        $verifiedQuery = "SELECT COUNT(*) as total FROM partners WHERE email_verified = 1";
        $result = $this->db->read($verifiedQuery);
        $stats['verified'] = $result[0]['total'];
        
        // Unverified partners
        $unverifiedQuery = "SELECT COUNT(*) as total FROM partners WHERE email_verified = 0";
        $result = $this->db->read($unverifiedQuery);
        $stats['unverified'] = $result[0]['total'];
        
        // New partners this month
        $newThisMonthQuery = "SELECT COUNT(*) as total FROM partners WHERE YEAR(created_at) = YEAR(CURRENT_DATE()) AND MONTH(created_at) = MONTH(CURRENT_DATE())";
        $result = $this->db->read($newThisMonthQuery);
        $stats['new_this_month'] = $result[0]['total'];
        
        return $stats;
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
            $whereClause .= " AND DATE(pe.created_at) >= '$startDate'";
        }
        
        if ($endDate) {
            $whereClause .= " AND DATE(pe.created_at) <= '$endDate'";
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
            $whereClause .= " AND DATE(created_at) >= '$startDate'";
        }
        
        if ($endDate) {
            $whereClause .= " AND DATE(created_at) <= '$endDate'";
        }
        
        $stats = [];
        
        // Total earnings
        $totalQuery = "SELECT SUM(amount_received) as total FROM partner_earnings $whereClause";
        $result = $this->db->read($totalQuery);
        $stats['total_amount'] = $result && $result[0]['total'] ? (float)$result[0]['total'] : 0.00;
        
        // Total transactions
        $countQuery = "SELECT COUNT(*) as total FROM partner_earnings $whereClause";
        $result = $this->db->read($countQuery);
        $stats['total_transactions'] = $result ? (int)$result[0]['total'] : 0;
        
        // Pending earnings
        $pendingQuery = "SELECT SUM(amount_received) as total FROM partner_earnings $whereClause AND status = 'pending'";
        $result = $this->db->read($pendingQuery);
        $stats['pending_amount'] = $result && $result[0]['total'] ? (float)$result[0]['total'] : 0.00;
        
        // Paid earnings
        $paidQuery = "SELECT SUM(amount_received) as total FROM partner_earnings $whereClause AND status = 'paid'";
        $result = $this->db->read($paidQuery);
        $stats['paid_amount'] = $result && $result[0]['total'] ? (float)$result[0]['total'] : 0.00;
        
        return $stats;
    }
    
    /**
     * Get payout logs (grouped by partner)
     */
    public function getPayoutLogs($page = 1, $limit = 20, $status = null, $startDate = null, $endDate = null) {
        $offset = ($page - 1) * $limit;
        
        // Build WHERE clause
        $whereClause = "WHERE 1=1";
        
        if ($startDate) {
            $whereClause .= " AND DATE(created_at) >= '$startDate'";
        }
        
        if ($endDate) {
            $whereClause .= " AND DATE(created_at) <= '$endDate'";
        }
        
        // Get total count
        $countQuery = "SELECT COUNT(DISTINCT partner_id) as total FROM partner_earnings $whereClause";
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
                    p.phone,
                    MAX(pe.status) as status
                 FROM partner_earnings pe 
                 LEFT JOIN partners p ON pe.partner_id = p.id 
                 $whereClause 
                 GROUP BY pe.partner_id, p.contact_name, p.company_name, p.email, p.phone";
        
        // Apply status filter after grouping
        if ($status) {
            // For grouped data, we need to filter by each partner's status
            // We'll do this by checking if all their earnings have the same status
            $query = "SELECT 
                        pe.partner_id,
                        SUM(pe.amount_received) as total_amount,
                        COUNT(pe.id) as transaction_count,
                        p.contact_name,
                        p.company_name,
                        p.email,
                        p.phone,
                        MAX(pe.status) as status
                     FROM partner_earnings pe 
                     LEFT JOIN partners p ON pe.partner_id = p.id 
                     $whereClause 
                     GROUP BY pe.partner_id, p.contact_name, p.company_name, p.email, p.phone
                     HAVING MAX(pe.status) = '$status'";
        }
        
        // Order by status (pending first, then paid), then by total amount descending
        $query .= " ORDER BY 
                     CASE 
                         WHEN pe.status = 'pending' THEN 0 
                         WHEN pe.status = 'paid' THEN 1 
                         ELSE 2 
                     END,
                     total_amount DESC 
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
    public function getPayoutLogsStatistics($status = null, $startDate = null, $endDate = null) {
        $whereClause = "WHERE 1=1";
        
        if ($startDate) {
            $whereClause .= " AND DATE(created_at) >= '$startDate'";
        }
        
        if ($endDate) {
            $whereClause .= " AND DATE(created_at) <= '$endDate'";
        }
        
        $stats = [];
        
        // Total payout amount (sum of all grouped amounts)
        $totalQuery = "SELECT SUM(total_amount) as total FROM (
            SELECT pe.partner_id, SUM(pe.amount_received) as total_amount, MAX(pe.status) as status
            FROM partner_earnings pe 
            $whereClause 
            GROUP BY pe.partner_id
            " . ($status ? " HAVING MAX(pe.status) = '$status'" : "") . "
        ) as grouped_earnings";
        $result = $this->db->read($totalQuery);
        $stats['total_amount'] = $result && $result[0]['total'] ? (float)$result[0]['total'] : 0.00;
        
        // Total partners
        $countQuery = "SELECT COUNT(DISTINCT partner_id) as total FROM partner_earnings $whereClause" . ($status ? " AND status = '$status'" : "");
        $result = $this->db->read($countQuery);
        $stats['total_partners'] = $result ? (int)$result[0]['total'] : 0;
        
        // Pending payout amount
        $pendingQuery = "SELECT SUM(total_amount) as total FROM (
            SELECT pe.partner_id, SUM(pe.amount_received) as total_amount
            FROM partner_earnings pe 
            $whereClause 
            GROUP BY pe.partner_id
            HAVING MAX(pe.status) = 'pending'
        ) as pending_earnings";
        $result = $this->db->read($pendingQuery);
        $stats['pending_amount'] = $result && $result[0]['total'] ? (float)$result[0]['total'] : 0.00;
        
        // Paid payout amount
        $paidQuery = "SELECT SUM(total_amount) as total FROM (
            SELECT pe.partner_id, SUM(pe.amount_received) as total_amount
            FROM partner_earnings pe 
            $whereClause 
            GROUP BY pe.partner_id
            HAVING MAX(pe.status) = 'paid'
        ) as paid_earnings";
        $result = $this->db->read($paidQuery);
        $stats['paid_amount'] = $result && $result[0]['total'] ? (float)$result[0]['total'] : 0.00;
        
        return $stats;
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
            $yearFilter = " AND YEAR(pph.created_at) = YEAR(CURRENT_DATE)";
        } elseif ($year === '2024') {
            $yearFilter = " AND YEAR(pph.created_at) = 2024";
        } elseif ($year === '2023') {
            $yearFilter = " AND YEAR(pph.created_at) = 2023";
        } elseif ($year === '2022') {
            $yearFilter = " AND YEAR(pph.created_at) = 2022";
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
            $whereClause .= " AND DATE(pph.created_at) >= '$startDate'";
        }
        
        if ($endDate) {
            $whereClause .= " AND DATE(pph.created_at) <= '$endDate'";
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
            $whereClause .= " AND DATE(created_at) >= '$startDate'";
        }
        
        if ($endDate) {
            $whereClause .= " AND DATE(created_at) <= '$endDate'";
        }
        
        $stats = [];
        
        // Total payout amount
        $totalQuery = "SELECT SUM(amount) as total FROM partner_payment_histories $whereClause";
        $result = $this->db->read($totalQuery);
        $stats['total_amount'] = $result && $result[0]['total'] ? (float)$result[0]['total'] : 0.00;
        
        // Total transactions
        $countQuery = "SELECT COUNT(*) as total FROM partner_payment_histories $whereClause";
        $result = $this->db->read($countQuery);
        $stats['total_transactions'] = $result ? (int)$result[0]['total'] : 0;
        
        // Pending amount
        $pendingQuery = "SELECT SUM(amount) as total FROM partner_payment_histories WHERE status = 'pending' $whereClause";
        $result = $this->db->read($pendingQuery);
        $stats['pending_amount'] = $result && $result[0]['total'] ? (float)$result[0]['total'] : 0.00;
        
        // Received amount
        $receivedQuery = "SELECT SUM(amount) as total FROM partner_payment_histories WHERE status = 'received' $whereClause";
        $result = $this->db->read($receivedQuery);
        $stats['received_amount'] = $result && $result[0]['total'] ? (float)$result[0]['total'] : 0.00;
        
        return $stats;
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
