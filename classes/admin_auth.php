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
     * Get all partners with pagination
     */
    public function getAllPartners($page = 1, $limit = 20) {
        $offset = ($page - 1) * $limit;
        
        // Get total count
        $totalQuery = "SELECT COUNT(*) as total FROM partners";
        $totalResult = $this->db->read($totalQuery);
        $total = $totalResult[0]['total'];
        
        // Get partners with pagination
        $partnersQuery = "SELECT * FROM partners ORDER BY created_at DESC LIMIT $limit OFFSET $offset";
        $partners = $this->db->read($partnersQuery);
        
        return [
            'partners' => $partners,
            'total' => $total,
            'page' => $page,
            'limit' => $limit,
            'total_pages' => ceil($total / $limit)
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
}
