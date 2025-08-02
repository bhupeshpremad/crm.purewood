<?php
session_start();
include_once __DIR__ . '/../config/config.php';
$user_type = $_SESSION['user_type'] ?? 'guest';

include_once ROOT_DIR_PATH . 'include/inc/header.php';
include_once ROOT_DIR_PATH . 'operationadmin/sidebar.php';

global $conn;

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

$tasks_percent = 50;
$pending_requests = 18;

$module_data = [];

if (has_module_access('bom')) {
    $stmt = $conn->prepare("SELECT COUNT(*) as total FROM bom_main");
    $stmt->execute();
    $module_data['bom'] = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$module_data['bom']) {
        $module_data['bom'] = ['total' => 0];
    }
    $module_data['bom']['pending'] = 0;
    $module_data['bom']['completed'] = 0;

} else {
    $module_data['bom'] = ['total' => 0, 'pending' => 0, 'completed' => 0];
}

if (has_module_access('po')) {
    $stmt = $conn->prepare("SELECT COUNT(*) as total FROM po_main");
    $stmt->execute();
    $module_data['po'] = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$module_data['po']) {
        $module_data['po'] = ['total' => 0];
    }
    $module_data['po']['pending'] = 0;
    $module_data['po']['completed'] = 0;
} else {
    $module_data['po'] = ['total' => 0, 'pending' => 0, 'completed' => 0];
}

if (has_module_access('so')) {
    $stmt = $conn->prepare("SELECT COUNT(*) as total FROM po_main");
    $stmt->execute();
    $module_data['so'] = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$module_data['so']) {
        $module_data['so'] = ['total' => 0];
    }
    $module_data['so']['pending'] = 0;
    $module_data['so']['completed'] = 0;
} else {
    $module_data['so'] = ['total' => 0, 'pending' => 0, 'completed' => 0];
}

if (has_module_access('jci')) {
    $stmt = $conn->prepare("SELECT COUNT(*) as total FROM jci_main");
    $stmt->execute();
    $module_data['jci'] = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$module_data['jci']) {
        $module_data['jci'] = ['total' => 0];
    }
    $module_data['jci']['pending'] = 0;
    $module_data['jci']['completed'] = 0;
} else {
    $module_data['jci'] = ['total' => 0, 'pending' => 0, 'completed' => 0];
}
?>

<body id="page-top">

    <div class="container-fluid">

        <?php include_once ROOT_DIR_PATH . 'include/inc/topbar.php'; ?>

        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Operation Dashboard</h1>
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
                            <?php echo strtoupper($module); ?>
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

    <?php include_once ROOT_DIR_PATH . 'include/inc/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.3.0/dist/chart.umd.min.js"></script>
    <script>
        const moduleLabels = <?php echo json_encode(array_map('strtoupper', array_keys($module_data))); ?>;
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