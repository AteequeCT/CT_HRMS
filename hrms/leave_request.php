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




// Define team lead emails
$team_lead_emails = [
    'sanjana@crawlerstechnologies.com',
    'abhishek.crawlerstechnology@gmail.com',
    'ateeque@crawlerstechnologies.com',
    'Sangeeta@crawlerstechnologies.com'
];

// List of admin emails
$admin_emails = [
    'admin@gmail.com'
];

// Check user roles
$isTeamLead = isset($_SESSION['email']) && in_array($_SESSION['email'], $team_lead_emails);
$isAdmin = isset($_SESSION['email']) && in_array($_SESSION['email'], $admin_emails);
$isHrAdmin = isset($_SESSION['admin']) && $_SESSION['admin'] === 'true';

// Combined check for HR or Admin access
$showHrSidebar = $isHrAdmin || $isAdmin;

// Initialize message variables
$message = '';
$messageType = '';

// Process leave request form
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_SESSION['email'])) {
        $message = 'You must be logged in to submit a leave request.';
        $messageType = 'error';
    } else {
        $session_email = $_SESSION['email'];
        $start_date = $_POST['start_date'];
        $end_date = $_POST['end_date'];
        $subject = $_POST['subject'];
        $reason = $_POST['reason'];

        // Validate dates
        if (strtotime($end_date) < strtotime($start_date)) {
            $message = 'End date cannot be before start date.';
            $messageType = 'error';
        } else {
            // Prepare and bind
            $stmt = $conn->prepare("INSERT INTO leave_request (session_email, start_date, end_date, subject, reason, status) VALUES (?, ?, ?, ?, ?, 'Pending')");
            $stmt->bind_param("sssss", $session_email, $start_date, $end_date, $subject, $reason);

            if ($stmt->execute()) {
                $message = 'Leave request submitted successfully.';
                $messageType = 'success';
            } else {
                $message = 'Error saving leave request: ' . $stmt->error;
                $messageType = 'error';
            }

            $stmt->close();
        }
    }
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leaves Request</title>
    <style>
        body {
            background-color: #f8f9fa;
        }

        #sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: 240px;
            height: 100vh;
            background-color: #f8f9fa;
            z-index: 1000;
            transition: all 0.3s;
            overflow-y: auto;
            padding-top: 10px;
        }

        #sidebar ul.nav {
            padding-left: 0;
        }

        #sidebar ul.nav li {
            margin-bottom: 15px;
            border-bottom: 1px solid #dee2e6;
        }

        #sidebar .nav-link.active {
            background-color: #dee2e6;
            color: #000;
            border-left: 4px solid rgb(65, 70, 78);
            font-weight: bold;
        }

        .nav-link {
            padding: 10px 20px;
            font-size: 16px;
            color: #333;
            display: block;
            position: relative;
        }

        .nav-link:hover,
        .nav-link.active {
            background-color: #e9ecef;
            border-radius: 5px;
        }

        .nav-link::after {
            content: '';
            position: absolute;
            left: 0;
            bottom: 0;
            height: 2px;
            width: 100%;
            background-color: rgba(0, 0, 0, 0.1);
            transition: background-color 0.3s;
        }

        .nav-link:hover::after {
            background-color: rgba(0, 0, 0, 0.3);
        }

        .nav-link.active::after {
            background-color: rgb(10, 10, 11);
        }

        #main {
            margin-left: 240px;
            padding: 20px;
        }

        @media (max-width: 768px) {
            #sidebar {
                left: -240px;
            }

            #sidebar.active {
                left: 0;
            }

            #main {
                margin-left: 0;
            }
        }

        .menu-toggle {
            position: fixed;
            top: 10px;
            left: 10px;
            z-index: 1100;
            background: rgb(65, 70, 78);
            color: white;
            border: none;
            border-radius: 4px;
            padding: 6px 12px;
            width: auto;
            height: auto;
        }

        .logo {
            max-width: 80%;
            height: auto;
            margin-right: 15px;
        }

        @media (max-width: 768px) {
            .logo {
                margin-left: 35px;
                max-width: 70%;
            }

            .menu-toggle {
                display: block;
            }
        }

        @media (min-width: 768px) {
            .menu-toggle {
                display: none;
            }
        }

        .logo-container {
            text-align: center;
            padding: 10px 0;
            margin-bottom: 20px;
        }

        .card {
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1), 0 6px 20px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            border: none;
        }

        .card-header {
            border-radius: 10px 10px 0 0 !important;
            background-color: #fff !important;
            border-bottom: 1px solid rgba(0, 0, 0, 0.1);
        }

        .form-row input,
        .form-row select {
            flex: 1;
            padding: 10px;
            border: 1px solid #ced4da;
            border-radius: 8px;
            box-sizing: border-box;
            transition: all 0.3s ease;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
        }

        .form-row input:focus,
        .form-row select:focus {
            border-color: #86b7fe;
            outline: 0;
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
            border-radius: 8px;
        }

        .form-container {
            max-width: 800px;
            padding: 25px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .form-container button[type="submit"] {
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }

        .form-container button[type="submit"]:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="page-inner">
            <div class="page-header">
                <h3 class="fw-bold mb-3">Employees</h3>
                <ul class="breadcrumbs mb-3">
                    <li class="nav-home">
                        <a href="#">
                            <i class="icon-home"></i>
                        </a>
                    </li>
                    <li class="separator">
                        <i class="icon-arrow-right"></i>
                    </li>
                    <li class="nav-item">
                        <a href="#">leave Requests</a>
                    </li>
                </ul>
            </div>
            <div class="row">
                <div class="col-md-10 offset-md-1">
                <div class="card p-4">
                    <h1 class="mb-4 text-center">Leaves Request</h1>
                    <div class="card-body">
                        <form id="leaveForm" action="" method="POST">
                            <div class="row row-input">
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="start_date" class="form-label">Leave Start</label>
                                        <input type="date" name="start_date" id="start_date" class="form-control" required />
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="end_date" class="form-label">Leave End</label>
                                        <input type="date" name="end_date" id="end_date" class="form-control" required />
                                    </div>
                                </div>
                            </div>

                            <div class="form-group mb-3">
                                <label for="subject" class="form-label">Subject</label>
                                <input type="text" name="subject" id="subject" class="form-control" required />
                            </div>
                            <div class="form-group mb-3">
                                <label for="reason" class="form-label">Reason</label>
                                <textarea type="textarea" rows="5" name="reason" id="reason" class="form-control" placeholder="Why you want leave" required></textarea>
                            </div>
                            <div class="d-flex justify-content-center">
                                <button type="submit" class="btn btn-primary">Submit</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function toggleSidebar() {
        var sidebar = document.getElementById("sidebar");
        sidebar.classList.toggle("active");
    }

    // Highlight current sidebar link
    document.querySelectorAll('#sidebar .nav-link').forEach(link => {
        if (link.href === window.location.href) {
            link.classList.add('active');
        }
    });

    // Show message if exists
    <?php if (!empty($message)): ?>
        $(document).ready(function() {
            swal({
                title: "<?php echo ($messageType === 'success') ? 'Success!' : 'Error!'; ?>",
                text: "<?php echo addslashes($message); ?>",
                icon: "<?php echo $messageType; ?>",
                button: "OK",
            }).then(() => {
                <?php if ($messageType === 'success'): ?>
                    window.location.href = "leave_acc.php";
                <?php endif; ?>
            });
        });
    <?php endif; ?>

   

    // Initialize DataTables for enhanced table functionality
    $(document).ready(function() {
        $('.table').DataTable({
            paging: false,
            searching: false,
            info: false,
            ordering: true,
            dom: 'Bfrtip',
            buttons: []
        });
    });
</script>
</body>
</html>