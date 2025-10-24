<?php
// Database class is loaded by autoloader

class PackagePlanManager {
    private $db;
    
    public function __construct() {
        $this->db = new Database();
    }
    
    // Get all active package plans
    public function getActivePackagePlans($major = null) {
        $whereClause = "WHERE status = 'active'";
        
        if ($major) {
            $whereClause .= " AND major = '$major'";
        }
        
        $query = "SELECT * FROM package_plans $whereClause ORDER BY sort_order ASC, price ASC";
        return $this->db->read($query);
    }
    
    // Get package plan by ID
    public function getPackagePlan($packageId) {
        $package = $this->db->read("SELECT * FROM package_plans WHERE id = '$packageId'");
        return $package ? $package[0] : null;
    }
    
    // Get courses included in a package
    public function getPackageCourses($packageId) {
        $query = "SELECT c.*, ppc.is_required, ppc.sort_order 
                 FROM package_plan_courses ppc 
                 JOIN courses c ON ppc.course_id = c.course_id 
                 WHERE ppc.package_id = '$packageId' 
                 ORDER BY ppc.sort_order ASC";
        
        return $this->db->read($query);
    }
    
    // Purchase a package plan
    public function purchasePackage($learnerPhone, $packageId, $promotionCodeId = null) {
        // Get package details
        $package = $this->getPackagePlan($packageId);
        
        if (!$package) {
            return ['success' => false, 'message' => 'Package not found'];
        }
        
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
                 VALUES ('$learnerPhone', '$packageId', '{$package['price']}', '$expiryDate', '$promotionCodeId')";
        
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
        
        return ['success' => false, 'message' => 'Failed to purchase package'];
    }
    
    // Grant VIP access to all courses in package
    private function grantPackageAccess($purchaseId, $packageId, $learnerPhone) {
        $courses = $this->getPackageCourses($packageId);
        
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
    
    // Check if user has access to a course through package
    public function hasPackageAccess($learnerPhone, $courseId) {
        $query = "SELECT pp.*, ppc.is_required 
                 FROM package_purchases pp 
                 JOIN package_plan_courses ppc ON pp.package_id = ppc.package_id 
                 WHERE pp.learner_phone = '$learnerPhone' 
                 AND ppc.course_id = '$courseId' 
                 AND pp.status = 'active' 
                 AND pp.expiry_date > NOW()";
        
        $access = $this->db->read($query);
        return $access ? $access[0] : null;
    }
    
    // Track course access from package
    public function trackPackageCourseAccess($learnerPhone, $courseId) {
        $packageAccess = $this->hasPackageAccess($learnerPhone, $courseId);
        
        if ($packageAccess) {
            // Get purchase ID
            $purchase = $this->db->read("SELECT id FROM package_purchases 
                                       WHERE learner_phone = '$learnerPhone' AND package_id = '{$packageAccess['package_id']}' 
                                       AND status = 'active' AND expiry_date > NOW()");
            
            if ($purchase) {
                $purchaseId = $purchase[0]['id'];
                
                // Check if access already tracked
                $existingAccess = $this->db->read("SELECT id, access_count FROM package_course_access 
                                                 WHERE purchase_id = '$purchaseId' AND course_id = '$courseId'");
                
                if ($existingAccess) {
                    // Update access count
                    $newCount = $existingAccess[0]['access_count'] + 1;
                    $this->db->save("UPDATE package_course_access 
                                   SET access_count = '$newCount', access_date = NOW() 
                                   WHERE id = '{$existingAccess[0]['id']}'");
                } else {
                    // Create new access record
                    $this->db->save("INSERT INTO package_course_access (purchase_id, course_id) 
                                   VALUES ('$purchaseId', '$courseId')");
                }
            }
        }
    }
    
    // Get user's active packages
    public function getUserActivePackages($learnerPhone) {
        $query = "SELECT pp.*, ppp.purchase_date, ppp.expiry_date, ppp.status as purchase_status
                 FROM package_purchases ppp 
                 JOIN package_plans pp ON ppp.package_id = pp.id 
                 WHERE ppp.learner_phone = '$learnerPhone' 
                 AND ppp.status = 'active' 
                 AND ppp.expiry_date > NOW()
                 ORDER BY ppp.purchase_date DESC";
        
        return $this->db->read($query);
    }
    
    // Get package purchase statistics
    public function getPackageStats($packageId = null) {
        $whereClause = $packageId ? "WHERE pp.package_id = '$packageId'" : "";
        
        $query = "SELECT 
                    COUNT(pp.id) as total_purchases,
                    SUM(pp.purchase_price) as total_revenue,
                    AVG(pp.purchase_price) as avg_purchase_price,
                    COUNT(DISTINCT pp.learner_phone) as unique_customers
                 FROM package_purchases pp 
                 $whereClause";
        
        $stats = $this->db->read($query);
        return $stats ? $stats[0] : null;
    }
    
    // Check package expiry and update status
    public function checkPackageExpiry() {
        $query = "UPDATE package_purchases 
                 SET status = 'expired' 
                 WHERE status = 'active' 
                 AND expiry_date <= NOW()";
        
        $this->db->save($query);
        
        // Also update VIP access for expired packages
        $expiredPurchases = $this->db->read("SELECT id FROM package_purchases WHERE status = 'expired'");
        
        foreach ($expiredPurchases as $purchase) {
            $this->db->save("UPDATE vipusers SET package_purchase_id = NULL WHERE package_purchase_id = '{$purchase['id']}'");
        }
    }
    
    // Get package plan with courses for display
    public function getPackagePlanWithCourses($packageId) {
        $package = $this->getPackagePlan($packageId);
        
        if ($package) {
            $package['courses'] = $this->getPackageCourses($packageId);
        }
        
        return $package;
    }
    
    // Calculate package discount percentage
    public function getPackageDiscount($packageId) {
        $package = $this->getPackagePlan($packageId);
        
        if ($package && $package['original_price']) {
            $discount = (($package['original_price'] - $package['price']) / $package['original_price']) * 100;
            return round($discount, 2);
        }
        
        return 0;
    }
    
    // Get packages by major with course count
    public function getPackagesByMajor($major) {
        $query = "SELECT pp.*, COUNT(ppc.course_id) as course_count 
                 FROM package_plans pp 
                 LEFT JOIN package_plan_courses ppc ON pp.id = ppc.package_id 
                 WHERE pp.major = '$major' AND pp.status = 'active'
                 GROUP BY pp.id 
                 ORDER BY pp.sort_order ASC";
        
        return $this->db->read($query);
    }
}
?>
