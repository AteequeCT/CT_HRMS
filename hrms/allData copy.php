<?php
session_start();
include 'connection.php';

// Verify database connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// List of team lead emails
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
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employees Information</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        @media (min-width: 768px) {
            button {
                width: 70px;
                overflow: hidden;
            }
        }

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
            /* light grey border */
        }

        /* Active link styling */
        #sidebar .nav-link.active {
            background-color: #dee2e6;
            color: #000;
            border-left: 4px solid rgb(65, 70, 78);
            /* left indicator */
            font-weight: bold;
            border-bottom: 2px solid rgb(65, 70, 78);
        }

        .nav-link {
            padding: 10px 20px;
            font-size: 16px;
            color: #333;
            display: block;
        }

        .nav-link:hover,
        .nav-link.active {
            background-color: #e9ecef;
            border-radius: 5px;
        }

        #main {
            margin-left: 240px;
            padding: 20px;
        }

        @media (max-width: 768px) {
            #sidebar {
                left: -240px;
                /* Hide sidebar off-screen by default on mobile */
            }

            #sidebar.active {
                left: 0;
                /* Show sidebar when active on mobile */
            }

            #main {
                margin-left: 0;
                /* Remove left margin for main content on mobile */
            }

            #main.active {
                margin-left: 240px;
                /* Adjust main content when sidebar is active */
            }

            .menu-toggle {
                display: block !important;
                /* Ensure the toggle button is visible on mobile */
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
            /* Removed display: none; */
        }

        .logo {
            max-width: 80%;
            height: auto;
            margin-right: 15px;
        }

        @media (min-width: 768px) {
            #mobile-logo {
                display: none;
            }

            #desktop-logo {
                display: block;
                font-size: 28px;
                font-weight: bold;
            }

            button {
                width: auto;
                /* Revert button width on desktop */
                overflow: visible;
            }
        }

        table.dataTable thead th {
            background-color: rgb(65, 70, 78);
            color: white;
        }

        /* Clean Pagination Buttons (No Background) */
        .dataTables_wrapper .dataTables_paginate .paginate_button {
            background: none !important;
            border: none !important;
            color: inherit !important;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button:hover,
        .dataTables_wrapper .dataTables_paginate .paginate_button:focus {
            background: none !important;
            box-shadow: none !important;
            color: inherit !important;
        }

        .active>.page-link,
        .page-link.active {
            z-index: 3;
            color: var(--bs-pagination-active-color);
            background-color: rgb(65, 70, 78);
            border-color: rgb(65, 70, 78);
        }

        .page-link {
            position: relative;
            display: block;
            padding: var(--bs-pagination-padding-y) var(--bs-pagination-padding-x);
            font-size: var(--bs-pagination-font-size);
            color: #24282d;
        }

        a {
            color: rgb(13 13 14);
            text-decoration: none;
        }

        .nav {
            --bs-nav-link-padding-x: 1rem;
            --bs-nav-link-padding-y: 0.5rem;
            --bs-nav-link-font-weight: ;
            --bs-nav-link-color: var(--bs-link-color);
            --bs-nav-link-hover-color: #151516;
            --bs-nav-link-disabled-color: var(--bs-secondary-color);
            display: flex;
            flex-wrap: wrap;
            padding-left: 0;
            margin-bottom: 0;
            list-style: none;
        }

        #sidebar::-webkit-scrollbar {
            display: none;
            /* Chrome, Safari, Opera */
        }

        .nav-link.active::after {
            background-color: rgb(10, 10, 11);
        }

        .button {
            width: none;
        }

        .logo {
            max-width: 80%;
            height: auto;
            margin-right: 15px;
            /* Shift logo to the right a bit */
        }

        /* Responsive adjustment for mobile */
        @media (max-width: 768px) {
            .logo {
                margin-left: 35px;
                /* Less margin for smaller screens */
                max-width: 70%;
                /* Scale down for mobile if needed */
            }
        }

        .logo-container {
            text-align: center;
            padding: 10px 0;
            margin-bottom: 20px;
        }
    </style>
</head>

