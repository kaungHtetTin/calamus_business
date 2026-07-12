-- Query-performance indexes for the partner and admin portals.
-- MariaDB 10.4+ supports IF NOT EXISTS for indexes.

ALTER TABLE partners
    ADD INDEX IF NOT EXISTS idx_partners_created_at (created_at),
    ADD INDEX IF NOT EXISTS idx_partners_status_created (status, created_at),
    ADD INDEX IF NOT EXISTS idx_partners_verification_queue (email_verified, account_verified, created_at);

ALTER TABLE partner_earnings
    ADD INDEX IF NOT EXISTS idx_partner_earnings_partner_created (partner_id, created_at),
    ADD INDEX IF NOT EXISTS idx_partner_earnings_partner_status_created (partner_id, status, created_at),
    ADD INDEX IF NOT EXISTS idx_partner_earnings_status_created (status, created_at);

ALTER TABLE funds
    ADD INDEX IF NOT EXISTS idx_funds_staff_id (staff_id, id);
