<?php
    include_once __DIR__ . '/../config/config.php'; 
    include_once __DIR__ . '/../include/inc/header.php';
?>

<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="<?php echo BASE_URL; ?>superadmin/superadmin_dashboard.php">
        <div class="sidebar-brand-icon rotate-n-15">
            <i class="fas fa-laugh-wink"></i>
        </div>
        <div class="sidebar-brand-text mx-3">SB Admin <sup>2</sup></div>
    </a>

    <hr class="sidebar-divider my-0">

    <li class="nav-item active">
        <a class="nav-link" href="<?php echo BASE_URL; ?>superadmin/superadmin_dashboard.php">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Dashboard</span></a>
    </li>

    <hr class="sidebar-divider">

    <div class="sidebar-heading">
        SALES
    </div>

    <!-- Lead -->
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseLead" aria-expanded="true" aria-controls="collapseLead">
            <i class="fas fa-fw fa-bullhorn"></i> <span>Lead</span>
        </a>
        <div id="collapseLead" class="collapse" aria-labelledby="headingLead" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item" href="<?php echo BASE_URL; ?>superadmin/sales/lead/add.php">Add Lead</a>
                <a class="collapse-item" href="<?php echo BASE_URL; ?>superadmin/sales/lead/index.php">View Lead</a>
            </div>
        </div>
    </li>

    <!-- Quotation -->
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseQuote" aria-expanded="true" aria-controls="collapseQuote">
            <i class="fas fa-fw fa-file-invoice"></i> <span>Quotation</span>
        </a>
        <div id="collapseQuote" class="collapse" aria-labelledby="headingQuote" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item" href="<?php echo BASE_URL; ?>superadmin/sales/quotation/add.php">Add Quotation</a>
                <a class="collapse-item" href="<?php echo BASE_URL; ?>superadmin/sales/quotation/index.php">View Quotation</a>
            </div>
        </div>
    </li>

    <!-- Customer -->
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseCustomer" aria-expanded="true" aria-controls="collapseCustomer">
            <i class="fas fa-fw fa-users"></i> <span>Customer</span>
        </a>
        <div id="collapseCustomer" class="collapse" aria-labelledby="headingCustomer" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item" href="<?php echo BASE_URL; ?>superadmin/sales/customer/index.php">View Customer Details</a>
            </div>
        </div>
    </li>

    <!-- Performa Invoice -->
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapsePerforma" aria-expanded="true" aria-controls="collapsePerforma">
            <i class="fas fa-fw fa-file-invoice-dollar"></i> <span>Performa Invoice</span>
        </a>
        <div id="collapsePerforma" class="collapse" aria-labelledby="headingPerforma" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item" href="<?php echo BASE_URL; ?>superadmin/sales/pi/index.php">View Performa Invoice</a>
            </div>
        </div>
    </li>

    <hr class="sidebar-divider">

    <div class="sidebar-heading">
        Accounts
    </div>
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseAccounts" aria-expanded="true" aria-controls="collapseAccounts">
            <i class="fas fa-fw fa-file-invoice-dollar"></i> <span>Purchase</span>
        </a>
        <div id="collapseAccounts" class="collapse" aria-labelledby="headingAccounts" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item" href="#">Add Job Card</a>
            </div>
        </div>
    </li>

    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapsePayment" aria-expanded="true" aria-controls="collapsePayment">
            <i class="fas fa-fw fa-file-invoice-dollar"></i> <span>Make Payment</span>
        </a>
        <div id="collapsePayment" class="collapse" aria-labelledby="headingPayment" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item" href="<?php echo BASE_URL; ?>superadmin/accounts/payment/add.php">Add Make Payment</a>
                <a class="collapse-item" href="<?php echo BASE_URL; ?>superadmin/accounts/payment/index.php">View Make Payment</a>
            </div>
        </div>
    </li>

    <hr class="sidebar-divider d-none d-md-block">

    <div class="sidebar-heading">
        Production
    </div>
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseProduction" aria-expanded="true" aria-controls="collapseProduction">
            <i class="fas fa-fw fa-file-invoice-dollar"></i> <span>Production</span>
        </a>
        <div id="collapseProduction" class="collapse" aria-labelledby="headingProduction" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item" href="#">Production</a>
            </div>
        </div>
    </li>

    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>
</ul>