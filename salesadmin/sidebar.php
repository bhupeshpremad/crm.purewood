
<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="<?php echo BASE_URL; ?>salesadmin/salesadmin_dashboard.php">
        <div class="sidebar-brand-icon rotate-n-15">
            <i class="fas fa-laugh-wink"></i>
        </div>
        <div class="sidebar-brand-text mx-3">
            <?php
            $username = $_SESSION['username'] ?? null;
            echo 'Sales Admin';
            if ($username) {
                echo ' - ' . htmlspecialchars($username);
            }
            ?>
        </div>
    </a>

    <hr class="sidebar-divider my-0">

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
                <a class="collapse-item" href="<?php echo BASE_URL; ?>salesadmin/sales/lead/add.php">Add Lead</a>
                <a class="collapse-item" href="<?php echo BASE_URL; ?>salesadmin/sales/lead/index.php">View Lead</a>
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
                <a class="collapse-item" href="<?php echo BASE_URL; ?>salesadmin/sales/quotation/add.php">Add Quotation</a>
                <a class="collapse-item" href="<?php echo BASE_URL; ?>salesadmin/sales/quotation/index.php">View Quotation</a>
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
                <a class="collapse-item" href="<?php echo BASE_URL; ?>salesadmin/sales/customer/index.php">View Customer Details</a>
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
                <a class="collapse-item" href="<?php echo BASE_URL; ?>salesadmin/sales/pi/index.php">View Performa Invoice</a>
            </div>
        </div>
    </li>

    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>
</ul>