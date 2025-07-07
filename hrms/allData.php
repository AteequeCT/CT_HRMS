<?php
session_start();
include 'includes/connection.php';
include 'includes/header.php';
include 'includes/hr_sidebar.php';

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
                  <a href="#">Tables</a>
                </li>
                <li class="separator">
                  <i class="icon-arrow-right"></i>
                </li>
                <li class="nav-item">
                  <a href="#">Datatables</a>
                </li>
              </ul>
            </div>


<div class="col-md-12">
    <div class="card">
        <div class="card-header">
            <h4 class="card-title">Employees Informations</h4>
        </div>
        <div class="card-body">
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
    </div>
</div>



<script>

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
            "columns": [{
                    "data": "id"
                },
                {
                    "data": "name"
                },
                {
                    "data": "email"
                },
                {
                    "data": "employee_id"
                },
                {
                    "data": "department"
                },
                {
                    "data": "role"
                },
                {
                    "data": "joining_date"
                },
                {
                    "data": "mobile"
                },
                {
                    "data": "address"
                },
                {
                    "data": "gender"
                },
                {
                    "data": "branch"
                },
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
            "pageLength": 10,
            "lengthMenu": [
                [10, 25, 50, 100, -1],
                [10, 25, 50, 100, "All"]
            ],
            "order": [
                [0, 'desc']
            ],
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
                    data: {
                        id: id
                    },
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
                    data: {
                        id: id
                    },
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