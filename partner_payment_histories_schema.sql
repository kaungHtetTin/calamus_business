-- Partner Payment Histories Table Schema
-- This table stores payment disbursement records sent to partners

CREATE TABLE IF NOT EXISTS partner_payment_histories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    partner_id INT NOT NULL,
    payment_method VARCHAR(100) NOT NULL,
    account_number VARCHAR(255) NOT NULL,
    account_name VARCHAR(255) NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    status ENUM('pending', 'received', 'rejected') DEFAULT 'pending',
    transaction_screenshot VARCHAR(500) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    -- Foreign key constraint
    FOREIGN KEY (partner_id) REFERENCES partners(id) ON DELETE CASCADE,
    
    -- Indexes for better performance
    INDEX idx_partner_payment_histories_partner_id (partner_id),
    INDEX idx_partner_payment_histories_status (status),
    INDEX idx_partner_payment_histories_created_at (created_at),
    INDEX idx_partner_payment_histories_partner_status (partner_id, status)
);

-- Insert sample data for testing
INSERT INTO partner_payment_histories (partner_id, payment_method, account_number, account_name, amount, status, transaction_screenshot) VALUES
(1, 'Bank Transfer', '1234567890', 'John Doe', 150.00, 'received', 'screenshot1.jpg'),
(1, 'PayPal', 'john.doe@email.com', 'John Doe', 75.50, 'pending', NULL),
(2, 'Bank Transfer', '9876543210', 'Jane Smith', 200.00, 'received', 'screenshot2.jpg'),
(2, 'Stripe', 'jane.smith@email.com', 'Jane Smith', 125.75, 'rejected', 'screenshot3.jpg');
