<?php
// Database schema for Affiliate Marketing System
// Run these SQL commands to create the required tables

$affiliate_schema = "
-- Partners/Affiliates table
CREATE TABLE IF NOT EXISTS partners (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    company_name VARCHAR(255),
    contact_name VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    website VARCHAR(500),
    commission_rate DECIMAL(5,2) DEFAULT 10.00, -- Percentage
    status ENUM('active', 'inactive', 'suspended') DEFAULT 'active',
    payment_method ENUM('bank_transfer', 'mobile_banking') DEFAULT 'bank_transfer',
    payment_details TEXT, -- Bank details or PayPal email
    total_earnings DECIMAL(10,2) DEFAULT 0.00,
    paid_amount DECIMAL(10,2) DEFAULT 0.00,
    pending_amount DECIMAL(10,2) DEFAULT 0.00,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    last_login TIMESTAMP NULL,
    email_verified BOOLEAN DEFAULT FALSE,
    verification_token VARCHAR(255),
    reset_token VARCHAR(255),
    reset_token_expires TIMESTAMP NULL
);

-- Affiliate links tracking
CREATE TABLE IF NOT EXISTS affiliate_links (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    partner_id INT(11) NOT NULL,
    link_code VARCHAR(50) UNIQUE NOT NULL, -- Unique tracking code
    campaign_name VARCHAR(255),
    target_course_id INT(11), -- Which course to promote
    target_major VARCHAR(20), -- Language/category
    custom_url VARCHAR(500), -- Custom landing page
    clicks INT(11) DEFAULT 0,
    conversions INT(11) DEFAULT 0,
    revenue DECIMAL(10,2) DEFAULT 0.00,
    commission_earned DECIMAL(10,2) DEFAULT 0.00,
    status ENUM('active', 'paused', 'deleted') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (partner_id) REFERENCES partners(id) ON DELETE CASCADE
);

-- Conversion tracking
CREATE TABLE IF NOT EXISTS conversions (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    partner_id INT(11) NOT NULL,
    affiliate_link_id INT(11) NOT NULL,
    user_id BIGINT(20), -- Reference to vipusers table
    conversion_type ENUM('vip_subscription', 'course_purchase', 'app_download') DEFAULT 'vip_subscription',
    conversion_value DECIMAL(10,2) NOT NULL, -- Amount of the sale
    commission_rate DECIMAL(5,2) NOT NULL, -- Commission rate at time of conversion
    commission_amount DECIMAL(10,2) NOT NULL, -- Calculated commission
    status ENUM('pending', 'approved', 'paid', 'cancelled') DEFAULT 'pending',
    conversion_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    payment_date TIMESTAMP NULL,
    ip_address VARCHAR(45),
    user_agent TEXT,
    referrer_url VARCHAR(500),
    FOREIGN KEY (partner_id) REFERENCES partners(id) ON DELETE CASCADE,
    FOREIGN KEY (affiliate_link_id) REFERENCES affiliate_links(id) ON DELETE CASCADE
);

-- Commission payments
CREATE TABLE IF NOT EXISTS commission_payments (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    partner_id INT(11) NOT NULL,
    payment_period_start DATE NOT NULL,
    payment_period_end DATE NOT NULL,
    total_commission DECIMAL(10,2) NOT NULL,
    payment_method ENUM('bank_transfer', 'paypal', 'stripe') NOT NULL,
    payment_details TEXT,
    payment_status ENUM('pending', 'processing', 'completed', 'failed') DEFAULT 'pending',
    payment_reference VARCHAR(255), -- Transaction ID
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    processed_at TIMESTAMP NULL,
    FOREIGN KEY (partner_id) REFERENCES partners(id) ON DELETE CASCADE
);

-- Partner login sessions
CREATE TABLE IF NOT EXISTS partner_sessions (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    partner_id INT(11) NOT NULL,
    session_token VARCHAR(255) UNIQUE NOT NULL,
    ip_address VARCHAR(45),
    user_agent TEXT,
    expires_at TIMESTAMP NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (partner_id) REFERENCES partners(id) ON DELETE CASCADE
);

-- Email verification and password reset
CREATE TABLE IF NOT EXISTS partner_auth_tokens (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    partner_id INT(11) NOT NULL,
    token_type ENUM('email_verification', 'password_reset') NOT NULL,
    token VARCHAR(255) NOT NULL,
    expires_at TIMESTAMP NOT NULL,
    used BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (partner_id) REFERENCES partners(id) ON DELETE CASCADE
);
";

echo $affiliate_schema;
?>
