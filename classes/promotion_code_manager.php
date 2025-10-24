<?php
// Database class is loaded by autoloader

class PromotionCodeManager {
    private $db;
    
    public function __construct() {
        $this->db = new Database();
    }
    
    // Generate a new promotion code
    public function generatePromotionCode($partnerId, $codeType, $categoryId, $targetCourseId = null, $targetPackageId = null, $price, $commissionRate = null, $expiresAt = null) {
        // Get partner details
        $partner = $this->db->read("SELECT * FROM partners WHERE id = '$partnerId'");
        if (!$partner) {
            return ['success' => false, 'message' => 'Partner not found'];
        }
        
        $partner = $partner[0];
        $codePrefix = $partner['code_prefix'] ?? 'PART';
        
        // Use partner's commission rate if not provided
        if ($commissionRate === null) {
            $commissionRate = $partner['commission_rate'];
        }
        
        // Generate unique code
        $code = $this->generateUniqueCode($codePrefix, $codeType, $categoryId, $targetCourseId, $targetPackageId);
        
        // Set expiration date (3 days from now if not provided)
        if (!$expiresAt) {
            $expiresAt = date('Y-m-d H:i:s', strtotime('+3 days'));
        }
        
        // Insert promotion code with amount_received = 0
        $query = "INSERT INTO promotion_codes 
                 (partner_id, code, target_course_id, target_package_id, price, commission_rate, amount_received, expired_at) 
                 VALUES ('$partnerId', '$code', '$targetCourseId', '$targetPackageId', '$price', '$commissionRate', '0.00', '$expiresAt')";
        
        if ($this->db->save($query)) {
            // Update partner code count
            $this->db->save("UPDATE partners SET total_codes_generated = total_codes_generated + 1 WHERE id = '$partnerId'");
            
            return [
                'success' => true,
                'code' => $code,
                'expired_at' => $expiresAt,
                'message' => 'Promotion code generated successfully'
            ];
        }
        
        return ['success' => false, 'message' => 'Failed to generate promotion code'];
    }
    
    // Generate unique code
    private function generateUniqueCode($prefix, $codeType, $categoryId, $targetCourseId, $targetPackageId) {
        do {
            // Create code format: PREFIX-TYPE-CATEGORY-TARGET-RANDOM
            $typeCode = $codeType === 'course_purchase' ? 'C' : 'P';
            $categoryCode = strtoupper(substr($categoryId, 0, 2)); // First 2 chars of category ID
            $targetCode = $targetCourseId ? $targetCourseId : ($targetPackageId ? $targetPackageId : '000');
            $randomCode = strtoupper(substr(md5(uniqid()), 0, 4));
            
            // Create the raw code string
            $rawCode = $prefix . '-' . $typeCode . '-' . $categoryCode . '-' . $targetCode . '-' . $randomCode;
            
            // Encode the code using base64
            $code = base64_encode($rawCode);
            
            $exists = $this->db->read("SELECT id FROM promotion_codes WHERE code = '$code'");
        } while ($exists);
        
        return $code;
    }
    
    // Decode base64 promotion code
    public function decodePromotionCode($encodedCode) {
        try {
            $decodedCode = base64_decode($encodedCode);
            if ($decodedCode === false) {
                return ['success' => false, 'message' => 'Invalid code format'];
            }
            
            // Parse the decoded code
            $parts = explode('-', $decodedCode);
            if (count($parts) !== 5) {
                return ['success' => false, 'message' => 'Invalid code structure'];
            }
            
            return [
                'success' => true,
                'prefix' => $parts[0],
                'type' => $parts[1],
                'category' => $parts[2],
                'target' => $parts[3],
                'random' => $parts[4],
                'raw_code' => $decodedCode
            ];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Code decoding failed'];
        }
    }
    
    // Get partner's promotion codes
    public function getPartnerPromotionCodes($partnerId, $status = null, $limit = 20) {
        $whereClause = "WHERE partner_id = '$partnerId'";
        if ($status) {
            $whereClause .= " AND status = '$status'";
        }
        
        $query = "SELECT * FROM promotion_codes $whereClause ORDER BY created_at DESC LIMIT $limit";
        $result = $this->db->read($query);
        
        // Return empty array if no results
        return $result ? $result : [];
    }
    
    // Get partner's code management records (now just uses promotion_codes table)
    public function getPartnerCodeManagement($partnerId, $status = null, $limit = 20) {
        $whereClause = "WHERE partner_id = '$partnerId'";
        if ($status) {
            $whereClause .= " AND status = '$status'";
        }
        
        $query = "SELECT pc.*, l.learner_name as user_name, pc.learner_phone as user_phone
                 FROM promotion_codes pc 
                 LEFT JOIN learners l ON pc.learner_phone = l.learner_phone 
                 $whereClause ORDER BY pc.created_at DESC LIMIT $limit";
        $result = $this->db->read($query);
        
        // Return empty array if no results
        return $result ? $result : [];
    }
    
    // Get partner code statistics
    public function getPartnerCodeStats($partnerId) {
        $stats = [];
        
        // Initialize default values
        $stats['total_generated'] = 0;
        $stats['pending'] = 0;
        $stats['approved'] = 0;
        $stats['rejected'] = 0;
        $stats['expired'] = 0;
        $stats['usage_rate'] = 0;
        $stats['commission_earned'] = 0;
        
        // Total codes generated
        $totalGenerated = $this->db->read("SELECT COUNT(*) as total FROM promotion_codes WHERE partner_id = '$partnerId'");
        if ($totalGenerated && isset($totalGenerated[0]['total'])) {
            $stats['total_generated'] = (int)$totalGenerated[0]['total'];
        }
        
        // If no codes exist, return default stats
        if ($stats['total_generated'] === 0) {
            return $stats;
        }
        
        // Codes by status
        $statusCounts = $this->db->read("SELECT status, COUNT(*) as count FROM promotion_codes WHERE partner_id = '$partnerId' GROUP BY status");
        
        if ($statusCounts) {
            foreach ($statusCounts as $statusCount) {
                if (isset($statusCount['status']) && isset($statusCount['count'])) {
                    $stats[$statusCount['status']] = (int)$statusCount['count'];
                }
            }
        }
        
        // Usage rate
        $usedCodes = $stats['approved'];
        $stats['usage_rate'] = $stats['total_generated'] > 0 ? 
            round(($usedCodes / $stats['total_generated']) * 100, 2) : 0;
        
        return $stats;
    }
    
