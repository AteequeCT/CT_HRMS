<?php
include 'includes/connection.php';

// Start session
session_start();

// Get today's date
$today = date('Y-m-d');

// Define default values for pagination
$limit = isset($_POST['length']) ? intval($_POST['length']) : 10;
$start = isset($_POST['start']) ? intval($_POST['start']) : 0;
$draw = isset($_POST['draw']) ? intval($_POST['draw']) : 1;

// Get logged-in user's email from the session
$sessionEmail = isset($_SESSION['email']) ? $_SESSION['email'] : null;

// Define the list of team lead emails
$team_lead_emails = ['vasant@crawlerstechnologies.com'];

// Check if the logged-in user is a team lead
$isTeamLead = $sessionEmail && in_array($sessionEmail, $team_lead_emails);

// Get total number of employees
$query_total = "SELECT COUNT(*) as total FROM register";
$result_total = mysqli_query($conn, $query_total);
$row_total = mysqli_fetch_assoc($result_total);
$total_records = $row_total['total'];

// Query to get present employees
$query_present_employees = "SELECT session_email FROM image_proof WHERE DATE(Date) = '$today'";
$result_present_employees = mysqli_query($conn, $query_present_employees);

// Store present employees' emails in an array
$present_emails = [];
while ($row = mysqli_fetch_assoc($result_present_employees)) {
    $present_emails[] = $row['session_email'];
}

// Prepare the query to fetch absent employees
if ($isTeamLead) {
    // Fetch the branches for the team lead (from 'register' table)
    $branchQuery = "SELECT branch FROM register WHERE email = '$sessionEmail'";
    $branchResult = mysqli_query($conn, $branchQuery);

    if ($branchResult && mysqli_num_rows($branchResult) > 0) {
        $branchRow = mysqli_fetch_assoc($branchResult);
        $branch = $branchRow['branch'];

        // Filter absent employees by branch (Bengaluru and Hyderabad)
        $query_absent_employees = "
            SELECT * FROM register 
            WHERE resign = 0 
              AND email NOT IN ('" . implode("','", $present_emails) . "') 
              AND branch IN ('Bengaluru', 'Hyderabad') 
            LIMIT $limit OFFSET $start
        ";
    } else {
        // If branch is not found, no absent employees will be shown
        $query_absent_employees = "SELECT * FROM register WHERE 1 = 0";
    }
} else {
    // For non-team leads, fetch all absent employees
    $query_absent_employees = "
        SELECT * FROM register 
        WHERE resign = 0 
          AND email NOT IN ('" . implode("','", $present_emails) . "') 
        LIMIT $limit OFFSET $start
    ";
}

// Execute the absent employees query
$result_absent_employees = mysqli_query($conn, $query_absent_employees);

// Prepare data for DataTables
$data = [];
while ($row = mysqli_fetch_assoc($result_absent_employees)) {
    $data[] = [
        $row['name'],
        $row['email'],
        $row['branch'],
        'Absent'
    ];
}

// Prepare response
$response = [
    "draw" => $draw,
    "recordsTotal" => $total_records,
    "recordsFiltered" => count($present_emails),
    "data" => $data
];

// Return JSON response
echo json_encode($response);

// Close connection
mysqli_close($conn);
?>
