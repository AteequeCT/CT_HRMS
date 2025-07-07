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
    .scrollable-cell {
  max-height: 100px;
  overflow-y: auto;
  padding: 5px;
  text-align: left;
}
</style>


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
                        <a href="#">Add Customers</a>
                    </li>
                </ul>
            </div>
</div>



<div class="content-page">
        <!-- Modal for adding data -->
        <div class="modal fade" id="addDataModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-body">
    
                        <!-- Tab Content -->
                        <div class="tab-content mt-3">
                            <!-- Conversation Form -->
                            <div class="tab-pane fade show active" id="conversation-form-tab" role="tabpanel" aria-labelledby="conversation-tab">
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
                                        <label for="adminName">Admin Name</label>
                                        <input type="text" id="adminName" name="adminName" class="form-control" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="user_conversation">User Conversation</label>
                                        <textarea id="user_conversation" name="user_conversation" class="form-control" required></textarea>
                                    </div>
                                    <div class="form-group">
                                        <label for="admin_conversation">Admin Conversation</label>
                                        <textarea id="admin_conversation" name="admin_conversation" class="form-control" required></textarea>
                                    </div>
                                    <button type="submit" class="btn btn-primary">Submit</button>
                                </form>
                            </div>
    
                            <!-- Add Call Form -->
                            <div class="tab-pane fade" id="add-call-form-tab" role="tabpanel" aria-labelledby="add-call-tab">
                                <form id="call-form">
                                    <input type="hidden" id="cust_id" name="cust_id" value="">
                                    <div class="form-group">
                                        <label for="callDate">Call Date</label>
                                        <input type="date" id="callDate" name="callDate" class="form-control" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="callTime">Call Time</label>
                                        <input type="time" id="callTime" name="callTime" class="form-control" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="callDetails">Call Details</label>
                                        <textarea id="callDetails" name="details" class="form-control" required></textarea>
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
        <table id="conversationTable" class="table table-bordered table-hover align-middle text-center">
    <thead class="table-light">
        <tr>
            <th style="width: 5%;">ID</th>
            <th style="width: 20%;">Customer Name</th>
            <th style="width: 35%;">User Conversation</th>
            <th style="width: 35%;">Employee Conversation</th>
        </tr>
    </thead>
    <tbody id="conversation-table">
        <!-- Data will be dynamically inserted here -->
    </tbody>
</table>

    
        <div id="emptyMessage" style="display: none;">
            No conversation found.
        </div>
    </div>
</div>
    
<script>

const searchInputs = [
  document.querySelector('#searchInput'),
  document.querySelector('#searchBarMobile')
];

const userDashboard = document.querySelector('#conversation-table'); // tbody of the table

