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
            'pending_earnings' => 0,
            'paid_earnings' => 0,
            'today_earnings' => 0,
            'yesterday_earnings' => 0
        ];
        
        $query = "SELECT
                    COALESCE(SUM(amount_received), 0) AS total_earnings,
                    COUNT(*) AS total_transactions,
                    COALESCE(SUM(CASE WHEN created_at >= DATE_FORMAT(CURDATE(), '%Y-%m-01') THEN amount_received ELSE 0 END), 0) AS this_month_earnings,
                    COALESCE(SUM(CASE WHEN status = 'pending' THEN amount_received ELSE 0 END), 0) AS pending_earnings,
                    COALESCE(SUM(CASE WHEN status = 'paid' THEN amount_received ELSE 0 END), 0) AS paid_earnings,
                    COALESCE(SUM(CASE WHEN created_at >= CURDATE() THEN amount_received ELSE 0 END), 0) AS today_earnings,
                    COALESCE(SUM(CASE WHEN created_at >= CURDATE() - INTERVAL 1 DAY AND created_at < CURDATE() THEN amount_received ELSE 0 END), 0) AS yesterday_earnings
                  FROM partner_earnings
                  WHERE partner_id = '$partnerId'";
        $result = $this->db->read($query);

        if ($result) {
            foreach ($stats as $key => $default) {
                $stats[$key] = $key === 'total_transactions'
                    ? (int) $result[0][$key]
                    : (float) $result[0][$key];
            }
        }
        
        return $stats;
    }
    
    // Get partner's earning statistics with filtering
    public function getPartnerEarningStatsFiltered($partnerId, $status = null, $startDate = null, $endDate = null) {
        $stats = [
            'total_earnings' => 0.00,
            'total_transactions' => 0,
            'this_month_earnings' => 0.00,
            'pending_earnings' => 0.00,
            'paid_earnings' => 0.00
        ];

        $whereClause = "WHERE partner_id = '$partnerId'";
        
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
                    COALESCE(SUM(amount_received), 0) AS total_earnings,
                    COUNT(*) AS total_transactions,
                    COALESCE(SUM(CASE WHEN status = 'pending' THEN amount_received ELSE 0 END), 0) AS pending_earnings,
                    COALESCE(SUM(CASE WHEN status = 'paid' THEN amount_received ELSE 0 END), 0) AS paid_earnings
                  FROM partner_earnings $whereClause";
        $result = $this->db->read($query);

        if ($result) {
            $stats['total_earnings'] = (float) $result[0]['total_earnings'];
            $stats['total_transactions'] = (int) $result[0]['total_transactions'];
            $stats['this_month_earnings'] = $stats['total_earnings'];
            $stats['pending_earnings'] = (float) $result[0]['pending_earnings'];
            $stats['paid_earnings'] = (float) $result[0]['paid_earnings'];
        }

        return $stats;
    }
    
    // Get partner's earnings with pagination and filtering
    public function getPartnerEarnings($partnerId, $status = null, $limit = 20, $offset = 0, $startDate = null, $endDate = null) {
        $whereClause = "WHERE pe.partner_id = '$partnerId'";
        
        if ($status) {
            $whereClause .= " AND pe.status = '$status'";
        }
        
        if ($startDate) {
            $whereClause .= " AND pe.created_at >= '$startDate 00:00:00'";
        }
        if ($endDate) {
            $whereClause .= " AND pe.created_at < DATE_ADD('$endDate', INTERVAL 1 DAY)";
        }
        
        $query = "SELECT pe.*, l.learner_name as learner_name 
                 FROM partner_earnings pe 
                 LEFT JOIN learners l ON pe.learner_phone = l.learner_phone 
                 $whereClause 
                 ORDER BY pe.created_at DESC 
                 LIMIT $limit OFFSET $offset";
        $result = $this->db->read($query);
        
        return $result ? $result : [];
    }
    
    // Get total count of partner's earnings with filtering
    public function getPartnerEarningsCount($partnerId, $status = null, $startDate = null, $endDate = null) {
        $whereClause = "WHERE partner_id = '$partnerId'";
        
        if ($status) {
            $whereClause .= " AND status = '$status'";
        }
        
        if ($startDate) {
            $whereClause .= " AND created_at >= '$startDate 00:00:00'";
        }
        if ($endDate) {
            $whereClause .= " AND created_at < DATE_ADD('$endDate', INTERVAL 1 DAY)";
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