<body>
    <button class="menu-toggle d-md-none" onclick="toggleSidebar()">☰</button>
    <div class="container-fluid">
        <div class="row">
            <?php if ($showHrSidebar): ?>
                <!-- HR Admin sidebar -->
                <nav id="sidebar" class="col-md-3 col-lg-2 bg-light sidebar">
                    <div class="position-sticky">
                        <div class="logo-container">
                            <img class="logo" src="crawlers.png" alt="Logo">
                        </div>
                        <ul class="nav flex-column mt-4">
                            <li class="nav-item"><a class="nav-link" href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                            <li class="nav-item"><a class="nav-link" href="registration.php"><i class="fas fa-user-plus"></i> Registration</a></li>
                            <li class="nav-item"><a class="nav-link" href="meeting.php"><i class="fas fa-handshake"></i> Meeting Details</a></li>
                            <li class="nav-item"><a class="nav-link active" href="allData.php"><i class="fas fa-users"></i> Employees</a></li>
                            <li class="nav-item"><a class="nav-link" href="salary_sheet.php"><i class="fas fa-file-invoice-dollar"></i> Salary Sheet</a></li>
                            <li class="nav-item"><a class="nav-link" href="today_data.php"><i class="fas fa-calendar-day"></i> Today's Data</a></li>
                            <li class="nav-item"><a class="nav-link" href="activityEmp.php"><i class="fas fa-tasks"></i> Employees Activity</a></li>
                            <li class="nav-item"><a class="nav-link" href="attendance.php"><i class="fas fa-clipboard-list"></i> Attendance</a></li>
                            <li class="nav-item"><a class="nav-link" href="leave_acc.php"><i class="fas fa-plane-departure"></i> Leave Requests</a></li>
                            <li class="nav-item"><a class="nav-link" href="absent.php"><i class="fas fa-user-times"></i> Today's Absent Data</a></li>
                            <li class="nav-item"><a class="nav-link" href="salary_slip.php"><i class="fas fa-file-invoice"></i> Salary Generate</a></li>
                            <li class="nav-item"><a class="nav-link" href="holidays_form.php"><i class="fas fa-plus-circle"></i> Add Holidays</a></li>
                            <li class="nav-item"><a class="nav-link" href="holiday_view.php"><i class="fas fa-umbrella-beach"></i> Holidays</a></li>
                            <li class="nav-item"><a class="nav-link" id="logoutButton" href="#"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                        </ul>
                    </div>
                </nav>
            <?php elseif ($isTeamLead): ?>
                <!-- Team Lead sidebar -->
                <nav id="sidebar" class="col-md-3 col-lg-2 bg-light sidebar">
                    <div class="position-sticky">
                        <div class="logo-container">
                            <img class="logo" src="crawlers.png" alt="Logo">
                        </div>
                        <ul class="nav flex-column mt-4">
                            <li class="nav-item"><a class="nav-link" href="userDashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                            <li class="nav-item"><a class="nav-link" href="meeting.php"><i class="fas fa-handshake"></i> Meeting Details</a></li>
                            <li class="nav-item"><a class="nav-link" href="activityEmp.php"><i class="fas fa-tasks"></i> Employees Activity</a></li>
                            <li class="nav-item"><a class="nav-link" href="attendance.php"><i class="fas fa-clipboard-list"></i> Attendance</a></li>
                            <li class="nav-item"><a class="nav-link" href="leave_acc.php"><i class="fas fa-plane-departure"></i> Leave Requests</a></li>
                            <li class="nav-item"><a class="nav-link" href="holiday_view.php"><i class="fas fa-umbrella-beach"></i> Holidays</a></li>
                            <li class="nav-item"><a class="nav-link" id="changePasswordBtn" href="#"><i class="fas fa-key"></i> Change Password</a></li>
                            <li class="nav-item"><a class="nav-link" id="logoutButton" href="#"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                        </ul>
                    </div>
                </nav>
            <?php else: ?>
                <!-- Regular user sidebar -->
                <nav id="sidebar" class="col-md-3 col-lg-2 bg-light sidebar">
                    <div class="position-sticky">
                        <div class="logo-container">
                            <img class="logo" src="crawlers.png" alt="Logo">
                        </div>
                        <ul class="nav flex-column mt-4">
                            <li class="nav-item"><a class="nav-link" href="userDashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                            <li class="nav-item"><a class="nav-link" href="meeting.php"><i class="fas fa-handshake"></i> Meeting Details</a></li>
                            <li class="nav-item"><a class="nav-link" href="activityEmp.php"><i class="fas fa-tasks"></i> Employees Activity</a></li>
                            <li class="nav-item"><a class="nav-link" href="table.php"><i class="fas fa-clipboard-list"></i> Attendance</a></li>
                            <li class="nav-item"><a class="nav-link" href="leave_acc.php"><i class="fas fa-plane-departure"></i> Leave Requests</a></li>
                            <li class="nav-item"><a class="nav-link" href="holiday_view.php"><i class="fas fa-umbrella-beach"></i> Holidays</a></li>
                            <li class="nav-item"><a class="nav-link" id="changePasswordBtn" href="#"><i class="fas fa-key"></i> Change Password</a></li>
                            <li class="nav-item"><a class="nav-link" id="logoutButton" href="#"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                        </ul>
                    </div>
                </nav>
            <?php endif; ?>
        </div>
    </div>
    <main id="main" class="col-md-8 ms-sm-auto col-lg-10 px-md-4">
        <div class="container-fluid">
            <h3 class="text-center mb-4" style="color:rgb(238, 29, 133);">Employees Information</h3>

            <div class="table-responsive">
                <table id="userTable" class="display table table-bordered table-striped">
                    <thead class="head">
                        <tr>
                            <th scope="col">ID</th>
                            <th scope="col">Name</th>
                            <th scope="col">Email</th>
                            <th scope="col">Employee ID</th>
                            <th scope="col">Department</th>
                            <th scope="col">Role</th>
                            <th scope="col">Joining Date</th>
                            <th scope="col">Mobile</th>
                            <th scope="col">Address</th>
                            <th scope="col">Gender</th>
                            <th scope="col">Branch</th>
                            <th scope="col">Resign Date</th>
                            <th scope="col">Action</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>

        <script src="https://code.jquery.com/jquery-3.7.1.js" crossorigin="anonymous"></script>
        <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
        <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
        <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

        <script>
            // Function to toggle sidebar
            function toggleSidebar() {
                var sidebar = document.getElementById("sidebar");
                var mainContent = document.getElementById("main");
                sidebar.classList.toggle("active");
                mainContent.classList.toggle("active");
            }

            $(document).ready(function() {
                // Initialize DataTable with server-side processing
                var table = $('#userTable').DataTable({
                    "processing": true,
                    "serverSide": true,
                    "ajax": {
                        "url": "get_adminData.php",
                        "type": "GET",
                        "dataType": "json",
                        "dataSrc": "data"
                    },
                    "columns": [
                        { "data": "id" },
                        { "data": "name" },
                        { "data": "email" },
                        { "data": "employee_id" },
                        { "data": "department" },
                        { "data": "role" },
                        { "data": "joining_date" },
                        { "data": "mobile" },
                        { "data": "address" },
                        { "data": "gender" },
                        { "data": "branch" },
                        { 
                            "data": "resign_date",
                            "render": function(data, type, row) {
                                return row.resign == 1 ? data : '-';
                            }
                        },
                        {
                            "data": null,
                            "render": function(data, type, row) {
                                var isResigned = row.resign == 1;
                                return `
                                    <button class="btn btn-info btn-xs updateBtn" data-id="${row.id}" style="padding: 2px 5px; font-size: 10px; margin-right: 2px;">Update</button>
                                    <button class="btn btn-warning btn-xs resignBtn" data-id="${row.id}" ${isResigned ? 'disabled' : ''} style="padding: 2px 5px; font-size: 10px; margin-right: 2px;">${isResigned ? 'Resigned' : 'Resign'}</button>
                                    <button class="btn btn-danger btn-xs deleteBtn" data-id="${row.id}" style="padding: 2px 5px; font-size: 10px;">Delete</button>
                                `;
                            },
                            "orderable": false
                        }
                    ],
                    "pageLength": 50,
                    "lengthMenu": [
                        [10, 25, 50, 100, -1],
                        [10, 25, 50, 100, "All"]
                    ],
                    "order": [[0, 'desc']],
                    "createdRow": function(row, data, dataIndex) {
                        if (data.resign == 1) {
                            $(row).addClass('table-danger');
                        }
                    }
                });

                // Update button
                $(document).on('click', '.updateBtn', function(event) {
                    event.stopPropagation();
                    var id = $(this).data('id');
                    window.location.href = 'update_data.php?id=' + id;
                });

                // Delete button
                $(document).on('click', '.deleteBtn', function(event) {
                    event.stopPropagation();
                    var id = $(this).data('id');
                    if (confirm("Are you sure you want to delete this record?")) {
                        $.ajax({
                            url: 'deleteRecord.php',
                            type: 'POST',
                            data: { id: id },
                            success: function(response) {
                                if (response == 'success') {
                                    swal("Deleted!", "The record has been deleted.", "success")
                                        .then(() => {
                                            table.ajax.reload();
                                        });
                                } else {
                                    swal("Error!", "Failed to delete the record.", "error");
                                }
                            },
                            error: function() {
                                swal("Error!", "Something went wrong.", "error");
                            }
                        });
                    }
                });

                // Resign button
                $(document).on('click', '.resignBtn', function(event) {
                    event.stopPropagation();
                    var id = $(this).data('id');
                    if (confirm("This employee will be marked as resigned.")) {
                        $.ajax({
                            url: 'updateResignStatus.php',
                            type: 'POST',
                            data: { id: id },
                            success: function(response) {
                                if (response === 'success') {
                                    swal("Resigned!", "The employee has been marked as resigned.", "success")
                                        .then(() => {
                                            table.ajax.reload();
                                        });
                                } else {
                                    swal("Error!", "Failed to update resign status.", "error");
                                }
                            },
                            error: function() {
                                swal("Error!", "Something went wrong.", "error");
                            }
                        });
                    }
                });

                // Logout button
                $('#logoutButton').on('click', function() {
                    if (confirm('Are you sure you want to logout?')) {
                        $.ajax({
                            url: 'logoutSession.php',
                            type: 'POST',
                            success: function(response) {
                                swal.fire({
                                    title: "Logout Successful!",
                                    icon: "success",
                                    button: "Done"
                                }).then(() => {
                                    window.location.href = "index.html";
                                });
                            },
                            error: function() {
                                alert('Error logging out.');
                            }
                        });
                    }
                });
            });
        </script>
    </main>
</body>
</html>