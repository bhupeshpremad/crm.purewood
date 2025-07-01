ALTER TABLE bom_main
ADD COLUMN costing_sheet_number VARCHAR(255) NOT NULL AFTER bom_number;
