<?php
session_start();
include 'includes/connection.php';


header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['email'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit();
}

// Validate required fields
if (!isset($_FILES['user_photo']) || $_FILES['user_photo']['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['success' => false, 'message' => 'No photo uploaded or upload error']);
    exit();
}

// Get employee details from session or POST
$email = $_SESSION['email'];
$employee_id = $_POST['employee_id'] ?? '';
$emp_name = $_POST['emp_name'] ?? '';
$latitude = !empty($_POST['latitude']) ? $_POST['latitude'] : null;
$longitude = !empty($_POST['longitude']) ? $_POST['longitude'] : null;

// Handle file upload
$uploadDir = 'uploads/attendance/';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

// Generate unique filename
$filename = uniqid('attendance_') . '_' . time() . '.png';
$filePath = $uploadDir . $filename;

// Move uploaded file
if (move_uploaded_file($_FILES['user_photo']['tmp_name'], $filePath)) {
    // Prepare SQL statement based on your database schema
    $sql = "INSERT INTO image_proof 
            (employee_id, emp_name, session_email, user_photo, Date, login_time, latitude, longitude) 
            VALUES (?, ?, ?, ?, CURDATE(), CURTIME(), ?, ?)";
    
    $stmt = $conn->prepare($sql);
    
    // Check if prepare was successful
    if ($stmt === false) {
        unlink($filePath); // Clean up uploaded file
        echo json_encode([
            'success' => false, 
            'message' => 'Database error: ' . $conn->error,
            'sql_error' => $conn->error
        ]);
        exit();
    }
    
    // Bind parameters
    $bindResult = $stmt->bind_param(
        "ssssdd", 
        $employee_id, 
        $emp_name, 
        $email, 
        $filePath, 
        $latitude, 
        $longitude
    );
    
    if ($bindResult === false) {
        unlink($filePath); // Clean up uploaded file
        echo json_encode([
            'success' => false, 
            'message' => 'Database error: Failed to bind parameters',
            'bind_error' => $stmt->error
        ]);
        exit();
    }
    
    // Execute the statement
    if ($stmt->execute()) {
        echo json_encode([
            'success' => true, 
            'message' => 'Attendance recorded successfully',
            'file_path' => $filePath
        ]);
    } else {
        unlink($filePath); // Clean up uploaded file
        echo json_encode([
            'success' => false, 
            'message' => 'Database error: ' . $stmt->error,
            'execute_error' => $stmt->error
        ]);
    }
    
    $stmt->close();
} else {
    echo json_encode([
        'success' => false, 
        'message' => 'Failed to save photo',
        'file_error' => $_FILES['user_photo']['error']
    ]);
}

$conn->close();
?>