-- Promotion Codes Table Schema
-- Run these SQL commands to create the promotion codes system

-- Create promotion_codes table
CREATE TABLE IF NOT EXISTS promotion_codes (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    partner_id INT(11) NOT NULL,
    code VARCHAR(50) UNIQUE NOT NULL,
    target_course_id INT(11) NULL,
    target_package_id INT(11) NULL,
    learner_phone BIGINT(20) NULL,
    price DECIMAL(10,2) NOT NULL,
    commission_rate DECIMAL(5,2) NOT NULL,
    amount_received DECIMAL(10,2) DEFAULT 0.00,
    status ENUM('pending', 'approved', 'rejected', 'expired') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    expired_at TIMESTAMP NOT NULL,
    FOREIGN KEY (partner_id) REFERENCES partners(id) ON DELETE CASCADE
);

-- Create indexes for better performance
CREATE INDEX idx_promotion_codes_partner ON promotion_codes(partner_id);
CREATE INDEX idx_promotion_codes_status ON promotion_codes(status);
CREATE INDEX idx_promotion_codes_expires ON promotion_codes(expired_at);
CREATE INDEX idx_promotion_codes_code ON promotion_codes(code);

-- Add code generation settings to partners table if not exists
ALTER TABLE partners ADD COLUMN IF NOT EXISTS code_prefix VARCHAR(10) DEFAULT 'PART' AFTER commission_rate;
ALTER TABLE partners ADD COLUMN IF NOT EXISTS total_codes_generated INT(11) DEFAULT 0 AFTER code_prefix;
ALTER TABLE partners ADD COLUMN IF NOT EXISTS total_codes_used INT(11) DEFAULT 0 AFTER total_codes_generated;
