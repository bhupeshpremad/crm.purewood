<?php
include_once __DIR__ . '/../config/config.php';
session_start();
$user_type = $_SESSION['user_type'] ?? 'guest';

// Include header and sidebar
include_once ROOT_DIR_PATH . 'include/inc/header.php';
include_once ROOT_DIR_PATH . 'accountsadmin/sidebar.php';

// Database connection
global $conn;

// Function to check module access for accountsadmin
function has_module_access($module) {
    global $user_type;
    $accounts_modules = [
        'purchase', 'make_payment'
    ];
    if ($user_type === 'accounts' && in_array($module, $accounts_modules)) {
        return true;
    }
    return false;
}

// Fetch dynamic data for dashboard cards
$earnings_monthly = 0;
$earnings_annual = 0;
$tasks_percent = 0;
$pending_requests = 0;

if (has_module_access('purchase')) {
    $stmt = $conn->prepare("SELECT COUNT(*) as total FROM po_main WHERE MONTH(created_at) = MONTH(CURRENT_DATE())");
    $stmt->execute();
    $purchase_monthly = $stmt->fetchColumn();
    $earnings_monthly += $purchase_monthly ?? 0;
}

if (has_module_access('make_payment')) {
    $stmt = $conn->prepare("SELECT COUNT(*) as total FROM payments WHERE YEAR(created_at) = YEAR(CURRENT_DATE())");
    $stmt->execute();
    $payments_annual = $stmt->fetchColumn();
    $earnings_annual += $payments_annual ?? 0;
}

$tasks_percent = 50;
$pending_requests = 18;
?>

<body id="page-top">

    <div id="wrapper">

        <div id="content-wrapper" class="d-flex flex-column">

            <div id="content">

                <?php include_once ROOT_DIR_PATH . 'include/inc/topbar.php'; ?>

                <div class="container-fluid">

                    <div class="d-sm-flex align-items-center justify-content-between mb-4">
                        <h1 class="h3 mb-0 text-gray-800">Dashboard</h1>
                        <a href="#" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm"><i
                                class="fas fa-download fa-sm text-white-50"></i> Generate Report</a>
                    </div>

                    <div class="row">

                        <?php if (has_module_access('purchase')): ?>
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-primary shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                                Earnings (Monthly)</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">$<?php echo number_format($earnings_monthly, 2); ?></div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-calendar fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>

                        <?php if (has_module_access('make_payment')): ?>
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-success shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                                Earnings (Annual)</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">$<?php echo number_format($earnings_annual, 2); ?></div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>

                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-info shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Tasks
                                            </div>
                                            <div class="row no-gutters align-items-center">
                                                <div class="col-auto">
                                                    <div class="h5 mb-0 mr-3 font-weight-bold text-gray-800"><?php echo $tasks_percent; ?>%</div>
                                                </div>
                                                <div class="col">
                                                    <div class="progress progress-sm mr-2">
                                                        <div class="progress-bar bg-info" role="progressbar"
                                                            style="width: <?php echo $tasks_percent; ?>%" aria-valuenow="<?php echo $tasks_percent; ?>" aria-valuemin="0"
                                                            aria-valuemax="100"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-warning shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                                Pending Requests</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $pending_requests; ?></div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-comments fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-xl-8 col-lg-7">
                            <div class="card shadow mb-4">
                                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                                    <h6 class="m-0 font-weight-bold text-primary">Module Reports Overview</h6>
                                </div>
                                <div class="card-body">
                                    <canvas id="moduleReportChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

            </div>

        </div>

    </div>

    <?php include_once ROOT_DIR_PATH . 'include/inc/footer-top.php'; ?>

    <?php include_once ROOT_DIR_PATH . 'include/inc/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctx = document.getElementById('moduleReportChart').getContext('2d');
        const moduleReportChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['January', 'February', 'March', 'April', 'May', 'June', 'July'],
                datasets: [{
                    label: 'Module Report',
                    data: [12, 19, 3, 5, 2, 3, 7],
                    backgroundColor: 'rgba(78, 115, 223, 0.05)',
                    borderColor: 'rgba(78, 115, 223, 1)',
                    borderWidth: 2
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
</body>
</html>
