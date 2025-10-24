-- Updated Database Schema for Promotion Code System
-- Run these SQL commands to update your database

-- Add promotion codes table
CREATE TABLE IF NOT EXISTS promotion_codes (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    partner_id INT(11) NOT NULL,
    code VARCHAR(50) UNIQUE NOT NULL,
    code_type ENUM('vip_subscription', 'course_purchase') NOT NULL,
    target_course_id INT(11),
    target_major VARCHAR(20),
    commission_rate DECIMAL(5,2) NOT NULL,
    status ENUM('active', 'used', 'expired', 'cancelled') DEFAULT 'active',
    generated_by INT(11) NOT NULL, -- Partner who generated it
    generated_for VARCHAR(255), -- Client name/description
    expires_at TIMESTAMP NULL,
    used_at TIMESTAMP NULL,
    used_by BIGINT(20), -- User who used it
    conversion_id INT(11), -- Link to conversion record
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (partner_id) REFERENCES partners(id) ON DELETE CASCADE,
    FOREIGN KEY (conversion_id) REFERENCES conversions(id) ON DELETE SET NULL
);

-- Add code tracking to conversions table
ALTER TABLE conversions ADD COLUMN promotion_code_id INT(11) AFTER affiliate_link_id;
ALTER TABLE conversions ADD FOREIGN KEY (promotion_code_id) REFERENCES promotion_codes(id) ON DELETE SET NULL;

-- Add code generation settings to partners table
ALTER TABLE partners ADD COLUMN code_prefix VARCHAR(10) DEFAULT 'PART' AFTER commission_rate;
ALTER TABLE partners ADD COLUMN total_codes_generated INT(11) DEFAULT 0 AFTER pending_amount;
ALTER TABLE partners ADD COLUMN total_codes_used INT(11) DEFAULT 0 AFTER total_codes_generated;

-- Create index for faster code lookups
CREATE INDEX idx_promotion_codes_code ON promotion_codes(code);
CREATE INDEX idx_promotion_codes_partner ON promotion_codes(partner_id);
CREATE INDEX idx_promotion_codes_status ON promotion_codes(status);
CREATE INDEX idx_promotion_codes_type ON promotion_codes(code_type);

-- Update affiliate_links table to be optional (for backward compatibility)
ALTER TABLE affiliate_links MODIFY COLUMN partner_id INT(11) NULL;
ALTER TABLE conversions MODIFY COLUMN affiliate_link_id INT(11) NULL;
