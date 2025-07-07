<?php
header('Content-Type: application/json');

// Include the database connection
include('includes/connection.php');

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['emp_id'])) {
    // Fetch conversations for the given emp_id
    $emp_id = $conn->real_escape_string($_GET['emp_id']);
    $cust_id = $conn->real_escape_string($_GET['cust_id']);
    // print_r($emp_id);exit   ;
    // Query to join conversations and customers tables based on cust_id
    $query = "
        SELECT 
            c.id, 
            c.adminName, 
            c.date, 
            TIME_FORMAT(c.time, '%H:%i') as time, 
            c.user_conversation, 
            c.admin_conversation, 
            c.number, 
            c.emp_id,
            c.cust_id, 
            cu.name AS customer_name
        FROM 
            conversations c
        LEFT JOIN 
            customer cu 
        ON 
            c.cust_id = cu.id
        WHERE 
            c.emp_id = '$emp_id' AND c.cust_id = '$cust_id'
    ";

    $result = $conn->query($query);

    $data = [];
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        echo json_encode($data);
    } else {
        echo json_encode(['success' => false, 'error' => $conn->error]);
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Store new conversation data
    $cust_id = $conn->real_escape_string($_POST['cust_id']);
    $emp_id = $conn->real_escape_string($_POST['emp_id']);
    $adminName = $conn->real_escape_string($_POST['adminName']);
    $date = $conn->real_escape_string($_POST['date']);
    $time = $conn->real_escape_string($_POST['time']);
    $user_convo = $conn->real_escape_string($_POST['user_conversation']);
    $admin_convo = $conn->real_escape_string($_POST['admin_conversation']);

    // Insert data into the database
    $query = "
        INSERT INTO conversations (cust_id, emp_id, adminName, date, time, user_conversation, admin_conversation) 
        VALUES ('$cust_id', '$emp_id', '$adminName', '$date', '$time', '$user_convo', '$admin_convo')
    ";

    if ($conn->query($query)) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => $conn->error]);
    }
}

$conn->close();
?>