    // Approve promotion code
    public function approvePromotionCode($codeId, $learnerPhone) {
        // Get code details
        $code = $this->db->read("SELECT * FROM promotion_codes WHERE id = '$codeId'");
        if (!$code) {
            return ['success' => false, 'message' => 'Code not found'];
        }
        
        $code = $code[0];
        
        // Check if code is still valid
        if ($code['status'] !== 'pending') {
            return ['success' => false, 'message' => 'Code is not in pending status'];
        }
        
        if (strtotime($code['expired_at']) < time()) {
            return ['success' => false, 'message' => 'Code has expired'];
        }
        
        // Get learner name from learners table
        $learner = $this->db->read("SELECT learner_name FROM learners WHERE learner_phone = '$learnerPhone'");
        $learnerName = $learner ? $learner[0]['learner_name'] : 'Unknown';
        
        // Calculate commission amount
        $commissionAmount = ($code['price'] * $code['commission_rate']) / 100;
        
        // Update promotion_codes table
        $updateQuery = "UPDATE promotion_codes SET 
                        status = 'approved', 
                        learner_phone = '$learnerPhone',
                        amount_received = '$commissionAmount',
                        updated_at = NOW() 
                        WHERE id = '$codeId'";
        
        if ($this->db->save($updateQuery)) {
            // Update partner code count
            $this->db->save("UPDATE partners SET total_codes_used = total_codes_used + 1 WHERE id = '{$code['partner_id']}'");
            
            return [
                'success' => true, 
                'message' => 'Code approved successfully',
                'commission_amount' => $commissionAmount
            ];
        }
        
        return ['success' => false, 'message' => 'Failed to approve code'];
    }
    
    // Reject promotion code
    public function rejectPromotionCode($codeId) {
        // Get code details
        $code = $this->db->read("SELECT * FROM promotion_codes WHERE id = '$codeId'");
        if (!$code) {
            return ['success' => false, 'message' => 'Code not found'];
        }
        
        $code = $code[0];
        
        // Update promotion_codes table
        $updateQuery = "UPDATE promotion_codes SET 
                        status = 'rejected', 
                        updated_at = NOW() 
                        WHERE id = '$codeId'";
        
        if ($this->db->save($updateQuery)) {
            return ['success' => true, 'message' => 'Code rejected successfully'];
        }
        
        return ['success' => false, 'message' => 'Failed to reject code'];
    }
    
    // Delete promotion code (only if pending)
    public function deletePromotionCode($codeId) {
        // Get code details
        $code = $this->db->read("SELECT * FROM promotion_codes WHERE id = '$codeId'");
        if (!$code) {
            return ['success' => false, 'message' => 'Code not found'];
        }
        
        $code = $code[0];
        
        // Check if code is pending
        if ($code['status'] !== 'pending') {
            return ['success' => false, 'message' => 'Can only delete codes in pending status'];
        }
        
        // Delete from promotion_codes table
        $deleteQuery = "DELETE FROM promotion_codes WHERE id = '$codeId'";
        
        if ($this->db->save($deleteQuery)) {
            return ['success' => true, 'message' => 'Code deleted successfully'];
        }
        
        return ['success' => false, 'message' => 'Failed to delete code'];
    }
    
    // Check if code exists and is valid
    public function validatePromotionCode($code) {
        $query = "SELECT * FROM promotion_codes WHERE code = '$code' AND status = 'pending' AND expired_at > NOW()";
        $result = $this->db->read($query);
        
        if ($result) {
            return [
                'valid' => true,
                'code_data' => $result[0]
            ];
        }
        
        return ['valid' => false, 'message' => 'Invalid or expired code'];
    }
    
    // Update partner code prefix
    public function updatePartnerCodePrefix($partnerId, $newPrefix) {
        $newPrefix = $this->db->connect()->real_escape_string($newPrefix);
        
        $query = "UPDATE partners SET code_prefix = '$newPrefix' WHERE id = '$partnerId'";
        
        if ($this->db->save($query)) {
            return ['success' => true, 'message' => 'Code prefix updated successfully'];
        }
        
        return ['success' => false, 'message' => 'Failed to update code prefix'];
    }
    
    // Get code usage history
    public function getCodeUsageHistory($partnerId, $limit = 20) {
        $query = "SELECT pc.*, l.learner_name, l.learner_email
                 FROM promotion_codes pc 
                 LEFT JOIN learners l ON pc.learner_phone = l.learner_phone 
                 WHERE pc.partner_id = '$partnerId' AND pc.status = 'approved'
                 ORDER BY pc.updated_at DESC 
                 LIMIT $limit";
        
        $result = $this->db->read($query);
        
        // Return empty array if no results
        return $result ? $result : [];
    }
    
    // Clean up expired codes
    public function cleanupExpiredCodes() {
        $expiredQuery = "UPDATE promotion_codes SET status = 'expired' WHERE expired_at < NOW() AND status = 'pending'";
        
        $this->db->save($expiredQuery);
        
        return ['success' => true, 'message' => 'Expired codes cleaned up'];
    }
}
?>