<?php
// All classes are loaded by autoloader

class PaymentMethodsManager {
    private $db;
    
    public function __construct() {
        $this->db = new Database();
    }
    
    // Get all payment methods for a partner
    public function getPartnerPaymentMethods($partnerId) {
        $query = "SELECT * FROM partner_payment_methods WHERE partner_id = '$partnerId' ORDER BY created_at DESC";
        $result = $this->db->read($query);
        return $result ? $result : [];
    }
    
    // Get a specific payment method by ID
    public function getPaymentMethod($paymentMethodId, $partnerId) {
        $query = "SELECT * FROM partner_payment_methods WHERE id = '$paymentMethodId' AND partner_id = '$partnerId'";
        $result = $this->db->read($query);
        return $result ? $result[0] : null;
    }
    
    // Add a new payment method
    public function addPaymentMethod($partnerId, $paymentMethod, $accountNumber, $accountName) {
        // Validate input
        if (empty($paymentMethod) || empty($accountNumber) || empty($accountName)) {
            return ['success' => false, 'message' => 'All fields are required'];
        }
        
        // Check if payment method already exists for this partner
        $existingQuery = "SELECT id FROM partner_payment_methods WHERE partner_id = '$partnerId' AND payment_method = '$paymentMethod' AND account_number = '$accountNumber'";
        $existing = $this->db->read($existingQuery);
        
        if ($existing) {
            return ['success' => false, 'message' => 'This payment method already exists'];
        }
        
        // Insert new payment method
        $query = "INSERT INTO partner_payment_methods (partner_id, payment_method, account_number, account_name) VALUES ('$partnerId', '$paymentMethod', '$accountNumber', '$accountName')";
        
        if ($this->db->save($query)) {
            return ['success' => true, 'message' => 'Payment method added successfully'];
        } else {
            return ['success' => false, 'message' => 'Failed to add payment method'];
        }
    }
    
    // Update an existing payment method
    public function updatePaymentMethod($paymentMethodId, $partnerId, $paymentMethod, $accountNumber, $accountName) {
        // Validate input
        if (empty($paymentMethod) || empty($accountNumber) || empty($accountName)) {
            return ['success' => false, 'message' => 'All fields are required'];
        }
        
        // Check if payment method exists and belongs to partner
        $existingQuery = "SELECT id FROM partner_payment_methods WHERE id = '$paymentMethodId' AND partner_id = '$partnerId'";
        $existing = $this->db->read($existingQuery);
        
        if (!$existing) {
            return ['success' => false, 'message' => 'Payment method not found'];
        }
        
        // Check if another payment method with same details exists
        $duplicateQuery = "SELECT id FROM partner_payment_methods WHERE partner_id = '$partnerId' AND payment_method = '$paymentMethod' AND account_number = '$accountNumber' AND id != '$paymentMethodId'";
        $duplicate = $this->db->read($duplicateQuery);
        
        if ($duplicate) {
            return ['success' => false, 'message' => 'This payment method already exists'];
        }
        
        // Update payment method
        $query = "UPDATE partner_payment_methods SET payment_method = '$paymentMethod', account_number = '$accountNumber', account_name = '$accountName', updated_at = CURRENT_TIMESTAMP WHERE id = '$paymentMethodId' AND partner_id = '$partnerId'";
        
        if ($this->db->save($query)) {
            return ['success' => true, 'message' => 'Payment method updated successfully'];
        } else {
            return ['success' => false, 'message' => 'Failed to update payment method'];
        }
    }
    
    // Delete a payment method
    public function deletePaymentMethod($paymentMethodId, $partnerId) {
        // Check if payment method exists and belongs to partner
        $existingQuery = "SELECT id FROM partner_payment_methods WHERE id = '$paymentMethodId' AND partner_id = '$partnerId'";
        $existing = $this->db->read($existingQuery);
        
        if (!$existing) {
            return ['success' => false, 'message' => 'Payment method not found'];
        }
        
        // Delete payment method
        $query = "DELETE FROM partner_payment_methods WHERE id = '$paymentMethodId' AND partner_id = '$partnerId'";
        
        if ($this->db->save($query)) {
            return ['success' => true, 'message' => 'Payment method deleted successfully'];
        } else {
            return ['success' => false, 'message' => 'Failed to delete payment method'];
        }
    }
    
    // Get payment method statistics for a partner
    public function getPaymentMethodStats($partnerId) {
        $stats = [];
        
        // Total payment methods
        $totalQuery = "SELECT COUNT(*) as total FROM partner_payment_methods WHERE partner_id = '$partnerId'";
        $totalResult = $this->db->read($totalQuery);
        $stats['total'] = $totalResult ? (int)$totalResult[0]['total'] : 0;
        
        // Payment methods by type
        $typeQuery = "SELECT payment_method, COUNT(*) as count FROM partner_payment_methods WHERE partner_id = '$partnerId' GROUP BY payment_method";
        $typeResult = $this->db->read($typeQuery);
        $stats['by_type'] = $typeResult ? $typeResult : [];
        
        return $stats;
    }
}
