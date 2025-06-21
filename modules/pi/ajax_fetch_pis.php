<?php
include_once __DIR__ . '/../../config/config.php';

header('Content-Type: application/json');

try {
    global $conn;

    $page = isset($_POST['page']) ? (int)$_POST['page'] : 1;
    $recordsPerPage = isset($_POST['records_per_page']) ? (int)$_POST['records_per_page'] : 10;
    $search = isset($_POST['search']) ? trim($_POST['search']) : '';

    $offset = ($page - 1) * $recordsPerPage;

    $searchQuery = '';
    $params = [];

    if ($search !== '') {
        $searchQuery = "WHERE pi_number LIKE :search OR quotation_number LIKE :search OR status LIKE :search";
        $params[':search'] = '%' . $search . '%';
    }

    // Count total records
    $countSql = "SELECT COUNT(*) FROM pi $searchQuery";
    $countStmt = $conn->prepare($countSql);
    $countStmt->execute($params);
    $totalRecords = (int)$countStmt->fetchColumn();

    // Fetch paginated data
    $dataSql = "SELECT pi_id, pi_number, quotation_number, status, date_of_pi_raised
                FROM pi
                $searchQuery
                ORDER BY pi_id DESC
                LIMIT :offset, :limit";

    $dataStmt = $conn->prepare($dataSql);

    foreach ($params as $key => $val) {
        $dataStmt->bindValue($key, $val, PDO::PARAM_STR);
    }
    $dataStmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $dataStmt->bindValue(':limit', $recordsPerPage, PDO::PARAM_INT);

    $dataStmt->execute();
    $pis = $dataStmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'data' => [
            'pis' => $pis,
            'total_records' => $totalRecords
        ]
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error fetching PIs: ' . $e->getMessage()
    ]);
}
?>
