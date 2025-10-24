<?php
// All classes are loaded by autoloader

// Example integration for VIP subscription with affiliate tracking
class VipSubscriptionHandler {
    private $commissionManager;
    
    public function __construct() {
        $this->commissionManager = new CommissionManager();
    }
    
    // Handle VIP subscription purchase
    public function handleVipPurchase($userId, $courseId, $amount, $paymentMethod = 'stripe') {
        try {
            // Process the VIP subscription and track affiliate conversion
            $result = $this->commissionManager->processVipSubscription(
                $userId, 
                $courseId, 
                $amount, 
                $paymentMethod
            );
            
            if ($result['success']) {
                // Log successful subscription
                error_log("VIP Subscription created: User $userId, Course $courseId, Amount $amount");
                
                // Send confirmation email to user
                $this->sendSubscriptionConfirmation($userId, $courseId, $amount);
                
                // Send notification to partner if affiliate conversion occurred
                if ($result['affiliate_commission'] > 0) {
                    $this->notifyPartnerOfConversion($userId, $result['affiliate_commission']);
                }
                
                return [
                    'success' => true,
                    'subscription_id' => $result['subscription_id'],
                    'message' => 'VIP subscription activated successfully',
                    'affiliate_commission' => $result['affiliate_commission']
                ];
            } else {
                return $result;
            }
            
        } catch (Exception $e) {
            error_log("VIP Subscription Error: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'An error occurred while processing your subscription'
            ];
        }
    }
    
    // Send subscription confirmation email
    private function sendSubscriptionConfirmation($userId, $courseId, $amount) {
        // Get user and course details
        $user = $this->getUserDetails($userId);
        $course = $this->getCourseDetails($courseId);
        
        if ($user && $course) {
            $subject = "VIP Subscription Confirmed - " . $course['title'];
            $message = "
            <h2>Welcome to VIP Access!</h2>
            <p>Dear {$user['learner_name']},</p>
            <p>Your VIP subscription for <strong>{$course['title']}</strong> has been activated successfully.</p>
            <p><strong>Course Details:</strong></p>
            <ul>
                <li>Course: {$course['title']}</li>
                <li>Language: {$course['major']}</li>
                <li>Amount Paid: $" . number_format($amount, 2) . "</li>
                <li>Access Level: VIP</li>
            </ul>
            <p>You now have unlimited access to all premium content in this course.</p>
            <p>Start learning now: <a href='http://localhost/business/course.php?id=$courseId'>Access Course</a></p>
            <p>Thank you for choosing our platform!</p>
            ";
            
            // Send email (implement with your preferred email service)
            mail($user['learner_email'], $subject, $message, "Content-Type: text/html");
        }
    }
    
    // Notify partner of conversion
    private function notifyPartnerOfConversion($userId, $commissionAmount) {
        // Get affiliate conversion details
        $conversion = $this->getConversionDetails($userId);
        
        if ($conversion) {
            $partner = $this->getPartnerDetails($conversion['partner_id']);
            
            if ($partner) {
                $subject = "New Conversion - Commission Earned!";
                $message = "
                <h2>Congratulations!</h2>
                <p>Dear {$partner['contact_name']},</p>
                <p>You have earned a new commission!</p>
                <p><strong>Conversion Details:</strong></p>
                <ul>
                    <li>Commission Amount: $" . number_format($commissionAmount, 2) . "</li>
                    <li>Conversion Type: VIP Subscription</li>
                    <li>Date: " . date('Y-m-d H:i:s') . "</li>
                </ul>
                <p>Login to your dashboard to view detailed analytics: <a href='http://localhost/business/index.php'>Partner Dashboard</a></p>
                <p>Keep up the great work!</p>
                ";
                
                mail($partner['email'], $subject, $message, "Content-Type: text/html");
            }
        }
    }
    
    // Get user details
    private function getUserDetails($userId) {
        $db = new Database();
        $user = $db->read("SELECT * FROM learners WHERE id = '$userId'");
        return $user ? $user[0] : null;
    }
    
    // Get course details
    private function getCourseDetails($courseId) {
        $db = new Database();
        $course = $db->read("SELECT * FROM courses WHERE course_id = '$courseId'");
        return $course ? $course[0] : null;
    }
    
    // Get conversion details
    private function getConversionDetails($userId) {
        $db = new Database();
        $conversion = $db->read("SELECT * FROM conversions WHERE user_id = '$userId' ORDER BY conversion_date DESC LIMIT 1");
        return $conversion ? $conversion[0] : null;
    }
    
    // Get partner details
    private function getPartnerDetails($partnerId) {
        $db = new Database();
        $partner = $db->read("SELECT * FROM partners WHERE id = '$partnerId'");
        return $partner ? $partner[0] : null;
    }
}
?>
