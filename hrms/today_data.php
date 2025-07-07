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



// Define constants for office timings
define('OFFICE_START', '09:30:00');
define('OFFICE_END', '18:30:00');

// Set timezone
date_default_timezone_set('Asia/Kolkata');

// Get the current date in "YYYY-MM-DD" format
$currentDate = date('Y-m-d');

// Define the list of team lead emails
$team_lead_emails = ['vasant@crawlerstechnologies.com'];

// Get the logged-in user's email from the session
$sessionEmail = isset($_SESSION['email']) ? $_SESSION['email'] : null;
// Check if HR admin is logged in
$isHrAdmin = isset($_SESSION['admin']) && $_SESSION['admin'] === 'true';

// Initialize the SQL query
$sql = "";

// Check if the logged-in user is a team lead
if ($sessionEmail && in_array($sessionEmail, $team_lead_emails)) {
  // Fetch the branch of the team lead from the 'register' table
  $branchQuery = "SELECT branch FROM register WHERE email = '$sessionEmail'";
  $branchResult = $conn->query($branchQuery);

  // Ensure the query succeeded and the branch is found
  if ($branchResult && $branchResult->num_rows > 0) {
    $branchRow = $branchResult->fetch_assoc();
    $branch = $branchRow['branch'];

    // Check if the branch is Bengaluru
    if ($branch === 'Bengaluru' || $branch === 'Hyderabad') {
      // Restrict data to Bengaluru branch
      $sql = "
                SELECT ip.*, r.branch 
                FROM image_proof ip
                INNER JOIN register r ON ip.session_email = r.email
                WHERE ip.Date = '$currentDate' AND r.branch IN ('Bengaluru', 'Hyderabad')
            ";
    } else {
      // If the branch is not Bengaluru, no data should be displayed
      $sql = "SELECT * FROM image_proof "; // No data
    }
  } else {
    // If no branch is found, display no data
    $sql = "SELECT * FROM image_proof"; // No data
  }
} else {
  // For non-team leads, display all employees' data for today
 $sql = "
    SELECT 
        ip.*, 
        r.branch, 
        r.employee_id, 
        r.name, 
        r.department 
    FROM image_proof ip
    INNER JOIN register r ON ip.session_email = r.email
    WHERE ip.Date = '$currentDate'
";
}

// Execute the query
$result = $conn->query($sql);

// Check for query execution errors
if (!$result) {
  die("SQL Error: " . $conn->error);
}


?>


