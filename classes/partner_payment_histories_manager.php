<?php
require_once __DIR__ . '/autoload.php';

class PartnerPaymentHistoriesManager {
    private $db;
    
    public function __construct() {
        $this->db = new Database();
    }
    
    // Get partner's payment histories
    public function getPartnerPaymentHistories($partnerId, $status = null, $limit = 20, $offset = 0, $startDate = null, $endDate = null) {
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
        
        $query = "SELECT * FROM partner_payment_histories $whereClause ORDER BY created_at DESC LIMIT $limit OFFSET $offset";
        $result = $this->db->read($query);
        
        return $result ? $result : [];
    }
    
    // Get total count of partner's payment histories with filtering
    public function getPartnerPaymentHistoriesCount($partnerId, $status = null, $startDate = null, $endDate = null) {
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
        
        $query = "SELECT COUNT(*) as total FROM partner_payment_histories $whereClause";
        $result = $this->db->read($query);
        
        return $result ? (int)$result[0]['total'] : 0;
    }
    
    // Get partner's payment statistics
    public function getPartnerPaymentStats($partnerId, $status = null, $startDate = null, $endDate = null) {
        $stats = [
            'total_received' => 0.00,
            'total_pending' => 0.00,
            'total_rejected' => 0.00,
            'total_payments' => 0
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

        // Total Received
        $query = "SELECT SUM(amount) as total FROM partner_payment_histories $whereClause AND status = 'received'";
        $result = $this->db->read($query);
        $stats['total_received'] = $result && $result[0]['total'] ? (float)$result[0]['total'] : 0.00;

        // Total Pending
        $query = "SELECT SUM(amount) as total FROM partner_payment_histories $whereClause AND status = 'pending'";
        $result = $this->db->read($query);
        $stats['total_pending'] = $result && $result[0]['total'] ? (float)$result[0]['total'] : 0.00;

        // Total Rejected
        $query = "SELECT SUM(amount) as total FROM partner_payment_histories $whereClause AND status = 'rejected'";
        $result = $this->db->read($query);
        $stats['total_rejected'] = $result && $result[0]['total'] ? (float)$result[0]['total'] : 0.00;

        // Total Payments Count
        $query = "SELECT COUNT(*) as total FROM partner_payment_histories $whereClause";
        $result = $this->db->read($query);
        $stats['total_payments'] = $result && $result[0]['total'] ? (int)$result[0]['total'] : 0;

        return $stats;
    }
    
    // Update payment status
    public function updatePaymentStatus($paymentId, $status, $partnerId) {
        // Verify the payment belongs to the partner
        $payment = $this->db->read("SELECT * FROM partner_payment_histories WHERE id = '$paymentId' AND partner_id = '$partnerId'");
        
        if (!$payment) {
            return ['success' => false, 'message' => 'Payment not found or access denied'];
        }
        
        $query = "UPDATE partner_payment_histories SET status = '$status', updated_at = NOW() WHERE id = '$paymentId' AND partner_id = '$partnerId'";
        
        if ($this->db->save($query)) {
            return ['success' => true, 'message' => 'Payment status updated successfully'];
        }
        
        return ['success' => false, 'message' => 'Failed to update payment status'];
    }
    
    // Get payment history by ID
    public function getPaymentHistoryById($paymentId, $partnerId) {
        $query = "SELECT * FROM partner_payment_histories WHERE id = '$paymentId' AND partner_id = '$partnerId'";
        $result = $this->db->read($query);
        
        return $result ? $result[0] : null;
    }
    
    // Add new payment history (for admin use)
    public function addPaymentHistory($partnerId, $data) {
        $query = "INSERT INTO partner_payment_histories 
                 (partner_id, payment_method, account_number, account_name, amount, status, transaction_screenshot) 
                 VALUES ('$partnerId', 
                         '{$data['payment_method']}', 
                         '{$data['account_number']}', 
                         '{$data['account_name']}', 
                         '{$data['amount']}', 
                         '{$data['status']}', 
                         '{$data['transaction_screenshot']}')";
        
        return $this->db->save($query);
    }
}
?>
