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


// List of admin emails
// $admin_emails = [
//     'admin@gmail.com'
// ];

// $isAdmin = isset($_SESSION['admin']) && in_array($_SESSION['admin'], $admin_emails);
$isHrAdmin = isset($_SESSION['hr']) && $_SESSION['hr'] === true;
$isAdmin = isset($_SESSION['admin']) && $_SESSION['admin'] === true;


// Combined check for HR or Admin access
$showHrSidebar = $isHrAdmin || $isAdmin;

// Pagination configuration
$records_per_page = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $records_per_page;

// Search functionality
$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';
$search_condition = $search ? "AND (session_email LIKE '%$search%' OR subject LIKE '%$search%' OR reason LIKE '%$search%')" : '';

// Fetch leave requests based on role
if ($isHrAdmin || $isAdmin) {
    // HR Admin or Admin fetches all leave requests
    $sql = "SELECT SQL_CALC_FOUND_ROWS id, start_date, end_date, session_email, subject, reason, status 
            FROM leave_request 
            WHERE 1 $search_condition
            ORDER BY start_date DESC 
            LIMIT $offset, $records_per_page";
} elseif ($isTeamLead) {
    // Team Lead fetches all requests
    $sql = "SELECT SQL_CALC_FOUND_ROWS id, start_date, end_date, session_email, subject, reason, status 
            FROM leave_request 
            WHERE 1 $search_condition
            ORDER BY start_date DESC 
            LIMIT $offset, $records_per_page";
} else {
    // Regular user fetches only their leave requests
    $sessionEmail = $conn->real_escape_string($_SESSION['email']);
    $sql = "SELECT SQL_CALC_FOUND_ROWS id, start_date, end_date, session_email, subject, reason, status 
            FROM leave_request 
            WHERE session_email = '$sessionEmail' $search_condition
            ORDER BY start_date DESC 
            LIMIT $offset, $records_per_page";
}

$result = $conn->query($sql);
$total_records = $conn->query("SELECT FOUND_ROWS()")->fetch_row()[0];
$total_pages = ceil($total_records / $records_per_page);

