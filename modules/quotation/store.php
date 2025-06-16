<?php
include_once __DIR__ . '/../../config/config.php';

global $conn;

$uploadDir = ROOT_DIR_PATH . 'assets/images/upload/quotation/';

if (!file_exists($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

$requiredFields = ['lead_id', 'quotation_date', 'quotation_number', 'customer_name', 'customer_email', 'customer_phone', 'delivery_term', 'terms_of_delivery'];
foreach ($requiredFields as $field) {
    if (empty($_POST[$field])) {
        echo json_encode(['success' => false, 'message' => "Field $field is required."]);
        exit;
    }
}

function insertProducts($conn, $quotationId, $productsJson, $uploadDir) {
    $products = json_decode($productsJson, true);
    if (!is_array($products)) {
        return;
    }

    // Debug: output the structure of $_FILES['products']
    file_put_contents('php://stderr', print_r($_FILES['products'], true));

    $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp'];
    $maxFileSize = 5 * 1024 * 1024;
    $productStmt = $conn->prepare("INSERT INTO quotation_products (quotation_id, item_name, item_code, description, assembly, item_h, item_w, item_d, box_h, box_w, box_d, cbm, wood_type, no_of_packet, iron_gauge, mdf_finish, quantity, price_usd, total_price_usd, comments, product_image_name) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    foreach ($products as $index => $product) {
        if (empty($product['item_name']) || empty($product['quantity']) || empty($product['price_usd'])) {
            continue;
        }
        $imageName = null;
        // Check if image file is uploaded for this product index in nested structure
        if (isset($_FILES['products']['name'][$index]['image']) && !empty($_FILES['products']['name'][$index]['image'])) {
            $file = [
                'name' => $_FILES['products']['name'][$index]['image'],
                'type' => $_FILES['products']['type'][$index]['image'],
                'tmp_name' => $_FILES['products']['tmp_name'][$index]['image'],
                'error' => $_FILES['products']['error'][$index]['image'],
                'size' => $_FILES['products']['size'][$index]['image']
            ];
            if ($file['error'] === 0) {
                if ($file['size'] <= $maxFileSize) {
                    $fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                    if (in_array($fileExtension, $allowedExtensions)) {
                        $newFileName = 'product_' . $quotationId . '_' . $index . '_' . time() . '.' . $fileExtension;
                        $targetFilePath = $uploadDir . $newFileName;
                        if (move_uploaded_file($file['tmp_name'], $targetFilePath)) {
                            $imageName = $newFileName;
                        }
                    }
                }
            }
        } elseif (!empty($product['existing_image_name'])) {
            $imageName = $product['existing_image_name'];
        }
        $totalPrice = $product['quantity'] * $product['price_usd'];
        $productStmt->execute([
            $quotationId,
            $product['item_name'],
            $product['item_code'] ?? '',
            $product['description'] ?? '',
            $product['assembly'] ?? '',
            $product['item_h'] ?? null,
            $product['item_w'] ?? null,
            $product['item_d'] ?? null,
            $product['box_h'] ?? null,
            $product['box_w'] ?? null,
            $product['box_d'] ?? null,
            $product['cbm'] ?? null,
            $product['wood_type'] ?? '',
            $product['no_of_packet'] ?? null,
            $product['iron_gauge'] ?? '',
            $product['mdf_finish'] ?? '',
            $product['quantity'],
            $product['price_usd'],
            $totalPrice,
            $product['comments'] ?? '',
            $imageName
        ]);
    }
}

$conn->beginTransaction();

try {
    if (isset($_POST['quotation_id']) && !empty($_POST['quotation_id'])) {
        $quotationId = intval($_POST['quotation_id']);
        $stmt = $conn->prepare("UPDATE quotations SET lead_id = ?, quotation_date = ?, quotation_number = ?, customer_name = ?, customer_email = ?, customer_phone = ?, delivery_term = ?, terms_of_delivery = ? WHERE id = ?");
        $stmt->execute([
            $_POST['lead_id'],
            $_POST['quotation_date'],
            $_POST['quotation_number'],
            $_POST['customer_name'],
            $_POST['customer_email'],
            $_POST['customer_phone'],
            $_POST['delivery_term'],
            $_POST['terms_of_delivery'],
            $quotationId
        ]);
        $statusStmt = $conn->prepare("INSERT INTO quotation_status (quotation_id, status_text, status_date) VALUES (?, ?, ?)");
        $statusStmt->execute([
            $quotationId,
            'Quotation updated',
            date('Y-m-d')
        ]);
        $deleteStmt = $conn->prepare("DELETE FROM quotation_products WHERE quotation_id = ?");
        $deleteStmt->execute([$quotationId]);
        if (!empty($_POST['products'])) {
            insertProducts($conn, $quotationId, $_POST['products'], $uploadDir);
        }
    } else {
        $checkStmt = $conn->prepare("SELECT id FROM quotations WHERE quotation_number = ?");
        $checkStmt->execute([$_POST['quotation_number']]);
        $existingQuotation = $checkStmt->fetch(PDO::FETCH_ASSOC);
        if ($existingQuotation) {
            $quotationId = $existingQuotation['id'];
            $stmt = $conn->prepare("UPDATE quotations SET lead_id = ?, quotation_date = ?, customer_name = ?, customer_email = ?, customer_phone = ?, delivery_term = ?, terms_of_delivery = ? WHERE id = ?");
            $stmt->execute([
                $_POST['lead_id'],
                $_POST['quotation_date'],
                $_POST['customer_name'],
                $_POST['customer_email'],
                $_POST['customer_phone'],
                $_POST['delivery_term'],
                $_POST['terms_of_delivery'],
                $quotationId
            ]);
            $statusStmt = $conn->prepare("INSERT INTO quotation_status (quotation_id, status_text, status_date) VALUES (?, ?, ?)");
            $statusStmt->execute([
                $quotationId,
                'Quotation updated',
                date('Y-m-d')
            ]);
            $deleteStmt = $conn->prepare("DELETE FROM quotation_products WHERE quotation_id = ?");
            $deleteStmt->execute([$quotationId]);
            if (!empty($_POST['products'])) {
                insertProducts($conn, $quotationId, $_POST['products'], $uploadDir);
            }
        } else {
            $stmt = $conn->prepare("INSERT INTO quotations (lead_id, quotation_date, quotation_number, customer_name, customer_email, customer_phone, delivery_term, terms_of_delivery) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $_POST['lead_id'],
                $_POST['quotation_date'],
                $_POST['quotation_number'],
                $_POST['customer_name'],
                $_POST['customer_email'],
                $_POST['customer_phone'],
                $_POST['delivery_term'],
                $_POST['terms_of_delivery']
            ]);
            $quotationId = $conn->lastInsertId();
            $statusStmt = $conn->prepare("INSERT INTO quotation_status (quotation_id, status_text, status_date) VALUES (?, ?, ?)");
            $statusStmt->execute([
                $quotationId,
                'Quotation created',
                date('Y-m-d')
            ]);
            if (!empty($_POST['products'])) {
                insertProducts($conn, $quotationId, $_POST['products'], $uploadDir);
            }
        }
    }
    $conn->commit();
    echo json_encode(['success' => true, 'message' => 'Quotation and products saved successfully.']);
} catch (Exception $e) {
    $conn->rollBack();
    echo json_encode(['success' => false, 'message' => 'Error saving data: ' . $e->getMessage()]);
}
?>
