<?php
session_start();
include_once __DIR__ . '/../config/config.php';
$user_type = $_SESSION['user_type'] ?? 'guest';

include_once ROOT_DIR_PATH . 'include/inc/header.php';
include_once ROOT_DIR_PATH . 'salesadmin/sidebar.php';

global $conn;

function has_module_access($module) {
    global $user_type;
    $salesadmin_modules = ['lead', 'quotation', 'customer', 'pi'];
    return $user_type === 'salesadmin' && in_array($module, $salesadmin_modules);
}

$module_data = [];

if (has_module_access('lead')) {
    $stmt = $conn->prepare("SELECT
        COUNT(*) as total,
        SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
        SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed
        FROM leads");
    $stmt->execute();
    $module_data['lead'] = $stmt->fetch(PDO::FETCH_ASSOC);
} else {
    $module_data['lead'] = ['total' => 0, 'pending' => 0, 'completed' => 0];
}

if (has_module_access('quotation')) {
    $stmt = $conn->prepare("SELECT
        COUNT(*) as total,
        SUM(CASE WHEN approve = 0 THEN 1 ELSE 0 END) as pending,
        SUM(CASE WHEN approve = 1 THEN 1 ELSE 0 END) as completed
        FROM quotations");
    $stmt->execute();
    $module_data['quotation'] = $stmt->fetch(PDO::FETCH_ASSOC);
} else {
    $module_data['quotation'] = ['total' => 0, 'pending' => 0, 'completed' => 0];
}

if (has_module_access('customer')) {
    $stmt = $conn->prepare("SELECT
        COUNT(*) as total,
        SUM(CASE WHEN approve = 0 THEN 1 ELSE 0 END) as pending,
        SUM(CASE WHEN approve = 1 THEN 1 ELSE 0 END) as completed
        FROM leads");
    $stmt->execute();
    $module_data['customer'] = $stmt->fetch(PDO::FETCH_ASSOC);
} else {
    $module_data['customer'] = ['total' => 0, 'pending' => 0, 'completed' => 0];
}

if (has_module_access('pi')) {
    $stmt = $conn->prepare("SELECT COUNT(*) as total FROM pi");
    $stmt->execute();
    $total = $stmt->fetchColumn();
    $module_data['pi'] = ['total' => (int)$total, 'pending' => 0, 'completed' => 0];
} else {
    $module_data['pi'] = ['total' => 0, 'pending' => 0, 'completed' => 0];
}
?>

<body id="page-top">
    <div id="wrapper" class="container-fluid">
        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                <?php include_once ROOT_DIR_PATH . 'include/inc/topbar.php'; ?>
                <div class="container-fluid">
                    <div class="d-sm-flex align-items-center justify-content-between mb-4">
                        <h1 class="h3 mb-0 text-gray-800">Dashboard</h1>
                        <a href="#" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
                            <i class="fas fa-download fa-sm text-white-50"></i> Generate Report
                        </a>
                    </div>

                    <div class="row">
                        <div class="col-xl-6 col-lg-6">
                            <div class="card shadow mb-4">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-primary">Module Overview (Line Chart)</h6>
                                </div>
                                <div class="card-body">
                                    <div style="position: relative; height: 350px;">
                                        <canvas id="moduleLineChart"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-6 col-lg-6">
                            <div class="card shadow mb-4">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-primary">Module Overview (Pie Chart)</h6>
                                </div>
                                <div class="card-body">
                                    <div style="position: relative; height: 350px;">
                                        <canvas id="modulePieChart"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <?php foreach ($module_data as $module => $counts): ?>
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-primary shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                        <?php echo ucfirst($module); ?>
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table table-sm table-bordered mb-0">
                                            <tbody>
                                                <tr><td>Total</td><td class="text-end"><?php echo $counts['total']; ?></td></tr>
                                                <tr><td>Pending</td><td class="text-end"><?php echo $counts['pending']; ?></td></tr>
                                                <tr><td>Completed</td><td class="text-end"><?php echo $counts['completed']; ?></td></tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <?php include_once ROOT_DIR_PATH . 'include/inc/footer-top.php'; ?>
            <?php include_once ROOT_DIR_PATH . 'include/inc/footer.php'; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.3.0/dist/chart.umd.min.js"></script>
    <script>
        const moduleLabels = <?php echo json_encode(array_map('ucfirst', array_keys($module_data))); ?>;
        const totalData = <?php echo json_encode(array_column($module_data, 'total')); ?>;
        const pendingData = <?php echo json_encode(array_column($module_data, 'pending')); ?>;
        const completedData = <?php echo json_encode(array_column($module_data, 'completed')); ?>;

        const ctxLine = document.getElementById('moduleLineChart').getContext('2d');
        new Chart(ctxLine, {
            type: 'line',
            data: {
                labels: moduleLabels,
                datasets: [
                    {
                        label: 'Total',
                        data: totalData,
                        borderColor: 'rgba(78, 115, 223, 1)',
                        backgroundColor: 'rgba(78, 115, 223, 0.2)',
                        fill: false,
                        tension: 0.3
                    },
                    {
                        label: 'Pending',
                        data: pendingData,
                        borderColor: 'rgba(255, 193, 7, 1)',
                        backgroundColor: 'rgba(255, 193, 7, 0.2)',
                        fill: false,
                        tension: 0.3
                    },
                    {
                        label: 'Completed',
                        data: completedData,
                        borderColor: 'rgba(40, 167, 69, 1)',
                        backgroundColor: 'rgba(40, 167, 69, 0.2)',
                        fill: false,
                        tension: 0.3
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'top' }
                },
                scales: {
                    y: { beginAtZero: true },
                    x: {
                        ticks: {
                            autoSkip: false,
                            maxRotation: 45,
                            minRotation: 45
                        }
                    }
                }
            }
        });

        const ctxPie = document.getElementById('modulePieChart').getContext('2d');
        new Chart(ctxPie, {
            type: 'pie',
            data: {
                labels: moduleLabels,
                datasets: [{
                    data: totalData,
                    backgroundColor: [
                        'rgba(78, 115, 223, 0.7)',
                        'rgba(255, 193, 7, 0.7)',
                        'rgba(40, 167, 69, 0.7)',
                        'rgba(220, 53, 69, 0.7)'
                    ],
                    borderColor: [
                        'rgba(78, 115, 223, 1)',
                        'rgba(255, 193, 7, 1)',
                        'rgba(40, 167, 69, 1)',
                        'rgba(220, 53, 69, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'right',
                        labels: {
                            generateLabels: function(chart) {
                                const data = chart.data;
                                return data.labels.map((label, i) => ({
                                    text: `${label}: ${data.datasets[0].data[i]}`,
                                    fillStyle: data.datasets[0].backgroundColor[i],
                                    strokeStyle: data.datasets[0].borderColor[i],
                                    lineWidth: 1,
                                    hidden: false,
                                    index: i
                                }));
                            }
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return `${context.label}: ${context.parsed}`;
                            }
                        }
                    }
                }
            }
        });
    </script>
</body>
</html>