<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Today's Data</title>
  <style>
    table {
      width: 100%;
      /* Full width table */
    }

    .head {
      background-color: #f2167a;
      color: white;
      align-items: center;
    }

    .text-center {
      color: #f2167a;
      font-size: 150%;
    }

    tr {
      cursor: pointer;
      /* Make table rows look clickable */
    }

    .logo {
      width: 45%;
    }

    .mt-1,
    .my-1 {
      margin-top: -5% !important;
    }

  

    /* Styling late times */
    /* .late-red {
      color: red;
    }

    .late-green {
      color: green;
    } */

    /* Small thumbnail size */
    .img-thumbnail {
      cursor: pointer;
      height: 50px;
      /* Initial small height */
      transition: all 0.3s ease-in-out;
      /* Smooth transition for zoom effect */
    }

    /* Zoomed-in image */
    .zoomed {
      position: fixed;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      z-index: 1000;
      width: 80%;
      /* Adjust width to fill a large portion of the screen */
      height: auto;
      /* Automatically maintain aspect ratio */
      max-width: 100%;
      /* Ensure it doesn't exceed the screen width */
      max-height: 100%;
      /* Ensure it doesn't exceed the screen height */
      cursor: zoom-out;
      box-shadow: 0px 4px 15px rgba(0, 0, 0, 0.5);
      /* Add shadow for a nice effect */
    }

    /* Backdrop when zoomed in */
    .zoomed-backdrop {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background-color: rgba(0, 0, 0, 0.7);
      /* Dark background */
      z-index: 999;
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



<div class="col-md-12">
    <div class="card">
        <div class="card-header">
            <h4 class="card-title">Employees Presnet Today </h4>
        </div>
        <div class="card-body">
            <div class="table-responsive">

            <?php
            if ($result->num_rows > 0) {
              echo " <table id='userTable' class='display table table-bordered table-striped'>";
              echo "<thead class='head'>
                        <tr>
                            <th>sr no</th>
                            <th>Emp ID</th>
                            <th>Employee Name</th>
                            <th>Deprtment Name</th>
                            <th>In Time</th>
                            <th>Out Time</th>
                            <th>Image</th>
                        </tr>
                      </thead>";
              echo "<tbody>";

              while ($row = $result->fetch_assoc()) {
                $id = $row['id'];
                $employee_id = $row['employee_id'];
                $name = $row['name'];
                $department = $row['department'];
                $login_time = $row['login_time'];
                $logout_time = $row['logout_time'];
                $image = $row['user_photo']; // Assuming this is the column for image paths

                // Convert times to PHP DateTime objects for calculation
                $login_dt = new DateTime($login_time);
                $logout_dt = isset($logout_time) ? new DateTime($logout_time) : null; // Check if logout time exists
                $lunch_start_dt = isset($lunch_start) ? new DateTime($lunch_start) : null;
                $lunch_end_dt = isset($lunch_end) ? new DateTime($lunch_end) : null;

                // Office standard times
                $office_start_dt = new DateTime($currentDate . ' ' . OFFICE_START);
                $office_end_dt = new DateTime($currentDate . ' ' . OFFICE_END);

                // Late threshold: 10 minutes after office start time
                $late_threshold_dt = clone $office_start_dt;
                $late_threshold_dt->modify('+10 minutes');

                // Calculate Login Status (Early or Late) and apply color
                $login_diff = $login_dt->diff($office_start_dt);
                // print_r($login_diff);exit;
                if ($login_dt > $late_threshold_dt) {
                  // More than 10 minutes late, display in red
                  $login_status = "<span ><strong>Late</strong> by " . $login_diff->format('%h hours, %i minutes') . "</span>";
                } elseif ($login_dt > $office_start_dt) {
                  // Up to 10 minutes late, display in green
                  $login_status = "<span ><strong>Late</strong> by " . $login_diff->format('%i minutes') . "</span>";
                } elseif ($login_dt == $office_start_dt) {
                  // Up to 10 minutes late, display in green
                  $login_status = "<span ><strong>present</strong> by ";
                } else {
                  // Early, display in green
                  $login_diff_early = $office_start_dt->diff($login_dt);
                  $login_status = "<span style='color:green;'><strong>Early</strong> by " . $login_diff_early->format('%h hours, %i minutes') . "</span>";
                }

                // Calculate Logout Status (if logout time exists)
                if ($logout_dt) {
                  $logout_diff = $logout_dt->diff($office_end_dt);
                  $logout_status = ($logout_dt < $office_end_dt) ?
                    "<span ><strong>Early</strong> by " . $logout_diff->format('%h hours, %i minutes') . "</span>" :
                    "<span ><strong>Late</strong> by " . $logout_diff->format('%h hours, %i minutes') . "</span>";
                } else {
                  $logout_status = "Not logged out";
                }

                // Calculate Total Work Duration (if logout time exists)
                if ($logout_dt) {
                  $work_duration = $login_dt->diff($logout_dt);
                  $total_work_hours = $work_duration->h;
                  $total_work_minutes = $work_duration->i;
                } else {
                  $total_work_hours = "Not available";
                }

                // Calculate Lunch Break Duration (if lunch times exist)
                if ($lunch_start_dt && $lunch_end_dt) {
                  $lunch_duration = $lunch_start_dt->diff($lunch_end_dt);
                  $lunch_display = $lunch_duration->format('%h hours, %i minutes');
                } else {
                  $lunch_display = "No Lunch";
                }

                // Display the data with image
                echo "<tr>";
                echo "<td>" . htmlspecialchars($id) . "</td>";
                echo "<td>" . htmlspecialchars($employee_id) . "</td>";
                echo "<td>" .  htmlspecialchars($name) . "</td>";
                echo "<td>" .  htmlspecialchars($department) . "</td>";
                echo "<td>" . date('H:i:s', strtotime($login_time)) . "</td>";
                echo "<td>" . ($logout_dt ? date('H:i:s', strtotime($logout_time)) : "--:--") . "</td>";
                echo "<td><img src='image_proof/" . htmlspecialchars($image) . "' alt='User Image' class='img-thumbnail' width='60' height='60'></td>";
                echo "</tr>";
              }

              echo "</tbody></table>";
            } else {
              echo "No records found for today.";
            }

            $conn->close();
            ?>
          </div>
        </div>
        <script>
        
          $(document).ready(function() {
            $('#userTable').DataTable({
              "paging": true,
              "searching": true,
              "ordering": true,
              "info": true
            });

            checkSession();
          });

          // Handle image click to zoom in and out
          $(document).on('click', '.img-thumbnail', function() {
            if ($(this).hasClass('zoomed')) {
              // If clicked image is already zoomed, remove the zoom effect
              $(this).removeClass('zoomed');
              $('.zoomed-backdrop').remove(); // Remove the dark backdrop
            } else {
              // Otherwise, add the zoom effect and backdrop
              $(this).addClass('zoomed');
              $('body').append('<div class="zoomed-backdrop"></div>');
            }
          });

          // Handle backdrop click to remove zoom
          $(document).on('click', '.zoomed-backdrop', function() {
            $('.zoomed').removeClass('zoomed');
            $(this).remove(); // Remove the backdrop
          });

          function checkSession() {
            let sessionID = "<?php echo $_SESSION['session_email'] ?? ''; ?>";
            console.log(sessionID);
          }

        </script>
    </div>
  </div>
</body>

</html>