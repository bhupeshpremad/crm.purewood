-- SQL schema for purchase tables with relations

-- Main purchase table
CREATE TABLE IF NOT EXISTS purchase_main (
    po_number VARCHAR(50) PRIMARY KEY,
    sell_order_number VARCHAR(50),
    jci_number VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Wood table related to purchase_main
CREATE TABLE IF NOT EXISTS purchase_wood (
    id INT AUTO_INCREMENT PRIMARY KEY,
    po_number VARCHAR(50),
    woodtype VARCHAR(50),
    length_ft DECIMAL(10,2),
    width_ft DECIMAL(10,2),
    thickness_inch DECIMAL(10,2),
    quantity DECIMAL(10,2),
    price DECIMAL(10,2),
    cft DECIMAL(10,2),
    total DECIMAL(10,2),
    FOREIGN KEY (po_number) REFERENCES purchase_main(po_number) ON DELETE CASCADE
);

-- Glow table related to purchase_main
CREATE TABLE IF NOT EXISTS purchase_glow (
    id INT AUTO_INCREMENT PRIMARY KEY,
    po_number VARCHAR(50),
    glowtype VARCHAR(50),
    quantity DECIMAL(10,2),
    price DECIMAL(10,2),
    total DECIMAL(10,2),
    FOREIGN KEY (po_number) REFERENCES purchase_main(po_number) ON DELETE CASCADE
);

-- PLY/NYDF table related to purchase_main
CREATE TABLE IF NOT EXISTS purchase_plynydf (
    id INT AUTO_INCREMENT PRIMARY KEY,
    po_number VARCHAR(50),
    quantity DECIMAL(10,2),
    width DECIMAL(10,2),
    length DECIMAL(10,2),
    price DECIMAL(10,2),
    total DECIMAL(10,2),
    FOREIGN KEY (po_number) REFERENCES purchase_main(po_number) ON DELETE CASCADE
);

-- Hardware table related to purchase_main
CREATE TABLE IF NOT EXISTS purchase_hardware (
    id INT AUTO_INCREMENT PRIMARY KEY,
    po_number VARCHAR(50),
    itemname VARCHAR(100),
    quantity DECIMAL(10,2),
    price DECIMAL(10,2),
    totalprice DECIMAL(10,2),
    FOREIGN KEY (po_number) REFERENCES purchase_main(po_number) ON DELETE CASCADE
);
