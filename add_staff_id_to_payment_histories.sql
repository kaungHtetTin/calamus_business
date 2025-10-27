-- Migration: Add staff_id column to partner_payment_histories table
-- Run this migration to add the staff_id column if it doesn't exist

ALTER TABLE partner_payment_histories 
ADD COLUMN IF NOT EXISTS staff_id INT NULL AFTER transaction_screenshot;

-- Add index for staff_id
ALTER TABLE partner_payment_histories 
ADD INDEX IF NOT EXISTS idx_partner_payment_histories_staff_id (staff_id);

