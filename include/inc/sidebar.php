<?php
// Common dynamic sidebar for superadmin and salesadmin based on session user_type

// Start the session if it's not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Determine user type from session; default to 'guest' if not set
$user_type = $_SESSION['user_type'] ?? 'guest';

// Initialize variables for sidebar links
$base_path = '';
$dashboard_link = '';
$lead_add = '';
$lead_view = '';
$quotation_add = '';
$quotation_view = '';
$customer_view = ''; // Variable for Customer link
$pi_add = '';
$pi_view = '';

// Set links based on user type
if ($user_type === 'superadmin') {
    $base_path = 'superadmin';
    $dashboard_link = BASE_URL . $base_path . '/superadmin_dashboard.php';
    $lead_add = BASE_URL . $base_path . '/sales/lead/add.php';
    $lead_view = BASE_URL . $base_path . '/sales/lead/index.php';
    $quotation_add = BASE_URL . $base_path . '/sales/quotation/add.php';
    $quotation_view = BASE_URL . $base_path . '/sales/quotation/index.php';
    $customer_view = BASE_URL . $base_path . '/sales/customer/index.php';
    $pi_add = BASE_URL . $base_path . '/sales/pi/add.php';
    $pi_view = BASE_URL . $base_path . '/sales/pi/index.php';
} elseif ($user_type === 'salesadmin') {
    $base_path = 'salesadmin';
    $dashboard_link = BASE_URL . $base_path . '/salesadmin_dashboard.php';
    $lead_add = BASE_URL . $base_path . '/sales/lead/add.php';
    $lead_view = BASE_URL . $base_path . '/sales/lead/index.php';
    $quotation_add = BASE_URL . $base_path . '/sales/quotation/add.php';
    $quotation_view = BASE_URL . $base_path . '/sales/quotation/index.php';
    $customer_view = BASE_URL . $base_path . '/sales/customer/index.php';
    $pi_add = BASE_URL . $base_path . '/sales/pi/add.php';
    $pi_view = BASE_URL . $base_path . '/sales/pi/index.php';
} else {
    // For guest or unknown users, set links to '#' or a default page
    $dashboard_link = BASE_URL; // Or a public dashboard
    $lead_add = '#';
    $lead_view = '#';
    $quotation_add = '#';
    $quotation_view = '#';
    $customer_view = '#';
    $pi_add = '#';
    $pi_view = '#';
}
?>

<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="<?php echo BASE_URL; ?>index.html">
        <div class="sidebar-brand-icon rotate-n-15">
            <i class="fas fa-laugh-wink"></i>
        </div>
        <div class="sidebar-brand-text mx-3">SB Admin <sup>2</sup></div>
    </a>

    <hr class="sidebar-divider my-0">

    <li class="nav-item active">
        <a class="nav-link" href="<?php echo $dashboard_link; ?>">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Dashboard</span></a>
    </li>

    <hr class="sidebar-divider">

    <div class="sidebar-heading">
        SALES
    </div>

    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseLead" aria-expanded="true" aria-controls="collapseLead">
            <i class="fas fa-fw fa-bullhorn"></i> <span>Lead</span>
        </a>
        <div id="collapseLead" class="collapse" aria-labelledby="headingLead" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item" href="<?php echo $lead_add; ?>">Add Lead</a>
                <a class="collapse-item" href="<?php echo $lead_view; ?>">View Lead</a>
            </div>
        </div>
    </li>

    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseQuote" aria-expanded="true" aria-controls="collapseQuote">
            <i class="fas fa-fw fa-file-invoice"></i> <span>Quotation</span>
        </a>
        <div id="collapseQuote" class="collapse" aria-labelledby="headingQuote" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item" href="<?php echo $quotation_add; ?>">Add Quotation</a>
                <a class="collapse-item" href="<?php echo $quotation_view; ?>">View Quotation</a>
            </div>
        </div>
    </li>

    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseCustomer" aria-expanded="true" aria-controls="collapseCustomer">
            <i class="fas fa-fw fa-users"></i> <span>Customer</span>
        </a>
        <div id="collapseCustomer" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item" href="<?php echo $customer_view; ?>">Customer</a>
            </div>
        </div>
    </li>

    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapsePerforma" aria-expanded="true" aria-controls="collapsePerforma">
            <i class="fas fa-fw fa-file-invoice-dollar"></i> <span>Performa Invoice</span>
        </a>
        <div id="collapsePerforma" class="collapse" aria-labelledby="headingPerforma" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item" href="<?php echo $pi_add; ?>">Add Performa Invoice</a>
                <a class="collapse-item" href="<?php echo $pi_view; ?>">View Performa Invoice</a>
            </div>
        </div>
    </li>

    <hr class="sidebar-divider">

    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>

</ul>