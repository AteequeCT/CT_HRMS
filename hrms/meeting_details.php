<?php
session_start(); // Start the session
include 'includes/connection.php';
include 'includes/header.php';
include 'includes/hr_sidebar.php';

$team_admin = ['Gautham@crawlerstechnologies.com'];

// Check if the user is HR admin or one of the team leads
$TeamLead = isset($_SESSION['email']) && in_array($_SESSION['email'], $team_admin);
// List of team lead emails
$team_lead_emails = [
  'sanjana@crawlerstechnologies.com',
  'abhishek.crawlerstechnology@gmail.com',
  'ateeque@crawlerstechnologies.com',
  'Sangeeta@crawlerstechnologies.com'
];

// Check if the user is HR admin or one of the team leads
$isTeamLead = isset($_SESSION['email']) && in_array($_SESSION['email'], $team_lead_emails);

// print_r($isTeamLead);exit;
// Check if HR admin is logged in
$isHrAdmin = isset($_SESSION['hr']) && $_SESSION['hr'] === 'true';


$conn->close();
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
                  <a href="#"> Add Meetings</a>
                </li>
              </ul>
            </div>


    <div class="col-md-12">
    <div class="card">
        <div class="card-header">
            <h4 class="card-title">Add Meetings</h4>
        </div>
        <div class="card-body">
            <form method="post" id="addClientForm">
              <input type="hidden" name="id" value="<?php echo isset($id) ? $id : ''; ?>">
              <div class="form-group mb-3">
                <label for="meetingDate" class="form-label">Meeting Date</label>
                <input type="date" name="date" id="meetingDate" class="form-control" required />
              </div>
              <div class="form-group mb-3">
                <label for="meetingTime" class="form-label">Meeting Time</label>
                <input type="time" name="time" id="meetingTime" class="form-control" required />
              </div>
              <div class="form-group mb-3">
                <label for="meetingPurpose" class="form-label">Purpose Of Meeting </label>
                <input type="text" name="purpose" id="meetingPurpose" class="form-control" placeholder="Purpose of the meeting " required />
              </div>
              <div class="form-group mb-3">
                <label for="meetingLink" class="form-label">Meeting Link</label>
                <input type="text" name="links" id="meetingLink" class="form-control" placeholder="Enter meeting link" required />
              </div>
              <div class="text-center">
                <button type="submit" class="btn btn-primary" id="save-estimate-btn" form="addClientForm">Submit</button>
              </div>
            </form>
          </div>
        </div>
<!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script> -->
<!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script> -->
<script>
    $(document).ready(function() {
        $('#addClientForm').submit(function(e) {
            e.preventDefault(); // Prevent default form submission

            let formData = new FormData(this);

            $.ajax({
                url: 'ajax.php?action=save_meeting', // Backend PHP file
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                success: function(response) {
                    if (response.trim() === '1') {
                        toastr.success('Data successfully added.');
                        setTimeout(function() {
                            window.location.href = 'index.php?page=employee_list';
                        }, 2000);
                    } else {
                        toastr.error('Failed to add data. Please try again.');
                    }
                },
                error: function() {
                    toastr.error('Server error. Please check your PHP backend.');
                }
            });
        });
    });
</script>

<!-- jQuery -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<!-- Toastr -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastr/build/toastr.min.css" />