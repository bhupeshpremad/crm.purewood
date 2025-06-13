CREATE TABLE payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    lead_id INT NULL,
    pon_number VARCHAR(255) NULL,
    po_amt DECIMAL(15,2) NOT NULL,
    son_number VARCHAR(255) NOT NULL,
    soa_number DECIMAL(15,2) NOT NULL,
    jc_number VARCHAR(255) NOT NULL,
    jc_amt DECIMAL(15,2) NOT NULL,
    supplier_name VARCHAR(255) NULL,
    invoice_number VARCHAR(255) NOT NULL,
    invoice_amount DECIMAL(15,2) NOT NULL,
    cheque_number VARCHAR(255) NULL,
    ptm_amount DECIMAL(15,2) NOT NULL,
    pd_acc_number VARCHAR(255) NOT NULL,
    payment_invoice_date DATE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE payment_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    payment_id INT NOT NULL,
    item_name VARCHAR(255) NOT NULL,
    item_quantity INT NOT NULL,
    item_price DECIMAL(15,2) NOT NULL,
    item_amount DECIMAL(15,2) NOT NULL,
    FOREIGN KEY (payment_id) REFERENCES payments(id) ON DELETE CASCADE
);
