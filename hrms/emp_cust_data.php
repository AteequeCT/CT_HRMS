<?php
session_start();
require 'includes/connection.php';

$users = [];

// ✅ Get emp_id from URL if passed
$emp_id = isset($_GET['emp_id']) ? intval($_GET['emp_id']) : 0;
// ✅ Build query with emp_id filter
$query = "
    SELECT 
        customer.*, 
        COUNT(conversations.cust_id) AS conversationCount 
    FROM customer
    LEFT JOIN conversations ON customer.id = conversations.cust_id
    WHERE customer.is_deleted = 0
";

// ✅ Filter only if emp_id is passed
if ($emp_id > 0) {
    $query .= " AND customer.emp_id = $emp_id ";
}

$query .= " GROUP BY customer.id ORDER BY customer.id DESC";

$result = $conn->query($query);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
}

echo json_encode($users);
?>
