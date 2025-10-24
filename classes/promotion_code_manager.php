<?php
// Database class is loaded by autoloader

class PromotionCodeManager {
    private $db;
    
    public function __construct() {
        $this->db = new Database();
    }
    
    // Generate a new promotion code
    public function generatePromotionCode($partnerId, $codeType, $targetCourseId = null, $targetMajor = null, $packageId = null, $clientName = '', $expiresAt = null) {
        // Get partner details
        $partner = $this->db->read("SELECT * FROM partners WHERE id = '$partnerId'");
        if (!$partner) {
            return ['success' => false, 'message' => 'Partner not found'];
        }
        
        $partner = $partner[0];
        $codePrefix = $partner['code_prefix'] ?? 'PART';
        
        // Generate unique code
        $code = $this->generateUniqueCode($codePrefix, $codeType, $targetCourseId, $targetMajor, $packageId);
        
        // Get commission rate
        $commissionRate = $partner['commission_rate'];
        
        // Insert promotion code
        $query = "INSERT INTO promotion_codes 
                 (partner_id, code, code_type, target_course_id, target_major, package_id, commission_rate, 
                  generated_by, generated_for, expires_at) 
                 VALUES ('$partnerId', '$code', '$codeType', '$targetCourseId', '$targetMajor', '$packageId', 
                         '$commissionRate', '$partnerId', '$clientName', '$expiresAt')";
        
        if ($this->db->save($query)) {
            // Update partner code count
            $this->db->save("UPDATE partners SET total_codes_generated = total_codes_generated + 1 WHERE id = '$partnerId'");
            
            return [
                'success' => true,
                'code' => $code,
                'message' => 'Promotion code generated successfully'
            ];
        }
        
        return ['success' => false, 'message' => 'Failed to generate promotion code'];
    }
    
    // Generate unique code
    private function generateUniqueCode($prefix, $codeType, $targetCourseId, $targetMajor, $packageId = null) {
        do {
            // Format: PREFIX-TYPE-COURSE/PACKAGE-RANDOM
            $typeCode = $this->getTypeCode($codeType);
            
            if ($packageId) {
                $targetCode = str_pad($packageId, 3, '0', STR_PAD_LEFT);
            } else {
                $targetCode = $targetCourseId ? str_pad($targetCourseId, 3, '0', STR_PAD_LEFT) : '000';
            }
            
            $randomCode = str_pad(rand(1000, 9999), 4, '0', STR_PAD_LEFT);
            
            $code = $prefix . '-' . $typeCode . '-' . $targetCode . '-' . $randomCode;
            
            // Check if code already exists
            $existing = $this->db->read("SELECT id FROM promotion_codes WHERE code = '$code'");
        } while ($existing);
        
        return $code;
    }
    
    // Get type code for code generation
    private function getTypeCode($codeType) {
        $typeCodes = [
            'vip_subscription' => 'VIP',
            'course_purchase' => 'CRS',
            'package_purchase' => 'PKG'
        ];
        return $typeCodes[$codeType] ?? 'GEN';
    }
    
