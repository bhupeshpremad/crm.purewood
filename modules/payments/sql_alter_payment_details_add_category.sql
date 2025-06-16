ALTER TABLE payment_details
ADD COLUMN payment_category ENUM('Job Card', 'Supplier') NOT NULL DEFAULT 'Job Card';