// Handle leave approval or rejection
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($isHrAdmin || $isTeamLead || $isAdmin)) {
    $leaveId = $_POST['leave_id'];
    $action = $_POST['action']; // "accept" or "reject"
    $newStatus = $action === 'accept' ? 'Accepted' : 'Rejected';

    // Update leave status in the database
    $updateSql = "UPDATE leave_request SET status = '$newStatus' WHERE id = $leaveId";
    $conn->query($updateSql);

    // Redirect back to the page to prevent form resubmission
    header("Location: {$_SERVER['PHP_SELF']}?page=$page" . ($search ? "&search=$search" : ""));
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leave Requests</title>
  

    <style>
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

        #sidebar ul.nav li:last-child {
            border-bottom: none;
            margin-bottom: 0;
        }

        .nav {
            --bs-nav-link-padding-x: 1rem;
            --bs-nav-link-padding-y: 0.5rem;
            --bs-nav-link-color: var(--bs-link-color);
            --bs-nav-link-hover-color: #000;
            --bs-nav-link-disabled-color: var(--bs-secondary-color);
            display: flex;
            flex-wrap: wrap;
            padding-left: 0;
            margin-bottom: 0;
            list-style: none;
            color: #000;
        }

        .nav-link {
            padding: 10px 20px;
            font-size: 16px;
            color: #333;
            display: block;
            position: relative;
            padding-bottom: 10px;
        }

        .nav-link:hover,
        .nav-link.active {
            background-color: #e9ecef;
            border-radius: 5px;
        }

        #sidebar .nav-link.active {
            background-color: #dee2e6;
            color: #000;
            border-left: 4px solid rgb(65, 70, 78);
            font-weight: bold;
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
            width: calc(100% - 240px);
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
                width: 100%;
                padding: 15px;
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
        }

        .logo {
            width: 110%;
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

            #sidebar {
                left: 0;
            }

            .logo1 {
                display: none;
            }
        }

        @media (max-width: 767px) {
            #mobile-logo {
                display: block;
                width: 40%;
            }

            #desktop-logo {
                display: none;
            }

            .logo1 {
                display: block;
                max-width: 50%;
                height: auto;
                margin-top: -8%;
                margin-left: -5%;
            }
        }

        .btn-primary {
            --bs-btn-color: #fff;
            --bs-btn-bg: #686a6e;
            --bs-btn-border-color: #57585a;
            --bs-btn-hover-color: #fff;
            --bs-btn-hover-bg: #444546;
            --bs-btn-hover-border-color: #1c1c1c;
            --bs-btn-active-color: #fff;
            --bs-btn-active-bg: #444546;
            --bs-btn-active-border-color: #444546;
        }

        #sidebar::-webkit-scrollbar {
            display: none;
        }

        .logo {
            max-width: 80%;
            height: auto;
            margin-right: 15px;
        }

        @media (max-width: 768px) {
            .logo {
                margin-left: 25px;
                max-width: 70%;
            }
        }

        .card {
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            border: none;
            margin-bottom: 20px;
            width: 100%;
        }
        
        .card-header {
            background-color: #f8f9fa;
            border-bottom: 1px solid rgba(0, 0, 0, 0.1);
            border-radius: 10px 10px 0 0 !important;
            padding: 15px 20px;
        }
        
        .card-title {
            margin-bottom: 0;
            font-weight: 600;
        }
        
        .table-responsive {
            border-radius: 8px;
            overflow-x: auto;
            width: 100%;
        }
        
        .table {
            margin-bottom: 0;
            width: 100%;
            min-width: 600px;
        }
        
        .table th {
            background-color: #41464e;
            color: white;
            font-weight: 500;
            white-space: nowrap;
        }
        
        .table td {
            vertical-align: middle;
        }
        
        .status-badge {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 500;
            display: inline-block;
            min-width: 80px;
            text-align: center;
        }
        
        .status-pending {
            background-color: #fff3cd;
            color: #856404;
        }
        
        .status-accepted {
            background-color: #d4edda;
            color: #155724;
        }
        
        .status-rejected {
            background-color: #f8d7da;
            color: #721c24;
        }
        
        .action-buttons {
            white-space: nowrap;
        }
        
        .btn-sm {
            padding: 0.25rem 0.5rem;
            font-size: 0.8rem;
        }
        
        .no-requests {
            padding: 20px;
            text-align: center;
            color: #6c757d;
        }
        
        .request-card {
            transition: all 0.3s ease;
        }
        
        .request-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.1);
        }

        .pagination .page-item.active .page-link {
            background-color: #41464e;
            border-color: #41464e;
            color: #fff;
        }

        .pagination .page-link {
            color: #41464e;
        }

        .search-box {
            max-width: 300px;
        }

        @media (max-width: 767px) {
            .search-box {
                max-width: 100%;
                margin-top: 10px;
            }
            
            .card-header {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .table-responsive {
                border: 1px solid #dee2e6;
            }
            
            .action-buttons .btn-sm {
                padding: 0.2rem 0.4rem;
                font-size: 0.7rem;
            }
            
            td:nth-child(5) { /* reason column */
                max-width: 150px;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
            }
        }

        .row {
            --bs-gutter-x: 1.5rem;
            --bs-gutter-y: 0;
            display: flex;
            flex-wrap: nowrap;
            margin-top: calc(-1 * var(--bs-gutter-y));
            margin-right: calc(-.5 * var(--bs-gutter-x));
            margin-left: calc(-.5 * var(--bs-gutter-x));
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
                  <a href="dashboard1.php">
                    <i class="icon-home"></i>
                  </a>
                </li>
                
                <li class="separator">
                  <i class="icon-arrow-right"></i>
                </li>
                <li class="nav-item">
                  <a href="#">Leave approval</a>
                </li>
              </ul>
            </div>


<div class="col-md-12">
               
                <div class="card request-card">
                    <div class="card-header d-flex justify-content-between align-items-center flex-wrap">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-calendar-alt me-2"></i>Leave Applications
                        </h5>
                        
                        <div class="d-flex align-items-center flex-wrap">
                            <span class="badge bg-secondary me-3 mb-2 mb-md-0">
                                <?php echo $total_records; ?> request(s)
                            </span>
                            <form method="GET" class="search-box">
                                <div class="input-group">
                                    <input type="text" name="search" class="form-control form-control-sm" 
                                           placeholder="Search..." value="<?php echo htmlspecialchars($search); ?>">
                                    <button class="btn btn-outline-secondary btn-sm" type="submit">
                                        <i class="fas fa-search"></i>
                                    </button>
                                    <?php if ($search): ?>
                                        <a href="leave_acc.php" class="btn btn-outline-danger btn-sm">
                                            <i class="fas fa-times"></i>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </form>
                        </div>
                    </div>

                     <div class="row mb-3">
                        <div class="col-md-12">
                            <form id="dateFilterForm" class="d-flex flex-wrap align-items-center justify-content-between">

                                <!-- Left: Date Filter -->
                                <div class="d-flex align-items-center mb-2 mb-md-0 ms-3">
                                    <label for="filter_date" class="me-2 mb-0">
                                        <i class="fas fa-calendar-alt text-primary"></i> 
                                    </label>
                                    <input type="date"
                                        id="filter_date"
                                        name="filter_date"
                                        class="form-control form-control-sm"
                                        value="<?php echo isset($_GET['filter_date']) ? htmlspecialchars($_GET['filter_date']) : ''; ?>">
                                </div>

                                <!-- Right: Buttons -->
                                <div class="d-flex align-items-center me-3">
                                    <button type="submit" class="btn btn-sm btn-success ms-3">
                                        <i class="fas fa-filter"></i> Filter
                                    </button>
                                    <a href="leave_acc.php" class="btn btn-sm btn-outline-secondary">
                                        <i class="fas fa-sync-alt"></i> Reset
                                    </a>
                                </div>

                            </form>
                        </div>
                    </div>
                     <?php
                    // Date filter logic
                    $filterDate = isset($_GET['filter_date']) && $_GET['filter_date'] !== '' ? $_GET['filter_date'] : null;

                    if ($filterDate) {
                        $sql = "SELECT * FROM leave_request WHERE start_date = ?";
                        $stmt = $conn->prepare($sql);
                        if ($stmt === false) {
                            die("Error preparing query: " . $conn->error);
                        }
                        $stmt->bind_param("s", $filterDate);
                        if (!$stmt->execute()) {
                            die("Error executing query: " . $stmt->error);
                        }
                        $result = $stmt->get_result();
                    }
                    ?>
                    
                    
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>Start Date</th>
                                        <th>End Date</th>
                                        <th>Employee</th>
                                        <th>Subject</th>
                                        <th>Reason</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if ($result->num_rows > 0): ?>
                                        <?php while ($row = $result->fetch_assoc()): ?>
                                            <tr>
                                                <td class="text-nowrap">
                                                    <?php echo date('d M Y', strtotime($row['start_date'])); ?>
                                                </td>
                                                <td class="text-nowrap">
                                                    <?php echo date('d M Y', strtotime($row['end_date'])); ?>
                                                </td>
                                                <td><?php echo $row['session_email']; ?></td>
                                                <td><?php echo htmlspecialchars($row['subject']); ?></td>
                                                <td style="max-width: 200px;" class="text-truncate" title="<?php echo htmlspecialchars($row['reason']); ?>">
                                                    <?php echo htmlspecialchars($row['reason']); ?>
                                                </td>
                                                <td class="text-nowrap">
                                                    <?php if ($row['status'] === 'Pending'): ?>
                                                        <span class="status-badge status-pending">
                                                            <i class="fas fa-clock me-1"></i>Pending
                                                        </span>
                                                    <?php elseif ($row['status'] === 'Accepted'): ?>
                                                        <span class="status-badge status-accepted">
                                                            <i class="fas fa-check-circle me-1"></i>Approved
                                                        </span>
                                                    <?php else: ?>
                                                        <span class="status-badge status-rejected">
                                                            <i class="fas fa-times-circle me-1"></i>Rejected
                                                        </span>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="action-buttons">
                                                    <?php if (( $isHrAdmin ) && $row['status'] === 'Pending'): ?>
                                                        <form method="POST" class="d-inline-flex gap-1">
                                                            <input type="hidden" name="leave_id" value="<?php echo $row['id']; ?>">
                                                            <button type="submit" name="action" value="accept" class="btn btn-success btn-sm">
                                                                <i class="fas fa-check"></i>
                                                            </button>
                                                            <button type="submit" name="action" value="reject" class="btn btn-danger btn-sm">
                                                                <i class="fas fa-times"></i>
                                                            </button>
                                                        </form>
                                                    <?php else: ?>
                                                        <span class="text-muted">-</span>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="7" class="no-requests">
                                                <i class="fas fa-inbox fa-2x mb-2"></i><br>
                                                No leave requests found
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                    <?php if ($total_pages > 1): ?>
                    <div class="card-footer bg-transparent">
                        <nav aria-label="Page navigation">
                            <ul class="pagination justify-content-center mb-0">
                                <!-- Previous Page Link -->
                                <li class="page-item <?php echo $page <= 1 ? 'disabled' : ''; ?>">
                                    <a class="page-link" 
                                       href="?page=<?php echo $page - 1; ?><?php echo $search ? '&search='.urlencode($search) : ''; ?>" 
                                       aria-label="Previous">
                                        <span aria-hidden="true">&laquo;</span>
                                    </a>
                                </li>
                                
                                <!-- Page Numbers -->
                                <?php 
                                $start_page = max(1, $page - 2);
                                $end_page = min($total_pages, $page + 2);
                                
                                if ($start_page > 1) {
                                    echo '<li class="page-item"><a class="page-link" href="?page=1'.($search ? '&search='.urlencode($search) : '').'">1</a></li>';
                                    if ($start_page > 2) {
                                        echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                                    }
                                }
                                
                                for ($i = $start_page; $i <= $end_page; $i++) {
                                    $active = $i == $page ? 'active' : '';
                                    echo '<li class="page-item '.$active.'"><a class="page-link" href="?page='.$i.($search ? '&search='.urlencode($search) : '').'">'.$i.'</a></li>';
                                }
                                
                                if ($end_page < $total_pages) {
                                    if ($end_page < $total_pages - 1) {
                                        echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                                    }
                                    echo '<li class="page-item"><a class="page-link" href="?page='.$total_pages.($search ? '&search='.urlencode($search) : '').'">'.$total_pages.'</a></li>';
                                }
                                ?>
                                
                                <!-- Next Page Link -->
                                <li class="page-item <?php echo $page >= $total_pages ? 'disabled' : ''; ?>">
                                    <a class="page-link" 
                                       href="?page=<?php echo $page + 1; ?><?php echo $search ? '&search='.urlencode($search) : ''; ?>" 
                                       aria-label="Next">
                                        <span aria-hidden="true">&raquo;</span>
                                    </a>
                                </li>
                            </ul>
                        </nav>
                    </div>
                    <?php endif; ?>
                </div>
            </main>
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

      
        
    </script>
</body>
</html>

<?php
$conn->close();
?>