-- ALTER TABLE queries to add missing columns in live database tables based on local database schema

-- Example for table job_cards (missing columns in live)
ALTER TABLE job_cards ADD COLUMN jc_type varchar(255) DEFAULT NULL;
ALTER TABLE job_cards ADD COLUMN contracture_name varchar(255) DEFAULT NULL;
ALTER TABLE job_cards ADD COLUMN labour_cost decimal(15,2) DEFAULT 0.00;
ALTER TABLE job_cards ADD COLUMN quantity int(11) DEFAULT 0;
ALTER TABLE job_cards ADD COLUMN total_amount decimal(15,2) DEFAULT 0.00;

-- Add more ALTER TABLE queries for other tables with missing columns as needed
