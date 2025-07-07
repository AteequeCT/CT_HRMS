<?php
require 'includes/connection.php';

// Pagination parameters
$start = isset($_GET['start']) ? (int)$_GET['start'] : 0;
$length = isset($_GET['length']) ? (int)$_GET['length'] : 50;

// Get total records count
$countQuery = "SELECT COUNT(*) as total FROM register";
$countResult = mysqli_query($conn, $countQuery);
$totalRecords = mysqli_fetch_assoc($countResult)['total'];

// Get filtered records count (same as total in this basic implementation)
$filteredRecords = $totalRecords;

// Main query with pagination
$sql = "SELECT 
            id, name, email, employee_id, department, role, 
            joining_date, mobile, address, gender, branch, 
            account_number, ifsc_code, salary_per_month, 
            resign, resign_date 
        FROM register 
        ORDER BY resign ASC, id DESC
        LIMIT $start, $length";

$result = mysqli_query($conn, $sql);

if (!$result) {
    die(json_encode([
        'error' => 'Query failed: ' . mysqli_error($conn)
    ]));
}

$data = array();
while ($row = mysqli_fetch_assoc($result)) {
    $data[] = $row;
}

header('Content-Type: application/json');
echo json_encode([
    'draw' => isset($_GET['draw']) ? (int)$_GET['draw'] : 1,
    'recordsTotal' => $totalRecords,
    'recordsFiltered' => $filteredRecords,
    'data' => $data
]);
?>