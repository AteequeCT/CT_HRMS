<?php
session_start();

include 'includes/connection.php';
include 'includes/header.php';
include 'includes/hr_sidebar.php';

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

// Get the current date
$currentDate = date('Y-m-d');

// Prepare and execute SQL query with error handling
$sql = "SELECT id, date, TIME_FORMAT(time, '%H:%i') as time, purpose, links FROM meeting_links WHERE date >= ?";
$stmt = $conn->prepare($sql);

if ($stmt === false) {
    die("Error preparing query: " . $conn->error);
}

$stmt->bind_param("s", $currentDate);

if (!$stmt->execute()) {
    die("Error executing query: " . $stmt->error);
}

$result = $stmt->get_result();
?>


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
                    <a href="#">Meetings</a>
                </li>
                <li class="separator">
                    <i class="icon-arrow-right"></i>
                </li>
                <li class="nav-item">
                    <a href="#">Meetings Links</a>
                </li>
            </ul>
        </div>

        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">Meeting Links</h4>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <form id="dateFilterForm" class="d-flex flex-wrap align-items-center justify-content-between">

                                <!-- Left: Date Filter -->
                                <div class="d-flex align-items-center mb-2 mb-md-0">
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
                                <div class="d-flex align-items-center">
                                    <button type="submit" class="btn btn-sm btn-success me-2">
                                        <i class="fas fa-filter"></i> Filter
                                    </button>
                                    <a href="meeting.php" class="btn btn-sm btn-outline-secondary">
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
                        $sql = "SELECT id,date, TIME_FORMAT(time, '%H:%i') as time, purpose, links FROM meeting_links WHERE date = ?";
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

                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Date</th>
                                    <th>Time</th>
                                    <th>Purpose</th>
                                    <th>Meeting Link</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if ($result && $result->num_rows > 0) {
                                    while ($row = $result->fetch_assoc()) {
                                        // Check if 'id' exists in the row to avoid undefined index error
                                        $meetingId = isset($row['id']) ? htmlspecialchars($row['id']) : '';
                                        echo "<tr data-meeting-id=\"{$meetingId}\">
                                            <td>" . htmlspecialchars($row['date']) . "</td>
                                            <td>" . htmlspecialchars($row['time']) . "</td>
                                            <td>" . htmlspecialchars($row['purpose']) . "</td>
                                            <td><a href=\"" . htmlspecialchars($row['links']) . "\" target=\"_blank\">" . htmlspecialchars($row['links']) . "</a></td>
                                          </tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='4' class='text-center'>No meetings found.</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                    <?php if ($isHrAdmin || $isAdmin): ?>
                        <!-- Delete Button -->
                        <div class="d-flex align-items-center mb-3">
                            <a href="meeting_details.php" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Add Meeting
                            </a>
                            <button id="deleteSelected" class="btn btn-danger ms-2">
                                <i class="fas fa-trash-alt"></i> Delete Selected
                            </button>
                        </div>
                    <?php endif; ?>

                    <script>
                    $(document).ready(function() {
                        // Add checkboxes to each row (except header)
                        $('table tbody tr').each(function() {
                            var meetingId = $(this).data('meeting-id');
                            $(this).prepend('<td><input type="checkbox" class="row-checkbox" data-id="' + meetingId + '"></td>');
                        });
                        // Add checkbox header
                        $('table thead tr').prepend('<th><input type="checkbox" id="selectAll"></th>');

                        // Select/Deselect all
                        $('#selectAll').on('change', function() {
                            $('.row-checkbox').prop('checked', this.checked);
                        });

                        $('#deleteSelected').on('click', function() {
                            var selectedIds = [];
                            $('.row-checkbox:checked').each(function() {
                                selectedIds.push($(this).data('id'));
                            });

                            if (selectedIds.length === 0) {
                                Toastify({
                                    text: "Please select at least one meeting to delete.",
                                    duration: 3000,
                                    gravity: "top",
                                    position: "right",
                                    backgroundColor: "#ffc107",
                                    close: true
                                }).showToast();
                                return;
                            }

                            if (!confirm("Are you sure you want to delete selected meetings?")) {
                                return;
                            }

                            $.ajax({
                                url: '', // Same page
                                type: 'POST',
                                data: { delete_meeting_ids: selectedIds },
                                success: function(response) {
                                    Toastify({
                                        text: "Selected meetings have been deleted.",
                                        duration: 3000,
                                        gravity: "top",
                                        position: "right",
                                        backgroundColor: "#28a745",
                                        close: true
                                    }).showToast();
                                    setTimeout(function() {
                                        location.reload();
                                    }, 1200);
                                },
                                error: function() {
                                    Toastify({
                                        text: "Could not delete meetings.",
                                        duration: 3000,
                                        gravity: "top",
                                        position: "right",
                                        backgroundColor: "#dc3545",
                                        close: true
                                    }).showToast();
                                }
                            });
                        });
                    });
                    </script>
                    <!-- Toastify CSS & JS -->
                    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
                    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>

                    <?php
                    // Backend: Handle AJAX delete request
                    if (
                        ($_SERVER['REQUEST_METHOD'] === 'POST') &&
                        ($isHrAdmin || $isAdmin) &&
                        isset($_POST['delete_meeting_ids']) &&
                        is_array($_POST['delete_meeting_ids'])
                    ) {
                        include_once 'includes/connection.php';
                        $ids = array_map('intval', $_POST['delete_meeting_ids']);
                        if (!empty($ids)) {
                            $placeholders = implode(',', array_fill(0, count($ids), '?'));
                            $types = str_repeat('i', count($ids));
                            $stmt = $conn->prepare("DELETE FROM meeting_links WHERE id IN ($placeholders)");
                            if ($stmt) {
                                $stmt->bind_param($types, ...$ids);
                                $stmt->execute();
                                $stmt->close();
                            }
                        }
                        // End script for AJAX
                        exit;
                    }
                    ?>


                </div>
            </div>
        </div>

    </div>
</div>
</div>

<script>
    $(document).ready(function() {
        $('.table').DataTable({
            "order": [
                [0, "asc"]
            ],
            "pageLength": 10
        });
    });


    // Highlight current sidebar link based on exact URL match
    document.querySelectorAll('#sidebar .nav-link').forEach(link => {
        if (link.href === window.location.href) {
            link.classList.add('active');
        } else {
            link.classList.remove('active'); // Ensure other links are not active
        }
    });

    document.getElementById('logoutButton').addEventListener('click', function() {
        $.ajax({
            url: 'logoutSession.php',
            type: 'POST',
            success: function(response) {
                Swal.fire({
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
    });

    function toggleSidebar() {
        var sidebar = document.getElementById("sidebar");
        sidebar.classList.toggle("active");
    }
</script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</body>

</html>

<?php
$conn->close();
?>