<?php
session_start();
include 'includes/connection.php';
include 'includes/header.php';

if (isset($_SESSION['hr']) || isset($_SESSION['admin'])) {
  include 'includes/hr_sidebar.php';
} else {
  include 'includes/emp_sidebar.php';
}


if (!isset($_SESSION['register_id'])) {
  header("Location: login.php");
  exit();
}
?>
<style>
  .btn-light {
    background: #1a2035 !important;
    color:white;
        border-color: transparent;
}
  .status-card {
    position: absolute;
    top: -35px;
    z-index: 999;
    width: 180px;
    margin-left: -205px;
  }

  .status-locked .status-card {
    display: none !important;
  }

  .status-selector .btn[disabled] {
    opacity: 0.7;
    pointer-events: none;
  }


  .status-dropdown-wrapper button {
    min-width: 140px;
    text-align: left;
  }

  .status-card {
    min-width: 160px;
    background-color: #fff;
    box-shadow: 0 2px 12px rgba(0, 0, 0, 0.1);
    border-radius: 10px;
  }

  .status-card .form-check-label {
    cursor: pointer;
    display: flex;
    align-items: center;
  }

  .scrollable-cell {
    max-height: 100px;
    /* Adjust height as needed */
    max-width: 300px;
    /* Adjust width as needed */
    overflow: auto;
    white-space: pre-wrap;
    /* Wrap text nicely */
    word-break: break-word;
    padding: 4px;
    /* border: 1px solid #ddd;
    border-radius: 4px;
    background-color: #f9f9f9; */
  }

  .meeting-toggle-wrapper {
    display: flex;
    align-items: center;
    gap: 10px;
    font-family: 'Segoe UI', sans-serif;
    font-size: 15px;
    background-color: #f5f8fb;
    padding: 12px 18px;
    border-radius: 10px;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
    max-width: 320px;
  }

  .meeting-toggle {
    position: relative;
    display: inline-block;
    width: 52px;
    height: 28px;
  }

  .meeting-toggle input {
    opacity: 0;
    width: 0;
    height: 0;
  }

  .meeting-toggle .slider {
    position: absolute;
    cursor: pointer;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: #ccc;
    transition: 0.4s;
    border-radius: 28px;
  }

  .meeting-toggle .slider:before {
    position: absolute;
    content: "";
    height: 20px;
    width: 20px;
    left: 4px;
    bottom: 4px;
    background-color: white;
    transition: 0.4s;
    border-radius: 50%;
  }

  .meeting-toggle input:checked+.slider {
    background-color: #28a745;
  }

  .meeting-toggle input:checked+.slider:before {
    transform: translateX(24px);
  }
</style>


<!-- <link rel="stylesheet" href="sidebar.css">
<link rel="stylesheet" href="css/backend.css"> -->
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
          <a href="#">Conversations</a>
        </li>
      </ul>
    </div>
  </div>

  <div class="content-page">

    <div style="display: flex;align-items: center;justify-content: space-between;flex-wrap: wrap;gap: 10px;
    padding: 15px 20px;background-color: #f5f8fb;border-radius: 12px;box-shadow: 0 2px 6px rgba(0,0,0,0.1);
    margin-bottom: 20px;font-family: sans-serif;">

      <!-- Date Picker -->
      <input type="date" id="date-input" onchange="fetchConversations()"
        style="padding: 6px 10px; border: 1px solid #ccc; border-radius: 6px; font-size: 14px;">

      <!-- Status Dropdown -->
      <div class="dropdown">
        <button class="btn btn-light dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
          <span id="selected-status"><i class="fas fa-tasks me-1"></i> Select Status</span>
        </button>
        <ul class="dropdown-menu p-3 shadow-sm" style="white-space: nowrap; min-width: 220px;">
          <li>
            <div class="form-check text-danger">
              <input class="form-check-input" type="radio" name="status" id="not_interested" value="not_interested">
              <label class="form-check-label" for="not_interested"><i class="fas fa-ban me-1"></i> Not Interested</label>
            </div>
          </li>
          <li>
            <div class="form-check text-success">
              <input class="form-check-input" type="radio" name="status" id="deal_closed" value="deal_closed">
              <label class="form-check-label" for="deal_closed"><i class="fas fa-handshake me-1"></i> Deal Closed</label>
            </div>
          </li>
          <li>
            <div class="form-check text-primary">
              <input class="form-check-input" type="radio" name="status" id="ongoing_communication" value="ongoing_communication">
              <label class="form-check-label" for="ongoing_communication"><i class="fas fa-comments me-1"></i> Ongoing Communication</label>
            </div>
          </li>
          <li>
            <div class="form-check text-warning">
              <input class="form-check-input" type="radio" name="status" id="meeting_done" value="meeting_done">
              <label class="form-check-label" for="meeting_done"><i class="fas fa-check-circle me-1"></i> Meeting Done</label>
            </div>
          </li>
          <li>
            <div class="form-check text-info">
              <input class="form-check-input" type="radio" name="status" id="meeting_scheduled" value="meeting_scheduled">
              <label class="form-check-label" for="meeting_scheduled"><i class="fas fa-calendar-alt me-1"></i> Meeting Scheduled</label>
            </div>
          </li>
          <li>
            <div class="form-check" style="color: lightgreen;">
              <input class="form-check-input" type="radio" name="status" id="quotation_sent" value="quotation_sent">
              <label class="form-check-label" for="quotation_sent"><i class="fas fa-file-invoice-dollar me-1"></i> Quotation Sent</label>
            </div>
          </li>
          <li>
            <div class="form-check text-orange" style="color: orange;">
              <input class="form-check-input" type="radio" name="status" id="non_responsive" value="non_responsive">
              <label class="form-check-label" for="non_responsive"><i class="fas fa-user-slash me-1"></i> Non-Responsive</label>
            </div>
          </li>
          <li>
            <div class="form-check" style="color: purple;">
              <input class="form-check-input" type="radio" name="status" id="sow_shared" value="sow_shared">
              <label class="form-check-label" for="sow_shared"><i class="fas fa-share-square me-1"></i> SOW Shared</label>
            </div>
          </li>
        </ul>
      </div>


      <!-- <div class="meeting-toggle-wrapper">
        <label class="meeting-toggle">
          <input type="checkbox" id="meeting-status-toggle">
          <span class="slider"></span>
        </label>
        <span id="meeting-status-label">
          <p>Meeting</p>❌ Not Completed
        </span>
      </div> -->


      <!-- Add Data Button -->
      <button class="btn" data-toggle="modal" data-target="#addDataModal"
        style="background-color:#1a2035; color: white; font-weight: 500;">
        <i class="fas fa-plus-circle me-1"></i> Add Data
      </button>

      <!-- Remove Button Example (Trash Icon) -->
      <button class="btn btn-outline-danger remove-user-btn" data-id="USER_ID_HERE">
        <i class="fas fa-trash-alt"></i>
      </button>
      <div class="dropdown">
        <label style="cursor: pointer;" title="File Options" data-bs-toggle="dropdown" aria-expanded="false">
          <i class="	fas fa-ellipsis-v" style="font-size: 20px;"></i>
        </label>
        <ul class="dropdown-menu shadow-sm p-2">
          <li>
            <label for="file-upload" class="dropdown-item" style="cursor: pointer;">
              <i class="fas fa-file-upload me-2 text-success"></i> Upload File
            </label>
          </li>
          <li>
            <button class="dropdown-item" id="view-files-btn">
              <i class="fas fa-folder-open me-2 text-info"></i> View Files
            </button>
          </li>
        </ul>
      </div>
      <input type="file" id="file-upload" style="display: none;">

    </div>

    <?php
