<?php
// All classes are loaded by autoloader

class PartnerDashboard {
    private $db;
    private $auth;
    
    public function __construct() {
        $this->db = new Database();
        $this->auth = new PartnerAuth();
    }
    
    // Get partner dashboard data
    public function getDashboardData($partnerId) {
        $data = [];
        
        // Basic partner info
        $partner = $this->db->read("SELECT * FROM partners WHERE id = '$partnerId'")[0];
        $data['partner'] = $partner;
        
        // Statistics
        $stats = $this->getPartnerStats($partnerId);
        $data['stats'] = $stats;
        
        // Monthly earnings chart data (empty for now)
        $data['monthly_earnings'] = [];
        
        return $data;
    }
    
    // Get partner statistics
    private function getPartnerStats($partnerId) {
        $stats = [];
        
        // Since affiliate functionality is removed, set all stats to 0
        $stats['total_clicks'] = 0;
        $stats['total_conversions'] = 0;
        $stats['conversion_rate'] = 0;
        $stats['total_earnings'] = 0;
        $stats['pending_earnings'] = 0;
        $stats['this_month_earnings'] = 0;
        $stats['active_links'] = 0;
        
        return $stats;
    }
    
    // Update partner profile
    public function updateProfile($partnerId, $data) {
        $allowedFields = ['contact_name', 'company_name', 'phone', 'website', 'payment_method', 'payment_details', 'profile_image'];
        $updates = [];
        
        foreach ($allowedFields as $field) {
            if (isset($data[$field])) {
                $value = $this->db->connect()->real_escape_string($data[$field]);
                $updates[] = "$field = '$value'";
            }
        }
        
        if (empty($updates)) {
            return ['success' => false, 'message' => 'No valid fields to update'];
        }
        
        $query = "UPDATE partners SET " . implode(', ', $updates) . " WHERE id = '$partnerId'";
        
        if ($this->db->save($query)) {
            return ['success' => true, 'message' => 'Profile updated successfully'];
        }
        
        return ['success' => false, 'message' => 'Failed to update profile'];
    }
}
?>