searchInputs.forEach(input => {
  if (input) {
    input.addEventListener('input', function () {
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

   // Set today's date as the minimum date for the input
      const today = new Date().toISOString().split('T')[0];
    document.getElementById('callDate').setAttribute('min', today);
    
       // JavaScript to toggle sidebar state
document.getElementById('sidebarToggle').addEventListener('click', function () {
    const sidebar = document.querySelector('.iq-sidebar');
    sidebar.classList.toggle('collapsed');
});



  document.addEventListener("DOMContentLoaded", function () {
        const urlParams = new URLSearchParams(window.location.search);
        const emp_id = urlParams.get('emp_id');
        const cust_id = urlParams.get('cust_id');
        if (emp_id && cust_id) {
        $('#cust_id').val(cust_id);
        $('#cust_id-display').html(`<p>Viewing data for Customer ID: <strong>${cust_id}</strong>, Employee ID: <strong>${emp_id}</strong></p>`);
        fetchConversations(emp_id, cust_id);
    } else {
        alert('Missing emp_id or cust_id in the URL.');
    }

       
    });
// Function to fetch conversations
function fetchConversations(emp_id, cust_id) {
// console.log(id);return false;

    $.ajax({
        url: `emp_conv.php?emp_id=${encodeURIComponent(emp_id)}&cust_id=${encodeURIComponent(cust_id)}`,
        type: 'GET',
        success: function (response) {
            try {
                const data = typeof response === 'string' ? JSON.parse(response) : response;
                let rows = '';
                // Update the admin name in the navbar
                // document.getElementById('customer_name').textContent = `(${data.customer_name})`;
                                if (data.length > 0) {
                    data.forEach(convo => {
                        rows += `
                            <tr data-id="${convo.id}" title="Date: ${convo.date} | Time: ${convo.time}">
                                <td>${convo.cust_id}</td>
                                <td class="editable">${convo.customer_name}</td>
                                <td class="editable">
                                <div class="scrollable-cell">${convo.user_conversation}</div>
                                </td>
                                <td class="editable">
                                <div class="scrollable-cell">${convo.admin_conversation}</div>
                                </td>
                            </tr>
                            `;
                    });
                    // <td>
                    //                 <i class="fas fa-edit editBtn" style="font-size:24px; cursor:pointer;"></i>
                    //                 <i class="fas fa-trash-alt deleteBtn" style="font-size:24px; color:red; cursor:pointer;" data-id="${convo.id}"></i>
                    //             </td>
                } else {
                    rows = '<tr><td colspan="6">No conversations found for this email.</td></tr>';
                }

                $('#conversation-table').html(rows);
            } catch (e) {
                console.error('Error parsing JSON:', e);
                alert('Failed to load data.');
            }
        },
        error: function () {
            alert('Failed to fetch conversations.');
        }
    });
}

$('#conversation-form').on('submit', function (e) {
    e.preventDefault();

    const emp_id = $('#emp_id').val();
    const date = $('input[name="date"]').val();
    const time = $('input[name="time"]').val();
    const adminName = $('input[name="adminName"]').val();
    const user_conversation = $('textarea[name="user_conversation"]').val();
    const admin_conversation = $('textarea[name="admin_conversation"]').val();

    // Basic validation to ensure no field is empty
    if (!emp_id || !date || !time || !adminName || !user_conversation || !admin_conversation) {
        alert('All fields are required.');
        return; // Prevent submission if fields are empty
    }

    const formData = $(this).serializeArray();  // Serialize the form data

    $.ajax({
        url: 'emp_conv.php',
        type: 'POST',
        data: $.param(formData),  // Send data as serialized string
        success: function (response) {
            // Handle response after submission
            try {
                // Check if response is a valid JSON string
                const result = typeof response === 'string' ? JSON.parse(response) : response;

                if (result.success) {
                    alert('Conversation added successfully!');
                    fetchConversations(cust_id);  // Refresh the conversations list after submission
                } else {
                    alert('Error: ' + result.error);
                }
            } catch (e) {
                console.error('Error parsing JSON:', e);
                alert('Failed to parse the response or something went wrong.');
            }
        },
        error: function () {
            alert('Failed to submit data.');
        }
    });
});
// Edit functionality
$(document).on('click', '.editBtn', function () {
    const row = $(this).closest('tr');
    row.find('.editable').each(function () {
        const content = $(this).text();
        $(this).html(`<input type="text" class="form-control" value="${content}">`);
    });

    $(this).replaceWith('<button class="btn btn-primary saveBtn">Save</button>');
});

// Save functionality
$(document).on('click', '.saveBtn', function () {
    const row = $(this).closest('tr');
    const id = row.data('id');  // Retrieve row ID
    const cust_id = $('#cust_id').val();

    // Collect updated data
    const updatedData = [];
    row.find('.editable input').each(function () {
        updatedData.push($(this).val());
    });

    const [date, time,adminName, user_conversation, admin_conversation] = updatedData;

    // Send updated data via AJAX
    $.ajax({
        url: 'updateConversation.php', // PHP script to handle updates
        type: 'POST',
        data: {
            id: id,
            date: date,
            time: time,
            adminName: adminName,
            user_conversation: user_conversation,
            admin_conversation: admin_conversation
        },
        success: function (response) {
            if (response.trim() === 'success') {
                Swal.fire('Updated!', 'Record updated successfully.', 'success').then(() => {
                    fetchConversations(cust_id); // Refresh table
                });
            } else {
                Swal.fire('Error!', 'Failed to update record.', 'error');
            }
        },
        error: function () {
            Swal.fire('Error!', 'Something went wrong.', 'error');
        }
    });
});

// Delete functionality
$(document).on('click', '.deleteBtn', function () {
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
                data: { id: id },
                success: function (response) {
                    if (response.trim() === 'success') {
                        Swal.fire('Deleted!', 'Record has been deleted.', 'success').then(() => {
                            fetchConversations(cust_id);
                        });
                    } else {
                        Swal.fire('Error!', 'Failed to delete record.', 'error');
                    }
                },
                error: function () {
                    Swal.fire('Error!', 'Something went wrong.', 'error');
                }
            });
        }
    });
});
// 

// Handle form submission
$('#call-form').on('submit', function (e) {
    e.preventDefault();

    const cust_id = $('#cust_id').val();
    const date = $('input[name="callDate"]').val();
    const time = $('input[name="callTime"]').val();
    const details = $('textarea[name="details"]').val();

    // Basic validation
    if (!cust_id || !date || !time || !details) {
        Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: 'All fields are required.'
        });
        return; // Prevent submission if fields are empty
    }

    const formData = {
        cust_id: cust_id,
        callDate: date,
        callTime: time,
        details: details,
    };

    $.ajax({
        url: 'emp_data.php',
        type: 'POST',
        data: formData,
        success: function (response) {
            try {
                const result = typeof response === 'string' ? JSON.parse(response) : response;

                if (result.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: result.message || 'Call details added successfully!'
                    }).then(function () {
                        location.reload(); // Page refresh after user clicks "OK"
                    });
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
                    text: 'Failed to parse the response or something went wrong.'
                });
            }
        },
        error: function () {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Failed to submit call details.'
            });
        }
    });
});
// Function to get email from URL query string
function getEmailFromURL() {
    const urlParams = new URLSearchParams(window.location.search);
    return urlParams.get('emp_id');  // Assuming the cust_id is passed as a query parameter, e.g. ?email=example@example.com
}

// Fetch notifications when the dropdown is opened
$('#dropdownMenuButtontwo').on('click', function() {
    const emp_id = getEmailFromURL();  // Get the cust_id from URL query string

    if (emp_id) {
        fetchNotifications(emp_id);  // Pass the email dynamically
    } else {
        console.error("customer id is missing in the URL.");
        $('#notification-container').html('<p>customer id is missing in the URL.</p>');
    }
});

// Function to fetch and display notifications
function fetchNotifications(emp_id) {
    $.ajax({
        url: `add_Call.php?emp_id=${encodeURIComponent(emp_id)}&cust_id=${encodeURIComponent(cust_id)}`,
        type: 'GET',
        data: { emp_id: emp_id }, // Pass email to fetch related call details
        success: function(response) {
            console.log("Response from server:", response);  // Log the response for debugging

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

