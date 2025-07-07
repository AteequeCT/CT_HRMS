<?php
require 'includes/connection.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // 🔑 SECURITY REMINDER: Always store hashed passwords in production!
    // Use password_verify() when checking.

    // 1️⃣ Check Admin/HR login (from admin table with role)
    $stmt = $conn->prepare("SELECT * FROM admin WHERE email = ? AND password = ?");
    $stmt->bind_param('ss', $email, $password);
    $stmt->execute();
    $admin_res = $stmt->get_result();

    if ($admin_res && $admin_res->num_rows > 0) {
        $row = $admin_res->fetch_assoc();

        if (strtolower($row['role']) === 'hr') {
            $_SESSION['hr'] = true;
            $_SESSION['email'] = $email;

            // Clear client/customer sessions
            clearClientSessions();

            echo "1"; // HR login
            exit;
        } elseif (strtolower($row['role']) === 'admin') {
            $_SESSION['admin'] = true;
            $_SESSION['email'] = $email;

            clearClientSessions();

            echo "5"; // Super admin login
            exit;
        }
    }

    // 2️⃣ Regular Employee Login
    $stmt = $conn->prepare("SELECT * FROM register WHERE email = ? AND password = ? AND resign = 0");
    $stmt->bind_param('ss', $email, $password);
    $stmt->execute();
    $employee_res = $stmt->get_result();

    if ($employee_res && $employee_res->num_rows > 0) {
        $employee_data = $employee_res->fetch_assoc();

        $_SESSION['register_id'] = $employee_data['id'];
        $_SESSION["email"] = $email;
        $_SESSION['name'] = $employee_data['name'];
        $_SESSION['hr'] = false;

        clearClientSessions();

        echo "2"; // Regular employee login
        exit;
    }

    // 3️⃣ Resigned Employee
    $stmt = $conn->prepare("SELECT id FROM register WHERE email = ? AND password = ? AND resign = 1");
    $stmt->bind_param('ss', $email, $password);
    $stmt->execute();
    $resigned_res = $stmt->get_result();

    if ($resigned_res && $resigned_res->num_rows > 0) {
        clearClientSessions();
        echo "3"; // Resigned employee
        exit;
    }

    // 4️⃣ Customer Login
    $stmt = $conn->prepare("SELECT * FROM customer WHERE email = ? AND password = ?");
    $stmt->bind_param('ss', $email, $password);
    $stmt->execute();
    $customer_res = $stmt->get_result();

    if ($customer_res && $customer_res->num_rows > 0) {
        $customer_data = $customer_res->fetch_assoc();

        $_SESSION['customer_id'] = $customer_data['id'];
        $_SESSION['customer_email'] = $customer_data['email'];
        $_SESSION['client_name'] = $customer_data['name'] ?? $customer_data['email'];
        $_SESSION['is_customer_login'] = true;
        $_SESSION['show_tech_sidebar'] = false;
        $_SESSION['show_marketing_sidebar'] = true;

        echo "7"; // Customer dashboard
        exit;
    }

    // 5️⃣ Client Login (client_projects table)
    $stmt = $conn->prepare("SELECT * FROM client_projects WHERE email = ? AND password = ?");
    $stmt->bind_param('ss', $email, $password);
    $stmt->execute();
    $client_res = $stmt->get_result();

    if ($client_res && $client_res->num_rows > 0) {
        $client_data = $client_res->fetch_assoc();

        $_SESSION['client_id'] = $client_data['id'];
        $_SESSION['client_email'] = $client_data['email'];
        $_SESSION['client_name'] = $client_data['client_name'];
        $_SESSION['show_tech_sidebar'] = false;
        $_SESSION['show_marketing_sidebar'] = false;

        // Check projects for sidebars
        $stmt2 = $conn->prepare("SELECT categories FROM client_projects WHERE email = ?");
        $stmt2->bind_param('s', $email);
        $stmt2->execute();
        $projects_res = $stmt2->get_result();

        if ($projects_res) {
            $technical_keywords = ['website development', 'app development', 'software development', 'web development'];
            while ($project = $projects_res->fetch_assoc()) {
                $categories = array_map('trim', explode(',', $project['categories']));
                foreach ($categories as $cat) {
                    $cat = strtolower($cat);
                    if (in_array($cat, $technical_keywords)) {
                        $_SESSION['show_tech_sidebar'] = true;
                    }
                    if ($cat === 'digital marketing') {
                        $_SESSION['show_marketing_sidebar'] = true;
                    }
                }
            }
        }

        echo "7"; // Client login
        exit;
    }

    // 6️⃣ Invalid credentials
    echo "0"; // Login failed
    exit;
}

// Function to clear irrelevant sessions
function clearClientSessions() {
    unset($_SESSION['is_customer_login']);
    unset($_SESSION['client_id']);
    unset($_SESSION['client_email']);
    unset($_SESSION['client_name']);
    unset($_SESSION['show_tech_sidebar']);
    unset($_SESSION['show_marketing_sidebar']);
}


$conn->close();
?>