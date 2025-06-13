<?php
// Include your database connection and other required files
// include 'config.php';

// Get the quotation ID and format from the URL
$quotation_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$format = isset($_GET['format']) ? $_GET['format'] : '';

// Validate inputs
if ($quotation_id <= 0 || !in_array($format, ['excel', 'pdf'])) {
    die("Invalid request");
}

// Fetch quotation data from database
// Example: $quotation = getQuotationById($quotation_id);

// For demonstration, let's create sample data
$quotation = [
    'id' => $quotation_id,
    'quotation_no' => 'Q-00' . $quotation_id,
    'customer_name' => 'Sample Customer',
    'total_amount' => 10000,
    'items' => [
        ['name' => 'Product 1', 'quantity' => 2, 'price' => 2500, 'total' => 5000],
        ['name' => 'Product 2', 'quantity' => 1, 'price' => 5000, 'total' => 5000],
    ]
];

// Export based on format
if ($format == 'excel') {
    // Set headers for Excel download
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="quotation_' . $quotation_id . '.xls"');
    header('Cache-Control: max-age=0');
    
    // Output Excel content
    echo "<table border='1'>";
    echo "<tr><th colspan='4'>Quotation: " . $quotation['quotation_no'] . "</th></tr>";
    echo "<tr><th colspan='4'>Customer: " . $quotation['customer_name'] . "</th></tr>";
    echo "<tr><th>Product</th><th>Quantity</th><th>Price</th><th>Total</th></tr>";
    
    foreach ($quotation['items'] as $item) {
        echo "<tr>";
        echo "<td>" . $item['name'] . "</td>";
        echo "<td>" . $item['quantity'] . "</td>";
        echo "<td>" . $item['price'] . "</td>";
        echo "<td>" . $item['total'] . "</td>";
        echo "</tr>";
    }
    
    echo "<tr><th colspan='3'>Total Amount</th><td>" . $quotation['total_amount'] . "</td></tr>";
    echo "</table>";
    
} else if ($format == 'pdf') {
    // For PDF, you would typically use a library like FPDF or TCPDF
    // For this example, we'll just output some text
    header('Content-Type: text/plain');
    header('Content-Disposition: attachment;filename="quotation_' . $quotation_id . '.txt"');
    
    echo "Quotation: " . $quotation['quotation_no'] . "\n";
    echo "Customer: " . $quotation['customer_name'] . "\n\n";
    echo "Products:\n";
    
    foreach ($quotation['items'] as $item) {
        echo $item['name'] . " - " . $item['quantity'] . " x " . $item['price'] . " = " . $item['total'] . "\n";
    }
    
    echo "\nTotal Amount: " . $quotation['total_amount'];
    
    // Note: For a real implementation, you would use a PDF library like:
    // require('fpdf/fpdf.php');
    // $pdf = new FPDF();
    // $pdf->AddPage();
    // $pdf->SetFont('Arial','B',16);
    // $pdf->Cell(40,10,'Quotation: ' . $quotation['quotation_no']);
    // ... more PDF generation code ...
    // $pdf->Output('D', 'quotation_' . $quotation_id . '.pdf');
}
?>