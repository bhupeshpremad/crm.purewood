<?php
    include_once __DIR__ . '/../config/config.php'; 
    include_once __DIR__ . '/../include/inc/header.php';
?>

<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="<?php echo BASE_URL; ?>accountsadmin/accounts_dashboard.php">
        <div class="sidebar-brand-icon rotate-n-15">
            <i class="fas fa-calculator"></i>
        </div>
        <div class="sidebar-brand-text mx-3">
            <?php
            $username = $_SESSION['username'] ?? null;
            echo 'Accounts Admin';
            if ($username) {
                echo ' - ' . htmlspecialchars($username);
            }
            ?>
        </div>
    </a>

    <hr class="sidebar-divider my-0">

    <li class="nav-item active">
        <a class="nav-link" href="<?php echo BASE_URL; ?>accountsadmin/accounts_dashboard.php">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Dashboard</span></a>
    </li>

    <hr class="sidebar-divider">

    <div class="sidebar-heading">
        Accounts Modules
    </div>

    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapsePurchase" aria-expanded="false" aria-controls="collapsePurchase">
            <i class="fas fa-fw fa-shopping-bag"></i> <span>Purchase</span>
        </a>
        <div id="collapsePurchase" class="collapse" aria-labelledby="headingPurchase" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item" href="<?php echo BASE_URL; ?>modules/purchase/add.php">Add Purchase</a>
                <a class="collapse-item" href="<?php echo BASE_URL; ?>modules/purchase/index.php">View Purchase</a>
            </div>
        </div>
    </li>

    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapsePayment" aria-expanded="false" aria-controls="collapsePayment">
            <i class="fas fa-fw fa-credit-card"></i> <span>Make Payment</span>
        </a>
        <div id="collapsePayment" class="collapse" aria-labelledby="headingPayment" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item" href="<?php echo BASE_URL; ?>modules/payments/add.php">Add Payment</a>
                <a class="collapse-item" href="<?php echo BASE_URL; ?>modules/payments/index.php">View Payments</a>
            </div>
        </div>
    </li>

    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>
</ul>