include 'includes/connection.php';
    $cust_id = $_GET['cust_id'] ?? null;

    if ($cust_id) {
      $check = $conn->prepare("SELECT requirement FROM requirements WHERE cust_id = ?");
      $check->bind_param("s", $cust_id);
      $check->execute();
      $result = $check->get_result();
      $requirement = $result->fetch_assoc();

      if ($requirement && !empty($requirement['requirement'])) {
        $requirementText = htmlspecialchars($requirement['requirement']);
        echo '<button class="btn btn-info view-btn" data-cust="' . $cust_id . '" data-req="' . htmlspecialchars($requirement['requirement']) . '">
                <i class="fa fa-eye"></i> View Requirements
              </button>';
      } else {
        echo '<button class="btn btn-success add-btn" data-cust="' . $cust_id . '">
                <i class="fa fa-plus"></i> Add Requirements
              </button>';
      }
    } else {
      echo 'Customer ID not provided in URL.';
    }

    ?>


    <!-- View Modal -->
    <div class="modal fade" id="viewModal" tabindex="-1">
      <div class="modal-dialog">
        <div class="modal-content position-relative">
          <div class="modal-header">
            <h5 class="modal-title">View Requirements</h5>
            <!-- Optional close button -->
            <button type="button" class="close" data-dismiss="modal">&times;</button>
          </div>

          <!-- Edit button top-right -->
          <button id="editBtn" class="btn btn-warning btn-sm position-absolute" style="top: 10px; right: 10px; z-index: 1051;">
            Update
          </button>

          <div class="modal-body" style="max-height: 390px; overflow:auto;">
            <p id="viewRequirementText" class="mb-2"><?= $requirementText ?></p>
            <textarea id="editRequirementTextarea" class="form-control d-none"><?= $requirementText ?></textarea>
            <input type="hidden" id="editCustId" value="<?= $cust_id ?>">

            <button type="button" class="btn btn-success mt-3 d-none" id="updateBtn">Save</button>
          </div>
        </div>
      </div>
    </div>


    <!-- Add Modal -->
    <div class="modal fade" id="addModal" tabindex="-1">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Add Requirement</h5>
          </div>
          <div class="modal-body">
            <textarea id="requirementInput" class="form-control" name="requirement" rows="4" placeholder="Enter requirement"></textarea>
          </div>
          <div class="modal-footer">
            <button class="btn btn-primary" id="saveRequirementBtn">Save</button>
          </div>
        </div>
      </div>
    </div>









    <div id="file-list" style="display:none; margin-top: 20px;"></div>


    <!-- Modal for adding data -->
    <div class="modal fade" id="addDataModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
      aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Add Data</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <!-- Tabs for selecting the form -->
            <ul class="nav nav-tabs" id="formTabs" role="tablist">
              <li class="nav-item">
                <a class="nav-link active" id="conversation-tab" data-toggle="tab"
                  href="#conversation-form-tab" role="tab" aria-controls="conversation-form-tab"
                  aria-selected="true">Conversation</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" id="add-call-tab" data-toggle="tab" href="#add-call-form-tab"
                  role="tab" aria-controls="add-call-form-tab" aria-selected="false" style="margin-left:25%; width: 100px;">Add Call</a>
              </li>
            </ul>

            <!-- Tab Content -->
            <div class="tab-content mt-3">
              <!-- Conversation Form -->
              <div class="tab-pane fade show active" id="conversation-form-tab" role="tabpanel"
                aria-labelledby="conversation-tab">
                <form id="conversation-form">
                  <input type="hidden" id="cust_id" name="cust_id" value="">
                  <div class="form-group">
                    <label for="date">Date</label>
                    <input type="date" id="date" name="date" class="form-control" required>
                  </div>
                  <div class="form-group">
                    <label for="time">Time</label>
                    <input type="time" id="time" name="time" class="form-control" required>
                  </div>

                  <div class="form-group">
                    <label for="user_conversation">User Conversation</label>
                    <textarea id="user_conversation" name="user_conversation"
                      class="form-control" required></textarea>
                  </div>
                  <div class="form-group">
                    <label for="admin_conversation">Admin Conversation</label>
                    <textarea id="admin_conversation" name="admin_conversation"
                      class="form-control" required></textarea>
                  </div>
                  <div class="form-group">
                    <label for="remarks_1">Remarks 1</label>
                    <textarea id="remarks1" name="remarks1" class="form-control"></textarea>
                  </div>
                  <div class="form-group">
                    <label for="remarks_2">Remarks 2</label>
                    <textarea id="remarks2" name="remarks2" class="form-control"></textarea>
                  </div>
                  <button type="submit" class="btn btn-primary">Submit</button>
                </form>
              </div>

              <!-- Add Call Form -->
              <div class="tab-pane fade" id="add-call-form-tab" role="tabpanel"
                aria-labelledby="add-call-tab">
                <form id="call-form">
                  <input type="hidden" id="cust_id" name="cust_id" value="">
                  <div class="form-group">
                    <label for="callDate">Call Date</label>
                    <input type="date" id="callDate" name="callDate" class="form-control"
                      required>
                  </div>
                  <div class="form-group">
                    <label for="callTime">Call Time</label>
                    <input type="time" id="callTime" name="callTime" class="form-control"
                      required>
                  </div>
                  <div class="form-group">
                    <label for="callDetails">Call Details</label>
                    <textarea id="callDetails" name="details" class="form-control"
                      required></textarea>
                  </div>

                  <button type="submit" class="btn btn-primary">Submit</button>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Table to display the conversations -->
    <div style="overflow-x: auto; max-width: 100%; border: 1px solid #ddd; margin-top: 50px;">
      <table id="conversationTable" class="table">
        <thead>
          <tr>
            <th>ID</th>
            <!-- <th> Date/Time </th> -->
            <!-- <th>Time</th> -->
            <th>Admin Name</th>
            <th>User Conversation</th>
            <th>Admin Conversation</th>
            <!-- <th>Remarks 1</th><td class="editable">${convo.remarks1}</td>
                    <th>Remarks 2</th><td class="editable">${convo.remarks2}</td> -->
            <th>Action</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody id="conversation-table">
          <!-- Data will be injected here dynamically -->
        </tbody>
      </table>

      <div id="emptyMessage" style="display: none;">
        No conversation found.
      </div>
    </div>
  </div>

  <script>
    $(document).ready(function() {
      cust_id = $('#cust_id').val();
      console.log("cust_id:", cust_id); // Debugging line
      // View modal
      $('.view-btn').click(function() {
        var reqText = $(this).data('req');
        $('#viewRequirementText').text(reqText);
        $('#viewModal').modal('show');
      });

      // Add modal
      $('.add-btn').click(function() {
        var cust_id = $(this).data('cust_id');
        $('#cust_id').val(cust_id);
        $('#requirementInput').val('');
        $('#addModal').modal('show');
      });
      // Edit button
      $('#editBtn').click(function() {
        $('#viewRequirementText').addClass('d-none');
        $('#editRequirementTextarea').removeClass('d-none');
        $('#editBtn').addClass('d-none');
        $('#updateBtn').removeClass('d-none');
      });

    });
  </script>
  <script>
    $(document).ready(function() {
      const custId = $('#cust_id').val();

      // Save button
      $('#saveRequirementBtn').click(function() {
        var requirement = $('#requirementInput').val();

        if (requirement.trim() === "") {
          Swal.fire('Error', 'Please enter a requirement.', 'error');
          return;
        }

        $.ajax({
          url: 'requirements.php',
          type: 'POST',
          data: {
            cust_id: cust_id,
            requirements: requirement
          },
          success: function(response) {
            Swal.fire('Saved!', 'Requirement has been stored.', 'success').then(() => {
              location.reload(); // reload to show View button
            });
          },
          error: function() {
            Swal.fire('Error', 'Something went wrong.', 'error');
          }
        });
      });

      // Update/save edited requirement
      $('#updateBtn').click(function() {
        const updatedReq = $('#editRequirementTextarea').val();
        const custId = $('#editCustId').val();

        $.ajax({
          url: 'requirements.php',
          type: 'POST',
          data: {
            cust_id: custId,
            requirements: updatedReq
          },
          success: function(response) {
            try {
              const res = JSON.parse(response);
              if (res.success) {
                Swal.fire('Updated!', 'Requirement has been updated.', 'success').then(() => {
                  location.reload();
                });
              } else {
                Swal.fire('Error', res.error, 'error');
              }
            } catch (e) {
              Swal.fire('Error', 'Invalid response from server.', 'error');
            }
          },
          error: function() {
            Swal.fire('Error', 'Request failed.', 'error');
          }
        });
      });

      // 🔁 1. Fetch status on page load
      $.ajax({
        url: 'update_status.php',
        method: 'GET',
        data: {
          cust_id: custId
        },
        dataType: 'json',
        success: function(response) {
          const status = response.status;

          if (status) {
            // Check corresponding radio button
            $(`input[name="status"][value="${status}"]`).prop('checked', true);

            // Update button label and color
            const $statusDisplay = $('#selected-status');
            let statusColorClass = 'text-secondary';
            let icon = '<i class="fas fa-question-circle me-1"></i>';

            if (status === 'accepted') {
              statusColorClass = 'text-success';
              icon = '<i class="fas fa-check-circle me-1"></i>';
            } else if (status === 'rejected') {
              statusColorClass = 'text-danger';
              icon = '<i class="fas fa-times-circle me-1"></i>';
            } else if (status === 'in_progress') {
              statusColorClass = 'text-warning';
              icon = '<i class="fas fa-spinner me-1"></i>';
            }

            $statusDisplay
              .removeClass()
              .addClass(statusColorClass)
              .html(`${icon} ${status.replace('_', ' ').toUpperCase()}`);
          }
        },
        error: function(xhr, status, error) {
          console.error('Error fetching status:', error);
        }
      });

      // ✅ 2. Update status on radio change
      $('input[name="status"]').on('change', function() {
        const selectedStatus = $(this).val();
        const $statusDisplay = $('#selected-status');

        const statusMap = {
          not_interested: {
            text: 'Not Interested',
            class: 'text-danger',
            icon: 'fa-ban'
          },
          deal_closed: {
            text: 'Deal Closed',
            class: 'text-success',
            icon: 'fa-handshake'
          },
          ongoing_communication: {
            text: 'ongoing_communication',
            class: 'text-primary',
            icon: 'fa-comments'
          },
          meeting_done: {
            text: 'Meeting Done',
            class: 'text-warning',
            icon: 'fa-check-circle'
          },
          meeting_scheduled: {
            text: 'Meeting Scheduled',
            class: 'text-info',
            icon: 'fa-calendar-alt'
          },
          quotation_sent: {
            text: 'Quotation Sent',
            class: '',
            icon: 'fa-file-invoice-dollar',
            customColor: 'lightgreen'
          },
          non_responsive: {
            text: 'Non-Responsive',
            class: '',
            icon: 'fa-user-slash',
            customColor: 'orange'
          },
          sow_shared: {
            text: 'SOW Shared',
            class: '',
            icon: 'fa-share-square',
            customColor: 'purple'
          }
        };

        const info = statusMap[selectedStatus] || {};
        $statusDisplay.removeClass().addClass(info.class || '').css('color', info.customColor || '').html(`<i class="fas ${info.icon} me-1"></i> ${info.text}`);

        // Send AJAX to update PHP
        $.ajax({
          url: 'update_status.php',
          method: 'POST',
          data: {
            cust_id: custId,
            status: selectedStatus
          },
          success: function(response) {
            if (response !== 'success') {
              console.log('Update failed:', response);
            }
          },
          error: function(xhr, status, error) {
            alert('Error updating status: ' + error);
          }
        });
      });


      // Fetch Meeting Status on page load using AJAX
      $.ajax({
        url: 'update_status.php',
        method: 'GET',
        data: {
          cust_id: custId
        },
        success: function(response) {
          try {
            // Parse the response text as JSON manually
            const data = typeof response === 'string' ? JSON.parse(response) : response;
            const meetingStatus = parseInt(data.meeting_status || 0);
            $('#meeting-status-toggle').prop('checked', meetingStatus === 1);
            updateMeetingLabel(meetingStatus);
          } catch (e) {
            console.error('Error parsing response:', e);
            // Set default values if parsing fails
            $('#meeting-status-toggle').prop('checked', false);
            updateMeetingLabel(0);
          }
        },
        error: function(xhr, status, error) {
          console.error('AJAX error:', error);
          // Set default values on error
          $('#meeting-status-toggle').prop('checked', false);
          updateMeetingLabel(0);
        }
      });

      // Toggle Meeting Status via AJAX (unchanged)
      $('#meeting-status-toggle').on('change', function() {
        const meetingStatus = this.checked ? 1 : 0;
        updateMeetingLabel(meetingStatus);

        $.ajax({
          url: 'update_status.php',
          method: 'POST',
          data: {
            cust_id: custId,
            meeting_status: meetingStatus
          },
          success: function(response) {
            if (response.trim() === 'success') {
              console.log('Meeting status saved!');
            } else {
              alert('Meeting update failed!');
            }
          },
          error: function(xhr, status, error) {
            alert('AJAX error: ' + error);
          }
        });
      });


    });




    const searchInputs = [
      document.querySelector('#searchInput'),
      document.querySelector('#searchBarMobile')
    ];

    const userDashboard = document.querySelector('#conversation-table'); // tbody of the table

    searchInputs.forEach(input => {
      if (input) {
        input.addEventListener('input', function() {
          const query = this.value.toLowerCase().trim();

          const rows = userDashboard.querySelectorAll('tr');
          rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            if (text.includes(query)) {
              row.style.display = '';
            } else {
              row.style.display = 'none';
            }
          });
        });
      }
    });


    document.getElementById('file-upload').addEventListener('change', function() {
      const file = this.files[0];
      if (!file) return;

      const urlParams = new URLSearchParams(window.location.search);
      const custId = urlParams.get("cust_id");

      if (!custId) {
        alert("Customer ID missing in URL.");
        return;
      }

      const formData = new FormData();
      formData.append("file", file);
      formData.append("cust_id", custId);

      $.ajax({
        url: 'cust_upload_files.php',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {

          Swal.fire({
            icon: 'success',
            title: 'uploaded Successfully!',
            showConfirmButton: false,
            timer: 2000
          });
        },
        error: function() {
          Swal.fire("Error", "Upload failed.", "error");
        }
      });
    });


    $('#view-files-btn').on('click', function() {
      const custId = new URLSearchParams(window.location.search).get("cust_id");
      if (!custId) return alert("Missing cust_id");

      $.ajax({
        url: "cust_upload_files.php",
        type: "POST",
        data: {
          cust_id: custId
        },
        success: function(data) {
          $('#file-list').html(data).slideDown();
        }
      });
    });




    $(document).on('click', '.remove-user-btn', function(e) {
      e.preventDefault();
      e.stopPropagation();

      const userId = $('#cust_id').val();
      console.log("User ID to delete:", userId); // Debug!

      Swal.fire({
        title: "Are you sure?",
        text: "This user will be permanently deleted!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#3085d6",
        confirmButtonText: "Yes, delete it!"
      }).then((result) => {
        if (result.isConfirmed) {
          $.ajax({
            url: 'delete_client.php',
            type: 'POST',
            data: {
              id: userId
            },
            success: function(response) {
              console.log("Server response:", response); // Debug
              if (response.trim() === 'success') {
                Swal.fire({
                  icon: 'success',
                  title: 'Deleted Successfully!',
                  showConfirmButton: false,
                  timer: 2000
                }).then(() => {
                  window.location.href = "text.php";
                });
              } else {
                Swal.fire("Error", response, "error");
              }
            },
            error: function() {
              Swal.fire("Error", "Something went wrong!", "error");
            }
          });
        }
      });
    });


    // Function to set current date and time
    function setCurrentDateTime() {
      // Get the current date and time
      const now = new Date();

      // Format date as yyyy-mm-dd
      const currentDate = now.toISOString().split('T')[0];

      // Format time as hh:mm (24-hour format)
      const currentTime = now.toTimeString().slice(0, 5);

      // Set the value of the date and time inputs
      document.getElementById('date').value = currentDate;
      document.getElementById('time').value = currentTime;
    }

    // Call the function when the page loads
    window.onload = setCurrentDateTime;
    // Set today's date as the minimum date for the input
    const today = new Date().toISOString().split('T')[0];
    document.getElementById('callDate').setAttribute('min', today);

    // JavaScript to toggle sidebar state
    document.getElementById('sidebarToggle').addEventListener('click', function() {
      const sidebar = document.querySelector('.iq-sidebar');
      sidebar.classList.toggle('collapsed');
    });

    // Function to handle search filtering
    function handleSearch(inputElement) {
      const searchTerm = inputElement.value.toLowerCase(); // Get the search term
      const rows = document.querySelectorAll('#conversationTable tbody tr'); // Select all rows

      rows.forEach(row => {
        const rowText = row.innerText.toLowerCase(); // Get the row text
        row.style.display = rowText.includes(searchTerm) ? '' : 'none'; // Show/hide rows
      });
    }

    // // Add event listeners to both desktop and mobile search bars
    // document.getElementById('searchBarDesktop').addEventListener('input', function() {
    //     handleSearch(this);
    // });

    // document.getElementById('searchBarMobile').addEventListener('input', function() {
    //     handleSearch(this);
    // });


    document.addEventListener("DOMContentLoaded", function() {
      const urlParams = new URLSearchParams(window.location.search);
      const cust_id = urlParams.get('cust_id');
      // console.log(cust_id);return false;
      if (cust_id && cust_id.trim() !== '') {
        $('#cust_id').val(cust_id);
        $('#cust_id-display').html(`<p>Viewing data for: <strong>${cust_id}</strong></p>`);
        fetchConversations(cust_id);
      } else {
        alert('No Id provided in the URL.');
      }


    });
    // Function to fetch conversations
    function fetchConversations(id) {

      // Get the selected date
      let date = document.getElementById('date-input').value; // Assuming your input element has an id="date-input"
      let cust_id = document.getElementById('cust_id').value;
      // console.log(cust_id);return false;

      // Construct the URL with the cust_id and date (if provided)
      let url = `conversation.php?cust_id=${encodeURIComponent(cust_id)}`;
      // console.log(url);
      if (date && cust_id) {
        url += `&date=${encodeURIComponent(date)}`; // Add date parameter if it's selected
      }

      // Make the AJAX request to the server
      $.ajax({
        url: url,
        type: 'GET',
        success: function(response) {
          console.log(response); // Log the response for debugging
          try {
            const data = typeof response === 'string' ? JSON.parse(response) : response;
            let rows = '';

            if (data.length > 0) {
              data.forEach((convo, index) => {
                // Determine row color and button visibility based on the status
                let rowColor = 'inherit';
                let statusText = {
                  accepted: 'Accepted',
                  rejected: 'Rejected',
                  in_progress: 'In Progress'
                };

                let statusBtnLabel = '<i class="fas fa-tasks me-1"></i>';
                let statusBtnDisabled = '';
                let acceptedChecked = '',
                  rejectedChecked = '',
                  inProgressChecked = '';
                let radioDisabled = '';

                // Determine row color and button label based on existing status
                if (convo.status === 'accepted') {
                  rowColor = 'lightgreen';
                  statusBtnLabel = `<i class="fas fa-check-double me-1"></i> ${statusText['accepted']}`;
                  statusBtnDisabled = 'disabled';
                  acceptedChecked = 'checked';
                  radioDisabled = 'disabled';
                } else if (convo.status === 'rejected') {
                  rowColor = 'lightcoral';
                  statusBtnLabel = `<i class="fas fa-check-double me-1"></i> ${statusText['rejected']}`;
                  statusBtnDisabled = 'disabled';
                  rejectedChecked = 'checked';
                  radioDisabled = 'disabled';
                } else if (convo.status === 'in_progress') {
                  rowColor = '#fff3cd';
                  statusBtnLabel = `<i class="fas fa-check-double me-1"></i> ${statusText['in_progress']}`;
                  statusBtnDisabled = 'disabled';
                  inProgressChecked = 'checked';
                  radioDisabled = 'disabled';
                }

                // Check if the logged-in employee can edit/delete
                let isEditable = convo.is_editable;
                let disableClass = isEditable ? '' : 'disabled-btn'; // Apply CSS class

                rows += `
                                    <tr 
                                        data-id="${convo.id}" 
                                        data-status="${convo.status}" 
                                        style="background-color: ${rowColor}" 
                                        title="Date: ${convo.date} | Time: ${convo.time}"
                                    >
                                        <td>${index + 1}</td>
                                        <td class="editable">${convo.adminName}</td>
                                        <td class="editable"><div class="scrollable-cell">${convo.user_conversation}</div></td>
                                        <td class="editable"><div class="scrollable-cell">${convo.admin_conversation}</div></td>
                                  
                                        <td>
                                            <i class="fas fa-edit editBtn ${disableClass}" style="font-size:20px; cursor:pointer;"></i>
                                            <i class="fas fa-trash-alt deleteBtn ${disableClass}" style="font-size:20px; color:red; cursor:pointer;" data-id="${convo.id}"></i>
                                        </td>
                                      <td>
                                    <div class="status-selector position-relative">
                                        <button class="btn btn-outline-primary btn-sm" onclick="toggleStatusCard(this)" ${statusBtnDisabled}>
                                        ${statusBtnLabel}
                                        </button>

                                        <div class="status-card shadow p-2 rounded bg-white border ${convo.status ? 'd-none' : ''}">
                                        <div class="form-check mb-1">
                                            <input class="form-check-input" type="radio" name="status_${convo.id}" value="accepted"
                                                onclick="selectStatus(this, ${convo.id})" ${acceptedChecked} ${radioDisabled}>
                                            <label class="form-check-label text-success"><i class="fas fa-check-circle me-1"></i> Accept</label>
                                        </div>
                                        <div class="form-check mb-1">
                                            <input class="form-check-input" type="radio" name="status_${convo.id}" value="rejected"
                                                onclick="selectStatus(this, ${convo.id})" ${rejectedChecked} ${radioDisabled}>
                                            <label class="form-check-label text-danger"><i class="fas fa-times-circle me-1"></i> Reject</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="status_${convo.id}" value="in_progress"
                                                onclick="selectStatus(this, ${convo.id})" ${inProgressChecked} ${radioDisabled}>
                                            <label class="form-check-label text-warning"><i class="fas fa-spinner me-1"></i> In Progress</label>
                                        </div>
                                        </div>
                                    </div>
                                    </td>

                                    </tr>
                                    `;

              });
            } else {
              rows = '<tr><td colspan="10">No conversations found for this customer.</td></tr>';
            }

            $('#conversation-table').html(rows);
            // Disable the buttons properly using JavaScript
            $('.disabled-btn').each(function() {
              $(this).css({
                "pointer-events": "none",
                "opacity": "0.5"
              }); // Visually disable
            });


          } catch (e) {
            console.error('Error parsing JSON:', e);
            alert('Failed to load data.');
          }
        },
        error: function(xhr, status, error) {
          console.error('Error details:', error);
          alert('Failed to fetch conversations.');
        }
      });
    }

    function toggleStatusCard(button) {
      const card = button.nextElementSibling;
      card.classList.toggle('d-none');
    }

    function selectStatus(radio, id) {
      const status = radio.value;
      const card = radio.closest('.status-card');
      const wrapper = card.closest('.status-selector');
      const row = wrapper.closest('tr');

      $.ajax({
        url: 'update_status1.php',
        type: 'POST',
        data: {
          id: id,
          status: status
        },
        success: function() {
          row.setAttribute('data-status', status);
          if (status === 'accepted') row.style.backgroundColor = 'lightgreen';
          else if (status === 'rejected') row.style.backgroundColor = 'lightcoral';
          else if (status === 'in_progress') row.style.backgroundColor = '#fff3cd';

          const statusText = {
            accepted: 'Accepted',
            rejected: 'Rejected',
            in_progress: 'In Progress'
          };

          wrapper.querySelector('button').innerHTML = `<i class="fas fa-check-double me-1"></i> ${statusText[status]}`;
          wrapper.querySelector('button').disabled = true;

          // Disable radio buttons after selection
          wrapper.querySelectorAll('input[type=radio]').forEach(r => r.disabled = true);
          card.classList.add('d-none');
        },
        error: function() {
          alert('Failed to update status.');
        }
      });
    }


    $('#conversation-form').on('submit', function(e) {
      e.preventDefault();

      const cust_id = $('#cust_id').val();
      const date = $('input[name="date"]').val();
      const time = $('input[name="time"]').val();
      const user_conversation = $('textarea[name="user_conversation"]').val();
      const admin_conversation = $('textarea[name="admin_conversation"]').val();
      const remarks1 = $('textarea[name="remarks1"]').val();
      const remarks2 = $('textarea[name="remarks2"]').val();

      // Basic validation to ensure no field is empty
      if (!cust_id || !date || !time || !user_conversation || !admin_conversation) {
        alert('All fields are required.');
        return; // Prevent submission if fields are empty
      }

      const formData = $(this).serializeArray(); // Serialize the form data


    });
    // Edit functionality
    $(document).on('click', '.editBtn', function() {
      const row = $(this).closest('tr');
      row.find('.editable').each(function() {
        const content = $(this).text();
        $(this).html(`<input type="text" class="form-control" value="${content}">`);
      });

      $(this).replaceWith('<button class="btn btn-primary saveBtn">Save</button>');
    });

    // Save functionality
    $(document).on('click', '.saveBtn', function() {
      const row = $(this).closest('tr');
      const id = row.data('id'); // Retrieve row ID
      const emp_id = row.data('emp_id');
      const cust_id = $('#cust_id').val();

      // Collect updated data
      const updatedData = [];
      row.find('.editable input').each(function() {
        updatedData.push($(this).val());
      });

      const [adminName, user_conversation, admin_conversation, remarks1, remarks2] = updatedData;

      // Send updated data via AJAX
      $.ajax({
        url: 'updateConversation.php', // PHP script to handle updates
        type: 'POST',
        data: {
          id: id,
          // time: time,
          adminName: adminName,
          user_conversation: user_conversation,
          admin_conversation: admin_conversation,
          remarks1: remarks1,
          remarks2: remarks2
        },
        success: function(response) {
          if (response.trim() === 'success') {
            Swal.fire('Updated!', 'Record updated successfully.', 'success').then(() => {
              fetchConversations(cust_id); // Refresh table
            });
          } else {
            Swal.fire('Error!', 'Failed to update record.', 'error');
          }
        },
        error: function() {
          Swal.fire('Error!', 'Something went wrong.', 'error');
        }
      });
    });

    // Delete functionality
    $(document).on('click', '.deleteBtn', function() {
      const id = $(this).data('id');
      const cust_id = $('#cust_id').val();

      Swal.fire({
        title: 'Are you sure?',
        text: "This action cannot be undone.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'Cancel'
      }).then((result) => {
        if (result.isConfirmed) {
          $.ajax({
            url: 'deleteConversation.php',
            type: 'POST',
            data: {
              id: id
            },
            success: function(response) {
              if (response.trim() === 'success') {
                Swal.fire('Deleted!', 'Record has been deleted.', 'success').then(() => {
                  fetchConversations(cust_id);
                });
              } else {
                Swal.fire('Error!', 'Failed to delete record.', 'error');
              }
            },
            error: function() {
              Swal.fire('Error!', 'Something went wrong.', 'error');
            }
          });
        }
      });
    });

    // Handle form submission
    $('#call-form').on('submit', function(e) {
      e.preventDefault();

      const cust_id = $('#cust_id').val();
      const date = $('input[name="callDate"]').val();
      const time = $('input[name="callTime"]').val();
      const details = $('textarea[name="details"]').val();

      if (!cust_id || !date || !time || !details) {
        Swal.fire({
          icon: 'error',
          title: 'Oops...',
          text: 'All fields are required.'
        });
        return;
      }

      const formData = {
        cust_id: cust_id,
        callDate: date,
        callTime: time,
        details: details,
      };

      console.log("Sending formData:", formData); // Debugging line

      $.ajax({
        url: 'add_call.php',
        type: 'POST',
        data: formData,
        success: function(response) {
          try {
            const result = typeof response === 'string' ? JSON.parse(response) : response;

            if (result.success) {
              Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: 'Call added successfully!',
                showConfirmButton: false,
                timer: 2000,
                timerProgressBar: true
              });

              setTimeout(() => {
                window.location.href = 'page-forms.php';
              }, 2000);
            } else {
              Swal.fire({
                icon: 'error',
                title: 'Error',
                text: result.message || 'An unknown error occurred.'
              });
            }
          } catch (e) {
            console.error('Error parsing JSON:', e);
            Swal.fire({
              icon: 'error',
              title: 'Error',
              text: 'Invalid JSON response.'
            });
          }
        },
        error: function() {
          Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'AJAX request failed.'
          });
        }
      });
    });

    // Function to get email from URL query string
    function getEmailFromURL() {
      const urlParams = new URLSearchParams(window.location.search);
      return urlParams.get('cust_id'); // Assuming the cust_id is passed as a query parameter, e.g. ?email=example@example.com
    }

    // Fetch notifications when the dropdown is opened
    $('#dropdownMenuButtontwo').on('click', function() {
      const cust_id = getEmailFromURL(); // Get the cust_id from URL query string

      if (cust_id) {
        fetchNotifications(cust_id); // Pass the email dynamically
      } else {
        console.error("customer id is missing in the URL.");
        $('#notification-container').html('<p>customer id is missing in the URL.</p>');
      }
    });

    // Function to fetch and display notifications
    function fetchNotifications(cust_id) {
      $.ajax({
        url: 'add_call.php', // PHP file for fetching data
        type: 'GET',
        data: {
          cust_id: cust_id
        }, // Pass email to fetch related call details
        success: function(response) {
          console.log("Response from server:", response); // Log the response for debugging

          // Check if the response contains an error
          if (response.error) {
            console.error('Error:', response.error);
            $('#notification-container').html('<p>No call details found for this email.</p>');
            return;
          }

          let notificationsHtml = '';

          // Check if there are call details to display
          if (response.length > 0) {
            response.forEach(function(call) {
              notificationsHtml += `
                        <a href="#" class="iq-sub-card" data-call-id="${call.id}" onclick="showCallDetails(${call.id})">
                            <div class="media align-items-center cust-card pt-3">
                                
                                <div class="media-body ml-4">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <h5 class="mb-0"><small>Call details:</small></h5>
                                        <small class="text-dark"><b>${call.callDate}</b></small>
                                        </div>
                                        <div class="d-flex align-items-center justify-content-between">
                                           <h5 class="mb-0">${call.details}</h5>
                                            <small class="text-dark"><b> ${call.callTime}</b></small>
                                        </div>
                                </div>
                            </div>
                        </a>
                    `;
            });
          } else {
            notificationsHtml = '<p>No call details found for this email.</p>';
          }

          // Insert the notifications into the HTML container
          $('#notification-container').html(notificationsHtml);
        },
        error: function(xhr, status, error) {
          console.error('AJAX error:', status, error);
          $('#notification-container').html('<p>Failed to fetch notifications.</p>');
        }
      });
    }
  </script>

  <script>
    $(document).ready(function() {
      $('#conversation-form').on('submit', function(e) {
        e.preventDefault();

        const formData = {
          cust_id: $('#cust_id').val(),
          date: $('#date').val(),
          time: $('#time').val(),
          user_conversation: $('#user_conversation').val(),
          admin_conversation: $('#admin_conversation').val(),
          remarks1: $('#remarks1').val(),
          remarks2: $('#remarks2').val()
        };

        $.ajax({
          url: 'conversation.php',
          type: 'POST',
          data: $.param(formData),
          success: function(response) {
            try {
              const result = typeof response === 'string' ? JSON.parse(response) : response;

              if (result.success) {
                Swal.fire({
                  toast: true,
                  position: 'top-end',
                  icon: 'success',
                  title: 'Conversation added successfully!',
                  showConfirmButton: false,
                  timer: 3000,
                  timerProgressBar: true
                });

                $('#conversation-form')[0].reset(); // Optional: reset the form
                fetchConversations(formData.cust_id); // Refresh the conversation list
              } else {
                Swal.fire({
                  toast: true,
                  position: 'top-end',
                  icon: 'error',
                  title: result.error || 'Something went wrong.',
                  showConfirmButton: false,
                  timer: 3000,
                  timerProgressBar: true
                });
              }
            } catch (e) {
              Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'error',
                title: 'Invalid response from server.',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true
              });
              console.error('JSON Parse Error:', e);
            }
          },
          error: function() {
            Swal.fire({
              toast: true,
              position: 'top-end',
              icon: 'error',
              title: 'Failed to submit data.',
              showConfirmButton: false,
              timer: 3000,
              timerProgressBar: true
            });
          }
        });
      });
    });
  </script>
