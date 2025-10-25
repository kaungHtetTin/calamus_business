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
            'paid_earnings' => 0
        ];
        
        // Total earnings (all statuses - paid + pending)
        $totalQuery = "SELECT SUM(amount_received) as total FROM partner_earnings 
                      WHERE partner_id = '$partnerId'";
        $totalResult = $this->db->read($totalQuery);
        $stats['total_earnings'] = $totalResult ? (float)$totalResult[0]['total'] : 0.00;
        
        // Total transactions (all statuses)
        $countQuery = "SELECT COUNT(*) as total FROM partner_earnings 
                      WHERE partner_id = '$partnerId'";
        $countResult = $this->db->read($countQuery);
        $stats['total_transactions'] = $countResult ? (int)$countResult[0]['total'] : 0;
        
        // This month earnings (all statuses)
        $monthQuery = "SELECT SUM(amount_received) as total FROM partner_earnings 
                      WHERE partner_id = '$partnerId' 
                      AND MONTH(created_at) = MONTH(CURRENT_DATE()) 
                      AND YEAR(created_at) = YEAR(CURRENT_DATE())";
        $monthResult = $this->db->read($monthQuery);
        $stats['this_month_earnings'] = $monthResult ? (float)$monthResult[0]['total'] : 0.00;
        
        // Pending earnings
        $pendingQuery = "SELECT SUM(amount_received) as total FROM partner_earnings 
                        WHERE partner_id = '$partnerId' AND status = 'pending'";
        $pendingResult = $this->db->read($pendingQuery);
        $stats['pending_earnings'] = $pendingResult ? (float)$pendingResult[0]['total'] : 0.00;
        
        // Paid earnings (for reference)
        $paidQuery = "SELECT SUM(amount_received) as total FROM partner_earnings 
                      WHERE partner_id = '$partnerId' AND status = 'paid'";
        $paidResult = $this->db->read($paidQuery);
        $stats['paid_earnings'] = $paidResult ? (float)$paidResult[0]['total'] : 0.00;
        
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
        
        if ($startDate && $endDate) {
            $whereClause .= " AND DATE(created_at) BETWEEN '$startDate' AND '$endDate'";
        } elseif ($startDate) {
            $whereClause .= " AND DATE(created_at) >= '$startDate'";
        } elseif ($endDate) {
            $whereClause .= " AND DATE(created_at) <= '$endDate'";
        }

        // Total Earnings (all matching criteria)
        $query = "SELECT SUM(amount_received) as total FROM partner_earnings $whereClause";
        $result = $this->db->read($query);
        $stats['total_earnings'] = $result && $result[0]['total'] ? (float)$result[0]['total'] : 0.00;

        // Total Transactions (all matching criteria)
        $query = "SELECT COUNT(*) as total FROM partner_earnings $whereClause";
        $result = $this->db->read($query);
        $stats['total_transactions'] = $result && $result[0]['total'] ? (int)$result[0]['total'] : 0;

        // This Month Earnings (same as total_earnings when filtered)
        $stats['this_month_earnings'] = $stats['total_earnings'];

        // Pending Earnings (only if status filter allows)
        if (!$status || $status === 'pending') {
            $pendingWhereClause = $whereClause . " AND status = 'pending'";
            $query = "SELECT SUM(amount_received) as total FROM partner_earnings $pendingWhereClause";
            $result = $this->db->read($query);
            $stats['pending_earnings'] = $result && $result[0]['total'] ? (float)$result[0]['total'] : 0.00;
        }

        // Paid Earnings (only if status filter allows)
        if (!$status || $status === 'paid') {
            $paidWhereClause = $whereClause . " AND status = 'paid'";
            $query = "SELECT SUM(amount_received) as total FROM partner_earnings $paidWhereClause";
            $result = $this->db->read($query);
            $stats['paid_earnings'] = $result && $result[0]['total'] ? (float)$result[0]['total'] : 0.00;
        }

        return $stats;
    }
    
    // Get partner's earnings with pagination and filtering
    public function getPartnerEarnings($partnerId, $status = null, $limit = 20, $offset = 0, $startDate = null, $endDate = null) {
        $whereClause = "WHERE partner_id = '$partnerId'";
        
        if ($status) {
            $whereClause .= " AND status = '$status'";
        }
        
        if ($startDate && $endDate) {
            $whereClause .= " AND DATE(created_at) BETWEEN '$startDate' AND '$endDate'";
        } elseif ($startDate) {
            $whereClause .= " AND DATE(created_at) >= '$startDate'";
        } elseif ($endDate) {
            $whereClause .= " AND DATE(created_at) <= '$endDate'";
        }
        
        $query = "SELECT * FROM partner_earnings $whereClause ORDER BY created_at DESC LIMIT $limit OFFSET $offset";
        $result = $this->db->read($query);
        
        return $result ? $result : [];
    }
    
    // Get total count of partner's earnings with filtering
    public function getPartnerEarningsCount($partnerId, $status = null, $startDate = null, $endDate = null) {
        $whereClause = "WHERE partner_id = '$partnerId'";
        
        if ($status) {
            $whereClause .= " AND status = '$status'";
        }
        
        if ($startDate && $endDate) {
            $whereClause .= " AND DATE(created_at) BETWEEN '$startDate' AND '$endDate'";
        } elseif ($startDate) {
            $whereClause .= " AND DATE(created_at) >= '$startDate'";
        } elseif ($endDate) {
            $whereClause .= " AND DATE(created_at) <= '$endDate'";
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
