<?php
session_start();
include_once __DIR__ . '/../config/config.php';
$user_type = $_SESSION['user_type'] ?? 'guest';

// Include header and sidebar
include_once ROOT_DIR_PATH . 'include/inc/header.php';
include_once ROOT_DIR_PATH . 'operationadmin/sidebar.php';

// Database connection
global $conn;

// Function to check module access for operationadmin
function has_module_access($module) {
    global $user_type;
    $operation_modules = [
        'bom', 'po', 'so', 'jci'
    ];
    if ($user_type === 'operation' && in_array($module, $operation_modules)) {
        return true;
    }
    return false;
}

// Fetch dynamic data for dashboard cards
$tasks_percent = 50;
$pending_requests = 18;

?>

<body id="page-top">

    <div class="container-fluid">

        <?php include_once ROOT_DIR_PATH . 'include/inc/topbar.php'; ?>

        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Operation Dashboard</h1>
        </div>

        <div class="row">

            <?php if (has_module_access('bom')): ?>
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">BOM</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">Bill of Materials</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <?php if (has_module_access('po')): ?>
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-success shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">PO</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">Purchase Orders</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-shopping-cart fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <?php if (has_module_access('so')): ?>
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-info shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">SO</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">Sale Orders</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-receipt fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <?php if (has_module_access('jci')): ?>
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-warning shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">JCI</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">Job Card Instructions</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-tasks fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>

        </div>

    </div>

    <?php include_once ROOT_DIR_PATH . 'include/inc/footer.php'; ?>
</body>
</html>
