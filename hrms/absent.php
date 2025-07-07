<?php
session_start();
include './includes/connection.php';
include './includes/header.php';

if (isset($_SESSION['admin']) && $_SESSION['admin'] === true) {
  include 'includes/hr_sidebar.php';
} elseif (isset($_SESSION['hr']) && $_SESSION['hr'] === true) {
  include 'includes/hr_sidebar.php';
} elseif (isset($_SESSION['register_id'])) {
  include 'includes/emp_sidebar.php';
}

$date_today = date('Y-m-d');

// 1️⃣ Get all active employees (not resigned)
$employees = [];
$sql = "SELECT id, employee_id, name, department FROM register WHERE resign = 0";
$result = mysqli_query($conn, $sql);
while ($row = mysqli_fetch_assoc($result)) {
    $employees[$row['employee_id']] = $row;
}

// 2️⃣ Get employee IDs present today (in image_proof)
$present_today = [];
$sql = "SELECT DISTINCT employee_id FROM image_proof WHERE DATE(Date) = '$date_today'";
$result = mysqli_query($conn, $sql);
while ($row = mysqli_fetch_assoc($result)) {
    $present_today[] = $row['employee_id'];
}

// 3️⃣ Find absent employees
$absent_employees = [];
foreach ($employees as $emp_id => $emp) {
    if (!in_array($emp_id, $present_today)) {
        $absent_employees[] = $emp;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Absent Employees</title>
  
</head>
<body>
 <div class="container">
        <div class="page-inner">
            <div class="page-header">
                <h3 class="fw-bold mb-3">Employees</h3>
                <ul class="breadcrumbs mb-3">
                    <li class="nav-home">
                        <a href="dashboard1.php">
                            <i class="icon-home"></i>
                        </a>
                    </li>
                    <li class="separator">
                        <i class="icon-arrow-right"></i>
                    </li>
                    <li class="nav-item">
                        <a href="#">Absent Employees</a>
                    </li>
                </ul>
            </div>




<div class="col-md-12">
    <div class="card">
        <div class="card-header">
            <h4 class="card-title">Presnet Employees </h4>
        </div>
        <div class="card-body">
            <div class="table-responsive">
        <table id="absentTable" class="table table-bordered display">
            <thead>
                <tr style="background: #f2167a; color: white;">
                    <th>Sr No</th>
                    <th>Employee ID</th>
                    <th>Name</th>
                    <th>Department</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $sr = 1;
                foreach ($absent_employees as $emp) {
                    echo "<tr>";
                    echo "<td>" . $sr++ . "</td>";
                    echo "<td>" . htmlspecialchars($emp['employee_id']) . "</td>";
                    echo "<td>" . htmlspecialchars($emp['name']) . "</td>";
                    echo "<td>" . htmlspecialchars($emp['department']) . "</td>";
                    echo "<td><span class='badge bg-danger'>Absent</span></td>";
                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

<script>
$(document).ready(function () {
    $('#absentTable').DataTable();
});
</script>

</body>
</html>
