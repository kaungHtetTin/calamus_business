<?php
require_once __DIR__ . '/autoload.php';

class PartnerEarningsManager {
    private $db;
    
    public function __construct() {
        $this->db = new Database();
    }
    
    // Get partner's earning history
    public function getPartnerEarningHistory($partnerId, $limit = 50) {
        $query = "SELECT * FROM partner_earnings 
                 WHERE partner_id = '$partnerId' 
                 ORDER BY created_at DESC 
                 LIMIT $limit";
        
        $result = $this->db->read($query);
        return $result ? $result : [];
    }
    
    // Get partner's earning statistics
    public function getPartnerEarningStats($partnerId) {
        $stats = [
            'total_earnings' => 0,
            'total_transactions' => 0,
            'this_month_earnings' => 0,
            'pending_earnings' => 0
        ];
        
        // Total earnings (paid only)
        $totalQuery = "SELECT SUM(amount_received) as total FROM partner_earnings 
                      WHERE partner_id = '$partnerId' AND status = 'paid'";
        $totalResult = $this->db->read($totalQuery);
        $stats['total_earnings'] = $totalResult ? (float)$totalResult[0]['total'] : 0.00;
        
        // Total transactions
        $countQuery = "SELECT COUNT(*) as total FROM partner_earnings 
                      WHERE partner_id = '$partnerId' AND status = 'paid'";
        $countResult = $this->db->read($countQuery);
        $stats['total_transactions'] = $countResult ? (int)$countResult[0]['total'] : 0;
        
        // This month earnings
        $monthQuery = "SELECT SUM(amount_received) as total FROM partner_earnings 
                      WHERE partner_id = '$partnerId' 
                      AND status = 'paid' 
                      AND MONTH(created_at) = MONTH(CURRENT_DATE()) 
                      AND YEAR(created_at) = YEAR(CURRENT_DATE())";
        $monthResult = $this->db->read($monthQuery);
        $stats['this_month_earnings'] = $monthResult ? (float)$monthResult[0]['total'] : 0.00;
        
        // Pending earnings
        $pendingQuery = "SELECT SUM(amount_received) as total FROM partner_earnings 
                        WHERE partner_id = '$partnerId' AND status = 'pending'";
        $pendingResult = $this->db->read($pendingQuery);
        $stats['pending_earnings'] = $pendingResult ? (float)$pendingResult[0]['total'] : 0.00;
        
        return $stats;
    }
    
    // Get partner's earnings with pagination
    public function getPartnerEarnings($partnerId, $status = null, $limit = 20, $offset = 0) {
        $whereClause = "WHERE partner_id = '$partnerId'";
        if ($status) {
            $whereClause .= " AND status = '$status'";
        }
        
        $query = "SELECT * FROM partner_earnings $whereClause ORDER BY created_at DESC LIMIT $limit OFFSET $offset";
        $result = $this->db->read($query);
        
        return $result ? $result : [];
    }
    
    // Get total count of partner's earnings
    public function getPartnerEarningsCount($partnerId, $status = null) {
        $whereClause = "WHERE partner_id = '$partnerId'";
        if ($status) {
            $whereClause .= " AND status = '$status'";
        }
        
        $query = "SELECT COUNT(*) as total FROM partner_earnings $whereClause";
        $result = $this->db->read($query);
        
        return $result ? (int)$result[0]['total'] : 0;
    }
    
    // Add new earning record
    public function addEarning($partnerId, $data) {
        $query = "INSERT INTO partner_earnings 
                 (partner_id, target_course_id, target_package_id, learner_phone, 
                  price, commission_rate, amount_received, status, created_at) 
                 VALUES ('$partnerId', 
                         '{$data['target_course_id']}', 
                         '{$data['target_package_id']}', 
                         '{$data['learner_phone']}', 
                         '{$data['price']}', 
                         '{$data['commission_rate']}', 
                         '{$data['amount_received']}', 
                         '{$data['status']}', 
                         NOW())";
        
        return $this->db->save($query);
    }
    
    // Update earning status
    public function updateEarningStatus($earningId, $status) {
        $query = "UPDATE partner_earnings 
                 SET status = '$status', updated_at = NOW() 
                 WHERE id = '$earningId'";
        
        return $this->db->save($query);
    }
    
    // Delete earning record (only if pending)
    public function deleteEarning($earningId) {
        // Check if earning exists and is pending
        $earning = $this->db->read("SELECT * FROM partner_earnings WHERE id = '$earningId'");
        if (!$earning) {
            return ['success' => false, 'message' => 'Earning not found'];
        }
        
        $earning = $earning[0];
        if ($earning['status'] !== 'pending') {
            return ['success' => false, 'message' => 'Only pending earnings can be deleted'];
        }
        
        $query = "DELETE FROM partner_earnings WHERE id = '$earningId'";
        if ($this->db->save($query)) {
            return ['success' => true, 'message' => 'Earning deleted successfully'];
        }
        
        return ['success' => false, 'message' => 'Failed to delete earning'];
    }
}
?>
