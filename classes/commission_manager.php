<?php
// All classes are loaded by autoloader

class CommissionManager {
    private $db;
    private $tracker;
    
    public function __construct() {
        $this->db = new Database();
        $this->tracker = new AffiliateTracker();
    }
    
    // Process package purchase with promotion code
    public function processPackagePurchaseWithCode($learnerPhone, $packageId, $amount, $codeData) {
        try {
            // First create the package purchase record
            $purchaseData = $this->createPackagePurchase($learnerPhone, $packageId, $amount, $codeData['id']);
            
            if (!$purchaseData['success']) {
                return $purchaseData;
            }
            
            // Calculate commission based on code data
            $commissionAmount = ($amount * $codeData['commission_rate']) / 100;
            
            // Create conversion record with promotion code
            $conversionQuery = "INSERT INTO conversions 
                               (partner_id, promotion_code_id, learner_phone, conversion_type, 
                                conversion_value, commission_rate, commission_amount, 
                                package_id, status, conversion_date) 
                               VALUES ('{$codeData['partner_id']}', '{$codeData['id']}', '$learnerPhone', 
                                       'package_purchase', '$amount', '{$codeData['commission_rate']}', 
                                       '$commissionAmount', '$packageId', 'approved', NOW())";
            
            if ($this->db->save($conversionQuery)) {
                $conversionId = $this->db->connect()->insert_id;
                
                // Update promotion code with conversion ID
                $this->db->save("UPDATE promotion_codes SET conversion_id = '$conversionId' WHERE id = '{$codeData['id']}'");
                
                // Update partner earnings
                $this->db->save("UPDATE partners SET 
                                total_earnings = total_earnings + $commissionAmount,
                                pending_amount = pending_amount + $commissionAmount
                                WHERE id = '{$codeData['partner_id']}'");
                
                // Send notifications
                $this->notifyPartnerOfPackageConversion($codeData, $commissionAmount, $purchaseData['package']);
                
                return [
                    'success' => true,
                    'purchase_id' => $purchaseData['purchase_id'],
                    'conversion_id' => $conversionId,
                    'commission_amount' => $commissionAmount,
                    'message' => 'Package purchase completed with promotion code'
                ];
            }
            
            return ['success' => false, 'message' => 'Failed to create conversion record'];
            
        } catch (Exception $e) {
            error_log("Package Purchase with Code Error: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'An error occurred while processing your package purchase'
            ];
        }
    }
    
    // Create package purchase record
    private function createPackagePurchase($learnerPhone, $packageId, $amount, $promotionCodeId = null) {
        // Get package details
        $package = $this->db->read("SELECT * FROM package_plans WHERE id = '$packageId'");
        
        if (!$package) {
            return ['success' => false, 'message' => 'Package not found'];
        }
        
        $package = $package[0];
        
        // Check if user already has active package
        $existingPurchase = $this->db->read("SELECT id FROM package_purchases 
                                           WHERE learner_phone = '$learnerPhone' AND package_id = '$packageId' 
                                           AND status = 'active' AND expiry_date > NOW()");
        
        if ($existingPurchase) {
            return ['success' => false, 'message' => 'User already has an active package of this type'];
        }
        
        // Calculate expiry date
        $expiryDate = date('Y-m-d H:i:s', strtotime("+{$package['duration_days']} days"));
        
        // Insert package purchase
        $query = "INSERT INTO package_purchases 
                 (learner_phone, package_id, purchase_price, expiry_date, promotion_code_id) 
                 VALUES ('$learnerPhone', '$packageId', '$amount', '$expiryDate', '$promotionCodeId')";
        
        if ($this->db->save($query)) {
            $purchaseId = $this->db->connect()->insert_id;
            
            // Grant VIP access to all courses in package
            $this->grantPackageAccess($purchaseId, $packageId, $learnerPhone);
            
            return [
                'success' => true,
                'purchase_id' => $purchaseId,
                'package' => $package,
                'expiry_date' => $expiryDate
            ];
        }
        
        return ['success' => false, 'message' => 'Failed to create package purchase'];
    }
    
    // Grant VIP access to all courses in package
    private function grantPackageAccess($purchaseId, $packageId, $learnerPhone) {
        $courses = $this->db->read("SELECT c.* FROM package_plan_courses ppc 
                                  JOIN courses c ON ppc.course_id = c.course_id 
                                  WHERE ppc.package_id = '$packageId'");
        
        foreach ($courses as $course) {
            // Check if user already has VIP access to this course
            $existingVip = $this->db->read("SELECT id FROM vipusers WHERE learner_phone = '$learnerPhone' AND course_id = '{$course['course_id']}'");
            
            if (!$existingVip) {
                // Create VIP access record
                $vipQuery = "INSERT INTO vipusers (learner_phone, course, course_id, major, package_purchase_id, date) 
                           VALUES ('$learnerPhone', '{$course['title']}', '{$course['course_id']}', '{$course['major']}', '$purchaseId', NOW())";
                
                $this->db->save($vipQuery);
                
                // Update course enrollment count
                $this->db->save("UPDATE courses SET enroll = enroll + 1 WHERE course_id = '{$course['course_id']}'");
            }
        }
    }
    
    // Notify partner of package conversion
    private function notifyPartnerOfPackageConversion($codeData, $commissionAmount, $package) {
        $partner = $this->getPartnerDetails($codeData['partner_id']);
        
        if ($partner) {
            $subject = "Package Purchase - Commission Earned!";
            $message = "
            <h2>Package Purchase Completed!</h2>
            <p>Dear {$partner['contact_name']},</p>
            <p>Your promotion code has been used for a package purchase and you have earned a commission!</p>
            <p><strong>Purchase Details:</strong></p>
            <ul>
                <li>Code: {$codeData['code']}</li>
                <li>Package: {$package['name']}</li>
                <li>Purchase Amount: $" . number_format($package['price'], 2) . "</li>
                <li>Commission Amount: $" . number_format($commissionAmount, 2) . "</li>
                <li>Commission Rate: {$codeData['commission_rate']}%</li>
                <li>Date: " . date('Y-m-d H:i:s') . "</li>
            </ul>
            <p>Login to your dashboard to view detailed analytics: <a href='http://localhost/business/index.php'>Partner Dashboard</a></p>
            <p>Thank you for your partnership!</p>
            ";
            
            mail($partner['email'], $subject, $message, "Content-Type: text/html");
        }
    }
    
    // Process VIP subscription with promotion code
    public function processVipSubscriptionWithCode($learnerPhone, $courseId, $amount, $codeData) {
        try {
            // First create the VIP subscription record
            $subscriptionData = $this->createVipSubscription($learnerPhone, $courseId, $amount, 'promotion_code');
            
            if (!$subscriptionData['success']) {
                return $subscriptionData;
            }
            
            // Calculate commission based on code data
            $commissionAmount = ($amount * $codeData['commission_rate']) / 100;
            
            // Create conversion record with promotion code
            $conversionQuery = "INSERT INTO conversions 
                               (partner_id, promotion_code_id, learner_phone, conversion_type, 
                                conversion_value, commission_rate, commission_amount, 
                                status, conversion_date) 
                               VALUES ('{$codeData['partner_id']}', '{$codeData['id']}', '$learnerPhone', 
                                       'vip_subscription', '$amount', '{$codeData['commission_rate']}', 
                                       '$commissionAmount', 'approved', NOW())";
            
            if ($this->db->save($conversionQuery)) {
                $conversionId = $this->db->connect()->insert_id;
                
                // Update promotion code with conversion ID
                $this->db->save("UPDATE promotion_codes SET conversion_id = '$conversionId' WHERE id = '{$codeData['id']}'");
                
                // Update partner earnings
                $this->db->save("UPDATE partners SET 
                                total_earnings = total_earnings + $commissionAmount,
                                pending_amount = pending_amount + $commissionAmount
                                WHERE id = '{$codeData['partner_id']}'");
                
                // Send notifications
                $this->notifyPartnerOfCodeConversion($codeData, $commissionAmount);
                
                return [
                    'success' => true,
                    'subscription_id' => $subscriptionData['subscription_id'],
                    'conversion_id' => $conversionId,
                    'commission_amount' => $commissionAmount,
                    'message' => 'VIP subscription activated with promotion code'
                ];
            }
            
            return ['success' => false, 'message' => 'Failed to create conversion record'];
            
        } catch (Exception $e) {
            error_log("VIP Subscription with Code Error: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'An error occurred while processing your subscription'
            ];
        }
    }
    
    // Notify partner of code conversion
    private function notifyPartnerOfCodeConversion($codeData, $commissionAmount) {
        $partner = $this->getPartnerDetails($codeData['partner_id']);
        
        if ($partner) {
            $subject = "Promotion Code Used - Commission Earned!";
            $message = "
            <h2>Promotion Code Used Successfully!</h2>
            <p>Dear {$partner['contact_name']},</p>
            <p>Your promotion code has been used and you have earned a commission!</p>
            <p><strong>Code Details:</strong></p>
            <ul>
                <li>Code: {$codeData['code']}</li>
                <li>Commission Amount: $" . number_format($commissionAmount, 2) . "</li>
                <li>Commission Rate: {$codeData['commission_rate']}%</li>
                <li>Date: " . date('Y-m-d H:i:s') . "</li>
            </ul>
            <p>Login to your dashboard to view detailed analytics: <a href='http://localhost/business/index.php'>Partner Dashboard</a></p>
            <p>Thank you for your partnership!</p>
            ";
            
            mail($partner['email'], $subject, $message, "Content-Type: text/html");
        }
    }
    
    // Process VIP subscription and track affiliate conversion (original method)
    public function processVipSubscription($learnerPhone, $courseId, $amount, $paymentMethod = 'stripe') {
        // First, create the VIP subscription record
        $subscriptionData = $this->createVipSubscription($learnerPhone, $courseId, $amount, $paymentMethod);
        
        if (!$subscriptionData['success']) {
            return $subscriptionData;
        }
        
        // Track affiliate conversion if user came from affiliate link
        $conversionResult = $this->tracker->trackConversion($learnerPhone, 'vip_subscription', $amount);
        
        if ($conversionResult['success']) {
            // Update subscription record with affiliate info
            $this->updateSubscriptionWithAffiliate($subscriptionData['subscription_id'], $conversionResult);
        }
        
        return [
            'success' => true,
            'subscription_id' => $subscriptionData['subscription_id'],
            'affiliate_commission' => $conversionResult['commission_amount'] ?? 0
        ];
    }
    
    // Create VIP subscription record
    private function createVipSubscription($learnerPhone, $courseId, $amount, $paymentMethod) {
        // Get course details
        $course = $this->db->read("SELECT * FROM courses WHERE course_id = '$courseId'");
        
        if (!$course) {
            return ['success' => false, 'message' => 'Course not found'];
        }
        
        $course = $course[0];
        
        // Check if user already has VIP access to this course
        $existingVip = $this->db->read("SELECT id FROM vipusers WHERE learner_phone = '$learnerPhone' AND course_id = '$courseId'");
        
        if ($existingVip) {
            return ['success' => false, 'message' => 'User already has VIP access to this course'];
        }
        
        // Create VIP subscription record
        $query = "INSERT INTO vipusers (learner_phone, course, course_id, major, date) 
                 VALUES ('$learnerPhone', '{$course['title']}', '$courseId', '{$course['major']}', NOW())";
        
        if ($this->db->save($query)) {
            $subscriptionId = $this->db->connect()->insert_id;
            
            // Update course enrollment count
            $this->db->save("UPDATE courses SET enroll = enroll + 1 WHERE course_id = '$courseId'");
            
            return [
                'success' => true,
                'subscription_id' => $subscriptionId,
                'course' => $course
            ];
        }
        
        return ['success' => false, 'message' => 'Failed to create VIP subscription'];
    }
    
    // Update subscription with affiliate information
    private function updateSubscriptionWithAffiliate($subscriptionId, $conversionResult) {
        // You can add affiliate tracking fields to vipusers table if needed
        // For now, we'll just log the affiliate conversion
        error_log("VIP Subscription $subscriptionId generated affiliate commission: $" . $conversionResult['commission_amount']);
    }
    
    // Calculate commission for a conversion
    public function calculateCommission($partnerId, $conversionValue, $conversionType = 'vip_subscription') {
        // Get partner commission rate
        $partner = $this->db->read("SELECT commission_rate FROM partners WHERE id = '$partnerId'");
        
        if (!$partner) {
            return ['success' => false, 'message' => 'Partner not found'];
        }
        
        $commissionRate = $partner[0]['commission_rate'];
        
        // Calculate commission based on conversion type
        $commissionAmount = 0;
        
        switch ($conversionType) {
            case 'vip_subscription':
                $commissionAmount = ($conversionValue * $commissionRate) / 100;
                break;
            case 'course_purchase':
                $commissionAmount = ($conversionValue * $commissionRate) / 100;
                break;
            case 'package_purchase':
                $commissionAmount = ($conversionValue * $commissionRate) / 100;
                break;
            case 'app_download':
                $commissionAmount = 5.00; // Fixed amount for app downloads
                break;
            default:
                $commissionAmount = ($conversionValue * $commissionRate) / 100;
        }
        
        return [
            'success' => true,
            'commission_rate' => $commissionRate,
            'commission_amount' => round($commissionAmount, 2)
        ];
    }
    
    // Process commission payments
    public function processCommissionPayments($partnerId, $paymentPeriod = 'monthly') {
        // Get pending commissions for the partner
        $pendingCommissions = $this->db->read("SELECT SUM(commission_amount) as total 
                                              FROM conversions 
                                              WHERE partner_id = '$partnerId' 
                                              AND status = 'approved' 
                                              AND payment_date IS NULL");
        
        $totalAmount = $pendingCommissions[0]['total'] ?? 0;
        
        if ($totalAmount < 50) { // Minimum payout threshold
            return ['success' => false, 'message' => 'Minimum payout amount not reached ($50)'];
        }
        
        // Get partner payment details
        $partner = $this->db->read("SELECT * FROM partners WHERE id = '$partnerId'");
        
        if (!$partner) {
            return ['success' => false, 'message' => 'Partner not found'];
        }
        
        $partner = $partner[0];
        
        // Create payment record
        $paymentPeriodStart = date('Y-m-01', strtotime('-1 month'));
        $paymentPeriodEnd = date('Y-m-t', strtotime('-1 month'));
        
        $query = "INSERT INTO commission_payments 
                 (partner_id, payment_period_start, payment_period_end, total_commission, payment_method, payment_details) 
                 VALUES ('$partnerId', '$paymentPeriodStart', '$paymentPeriodEnd', '$totalAmount', 
                         '{$partner['payment_method']}', '{$partner['payment_details']}')";
        
        if ($this->db->save($query)) {
            $paymentId = $this->db->connect()->insert_id;
            
            // Update conversion records as paid
            $this->db->save("UPDATE conversions 
                           SET status = 'paid', payment_date = NOW() 
                           WHERE partner_id = '$partnerId' 
                           AND status = 'approved' 
                           AND payment_date IS NULL");
            
            // Update partner payment amounts
            $this->db->save("UPDATE partners 
                           SET paid_amount = paid_amount + $totalAmount, 
                               pending_amount = pending_amount - $totalAmount 
                           WHERE id = '$partnerId'");
            
            return [
                'success' => true,
                'payment_id' => $paymentId,
                'amount' => $totalAmount,
                'payment_method' => $partner['payment_method']
            ];
        }
        
        return ['success' => false, 'message' => 'Failed to process payment'];
    }
    
    // Get commission summary for admin
    public function getCommissionSummary($period = '30') {
        $query = "SELECT 
                    COUNT(*) as total_conversions,
                    SUM(conversion_value) as total_revenue,
                    SUM(commission_amount) as total_commissions,
                    COUNT(DISTINCT partner_id) as active_partners,
                    AVG(commission_amount) as avg_commission
                 FROM conversions 
                 WHERE status = 'approved'
                 AND conversion_date >= DATE_SUB(NOW(), INTERVAL $period DAY)";
        
        $summary = $this->db->read($query);
        
        if ($summary) {
            return $summary[0];
        }
        
        return [
            'total_conversions' => 0,
            'total_revenue' => 0,
            'total_commissions' => 0,
            'active_partners' => 0,
            'avg_commission' => 0
        ];
    }
    
    // Get pending payments
    public function getPendingPayments() {
        $query = "SELECT 
                    p.id, p.contact_name, p.company_name, p.email,
                    SUM(c.commission_amount) as pending_amount,
                    COUNT(c.id) as pending_conversions
                 FROM partners p
                 JOIN conversions c ON p.id = c.partner_id
                 WHERE c.status = 'approved' 
                 AND c.payment_date IS NULL
                 GROUP BY p.id
                 HAVING pending_amount >= 50
                 ORDER BY pending_amount DESC";
        
        return $this->db->read($query);
    }
    
    // Approve conversion (admin function)
    public function approveConversion($conversionId) {
        return $this->tracker->approveConversion($conversionId);
    }
    
    // Cancel conversion (admin function)
    public function cancelConversion($conversionId) {
        return $this->tracker->cancelConversion($conversionId);
    }
    
    // Update partner commission rate
    public function updatePartnerCommissionRate($partnerId, $newRate) {
        if ($newRate < 0 || $newRate > 50) {
            return ['success' => false, 'message' => 'Commission rate must be between 0 and 50%'];
        }
        
        $query = "UPDATE partners SET commission_rate = '$newRate' WHERE id = '$partnerId'";
        
        if ($this->db->save($query)) {
            return ['success' => true, 'message' => 'Commission rate updated successfully'];
        }
        
        return ['success' => false, 'message' => 'Failed to update commission rate'];
    }
    
    // Get partner performance report
    public function getPartnerPerformanceReport($partnerId, $period = '30') {
        $query = "SELECT 
                    p.contact_name, p.company_name, p.commission_rate,
                    COUNT(c.id) as conversions,
                    SUM(c.conversion_value) as revenue,
                    SUM(c.commission_amount) as commission,
                    AVG(c.conversion_value) as avg_order_value,
                    COUNT(DISTINCT DATE(c.conversion_date)) as active_days
                 FROM partners p
                 LEFT JOIN conversions c ON p.id = c.partner_id 
                 AND c.status = 'approved'
                 AND c.conversion_date >= DATE_SUB(NOW(), INTERVAL $period DAY)
                 WHERE p.id = '$partnerId'
                 GROUP BY p.id";
        
        $report = $this->db->read($query);
        
        if ($report) {
            return $report[0];
        }
        
        return null;
    }
    
    // Get partner details
    private function getPartnerDetails($partnerId) {
        $db = new Database();
        $partner = $db->read("SELECT * FROM partners WHERE id = '$partnerId'");
        return $partner ? $partner[0] : null;
    }
}
?>