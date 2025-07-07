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

$team_admin = ['vasant@crawlerstechnologies.com'];

// Check if the user is HR admin or one of the team leads
$TeamLead = isset($_SESSION['email']) && in_array($_SESSION['email'], $team_admin);
// List of team lead emails
$team_lead_emails = [ 'abhishek.crawlerstechnology@gmail.com',
                      'ateeque.crawlerstechnologies@gmail.com'];

// Check if the user is HR admin or one of the team leads
$isTeamLead = isset($_SESSION['email']) && in_array($_SESSION['email'], $team_lead_emails);

// print_r($isTeamLead);exit;
// Check if HR admin is logged in
$isHrAdmin = isset($_SESSION['admin']) && $_SESSION['admin'] === 'true';
// print_r($isHrAdmin);exit;

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Absent Employees</title>
    
    <style>
           #sidebar {
      transition: all 0.3s;
      position: absolute;
      z-index: 1000;
      width: 220px;
      height: 100%;
      left: -250px; /* Hide sidebar off-screen by default */
    }
    #sidebar.active {
      left: 0; /* Show sidebar when active */
    }
    .sidebar-toggle {
      cursor: pointer;
    }
    .logo{
        width:110%;
    }

    /* Hide sidebar completely on larger screens (computer) */
    @media (min-width: 768px) {
      #sidebar {
        position: static;
        left: 0;
      }
    }
    
    /* Styling for nav items */
    .nav-link {
      position: relative; /* Position relative for the line */
        padding: 5%;
      padding-bottom: 10px; /* Add some space for the line */
    }
    
      .nav-link::after {
      content: ''; /* Create a pseudo-element for the line */
      position: absolute;
      left: 0;
      bottom: 0;
      height: 2px; /* Thickness of the line */
      width: 100%; /* Full width */
      background-color: rgba(0, 0, 0, 0.1); /* Light line color */
      transition: background-color 0.3s; /* Smooth transition */
    }

    .nav-link:hover::after {
      background-color: rgba(0, 0, 0, 0.3); /* Darker line on hover */
    }

    .nav-link.active::after {
      background-color: rgba(0, 123, 255, 1); /* Highlight line for active link */
    }
    #toggleSidebar{
  background-color: #ef0d77;
  border: none;
}

 
.berger{
    width:15%;
    border-radius: 20%;
}

        /* Table scroll settings */
        .table-container {
            overflow-x: auto; /* Enable horizontal scrolling */
            margin-top: 20px;
        }

        table {
            width: 100%;
        }

          /* Show the logo on mobile view, hide on desktop */
    @media (min-width: 768px) {
            .logo1 {
                display: none;
            }
        }

        @media (max-width: 767px) {
            .logo1 {
                display: block;
                max-width: 60%;
                height: auto;
                margin-top: 2%;
                margin-left: -5%;
        }
    }

        h2 {
            color: #ee0d7b;
            margin-top: 3%;
            text-align: center;
        }

        /* Scrollbar for the table */
        /*.table-responsive {*/
        /*    overflow-x: auto;*/
        /*}*/
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



<div class="col-md-12">
    <div class="card">
        <div class="card-header">
            <h4 class="card-title">Absent Employees </h4>
        </div>
        <div class="card-body">
            <div class="table-responsive">


                    <!-- Content section (table scrolls horizontally) -->
                    <div class="content table-container">
                        <div class="table-responsive">
                            <table id="absentTable" class="table display" border="1" cellpadding="10" cellspacing="0">
                                <thead>
                                    <tr style="background: #ee0d7b; color: white;">
                                        <th style="text-align: center;">Sr No</th>
                                        <th style="text-align: center;">Employee ID</th>
                                        <th style="text-align: center;">Name</th>
                                        <th style="text-align: center;">Department</th>
                                        <th style="text-align: center;">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Table rows will be populated via AJAX -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <script>
        document.getElementById('burgerButton').addEventListener('click', function() {
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('active');
        });

        $(document).ready(function() {
            // Initialize DataTables with AJAX
            $('#absentTable').DataTable({
                "processing": true,
                "serverSide": true,
                "ajax": {
                    "url": "getAbsentEmployees.php",
                    "type": "POST"
                },
                "columns": [
                    { "data": 0 },
                    { "data": 1 },
                    { "data": 2 }
                ],
                "pageLength": 10
            });
        });
   
       
    </script>
</body>
</html>
