<?php
// Simple test script to check if basic quotation save works
include_once __DIR__ . '/../../config/config.php';

// Test data
$testData = [
    'lead_id' => 1,
    'quotation_date' => date('Y-m-d'),
    'quotation_number' => 'TEST-' . time(),
    'customer_name' => 'Test Customer',
    'customer_email' => 'test@example.com',
    'customer_phone' => '1234567890',
    'delivery_term' => 'Test Terms',
    'terms_of_delivery' => 'Test Delivery'
];

$testProducts = [
    [
        'item_name' => 'Test Product 1',
        'item_code' => 'TP001',
        'quantity' => 1,
        'price_usd' => 100
    ],
    [
        'item_name' => 'Test Product 2', 
        'item_code' => 'TP002',
        'quantity' => 2,
        'price_usd' => 200
    ]
];

try {
    global $conn;
    
    // Insert quotation
    $stmt = $conn->prepare("INSERT INTO quotations (lead_id, quotation_date, quotation_number, customer_name, customer_email, customer_phone, delivery_term, terms_of_delivery) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([
        $testData['lead_id'], $testData['quotation_date'], $testData['quotation_number'],
        $testData['customer_name'], $testData['customer_email'], $testData['customer_phone'],
        $testData['delivery_term'], $testData['terms_of_delivery']
    ]);
    
    $quotationId = $conn->lastInsertId();
    echo "Quotation created with ID: $quotationId\n";
    
    // Insert products
    $productStmt = $conn->prepare(
        "INSERT INTO quotation_products (quotation_id, item_name, item_code, assembly, item_w, item_d, item_h, box_w, box_d, box_h, cbm, wood_type, no_of_packet, quantity, price_usd, total_price_usd, comments, product_image_name) 
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
    );
    
    foreach ($testProducts as $product) {
        $totalPrice = $product['quantity'] * $product['price_usd'];
        
        $productStmt->execute([
            $quotationId,
            $product['item_name'],
            $product['item_code'],
            '', // assembly
            null, null, null, // item dimensions
            null, null, null, // box dimensions
            null, // cbm
            '', // wood_type
            null, // no_of_packet
            $product['quantity'],
            $product['price_usd'],
            $totalPrice,
            '', // comments
            null // product_image_name
        ]);
    }
    
    echo "Products inserted successfully\n";
    echo "Test completed successfully!\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>