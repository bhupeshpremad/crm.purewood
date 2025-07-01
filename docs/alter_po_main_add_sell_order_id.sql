ALTER TABLE po_main
ADD COLUMN sell_order_id INT NULL AFTER id,
ADD COLUMN sell_order_number VARCHAR(50) NULL AFTER sell_order_id,
ADD COLUMN jci_number VARCHAR(50) NULL AFTER sell_order_number;
