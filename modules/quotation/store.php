<?php
// Basic error handling
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(0);

// Increase limits for large data
ini_set('memory_limit', '512M');
ini_set('max_execution_time', 300);
ini_set('max_input_vars', 5000);

include_once __DIR__ . '/../../config/config.php';

global $conn;

global $conn;

// Define the correct, absolute path for uploads
$uploadDir = rtrim(ROOT_DIR_PATH, '/\\') . '/assets/images/upload/quotation/';

// Ensure the upload directory exists
if (!file_exists($uploadDir)) {
    if (!mkdir($uploadDir, 0777, true)) {
        echo json_encode(['success' => false, 'message' => 'Failed to create upload directory.']);
        exit;
    }
}

// Basic validation for main form fields
$requiredFields = ['lead_id', 'quotation_date', 'quotation_number', 'customer_name', 'customer_email', 'customer_phone'];
foreach ($requiredFields as $field) {
    if (empty($_POST[$field])) {
        echo json_encode(['success' => false, 'message' => "Field '$field' is required."]);
        exit;
    }
}

function insertProducts($conn, $quotationId, $productsJson, $uploadDir) {
    $products = json_decode($productsJson, true);
    if (!is_array($products) || empty($products)) {
        return;
    }

    try {
        $productStmt = $conn->prepare(
            "INSERT INTO quotation_products (quotation_id, item_name, item_code, assembly, item_w, item_d, item_h, box_w, box_d, box_h, cbm, wood_type, no_of_packet, quantity, price_usd, total_price_usd, comments, product_image_name) 
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
        );

        foreach ($products as $index => $product) {
            if (empty($product['item_name'])) {
                continue;
            }

            $imageName = $product['existing_image_name'] ?? null;

            // Handle file upload
            if (isset($_FILES['product_images']) && isset($_FILES['product_images']['error'][$index]) && $_FILES['product_images']['error'][$index] === UPLOAD_ERR_OK) {
                $fileTmpName = $_FILES['product_images']['tmp_name'][$index];
                $fileName = basename($_FILES['product_images']['name'][$index]);
                $fileSize = $_FILES['product_images']['size'][$index];
                $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                
                $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                $maxFileSize = 2 * 1024 * 1024;

                if (in_array($fileExt, $allowedExtensions) && $fileSize <= $maxFileSize) {
                    $newFileName = 'prod_' . $quotationId . '_' . ($index + 1) . '_' . time() . '.' . $fileExt;
                    $targetFilePath = $uploadDir . $newFileName;

                    if (move_uploaded_file($fileTmpName, $targetFilePath)) {
                        $imageName = $newFileName;
                    }
                }
            }

            $quantity = floatval($product['quantity'] ?? 0);
            $priceUsd = floatval($product['price_usd'] ?? 0);
            $totalPrice = $quantity * $priceUsd;

            $productStmt->execute([
                $quotationId,
                $product['item_name'],
                $product['item_code'] ?? '',
                $product['assembly'] ?? '',
                !empty($product['item_w']) ? floatval($product['item_w']) : null,
                !empty($product['item_d']) ? floatval($product['item_d']) : null,
                !empty($product['item_h']) ? floatval($product['item_h']) : null,
                !empty($product['box_w']) ? floatval($product['box_w']) : null,
                !empty($product['box_d']) ? floatval($product['box_d']) : null,
                !empty($product['box_h']) ? floatval($product['box_h']) : null,
                !empty($product['cbm']) ? floatval($product['cbm']) : null,
                $product['wood_type'] ?? '',
                !empty($product['no_of_packet']) ? intval($product['no_of_packet']) : null,
                $quantity,
                $priceUsd,
                $totalPrice,
                $product['comments'] ?? '',
                $imageName
            ]);
        }
        
    } catch (Exception $e) {
        throw $e;
    }
}

try {
    $conn->beginTransaction();
    
    if (isset($_POST['quotation_id']) && !empty($_POST['quotation_id'])) {
        // UPDATE EXISTING QUOTATION
        $quotationId = intval($_POST['quotation_id']);
        
        $stmt = $conn->prepare("UPDATE quotations SET lead_id = ?, quotation_date = ?, quotation_number = ?, customer_name = ?, customer_email = ?, customer_phone = ?, delivery_term = ?, terms_of_delivery = ? WHERE id = ?");
        $stmt->execute([
            $_POST['lead_id'], $_POST['quotation_date'], $_POST['quotation_number'],
            $_POST['customer_name'], $_POST['customer_email'], $_POST['customer_phone'],
            $_POST['delivery_term'] ?? '', $_POST['terms_of_delivery'] ?? '',
            $quotationId
        ]);

        // Delete old products
        $deleteStmt = $conn->prepare("DELETE FROM quotation_products WHERE quotation_id = ?");
        $deleteStmt->execute([$quotationId]);

    } else {
        // CREATE NEW QUOTATION
        $stmt = $conn->prepare("INSERT INTO quotations (lead_id, quotation_date, quotation_number, customer_name, customer_email, customer_phone, delivery_term, terms_of_delivery) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $_POST['lead_id'], $_POST['quotation_date'], $_POST['quotation_number'],
            $_POST['customer_name'], $_POST['customer_email'], $_POST['customer_phone'],
            $_POST['delivery_term'] ?? '', $_POST['terms_of_delivery'] ?? ''
        ]);
        $quotationId = $conn->lastInsertId();
    }
    
    // Insert products
    if (!empty($_POST['products'])) {
        insertProducts($conn, $quotationId, $_POST['products'], $uploadDir);
    }

    $conn->commit();
    echo json_encode(['success' => true, 'message' => 'Quotation saved successfully.', 'quotation_id' => $quotationId]);

} catch (Exception $e) {
    if ($conn->inTransaction()) {
        $conn->rollBack();
    }
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>