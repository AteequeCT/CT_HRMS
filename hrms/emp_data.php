<?php
// Database connection
require 'includes/connection.php'; // Ensure this file correctly establishes a $conn variable


if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Fetch all users from register table
    $result = $conn->query("SELECT * FROM register where crm = 1 ORDER BY id DESC ");

    $users = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $users[] = $row;
        }
    }

    echo json_encode($users); // Return all users as a JSON response
    exit;
}

// Check if emp_id is passed in the URL
if (isset($_GET['emp_id'])) {
    $emp_id = $_GET['emp_id'];

    // Fetch the user's conversations using emp_id
    $sql = "SELECT c.id, c.message, c.timestamp, r.name AS employee_name, r.email AS employee_email
            FROM conversations c
            JOIN register r ON c.emp_id = r.id
            WHERE c.emp_id = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $emp_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $conversations = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $conversations[] = $row;
        }
        echo json_encode($conversations);
    } else {
        echo json_encode(['success' => false, 'message' => 'No conversations found for this user.']);
    }

    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'emp_id parameter is missing.']);
}

$conn->close();
?>
