CREATE TABLE IF NOT EXISTS pi (
    pi_id INT AUTO_INCREMENT PRIMARY KEY,
    quotation_id INT NOT NULL,
    pi_number VARCHAR(20) NOT NULL UNIQUE,
    payment_term TEXT,
    inspection TEXT,
    date_of_pi_raised DATE,
    sample_approval_date DATE,
    detailed_seller_address TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (quotation_id) REFERENCES quotations(id) ON DELETE CASCADE
);
