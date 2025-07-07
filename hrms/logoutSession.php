<?php
session_start(); // Start the session

// Destroy the session and clear session variables
session_unset();  // Unset all session variables
session_destroy(); // Destroy the session

// Send response back to the AJAX call
echo json_encode(['status' => 'success', 'message' => 'Session destroyed']);
?>
