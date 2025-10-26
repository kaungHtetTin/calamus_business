-- Password Reset Tokens Table Schema
CREATE TABLE IF NOT EXISTS partner_password_reset_tokens (
    id INT AUTO_INCREMENT PRIMARY KEY,
    partner_id INT NOT NULL,
    token VARCHAR(64) NOT NULL UNIQUE,
    expires_at DATETIME NOT NULL,
    used TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (partner_id) REFERENCES partners(id) ON DELETE CASCADE,
    INDEX idx_token (token),
    INDEX idx_partner_id (partner_id),
    INDEX idx_expires_at (expires_at)
);

-- Clean up expired tokens (optional - can be run periodically)
-- DELETE FROM partner_password_reset_tokens WHERE expires_at < NOW() OR used = 1;
