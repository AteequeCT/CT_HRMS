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
    <!-- loader END -->


        <div class="content-page">
            <!-- Page end  -->

            <div class="container-fluid">
                <div id="user-dashboard" class="row">
                    <!-- Dynamically added user cards will appear here -->
                </div>
            </div>



            <div id="emptyMessage" style="display: none;">
                No conversation found.
            </div>

            <!-- Page end  -->
        </div>
    
        <script>
  
  
  $(document).ready(function () {
    fetchUsers(); // Fetch and display employees on page load

    // Function to fetch employees
    function fetchUsers() {
    $.ajax({
        url: 'emp_data.php', // PHP script to fetch employees
        type: 'GET',
        success: function (response) {
            const users = JSON.parse(response);

            $('#user-dashboard').empty(); // Clear the dashboard
            if (users.length > 0) {
                users.forEach(function (user) {
                    // Only display users who have not resigned
                    if (user.resign == 0) {
                        const genderIcon = user.gender.toLowerCase() === 'male'
                            ? 'assets/img/men.png' // Male profile image path
                            : 'assets/img/girl.png'; // Female profile image path

                        const userCard = `
                            <div class="col-md-6 mb-3">
                                <a href="emp_leads.php?emp_id=${encodeURIComponent(user.id)}" style="text-decoration: none;">
                                    <div class="card">
                                        <div class="card-body text-center">
                                            <img src="${genderIcon}" alt="Profile Image" class="img-fluid rounded-circle mb-2" style="width: 80px; height: 80px;">
                                            <h5>${user.name}</h5>
                                            <p>${user.role}</p>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        `;
                        $('#user-dashboard').append(userCard);
                    }
                });

                // If all employees were resigned and none were shown
                if ($('#user-dashboard').children().length === 0) {
                    $('#user-dashboard').append('<p>No active employees found.</p>');
                }

            } else {
                $('#user-dashboard').append('<p>No employees found.</p>');
            }
        },
        error: function () {
            alert('Failed to fetch employees.');
        }
    });
}

    // Handle form submission to add a new employee
    $('#add-user-form').on('submit', function (e) {
        e.preventDefault(); // Prevent default form submission

        const formData = {
            name: $('#user-name').val(),
            password: $('#user-password').val(),
            gender: $('#user-gender').val(),
            number: $('#user-number').val()
        };

        $.ajax({
            url: 'emp_data.php', // PHP script to add employee
            type: 'POST',
            data: formData,
            success: function (response) {
                const result = JSON.parse(response);
                if (result.success) {
                    const genderIcon = result.data.gender.toLowerCase() === 'male'
                        ? 'assets/img/men.png'
                        : 'assets/img/girl.png';

                    const userCard = `
                        <div class="col-md-6 mb-3">
                              <a href="emp-forms.php?emp_id=${encodeURIComponent(user.id)}" style="text-decoration: none;">
                            <div class="card">
                                <div class="card-body text-center">
                                    <img src="${genderIcon}" alt="Profile Image" class="img-fluid rounded-circle mb-2" style="width: 80px; height: 80px;">
                                    <h5>${result.data.name}</h5>
                                    <p>${result.data.number}</p>
                                </div>
                            </div>
                             </a>
                        </div>
                    `;
                    $('#user-dashboard').append(userCard); // Add the new employee to the dashboard
                    $('#addUserModal').modal('hide'); // 77777777777777777777777777777777777777777777777777777777777777777777Close the modal
                    $('#add-user-form')[0].reset(); // Reset the form
                } else {
                    alert('Error: ' + result.message);
                }
            },
            error: function () {
                alert('An error occurred. Please try again.');
            }
        });
    });
});

    
        </script>


