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
        
        // Recent conversions
        $conversions = $this->getRecentConversions($partnerId, 10);
        $data['recent_conversions'] = $conversions;
        
        // Top performing links
        $topLinks = $this->getTopPerformingLinks($partnerId, 5);
        $data['top_links'] = $topLinks;
        
        // Monthly earnings chart data
        $monthlyEarnings = $this->getMonthlyEarnings($partnerId, 12);
        $data['monthly_earnings'] = $monthlyEarnings;
        
        return $data;
    }
    
    // Get partner statistics
    private function getPartnerStats($partnerId) {
        $stats = [];
        
        // Total clicks
        $totalClicks = $this->db->read("SELECT SUM(clicks) as total FROM affiliate_links WHERE partner_id = '$partnerId'");
        $stats['total_clicks'] = $totalClicks[0]['total'] ?? 0;
        
        // Total conversions
        $totalConversions = $this->db->read("SELECT COUNT(*) as total FROM conversions WHERE partner_id = '$partnerId' AND status = 'approved'");
        $stats['total_conversions'] = $totalConversions[0]['total'] ?? 0;
        
        // Conversion rate
        $stats['conversion_rate'] = $stats['total_clicks'] > 0 ? 
            round(($stats['total_conversions'] / $stats['total_clicks']) * 100, 2) : 0;
        
        // Total earnings
        $totalEarnings = $this->db->read("SELECT SUM(commission_amount) as total FROM conversions WHERE partner_id = '$partnerId' AND status = 'approved'");
        $stats['total_earnings'] = $totalEarnings[0]['total'] ?? 0;
        
        // Pending earnings
        $pendingEarnings = $this->db->read("SELECT SUM(commission_amount) as total FROM conversions WHERE partner_id = '$partnerId' AND status = 'pending'");
        $stats['pending_earnings'] = $pendingEarnings[0]['total'] ?? 0;
        
        // This month's earnings
        $thisMonth = $this->db->read("SELECT SUM(commission_amount) as total FROM conversions 
                                    WHERE partner_id = '$partnerId' AND status = 'approved' 
                                    AND MONTH(conversion_date) = MONTH(NOW()) AND YEAR(conversion_date) = YEAR(NOW())");
        $stats['this_month_earnings'] = $thisMonth[0]['total'] ?? 0;
        
        // Active links
        $activeLinks = $this->db->read("SELECT COUNT(*) as total FROM affiliate_links WHERE partner_id = '$partnerId' AND status = 'active'");
        $stats['active_links'] = $activeLinks[0]['total'] ?? 0;
        
        return $stats;
    }
    
    // Get recent conversions
    private function getRecentConversions($partnerId, $limit = 10) {
        $query = "SELECT c.*, al.campaign_name, al.link_code 
                 FROM conversions c 
                 JOIN affiliate_links al ON c.affiliate_link_id = al.id 
                 WHERE c.partner_id = '$partnerId' 
                 ORDER BY c.conversion_date DESC 
                 LIMIT $limit";
        
        return $this->db->read($query);
    }
    
    // Get top performing links
    private function getTopPerformingLinks($partnerId, $limit = 5) {
        $query = "SELECT al.*, 
                        COUNT(c.id) as conversions,
                        SUM(c.commission_amount) as total_commission,
                        ROUND((COUNT(c.id) / al.clicks) * 100, 2) as conversion_rate
                 FROM affiliate_links al 
                 LEFT JOIN conversions c ON al.id = c.affiliate_link_id AND c.status = 'approved'
                 WHERE al.partner_id = '$partnerId' 
                 GROUP BY al.id 
                 ORDER BY total_commission DESC 
                 LIMIT $limit";
        
        return $this->db->read($query);
    }
    
    // Get monthly earnings for chart
    private function getMonthlyEarnings($partnerId, $months = 12) {
        $query = "SELECT 
                    DATE_FORMAT(conversion_date, '%Y-%m') as month,
                    SUM(commission_amount) as earnings
                 FROM conversions 
                 WHERE partner_id = '$partnerId' 
                 AND status = 'approved'
                 AND conversion_date >= DATE_SUB(NOW(), INTERVAL $months MONTH)
                 GROUP BY DATE_FORMAT(conversion_date, '%Y-%m')
                 ORDER BY month ASC";
        
        return $this->db->read($query);
    }
    
    // Create new affiliate link
    public function createAffiliateLink($partnerId, $campaignName, $targetCourseId = null, $targetMajor = null, $customUrl = '') {
        // Generate unique link code
        $linkCode = $this->generateLinkCode();
        
        $query = "INSERT INTO affiliate_links (partner_id, link_code, campaign_name, target_course_id, target_major, custom_url) 
                 VALUES ('$partnerId', '$linkCode', '$campaignName', '$targetCourseId', '$targetMajor', '$customUrl')";
        
        if ($this->db->save($query)) {
            return [
                'success' => true, 
                'link_code' => $linkCode,
                'affiliate_url' => "http://localhost/business/affiliate.php?ref=$linkCode"
            ];
        }
        
        return ['success' => false, 'message' => 'Failed to create affiliate link'];
    }
    
    // Generate unique link code
    private function generateLinkCode() {
        do {
            $code = strtoupper(substr(md5(uniqid()), 0, 8));
            $exists = $this->db->read("SELECT id FROM affiliate_links WHERE link_code = '$code'");
        } while ($exists);
        
        return $code;
    }
    
    // Get partner's affiliate links
    public function getAffiliateLinks($partnerId) {
        $query = "SELECT * FROM affiliate_links WHERE partner_id = '$partnerId' ORDER BY created_at DESC";
        return $this->db->read($query);
    }
    
    // Get conversion history
    public function getConversionHistory($partnerId, $page = 1, $limit = 20) {
        $offset = ($page - 1) * $limit;
        
        $query = "SELECT c.*, al.campaign_name, al.link_code 
                 FROM conversions c 
                 JOIN affiliate_links al ON c.affiliate_link_id = al.id 
                 WHERE c.partner_id = '$partnerId' 
                 ORDER BY c.conversion_date DESC 
                 LIMIT $limit OFFSET $offset";
        
        return $this->db->read($query);
    }
    
    // Get payment history
    public function getPaymentHistory($partnerId) {
        $query = "SELECT * FROM commission_payments WHERE partner_id = '$partnerId' ORDER BY created_at DESC";
        return $this->db->read($query);
    }
    
    // Update partner profile
    public function updateProfile($partnerId, $data) {
        $allowedFields = ['contact_name', 'company_name', 'phone', 'website', 'payment_method', 'payment_details'];
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
