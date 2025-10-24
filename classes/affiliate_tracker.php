<?php
// Database class is loaded by autoloader

class AffiliateTracker {
    private $db;
    
    public function __construct() {
        $this->db = new Database();
    }
    
    // Track affiliate click
    public function trackClick($linkCode, $ipAddress = '', $userAgent = '', $referrer = '') {
        // Get affiliate link info
        $link = $this->db->read("SELECT * FROM affiliate_links WHERE link_code = '$linkCode' AND status = 'active'");
        
        if (!$link) {
            return ['success' => false, 'message' => 'Invalid affiliate link'];
        }
        
        $link = $link[0];
        
        // Increment click count
        $this->db->save("UPDATE affiliate_links SET clicks = clicks + 1 WHERE id = '{$link['id']}'");
        
        // Store click data in session/cookie for conversion tracking
        $clickData = [
            'affiliate_link_id' => $link['id'],
            'partner_id' => $link['partner_id'],
            'link_code' => $linkCode,
            'ip_address' => $ipAddress,
            'user_agent' => $userAgent,
            'referrer' => $referrer,
            'timestamp' => time()
        ];
        
        // Set cookie for 30 days
        setcookie('affiliate_tracking', json_encode($clickData), time() + (30 * 24 * 60 * 60), '/');
        
        return [
            'success' => true, 
            'link' => $link,
            'redirect_url' => $this->getRedirectUrl($link)
        ];
    }
    
    // Track conversion (VIP subscription)
    public function trackConversion($userId, $conversionType = 'vip_subscription', $conversionValue = 0) {
        // Check if user came from affiliate link
        $clickData = $this->getAffiliateClickData();
        
        if (!$clickData) {
            return ['success' => false, 'message' => 'No affiliate tracking data found'];
        }
        
        // Check if conversion already exists for this user
        $existingConversion = $this->db->read("SELECT id FROM conversions WHERE user_id = '$userId' AND conversion_type = '$conversionType'");
        
        if ($existingConversion) {
            return ['success' => false, 'message' => 'Conversion already tracked for this user'];
        }
        
        // Get partner commission rate
        $partner = $this->db->read("SELECT commission_rate FROM partners WHERE id = '{$clickData['partner_id']}'");
        $commissionRate = $partner[0]['commission_rate'] ?? 10.0;
        
        // Calculate commission
        $commissionAmount = ($conversionValue * $commissionRate) / 100;
        
        // Create conversion record
        $query = "INSERT INTO conversions (
            partner_id, affiliate_link_id, user_id, conversion_type, 
            conversion_value, commission_rate, commission_amount, 
            ip_address, user_agent, referrer_url
        ) VALUES (
            '{$clickData['partner_id']}', 
            '{$clickData['affiliate_link_id']}', 
            '$userId', 
            '$conversionType', 
            '$conversionValue', 
            '$commissionRate', 
            '$commissionAmount',
            '{$clickData['ip_address']}',
            '{$clickData['user_agent']}',
            '{$clickData['referrer']}'
        )";
        
        if ($this->db->save($query)) {
            // Update affiliate link conversion count
            $this->db->save("UPDATE affiliate_links SET conversions = conversions + 1, revenue = revenue + $conversionValue, commission_earned = commission_earned + $commissionAmount WHERE id = '{$clickData['affiliate_link_id']}'");
            
            // Update partner total earnings
            $this->db->save("UPDATE partners SET total_earnings = total_earnings + $commissionAmount, pending_amount = pending_amount + $commissionAmount WHERE id = '{$clickData['partner_id']}'");
            
            // Clear tracking cookie
            setcookie('affiliate_tracking', '', time() - 3600, '/');
            
            return [
                'success' => true, 
                'message' => 'Conversion tracked successfully',
                'commission_amount' => $commissionAmount
            ];
        }
        
