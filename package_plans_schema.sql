-- Database Schema for Package Plans System
-- Run these SQL commands to create the package plan structure

-- Package Plans table
CREATE TABLE IF NOT EXISTS package_plans (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    original_price DECIMAL(10,2), -- For discount display
    duration_days INT(11), -- Package validity in days
    max_courses INT(11), -- Maximum courses allowed in package
    major VARCHAR(20), -- Language/category (english, chinese, etc.)
    status ENUM('active', 'inactive', 'archived') DEFAULT 'active',
    sort_order INT(11) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Package Plan Courses (Many-to-Many relationship)
CREATE TABLE IF NOT EXISTS package_plan_courses (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    package_id INT(11) NOT NULL,
    course_id SMALLINT(3) NOT NULL,
    is_required BOOLEAN DEFAULT FALSE, -- Required course in package
    sort_order INT(11) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (package_id) REFERENCES package_plans(id) ON DELETE CASCADE,
    FOREIGN KEY (course_id) REFERENCES courses(course_id) ON DELETE CASCADE,
    UNIQUE KEY unique_package_course (package_id, course_id)
);

-- Create indexes for better performance
CREATE INDEX idx_package_plans_major ON package_plans(major);
CREATE INDEX idx_package_plans_status ON package_plans(status);
CREATE INDEX idx_package_purchases_user ON package_purchases(learner_phone);
CREATE INDEX idx_package_purchases_status ON package_purchases(status);
CREATE INDEX idx_package_purchases_expiry ON package_purchases(expiry_date);
CREATE INDEX idx_package_course_access_purchase ON package_course_access(purchase_id);

-- Insert sample package plans
INSERT INTO package_plans (name, description, price, original_price, duration_days, max_courses, major, status) VALUES
('English Complete Package', 'Complete English learning package with all courses', 299.99, 399.99, 365, 10, 'english', 'active'),
('Chinese Starter Package', 'Beginner Chinese learning package', 199.99, 249.99, 180, 5, 'chinese', 'active'),
('Japanese Pro Package', 'Advanced Japanese learning package', 399.99, 499.99, 365, 15, 'japanese', 'active'),
('Korean Essentials Package', 'Essential Korean learning package', 179.99, 229.99, 120, 4, 'korean', 'active'),
('Russian Master Package', 'Master Russian learning package', 349.99, 429.99, 365, 12, 'russian', 'active');
