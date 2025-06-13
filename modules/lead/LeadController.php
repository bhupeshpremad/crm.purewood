<?php
header('Content-Type: application/json');
include_once '../../../config/config.php';
include_once '../../../core/services/LeadService.php';

$database = new Database();
$conn = $database->getConnection();
$leadService = new LeadService($conn);

$response = ['success' => false, 'message' => 'Unknown error occurred'];

$action = $_POST['action'] ?? $_GET['action'] ?? '';

switch ($action) {
    case 'get_next_lead_number':
        try {
            $nextLeadNumber = $leadService->getNextLeadNumber();
            $response['success'] = true;
            $response['lead_number'] = $nextLeadNumber;
        } catch (Exception $e) {
            $response['message'] = 'Error: ' . $e->getMessage();
        }
        break;

    case 'get_lead':
        $lead_id = $_POST['lead_id'] ?? null;
        if ($lead_id) {
            $lead = $leadService->getLeadById($lead_id);
            if ($lead) {
                $response['success'] = true;
                $response['lead'] = $lead;
            } else {
                $response['message'] = 'Lead not found.';
            }
        } else {
            $response['message'] = 'Missing lead ID.';
        }
        break;

    case 'get_status_history':
        $lead_id = $_POST['lead_id'] ?? null;
        if ($lead_id) {
            $statusHistory = $leadService->getStatusHistory($lead_id);
            if ($statusHistory['success']) {
                $response['success'] = true;
                $response['statuses'] = $statusHistory['data'];
            } else {
                $response['message'] = $statusHistory['message'];
            }
        } else {
            $response['message'] = 'Missing lead ID.';
        }
        break;

    case 'create':
    case 'update':
        $lead_id = $_POST['lead_id'] ?? null;
        $data = [
            'lead_number' => $_POST['lead_number'] ?? '',
            'entry_date' => $_POST['entry_date'] ?? '',
            'company_name' => $_POST['company_name'] ?? '',
            'contact_name' => $_POST['contact_person'] ?? '',
            'contact_phone' => $_POST['phone'] ?? '',
            'contact_email' => $_POST['email'] ?? '',
            'country' => $_POST['country'] ?? '',
            'state' => $_POST['state'] ?? '',
            'city' => $_POST['city'] ?? '',
            'lead_source' => $_POST['lead_source'] ?? ''
        ];

        if ($action === 'create') {
            $result = $leadService->createLead($data);
        } else {
            if ($lead_id) {
                $result = $leadService->updateLead($lead_id, $data);
            } else {
                $result = ['success' => false, 'message' => 'Missing lead ID for update.'];
            }
        }
        $response = $result;
        break;

    case 'add_status':
        $lead_id = $_POST['lead_id'] ?? null;
        $status_text = $_POST['status_text'] ?? '';
        $status_date = $_POST['status_date'] ?? '';
        if ($lead_id && $status_text && $status_date) {
            $result = $leadService->addStatus($lead_id, $status_text, $status_date);
            $response = $result;
        } else {
            $response['message'] = 'Please fill all status fields.';
        }
        break;

    case 'toggle_status':
        $lead_id = $_POST['lead_id'] ?? null;
        $status = $_POST['status'] ?? null;
        if ($lead_id && $status) {
            // Assuming toggle status is updating the status field in leads table
            $result = $leadService->updateLead($lead_id, ['status' => $status]);
            $response = $result;
        } else {
            $response['message'] = 'Missing lead ID or status.';
        }
        break;

    case 'toggle_approve':
        $lead_id = $_POST['lead_id'] ?? null;
        $approve = $_POST['approve'] ?? null;
        if ($lead_id !== null && $approve !== null) {
            $result = $leadService->toggleApprove($lead_id, $approve);
            $response = $result;
        } else {
            $response['message'] = 'Missing lead ID or approval value.';
        }
        break;

    default:
        $response['message'] = 'Invalid request.';
        break;
}

echo json_encode($response);
exit;
?>
