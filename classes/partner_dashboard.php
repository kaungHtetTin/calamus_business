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
    public function getDashboardData($partnerId, $earningStats = null) {
        $data = [];

        // Get earnings data
        $earningsManager = new PartnerEarningsManager();
        if ($earningStats === null) {
            $earningStats = $earningsManager->getPartnerEarningStats($partnerId);
        }
        
        // Map earnings stats to dashboard data
        $data['total_earnings'] = $earningStats['total_earnings'];
        $data['total_transactions'] = $earningStats['total_transactions'];
        $data['this_month_earnings'] = $earningStats['this_month_earnings'];
        $data['pending_earnings'] = $earningStats['pending_earnings'];
        $data['today_earnings'] = $earningStats['today_earnings'];
        $data['yesterday_earnings'] = $earningStats['yesterday_earnings'];
        
        // Get recent earnings
        $data['recent_earnings'] = $earningsManager->getPartnerEarningHistory($partnerId, 10);
        
        // Monthly earnings chart data (empty for now)
        $data['monthly_earnings'] = [];
        
        return $data;
    }
    
    // Update partner profile
    public function updateProfile($partnerId, $data) {
        $allowedFields = ['contact_name', 'company_name', 'phone', 'website', 'profile_image'];
        $updates = [];
        
        foreach ($allowedFields as $field) {
            if (isset($data[$field])) {
                $value = $this->db->escape($data[$field]);
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