    // Validate promotion code
    public function validatePromotionCode($code) {
        $promoCode = $this->db->read("SELECT pc.*, p.contact_name, p.company_name 
                                    FROM promotion_codes pc 
                                    JOIN partners p ON pc.partner_id = p.id 
                                    WHERE pc.code = '$code'");
        
        if (!$promoCode) {
            return ['valid' => false, 'message' => 'Invalid promotion code'];
        }
        
        $promoCode = $promoCode[0];
        
        // Check if code is active
        if ($promoCode['status'] !== 'active') {
            return ['valid' => false, 'message' => 'Promotion code is not active'];
        }
        
        // Check if code has expired
        if ($promoCode['expires_at'] && strtotime($promoCode['expires_at']) < time()) {
            // Mark as expired
            $this->db->save("UPDATE promotion_codes SET status = 'expired' WHERE id = '{$promoCode['id']}'");
            return ['valid' => false, 'message' => 'Promotion code has expired'];
        }
        
        return [
            'valid' => true,
            'code_data' => $promoCode,
            'message' => 'Valid promotion code'
        ];
    }
    
    // Use promotion code (mark as used)
    public function usePromotionCode($code, $learnerPhone) {
        $validation = $this->validatePromotionCode($code);
        
        if (!$validation['valid']) {
            return $validation;
        }
        
        $codeData = $validation['code_data'];
        
        // Mark code as used
        $query = "UPDATE promotion_codes 
                 SET status = 'used', used_at = NOW(), used_by = '$learnerPhone' 
                 WHERE code = '$code'";
        
        if ($this->db->save($query)) {
            // Update partner used code count
            $this->db->save("UPDATE partners SET total_codes_used = total_codes_used + 1 WHERE id = '{$codeData['partner_id']}'");
            
            return [
                'success' => true,
                'code_data' => $codeData,
                'message' => 'Promotion code used successfully'
            ];
        }
        
        return ['success' => false, 'message' => 'Failed to use promotion code'];
    }
    
    // Get partner's promotion codes
    public function getPartnerPromotionCodes($partnerId, $status = null, $limit = 50) {
        $whereClause = "WHERE partner_id = '$partnerId'";
        
        if ($status) {
            $whereClause .= " AND status = '$status'";
        }
        
        $query = "SELECT pc.*, c.title as course_title 
                 FROM promotion_codes pc 
                 LEFT JOIN courses c ON pc.target_course_id = c.course_id 
                 $whereClause 
                 ORDER BY pc.created_at DESC 
                 LIMIT $limit";
        
        return $this->db->read($query);
    }
    
    // Get promotion code statistics for partner
    public function getPartnerCodeStats($partnerId) {
        $stats = [];
        
        // Total codes generated
        $totalGenerated = $this->db->read("SELECT COUNT(*) as total FROM promotion_codes WHERE partner_id = '$partnerId'");
        $stats['total_generated'] = $totalGenerated[0]['total'] ?? 0;
        
        // Active codes
        $activeCodes = $this->db->read("SELECT COUNT(*) as total FROM promotion_codes WHERE partner_id = '$partnerId' AND status = 'active'");
        $stats['active_codes'] = $activeCodes[0]['total'] ?? 0;
        
        // Used codes
        $usedCodes = $this->db->read("SELECT COUNT(*) as total FROM promotion_codes WHERE partner_id = '$partnerId' AND status = 'used'");
        $stats['used_codes'] = $usedCodes[0]['total'] ?? 0;
        
        // Expired codes
        $expiredCodes = $this->db->read("SELECT COUNT(*) as total FROM promotion_codes WHERE partner_id = '$partnerId' AND status = 'expired'");
        $stats['expired_codes'] = $expiredCodes[0]['total'] ?? 0;
        
        // Usage rate
        $stats['usage_rate'] = $stats['total_generated'] > 0 ? 
            round(($stats['used_codes'] / $stats['total_generated']) * 100, 2) : 0;
        
        // Commission earned from codes (set to 0 since conversions table is removed)
        $stats['commission_earned'] = 0;
        
        return $stats;
    }
    
    // Cancel promotion code
    public function cancelPromotionCode($codeId, $partnerId) {
        $code = $this->db->read("SELECT * FROM promotion_codes WHERE id = '$codeId' AND partner_id = '$partnerId'");
        
        if (!$code) {
            return ['success' => false, 'message' => 'Promotion code not found'];
        }
        
        $code = $code[0];
        
        if ($code['status'] !== 'active') {
            return ['success' => false, 'message' => 'Only active codes can be cancelled'];
        }
        
        $query = "UPDATE promotion_codes SET status = 'cancelled' WHERE id = '$codeId'";
        
        if ($this->db->save($query)) {
            return ['success' => true, 'message' => 'Promotion code cancelled successfully'];
        }
        
        return ['success' => false, 'message' => 'Failed to cancel promotion code'];
    }
    
    // Get code usage history
    public function getCodeUsageHistory($partnerId, $limit = 20) {
        $query = "SELECT pc.*, l.learner_name, l.learner_email
                 FROM promotion_codes pc 
                 LEFT JOIN learners l ON pc.used_by = l.id 
                 WHERE pc.partner_id = '$partnerId' AND pc.status = 'used'
                 ORDER BY pc.used_at DESC 
                 LIMIT $limit";
        
        return $this->db->read($query);
    }
    
    // Update partner code prefix
    public function updatePartnerCodePrefix($partnerId, $newPrefix) {
        // Validate prefix (3-6 characters, alphanumeric)
        if (!preg_match('/^[A-Z0-9]{3,6}$/', $newPrefix)) {
            return ['success' => false, 'message' => 'Prefix must be 3-6 uppercase letters/numbers'];
        }
        
        $query = "UPDATE partners SET code_prefix = '$newPrefix' WHERE id = '$partnerId'";
        
        if ($this->db->save($query)) {
            return ['success' => true, 'message' => 'Code prefix updated successfully'];
        }
        
        return ['success' => false, 'message' => 'Failed to update code prefix'];
    }
}
?>
