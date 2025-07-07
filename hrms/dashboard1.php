<?php
session_start();
include './includes/connection.php';
include './includes/header.php';

if (isset($_SESSION['admin']) && $_SESSION['admin'] === true) {
    include 'includes/hr_sidebar.php'; // Super Admin gets HR sidebar
} elseif (isset($_SESSION['hr']) && $_SESSION['hr'] === true) {
    include 'includes/hr_sidebar.php'; // HR gets HR sidebar
} elseif (isset($_SESSION['register_id'])) {
    include 'includes/emp_sidebar.php'; // Regular employee
}
// else{
//     header('Location: login.php');
//     exit(); 
// }




$yearMonth = date('Y-m');  // e.g., 2025-06
$daysInMonth = date('t', strtotime($yearMonth . '-01'));
$presentData = [];
$labels = [];
$markers = [];  // For Sundays + holidays

for ($day = 1; $day <= $daysInMonth; $day++) {
    $date = sprintf('%s-%02d', $yearMonth, $day);
    $labels[] = $day;

    // Get present count
    $result = $conn->query("SELECT COUNT(*) AS present_count FROM image_proof WHERE DATE(Date) = '$date'");
    $row = $result->fetch_assoc();
    $presentData[] = $row ? (int)$row['present_count'] : 0;

    // Check if Sunday
    $dayName = date('l', strtotime($date));
    if ($dayName === 'Sunday') {
        $markers[] = [
            'day' => $day,
            'label' => 'Sunday'
        ];
    }

    // Check holidays table
    $resHoliday = $conn->query("SELECT reason FROM holidays WHERE date = '$date'");
    if ($resHoliday && $holidayRow = $resHoliday->fetch_assoc()) {
        $markers[] = [
            'day' => $day,
            'label' => $holidayRow['holiday']
        ];
    }
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>Crawlers -  Dashboard</title>
    <meta content="width=device-width, initial-scale=1.0, shrink-to-fit=no" name="viewport" />

</head>

<body>
    <!-- Sidebar -->

    <!-- End Sidebar -->

        <div class="container">
            <div class="page-inner">
                <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-2 pb-4">
                    <div>
                        <h3 class="fw-bold mb-3">Dashboard</h3>
                        <h6 class="op-7 mb-2">Crawlers Dashboard</h6>
                    </div>
                    <div class="ms-md-auto py-2 py-md-0">
                        <a href="#" class="btn btn-label-info btn-round me-2">Manage</a>
                        <a href="#" class="btn btn-primary btn-round">Add Customer</a>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-6 col-md-3">
                        <div class="card card-stats card-round">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col-icon">
                                        <div class="icon-big text-center icon-primary bubble-shadow-small">
                                            <i class="fas fa-users"></i>
                                        </div>
                                    </div>
                                    <div class="col col-stats ms-3 ms-sm-0">
                                        <div class="numbers">
                                            <p class="card-category">Employees</p>
                                            <h4 class="card-title" id="totalUsers">
                                                <?php
                                                $result = $conn->query("SELECT COUNT(*) AS total FROM register WHERE resign = 0");
                                                $row = $result->fetch_assoc();
                                                $totalUsers = $row ? $row['total'] : 0;
                                                echo $totalUsers;
                                                ?>
                                            </h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-md-3">
                        <div class="card card-stats card-round">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col-icon">
                                        <div class="icon-big text-center icon-info bubble-shadow-small">
                                            <i class="fas fa-user-check"></i>
                                        </div>
                                    </div>
                                    <div class="col col-stats ms-3 ms-sm-0">
                                        <div class="numbers">
                                            <p class="card-category">Present</p>
                                            <h4 class="card-title" id="presentEmployees">
                                                <?php
                                                $dateToday = date("Y-m-d");
                                                $result = $conn->query("SELECT COUNT(*) as present_employees FROM image_proof WHERE  DATE(Date) = '$dateToday' ");
                                                $row = $result->fetch_assoc();
                                                $presentEmployees = $row ? $row['present_employees'] : 0;
                                                echo $presentEmployees;
                                                ?>
                                            </h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-md-3">
                        <div class="card card-stats card-round">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col-icon">
                                        <div class="icon-big text-center icon-success bubble-shadow-small">
                                            <i class="fas fa-luggage-cart"></i>
                                        </div>
                                    </div>
                                    <div class="col col-stats ms-3 ms-sm-0">
                                        <div class="numbers">
                                            <p class="card-category">absent</p>
                                            <h4 class="card-title" id="absentEmployees">
                                                <?php
                                                $absentEmployees = $totalUsers - $presentEmployees;
                                                echo $absentEmployees >= 0 ? $absentEmployees : 0; // prevent negative
                                                ?>
                                            </h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-md-3">
                        <div class="card card-stats card-round">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col-icon">
                                        <div class="icon-big text-center icon-secondary bubble-shadow-small">
                                            <i class="far fa-check-circle"></i>
                                        </div>
                                    </div>
                                    <div class="col col-stats ms-3 ms-sm-0">
                                        <div class="numbers">
                                            <p class="card-category">Late</p>
                                            <h4 class="card-title">
                                                <?php
                                                 $result = $conn->query("SELECT COUNT(*) as late_count FROM image_proof WHERE  DATE(Date) = '$dateToday' and TIME(`login_time`) > '09:45:00'");
                                                $row = $result->fetch_assoc();
                                                $late_count = $row ? $row['late_count'] : 0;
                                                echo $late_count;
                                                ?>
                                            </h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- In your HTML section -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="card card-round">
                            <div class="card-header">
                                <div class="card-title">Employees Daily Status (In Percentage)</div>
                            </div>
                            <div class="card-body">
                                <div class="d-flex justify-content-center">
                                    <div style="width: 80%; height: 300px">
                                        <canvas id="dailyStatusChart"></canvas>
                                    </div>
                                </div>
                                <div class="text-center mt-3">
                                    <div class="d-inline-block mx-3">
                                        <span class="badge badge-primary">■</span> Present: <span id="presentPercent"><?php echo round(($presentEmployees / $totalUsers) * 100, 2) ?>%</span>
                                    </div>
                                    <div class="d-inline-block mx-3">
                                        <span class="badge badge-danger">■</span> Absent: <span id="absentPercent"><?php echo round(($absentEmployees / $totalUsers) * 100, 2) ?>%</span>
                                    </div>
                                    <div class="d-inline-block mx-3">
                                        <span class="badge badge-warning">■</span> Late: <span id="latePercent"><?php echo round(($late_count / $totalUsers) * 100, 2) ?>%</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card card-round">
                            <div class="card-header">
                                <div class="card-title">Employees Monthly Status</div>
                                <div class="card-tools">
                                    <select id="monthSelect" class="form-select form-select-sm me-2" style="width:auto; display:inline-block;">
                                        <option value="<?php echo date('Y-m'); ?>" selected><?php echo date('M Y'); ?></option>
                                        <option value="<?php echo date('Y-m', strtotime('-1 month')); ?>"><?php echo date('M Y', strtotime('-1 month')); ?></option>
                                    </select>
                                    <select id="statusSelect" class="form-select form-select-sm" style="width:auto; display:inline-block;">
                                        <option value="Present" selected>Present</option>
                                        <option value="Absent">Absent</option>
                                    </select>
                                </div>
                            </div>
                            <div class="card-body">
                                <div style="height: 300px">
                                    <canvas id="monthlyStatusChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>



            </div>

          <div class="row">
    <div class="col-md-12">
        <div class="card card-round">
           <div class="card-header d-flex  align-items-center">
    <h5 class="mb-0">Employees Informations (Last 5 Records) </h5>
    <a href="alldata.php" class=" btn-sm">For More Details Click Here <i class="fas fa-chevron-right ml-1"></i>
    </a>
</div>
            <div class="card-body">
                <div class="table-responsive" style="overflow-x: auto; max-width: 100%">
                    <table id="userTable" class="display table table-sm table-bordered table-striped" style="width: 100%">
                        <thead class="head">
                            <tr>
                                <th scope="col" style="min-width: 30px">ID</th>
                                <th scope="col" style="min-width: 100px">Name</th>
                                <th scope="col" style="min-width: 80px">Emp ID</th>
                                <th scope="col" style="min-width: 80px">Dept</th>
                                <th scope="col" style="min-width: 80px">Joining</th>
                                <th scope="col" style="min-width: 80px">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $result = $conn->query("SELECT * FROM register WHERE resign = 0 ORDER BY id DESC LIMIT 5");
                            while ($row = $result->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td>{$row['id']}</td>";
                                echo "<td>{$row['name']}</td>";
                                echo "<td>{$row['employee_id']}</td>";
                                echo "<td>{$row['department']}</td>";
                                echo "<td>" . date('d/m/Y', strtotime($row['joining_date'])) . "</td>";
                                echo "<td>
                                        <button class='btn btn-sm btn-primary text-white' >
                                        <a class='text-white' href = 'alldata.php'>View</a></button>
                                      </td>";
                                echo "</tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

        </div>

       
         <?php
include './includes/footer.php';
?>
        </div>

        <!-- Custom template | don't include it in your project! -->
        <div class="custom-template">
            <div class="title">Settings</div>
            <div class="custom-content">
                <div class="switcher">
                    <div class="switch-block">
                        <h4>Logo Header</h4>
                        <div class="btnSwitch">
                            <button type="button" class="selected changeLogoHeaderColor" data-color="dark"></button>
                            <button type="button" class="changeLogoHeaderColor" data-color="blue"></button>
                            <button type="button" class="changeLogoHeaderColor" data-color="purple"></button>
                            <button type="button" class="changeLogoHeaderColor" data-color="light-blue"></button>
                            <button type="button" class="changeLogoHeaderColor" data-color="green"></button>
                            <button type="button" class="changeLogoHeaderColor" data-color="orange"></button>
                            <button type="button" class="changeLogoHeaderColor" data-color="red"></button>
                            <button type="button" class="changeLogoHeaderColor" data-color="white"></button>
                            <br />
                            <button type="button" class="changeLogoHeaderColor" data-color="dark2"></button>
                            <button type="button" class="changeLogoHeaderColor" data-color="blue2"></button>
                            <button type="button" class="changeLogoHeaderColor" data-color="purple2"></button>
                            <button type="button" class="changeLogoHeaderColor" data-color="light-blue2"></button>
                            <button type="button" class="changeLogoHeaderColor" data-color="green2"></button>
                            <button type="button" class="changeLogoHeaderColor" data-color="orange2"></button>
                            <button type="button" class="changeLogoHeaderColor" data-color="red2"></button>
                        </div>
                    </div>
                    <div class="switch-block">
                        <h4>Navbar Header</h4>
                        <div class="btnSwitch">
                            <button type="button" class="changeTopBarColor" data-color="dark"></button>
                            <button type="button" class="changeTopBarColor" data-color="blue"></button>
                            <button type="button" class="changeTopBarColor" data-color="purple"></button>
                            <button type="button" class="changeTopBarColor" data-color="light-blue"></button>
                            <button type="button" class="changeTopBarColor" data-color="green"></button>
                            <button type="button" class="changeTopBarColor" data-color="orange"></button>
                            <button type="button" class="changeTopBarColor" data-color="red"></button>
                            <button type="button" class="selected changeTopBarColor" data-color="white"></button>
                            <br />
                            <button type="button" class="changeTopBarColor" data-color="dark2"></button>
                            <button type="button" class="changeTopBarColor" data-color="blue2"></button>
                            <button type="button" class="changeTopBarColor" data-color="purple2"></button>
                            <button type="button" class="changeTopBarColor" data-color="light-blue2"></button>
                            <button type="button" class="changeTopBarColor" data-color="green2"></button>
                            <button type="button" class="changeTopBarColor" data-color="orange2"></button>
                            <button type="button" class="changeTopBarColor" data-color="red2"></button>
                        </div>
                    </div>
                    <div class="switch-block">
                        <h4>Sidebar</h4>
                        <div class="btnSwitch">
                            <button type="button" class="changeSideBarColor" data-color="white"></button>
                            <button type="button" class="selected changeSideBarColor" data-color="dark"></button>
                            <button type="button" class="changeSideBarColor" data-color="dark2"></button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="custom-toggle">
                <i class="icon-settings"></i>
            </div>
        </div>
        <!-- End Custom template -->
     </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
   <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize charts
        let dailyChart, monthlyChart;
        
        // Daily Status Pie Chart
        const dailyCtx = document.getElementById('dailyStatusChart').getContext('2d');
        dailyChart = new Chart(dailyCtx, {
            type: 'doughnut',
            data: {
                labels: ['Present', 'Absent', 'Late'],
                datasets: [{
                    data: [
                        <?php echo $presentEmployees; ?>,
                        <?php echo $absentEmployees; ?>,
                        0 // Late employees - replace with dynamic value if available
                    ],
                    backgroundColor: [
                        '#4CAF50', // Green
                        '#F44336', // Red
                        '#FF9800' // Orange
                    ],
                    borderWidth: 0,
                    cutout: '70%'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.raw || 0;
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = Math.round((value / total) * 100);
                                return `${label}: ${percentage}% (${value})`;
                            }
                        }
                    }
                }
            }
        });

        // Monthly Status Bar Chart - Initial chart
        const monthlyCtx = document.getElementById('monthlyStatusChart').getContext('2d');
        monthlyChart = new Chart(monthlyCtx, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($labels); ?>,
                datasets: [{
                    label: 'Present Employees',
                    data: <?php echo json_encode($presentData); ?>,
                    backgroundColor: '#4CAF50',
                    borderWidth: 0,
                    borderRadius: 4,
                    borderSkipped: false
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        max: <?php echo $totalUsers; ?>,
                        grid: {
                            display: true,
                            drawBorder: false
                        },
                        ticks: {
                            stepSize: 1,
                            callback: function(value) {
                                if (value % 1 === 0) {
                                    return value;
                                }
                            }
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return `Present: ${context.raw}`;
                            },
                            afterLabel: function(context) {
                                const total = <?php echo $totalUsers; ?>;
                                const present = context.raw;
                                const absent = total - present;
                                return `Absent: ${absent}\nTotal: ${total}`;
                            }
                        }
                    }
                }
            }
        });

        // Event listener for month selection
        document.getElementById('monthSelect').addEventListener('change', function() {
            const selectedMonth = this.value;
            fetchMonthlyData(selectedMonth);
        });

        // Event listener for status selection
        document.getElementById('statusSelect').addEventListener('change', function() {
            const selectedMonth = document.getElementById('monthSelect').value;
            fetchMonthlyData(selectedMonth);
        });

        // Function to fetch monthly data
        function fetchMonthlyData(month) {
            const statusType = document.getElementById('statusSelect').value;
            
            fetch('get_monthly_data.php?month=' + month + '&status=' + statusType)
                .then(response => response.json())
                .then(data => {
                    // Update the chart with new data
                    monthlyChart.data.labels = data.labels;
                    monthlyChart.data.datasets[0].data = data.data;
                    monthlyChart.data.datasets[0].label = statusType + ' Employees';
                    monthlyChart.data.datasets[0].backgroundColor = statusType === 'Present' ? '#4CAF50' : '#F44336';
                    monthlyChart.options.scales.y.max = data.totalUsers;
                    
                    // Update tooltip callback based on status
                    monthlyChart.options.plugins.tooltip.callbacks = {
                        label: function(context) {
                            return `${statusType}: ${context.raw}`;
                        },
                        afterLabel: function(context) {
                            const total = data.totalUsers;
                            const count = context.raw;
                            const opposite = total - count;
                            const oppositeStatus = statusType === 'Present' ? 'Absent' : 'Present';
                            return `${oppositeStatus}: ${opposite}\nTotal: ${total}`;
                        }
                    };
                    
                    monthlyChart.update();
                })
                .catch(error => console.error('Error:', error));
        }

        // Update percentages when data changes
        function updatePercentages() {
            const total = <?php echo $totalUsers; ?>;
            const present = <?php echo $presentEmployees; ?>;
            const absent = <?php echo $absentEmployees; ?>;

            document.getElementById('presentPercent').textContent = Math.round((present / total) * 100) + '%';
            document.getElementById('absentPercent').textContent = Math.round((absent / total) * 100) + '%';
        }
    });
</script>

</body>

</html>