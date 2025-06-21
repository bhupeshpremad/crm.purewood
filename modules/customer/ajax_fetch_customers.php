<?php
include_once __DIR__ . '/../../config/config.php';

header('Content-Type: application/json');

try {
    global $conn;

    $page = isset($_POST['page']) ? (int)$_POST['page'] : 1;
    $recordsPerPage = isset($_POST['records_per_page']) ? (int)$_POST['records_per_page'] : 20;
    $search = isset($_POST['search']) ? trim($_POST['search']) : '';

    $offset = ($page - 1) * $recordsPerPage;

    $searchQuery = '';
    $params = [];

    if ($search !== '') {
        $searchQuery = "WHERE l.company_name LIKE :search OR l.contact_email LIKE :search OR l.contact_phone LIKE :search";
        $params[':search'] = '%' . $search . '%';
    }

    // Count total records
    $countSql = "SELECT COUNT(DISTINCT l.id) as total FROM leads l
                 LEFT JOIN quotations q ON q.lead_id = l.id
                 LEFT JOIN pi p ON p.quotation_id = q.id
                 $searchQuery";

    $countStmt = $conn->prepare($countSql);
    $countStmt->execute($params);
    $totalRecords = (int)$countStmt->fetchColumn();

    // Fetch paginated data
    $dataSql = "SELECT 
                    l.id as lead_id, 
                    l.company_name, 
                    l.contact_email, 
                    l.contact_phone, 
                    COUNT(DISTINCT l.id) as total_leads,
                    COUNT(DISTINCT q.id) as total_quotations,
                    COALESCE(pc.pi_count, 0) as total_pis
                FROM leads l
                LEFT JOIN quotations q ON q.lead_id = l.id
                LEFT JOIN (
                    SELECT q.lead_id, COUNT(p.pi_id) as pi_count
                    FROM pi p
                    INNER JOIN quotations q ON p.quotation_id = q.id
                    GROUP BY q.lead_id
                ) pc ON pc.lead_id = l.id
                $searchQuery
                GROUP BY l.id, l.company_name, l.contact_email, l.contact_phone
                ORDER BY l.company_name ASC
                LIMIT :offset, :limit";

    $dataStmt = $conn->prepare($dataSql);

    // Bind parameters
    foreach ($params as $key => $val) {
        $dataStmt->bindValue($key, $val, PDO::PARAM_STR);
    }
    $dataStmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $dataStmt->bindValue(':limit', $recordsPerPage, PDO::PARAM_INT);

    $dataStmt->execute();
    $customers = $dataStmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'data' => [
            'customers' => $customers,
            'total_records' => $totalRecords
        ]
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error fetching customers: ' . $e->getMessage()
    ]);
}
?>
