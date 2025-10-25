-- Partner Payment Methods Table Schema
-- This table stores payment method information for partners

CREATE TABLE IF NOT EXISTS partner_payment_methods (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    partner_id INT(11) NOT NULL,
    payment_method VARCHAR(100) NOT NULL,
    account_number VARCHAR(255) NOT NULL,
    account_name VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (partner_id) REFERENCES partners(id) ON DELETE CASCADE
);

-- Create indexes for better performance
CREATE INDEX idx_partner_payment_methods_partner_id ON partner_payment_methods(partner_id);
CREATE INDEX idx_partner_payment_methods_payment_method ON partner_payment_methods(payment_method);