        return ['success' => false, 'message' => 'Failed to track conversion'];
    }
    
    // Get affiliate click data from cookie
    private function getAffiliateClickData() {
        if (isset($_COOKIE['affiliate_tracking'])) {
            $data = json_decode($_COOKIE['affiliate_tracking'], true);
            
            // Check if click data is not too old (30 days)
            if ($data && (time() - $data['timestamp']) < (30 * 24 * 60 * 60)) {
                return $data;
            }
        }
        
        return null;
    }
    
    // Get redirect URL based on affiliate link settings
    private function getRedirectUrl($link) {
        if (!empty($link['custom_url'])) {
            return $link['custom_url'];
        }
        
        // Default redirect based on target course or major
        if ($link['target_course_id']) {
            return "http://localhost/business/course.php?id={$link['target_course_id']}&ref={$link['link_code']}";
        }
        
        if ($link['target_major']) {
            return "http://localhost/business/courses.php?major={$link['target_major']}&ref={$link['link_code']}";
        }
        
        // Default homepage with tracking
        return "http://localhost/business/index.php?ref={$link['link_code']}";
    }
    
    // Get conversion statistics for a partner
    public function getPartnerStats($partnerId, $period = '30') {
        $query = "SELECT 
                    COUNT(*) as total_conversions,
                    SUM(conversion_value) as total_revenue,
                    SUM(commission_amount) as total_commission,
                    AVG(conversion_value) as avg_order_value,
                    COUNT(DISTINCT DATE(conversion_date)) as active_days
                 FROM conversions 
                 WHERE partner_id = '$partnerId' 
                 AND status = 'approved'
                 AND conversion_date >= DATE_SUB(NOW(), INTERVAL $period DAY)";
        
        $stats = $this->db->read($query);
        
        if ($stats) {
            return $stats[0];
        }
        
        return [
            'total_conversions' => 0,
            'total_revenue' => 0,
            'total_commission' => 0,
            'avg_order_value' => 0,
            'active_days' => 0
        ];
    }
    
    // Get top performing partners
    public function getTopPartners($limit = 10, $period = '30') {
        $query = "SELECT 
                    p.id, p.contact_name, p.company_name, p.email,
                    COUNT(c.id) as conversions,
                    SUM(c.conversion_value) as revenue,
                    SUM(c.commission_amount) as commission
                 FROM partners p
                 LEFT JOIN conversions c ON p.id = c.partner_id 
                 AND c.status = 'approved'
                 AND c.conversion_date >= DATE_SUB(NOW(), INTERVAL $period DAY)
                 WHERE p.status = 'active'
                 GROUP BY p.id
                 ORDER BY commission DESC
                 LIMIT $limit";
        
        return $this->db->read($query);
    }
    
    // Approve conversion (admin function)
    public function approveConversion($conversionId) {
        $conversion = $this->db->read("SELECT * FROM conversions WHERE id = '$conversionId'");
        
        if (!$conversion) {
            return ['success' => false, 'message' => 'Conversion not found'];
        }
        
        $conversion = $conversion[0];
        
        // Update conversion status
        $this->db->save("UPDATE conversions SET status = 'approved' WHERE id = '$conversionId'");
        
        // Update partner earnings
        $this->db->save("UPDATE partners SET 
                        pending_amount = pending_amount - {$conversion['commission_amount']},
                        total_earnings = total_earnings + {$conversion['commission_amount']}
                        WHERE id = '{$conversion['partner_id']}'");
        
        return ['success' => true, 'message' => 'Conversion approved successfully'];
    }
    
    // Cancel conversion (admin function)
    public function cancelConversion($conversionId) {
        $conversion = $this->db->read("SELECT * FROM conversions WHERE id = '$conversionId'");
        
        if (!$conversion) {
            return ['success' => false, 'message' => 'Conversion not found'];
        }
        
        $conversion = $conversion[0];
        
        // Update conversion status
        $this->db->save("UPDATE conversions SET status = 'cancelled' WHERE id = '$conversionId'");
        
        // Update partner earnings
        $this->db->save("UPDATE partners SET 
                        pending_amount = pending_amount - {$conversion['commission_amount']}
                        WHERE id = '{$conversion['partner_id']}'");
        
        // Update affiliate link stats
        $this->db->save("UPDATE affiliate_links SET 
                        conversions = conversions - 1,
                        revenue = revenue - {$conversion['conversion_value']},
                        commission_earned = commission_earned - {$conversion['commission_amount']}
                        WHERE id = '{$conversion['affiliate_link_id']}'");
        
        return ['success' => true, 'message' => 'Conversion cancelled successfully'];
    }
    
    // Generate affiliate link URL
    public function generateAffiliateUrl($linkCode, $baseUrl = '') {
        if (empty($baseUrl)) {
            $baseUrl = 'http://localhost/business/affiliate.php';
        }
        
        return $baseUrl . '?ref=' . $linkCode;
    }
    
    // Validate affiliate link
    public function validateAffiliateLink($linkCode) {
        $link = $this->db->read("SELECT al.*, p.status as partner_status 
                               FROM affiliate_links al 
                               JOIN partners p ON al.partner_id = p.id 
                               WHERE al.link_code = '$linkCode'");
        
        if (!$link) {
            return ['valid' => false, 'message' => 'Invalid affiliate link'];
        }
        
        $link = $link[0];
        
        if ($link['status'] !== 'active') {
            return ['valid' => false, 'message' => 'Affiliate link is not active'];
        }
        
        if ($link['partner_status'] !== 'active') {
            return ['valid' => false, 'message' => 'Partner account is not active'];
        }
        
        return ['valid' => true, 'link' => $link];
    }
}
?>
