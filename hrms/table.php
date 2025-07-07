<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
    <title>Attendance Management System</title>
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
        }

        /* Add bottom border to each sidebar nav item */
        #sidebar ul.nav li {
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
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
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

            button {
                width: 0;
                /* Set the button width to 0 in mobile view */

                overflow: hidden;
                /* To ensure the text doesn't overflow if there was any */
                /* You might also want to adjust other properties like margin, border, etc. depending on the desired effect */
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

        .btn {
            width: 80px;
        }

        @media (min-width: 768px) {
            button {
                width: 70px;
                overflow: hidden;
            }
        }
    </style>
</head>

<body>
    <button class="menu-toggle d-md-none" onclick="toggleSidebar()">☰</button>
    <div class="container-fluid">
        <div class="row">

            <nav id="sidebar" class="col-md-3 col-lg-2 bg-light sidebar">
                <div class="position-sticky">
                    <img class="mt-1 text-center logo" src="crawlers">
                    <ul class="nav flex-column mt-4">
                         <li class="nav-item"><a class="nav-link" href="userDashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                            <li class="nav-item"><a class="nav-link" href="meeting.php"><i class="fas fa-handshake"></i> Meeting Details</a></li>
                            <li class="nav-item"><a class="nav-link" href="employee_task.php"><i class="fas fa-tasks"></i> Employees Task</a></li>
                            <li class="nav-item"><a class="nav-link" href="activityEmp.php"><i class="fas fa-tasks"></i> Employees Activity</a></li>
                            <li class="nav-item"><a class="nav-link" href="table.php"><i class="fas fa-clipboard-list"></i> Attendance</a></li>
                            <li class="nav-item"><a class="nav-link" href="leave_acc.php"><i class="fas fa-plane-departure"></i> Leave Requests</a></li>
                            <li class="nav-item"><a class="nav-link" href="holiday_view.php"><i class="fas fa-umbrella-beach"></i> Holidays</a></li>
                            <li class="nav-item"><a class="nav-link " id="changePasswordBtn" href="#"><i class="fas fa-key"></i> Change Password</a></li>
                            <li class="nav-item"><a class="nav-link" id="logoutButton" href="#"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                    </ul>
                </div>
            </nav>
            <div class="col-md-2 col-lg-2">
            </div>
            <!-- Main Content -->
            <div class="col-md-9 col-lg-10">

                <div class="container mt-5">
                    <h2 class="text-center">Attendance</h2>

                    <!-- Responsive table wrapper -->
                    <div class="table-responsive">
                        <table id="attendanceTable" class="display table table-bordered table-striped" style="width:100%">
                            <thead class="head">
                                <tr>
                                    <th>Date</th>
                                    <th>Action</th>
                                    <th>Image</th>
                                    <th>Login Time</th>
                                    <th>Login Status</th>
                                    <th>Logout</th>
                                    <th>Logout Time</th>
                                    <th>Logout Status</th>
                                    <th>Total Hours Worked</th>
                                    <th>Lunch Break</th> <!-- New Lunch Break column -->
                                </tr>
                            </thead>
                            <tbody>
                                <!-- DataTables will dynamically populate this -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- First Modal for verifying email and old password -->
        <div id="verifyCredentialsModal" class="modal">
            <div class="modal-content">
                <span class="close-btn" id="closeVerifyModal">&times;</span>
                <h2>Verify Your Credentials</h2>
                <form id="verifyCredentialsForm">
                    <input type="email" id="email" placeholder="Email" required>
                    <input type="password" id="old_password" placeholder="Old Password" required>
                    <button type="submit">Verify</button>
                </form>
                <div id="verifyMessage"></div>
            </div>
        </div>

        <!-- Second Modal for setting a new password (shown only after successful verification) -->
        <div id="setNewPasswordModal" class="modal">
            <div class="modal-content">
                <span class="close-btn" id="closeSetPasswordModal">&times;</span>
                <h2>Set New Password</h2>
                <form id="setNewPasswordForm">
                    <input type="password" id="new_password"
                        placeholder="New Password" pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$"
                        title="Password must be at least 8 characters long, contain one uppercase letter, one lowercase letter, one number, and one special character."
                        required>
                    <input type="password" id="new_password_confirm" placeholder="Confirm New Password" required>
                    <button type="submit">Set Password</button>
                </form>
                <div id="setPasswordMessage"></div>
            </div>
        </div>


    </div>
    <script>
        // Handle image click to zoom in and out
        $(document).on('click', '.img-thumbnail', function() {
            if ($(this).hasClass('zoomed')) {
                // If the image is already zoomed, remove zoom and backdrop
                $(this).removeClass('zoomed');
                $('.zoomed-backdrop').remove();
            } else {
                // Otherwise, add zoom and backdrop
                $(this).addClass('zoomed');
                $('body').append('<div class="zoomed-backdrop"></div>'); // Add dark backdrop

                // Handle click on backdrop to zoom out
                $('.zoomed-backdrop').click(function() {
                    $('.img-thumbnail').removeClass('zoomed');
                    $(this).remove(); // Remove backdrop
                });
            }
        });


        // Variable to store lunch start time
        let lunchStartTime = {};

        // Function to handle check-in button click
        function checkin(button) {
            window.location.href = "imageupload.html";
        }

        // Function to handle checkout button click
        function checkout(recordIndex, button) {
            $.ajax({
                url: 'logout.php',
                type: 'POST',
                data: {},
                success: function(response) {
                    response = JSON.parse(response);

                    if (response.success) {
                        alert('Checkout successful');
                        $(button).text('Checked-Out').prop('disabled', true);
                        var table = $('#attendanceTable').DataTable();
                        var rowIndex = table.row($(button).parents('tr')).index();
                        var rowData = table.row(rowIndex).data();
                        rowData.logout_time = response.logout_time;
                        rowData.logout_status = response.logout_status;
                        rowData.total_hours_worked = response.total_hours_worked;
                        rowData.total_lunch_duration = response.total_lunch_duration;
                        table.row(rowIndex).data(rowData).draw();
                    } else {
                        alert('Checkout failed: ' + response.message);
                    }
                },
                error: function() {
                    alert('An error occurred during checkout.');
                }
            });
        }

        // Function to start lunch break
        function startLunch(recordId, button) {
            lunchStartTime[recordId] = new Date(); // Store the start time
            $(button).hide(); // Hide the Start button
            $(button).siblings('.end-lunch-btn').show(); // Show the End button

            // AJAX request to store the start time
            $.ajax({
                url: 'lunchBreak.php',
                type: 'POST',
                data: {
                    action: 'start',
                    recordId: recordId
                },
                success: function(response) {
                    response = JSON.parse(response);
                    if (!response.success) {
                        alert('Error: ' + response.message);
                    }
                },
                error: function() {
                    alert('An error occurred while starting lunch.');
                }
            });
        }

        // Function to end lunch break
        function endLunch(recordId, button) {
            const endTime = new Date();
            const startTime = lunchStartTime[recordId];
            const duration = Math.round((endTime - startTime) / 60000); // Duration in minutes
            $(button).hide(); // Hide the End button
            $(button).siblings('.start-lunch-btn').show(); // Show the Start button again
            $(button).siblings('.lunch-duration').text(duration + ' min'); // Display lunch duration

            // AJAX request to store the end time
            $.ajax({
                url: 'lunchBreak.php',
                type: 'POST',
                data: {
                    action: 'end',
                    recordId: recordId,
                    duration: duration
                },
                success: function(response) {
                    response = JSON.parse(response);
                    if (!response.success) {
                        alert('Error: ' + response.message);
                    }
                },
                error: function() {
                    alert('An error occurred while ending lunch.');
                }
            });
        }
        $(document).ready(function() {
            $('#attendanceTable').DataTable({
                scrollX: true,
                "ajax": {
                    "url": "loadTable.php",
                    "dataSrc": function(json) {
                        const records = json.data ? json.data : json;

                        // Process records to check today's data
                        const today = new Date();
                        const currentDate = today.getFullYear() + '-' +
                            String(today.getMonth() + 1).padStart(2, '0') + '-' +
                            String(today.getDate()).padStart(2, '0');

                        const isTodayPresent = records.some(record => record.date === currentDate);

                        if (!isTodayPresent) {
                            records.push({
                                date: currentDate,
                                login_time: '',
                                user_photo: '',
                                login_status: 'Pending',
                                action: '',
                                logout_time: '',
                                logout_status: '',
                                total_hours_worked: '',
                                lunch_start_time: '',
                                lunch_end_time: ''
                            });
                        }

                        records.sort(function(a, b) {
                            return new Date(b.date) - new Date(a.date);
                        });

                        return records;
                    }
                },
                "order": [
                    [0, "desc"]
                ],
                "columns": [{
                        "data": "date"
                    },
                    {
                        "data": null,
                        "render": function(data, type, row) {
                            const currentTime = new Date();
                            const cutoffTime = new Date();
                            cutoffTime.setHours(9, 25, 0); // Set cutoff time to 9:25 AM

                            if (row.leave_request) { // Check if leave request exists
                                return "<b style='color:blue;'>Leave</b>"; // Display "Leave"
                            } else if (row.login_status === "Absent") {
                                return "Absent"; // Display "Absent"
                            } else {
                                if (row.login_time) {
                                    return "<button class='btn btn-primary' disabled>Checked-In</button>";
                                } else if (row.login_status === "Pending") {
                                    if (currentTime < cutoffTime) {
                                        // Before 9:25, button is disabled
                                        return "<button class='btn btn-primary' disabled>Login</button>";
                                    } else {
                                        // After 9:25, button is clickable
                                        return "<button class='btn btn-primary' onclick='checkin(this)'>Login</button>";
                                    }
                                }
                            }
                        }
                    },
                    {
                        "data": "user_photo",
                        "render": function(data, type, row) {
                            if (row.user_photo !== "") {
                                return '<img id="zoomableImage" src="image_proof/' + data + '" alt="User Photo" class="img-thumbnail" style="cursor: pointer;">';
                            } else {
                                return 'N/A';
                            }
                        }
                    },
                    {
                        "data": "login_time"
                    },
                    {
                        "data": "login_status"
                    },
                    {
                        "data": null,
                        "render": function(data, type, row, meta) {
                            // Check if required data fields are null and provide defaults
                            const loginStatus = row.login_status || "Present"; // Default to "Present" if null
                            const loginTime = row.login_time || ""; // Empty string if no login_time
                            const logoutTime = row.logout_time || ""; // Empty string if no logout_time

                            // Get today's date
                            const today = new Date();
                            const currentDate = today.getFullYear() + '-' +
                                String(today.getMonth() + 1).padStart(2, '0') + '-' +
                                String(today.getDate()).padStart(2, '0');

                            // Handle previous day's logout status
                            const previousRow = meta.row > 0 ? $('#attendanceTable').DataTable().row(meta.row - 1).data() : null;
                            const disableTodayLogout = previousRow && !previousRow.logout_time;

                            if (loginStatus === "Absent") {
                                return "Absent";
                            } else {
                                if (row.date === currentDate) {
                                    if (loginTime && !logoutTime) {
                                        return `<button class='btn btn-primary' onclick='checkout(${row.id}, this)' ${disableTodayLogout ? 'disabled' : ''}>Logout</button>`;
                                    } else if (logoutTime) {
                                        return "<button class='btn btn-primary' disabled>Checked-Out</button>";
                                    }
                                } else {
                                    return "<button class='btn btn-primary' disabled>Logout</button>";
                                }
                            }
                        },
                        "defaultContent": "-" // Display "-" if data is missing entirely
                    },


                    {
                        "data": "logout_time"
                    },
                    {
                        "data": "logout_status"
                    },
                    {
                        "data": "total_hours_worked"
                    },
                    {
                        "data": null,
                        "render": function(data, type, row) {


                            // Check if both lunch start and end times are available
                            if (row.lunch_start_time && row.lunch_end_time) {
                                // Parse the times as Date objects
                                const startTime = new Date(row.lunch_start_time);
                                const endTime = new Date(row.lunch_end_time);

                                // Check if the startTime and endTime are valid Date objects
                                if (!isNaN(startTime) && !isNaN(endTime)) {
                                    // Calculate the total lunch duration in milliseconds
                                    const lunchDuration = endTime - startTime;

                                    // Convert the duration to minutes and seconds
                                    const lunchDurationMinutes = Math.floor(lunchDuration / (1000 * 60));
                                    const lunchDurationHours = Math.floor(lunchDurationMinutes / 60);
                                    const remainingMinutes = lunchDurationMinutes % 60;

                                    // Format the output
                                    return "<span class='lunch-duration'>" + lunchDurationHours + " hours " + remainingMinutes + " minutes</span>";
                                } else {
                                    // Invalid start or end time, display a message
                                    return "<span class='lunch-duration'>Invalid Time</span>";
                                }
                            } else if (row.lunch_start_time) {
                                // Lunch break is ongoing
                                return "<button class='btn btn-danger end-lunch-btn' onclick='endLunch(" + row.id + ", this)'>End Lunch</button><span class='lunch-duration'></span>";
                            } else {
                                if (row.login_time && !row.logout_time) {
                                    return "<button class='btn btn-warning start-lunch-btn' onclick='startLunch(" + row.id + ", this)'>Start Lunch</button><button class='btn btn-danger end-lunch-btn' style='display: none;' onclick='endLunch(" + row.id + ", this)'>End Lunch</button><span class='lunch-duration'></span>";

                                }
                                // Lunch break not started yet
                                return "<button class='btn btn-warning start-lunch-btn' onclick='startLunch(" + row.id + ", this)'  disabled>Start Lunch</button><button class='btn btn-danger end-lunch-btn' style='display: none;' onclick='endLunch(" + row.id + ", this)'>End Lunch</button><span class='lunch-duration'></span>";
                            }

                        }
                    }
                ]
            });

            checkEMployeeSession();
        });

        function checkEMployeeSession() {
            $.ajax({
                url: 'checkEmployeeSession.php', // PHP script for ending session
                type: 'get',
                success: function(response) {
                    console.log(response);
                    // return false;
                    if (response == 1) {
                        window.location.href = "index.html";
                    }
                }
            })
        }

        // Function to start lunch break (Duplicate from above, can be removed)
        function startLunch(recordId, button) {
            $.ajax({
                url: 'lunchBreak.php',
                type: 'POST',
                data: {
                    action: 'start',
                    record_id: recordId
                },
                success: function(response) {
                    response = JSON.parse(response);
                    if (response.success) {
                        alert('Lunch break started successfully.');
                        $(button).replaceWith(`<button class='btn btn-danger end-lunch-btn' onclick='endLunch(${recordId}, this)'>End Lunch</button>`);
                    } else {
                        alert('Error: ' + response.message);
                    }
                },
                error: function() {
                    alert('An error occurred while starting lunch.');
                }
            });
        }

        // Function to end lunch break (Duplicate from above, can be removed)
        function endLunch(recordId, button) {
            $.ajax({
                url: 'lunchBreak.php',
                type: 'POST',
                data: {
                    action: 'end',
                    record_id: recordId
                },
                success: function(response) {
                    response = JSON.parse(response);
                    if (response.success) {
                        alert('Lunch break ended successfully.');
                        const totalDuration = response.total_duration;
                        $(button).replaceWith(`<span class='lunch-duration'>${totalDuration}</span>`); // Display lunch duration
                    } else {
                        alert('Error: ' + response.message);
                    }
                },
                error: function() {
                    alert('An error occurred while ending lunch.');
                }
            });
        }

        // Logout button with confirmation

        $(document).ready(function() {
            // Show the first modal to verify credentials
            $("#changePasswordBtn").click(function() {
                $("#verifyCredentialsModal").css("display", "flex"); // Show as flexbox
            });

            // Handle closing the modals
            $("#closeVerifyModal").click(function() {
                $("#verifyCredentialsModal").hide();
            });

            $("#closeSetPasswordModal").click(function() {
                $("#setNewPasswordModal").hide();
            });

            // Handle first form submission: Verify email and old password
            $("#verifyCredentialsForm").submit(function(event) {
                event.preventDefault(); // Prevent default form submission

                // Get form data
                var email = $("#email").val();
                var old_password = $("#old_password").val();

                // Make AJAX request to check credentials
                $.ajax({
                    url: "verify_credentials.php", // PHP script to verify credentials
                    type: "POST",
                    data: {
                        email: email,
                        old_password: old_password
                    },
                    success: function(response) {
                        var result = JSON.parse(response);

                        if (result.status === 'success') {
                            alert('Credentials verified successfully. Now you can set a new password.');
                            $("#verifyCredentialsModal").hide(); // Hide the first modal
                            $("#setNewPasswordModal").css("display", "flex"); // Show the new password modal
                        } else {
                            alert('invalid email/password');
                            $("#verifyMessage").text(result.message); // Show error message
                        }
                    }
                });
            });

            // Handle second form submission: Set new password
            $("#setNewPasswordForm").submit(function(event) {
                event.preventDefault();

                // Get new password values
                var new_password = $("#new_password").val();
                var new_password_confirm = $("#new_password_confirm").val();

                // Check if new passwords match
                if (new_password !== new_password_confirm) {
                    $("#setPasswordMessage").text("Passwords do not match.");
                    return;
                }

                // Make AJAX request to update password
                $.ajax({
                    url: "reset.php", // PHP script to update password
                    type: "POST",
                    data: {
                        email: $("#email").val(),
                        new_password: new_password
                    },
                    success: function(response) {
                        var result = JSON.parse(response);
                        $("#setPasswordMessage").text(result.message);
                    }
                });
            });
        });
    </script>
    <script>
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

</body>

</html>