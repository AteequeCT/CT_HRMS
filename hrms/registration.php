<?php
include 'includes/hr_sidebar.php';
include 'includes/header.php';
?>

<div class="container">
    <div class="page-inner">
        <div class="page-header">
            <h3 class="fw-bold mb-3">Forms</h3>
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
                    <a href="#">Forms</a>
                </li>
                <li class="separator">
                    <i class="icon-arrow-right"></i>
                </li>
                <li class="nav-item">
                    <a href="#">Basic Form</a>
                </li>
            </ul>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="card">

                    <div class="card-header">
                        <div class="card-title">Form Elements</div>
                    </div>
                    <form method="POST" id="manage-estimate">
                        <input type="hidden" name="id" value="<?php echo isset($id) ? $id : ''; ?>">

                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="name" class="form-label">Name</label>
                                        <input type="text" class="form-control" id="name" name="name" placeholder="Enter name"
                                            required minlength="3" maxlength="50" pattern="[A-Za-z\s]+"
                                            title="Name should only contain letters and spaces.">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="email" class="form-label">Email</label>
                                        <input type="email" class="form-control" id="email" name="email"
                                            placeholder="Enter email" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="password" class="form-label">Password</label>
                                        <input type="password" class="form-control" id="password" name="password"
                                            placeholder="Password" required minlength="8" maxlength="20"
                                            pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}"
                                            title="Password must contain at least one number, one uppercase and lowercase letter, and at least 8 or more characters.">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="mobile" class="form-label">Mobile Number</label>
                                        <input type="text" class="form-control" id="mobile" name="mobile" maxlength="10"
                                            placeholder="Mobile Number" required pattern="^[6-9]\d{9}$"
                                            title="Please enter a valid 10-digit mobile number">
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="gender" class="form-label">Gender</label>
                                        <select class="form-control" id="gender" name="gender" required>
                                            <option selected disabled value="">Select Gender</option>
                                            <option value="male">Male</option>
                                            <option value="female">Female</option>
                                            <option value="other">Other</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="joining_date" class="form-label">Joining Date</label>
                                        <input type="date" class="form-control" id="joining_date" name="joining_date" required>
                                    </div>
                                </div>
                            </div>


                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="department" class="form-label">Department</label>
                                        <select class="form-control" id="department" name="department" required>
                                            <option selected disabled value="">Select Department</option>
                                            <option value="Development">Development</option>
                                            <option value="Testing">Testing</option>
                                            <option value="Management">Management</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="role" class="form-label">Role</label>
                                        <select class="form-control" id="role" name="role" required>
                                            <option selected disabled value="">Select Role</option>
                                            <option value="Software Developer">Software Developer</option>
                                            <option value="Software Engineer">Software Engineer</option>
                                            <option value="Junior Developer">Junior Developer</option>
                                            <option value="UI UX Designer">UI UX Designer</option>
                                            <option value="HR Manager">HR Manager</option>
                                            <option value="HR Head">HR Head</option>
                                            <option value="Full Stack Developer">Full Stack Developer</option>
                                            <option value="Flutter Developer">Flutter Developer</option>
                                            <option value="Java Developer">Java Developer</option>
                                            <option value="Cloud Engineer">Cloud Engineer</option>
                                            <option value="Software Tester">Software Tester</option>
                                            <option value="Manual Testing">Manual Testing</option>
                                            <option value="DevOps Engineer">DevOps Engineer</option>
                                            <option value="Fullstack Developer Engineer">Fullstack Developer Engineer
                                            </option>
                                            <option value="Testing Engineer">Testing Engineer</option>
                                            <option value="Digital Marketing Intern">Digital Marketing Intern</option>
                                            <option value="Digital Marketing Manager1">Digital Marketing Manager1</option>
                                            <option value="Digital Marketing Executive">Digital Marketing Executive</option>
                                            <option value="Customer Service Representative">Customer Service Representative
                                            </option>
                                            <option value="business developer">Business Developer</option>
                                            <option value="Outbound Sales Executive">Outbound Sales Executive</option>
                                            <option value="Technical Head">Technical Head</option>
                                            <option value="UI/UX Designer - intern">UI/UX Designer - intern</option>
                                            <option value="PHP Developer">PHP Developer</option>
                                            <option value="Team Lead">Team Leader</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="address" class="form-label">Address</label>
                                        <textarea row="10" class="form-control" id="address" name="address"
                                            placeholder="Enter Address" required></textarea>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="employee_id" class="form-label">Employee Id</label>
                                        <input type="text" class="form-control" id="employee_id" name="employee_id"
                                            placeholder="Employee Id" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="branch" class="form-label">Branch</label>
                                        <select class="form-control" id="branch" name="branch" required>
                                            <option selected disabled value="">Select Branch</option>
                                            <option value="Hubli">Hubli</option>
                                            <option value="Bengaluru">Bengaluru</option>
                                            <option value="Hyderabad">Hyderabad</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="salary_per_month" class="form-label">Salary Per Month</label>
                                        <input type="text" class="form-control" id="salary_per_month" name="salary_per_month"
                                            placeholder="Salary Per Month" required>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="ifsc_code" class="form-label">IFSC Code</label>
                                            <input type="text" class="form-control" id="ifsc_code" name="ifsc_code"
                                                placeholder="IFSC Code" required>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="account_number" class="form-label">Account Number</label>
                                            <input type="text" class="form-control" id="account_number" name="account_number"
                                                placeholder="Account Number" required>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="consultency" class="form-label">Consultency</label>
                                            <select class="form-control" id="consultency" name="consultency" required>
                                                <option selected disabled value="">Consultency</option>
                                                <option value="Gautham and Preetam">Gautham and Preetam</option>
                                                <option value="Gautham">Gautham</option>
                                                <option value="Preetam">Preetam</option>
                                                <option value="Sameer Shaikh">Sameer Shaikh</option>
                                                <option value="Naseer">Naseer</option>
                                                <option value="Direct Selected">Direct Selected</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 offset-md-1">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="crm" name="crm" value="1">
                                            <label class="form-check-label" for="crm_access">
                                                Access CRM
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card-action">
                            <button class="btn btn-success" id="save-estimate-btn" form="manage-estimate">Submit</button>
                            <button class="btn btn-danger">Cancel</button>
                    </form>
                </div>

            </div>
        </div>
    </div>
</div>
</div>

<?php
include 'includes/footer.php';
?>


<script>
    $(document).ready(function() {
        $('#manage-estimate').on('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            // alert(formData);


            // Validate form data
            const name = $('#name').val().trim();

            $.ajax({
                url: 'ajax.php?action=save_register',
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                success: function(resp) {
                    if (resp.trim() === 1) {
                        toastr.success('Registration successful');
                        setTimeout(() => window.location.href = 'registration.php', 1500);
                    } else {
                        toastr.error('Error: ' + resp);
                    }
                },
                error: function(xhr) {
                    toastr.error('AJAX error: ' + xhr.statusText);
                }
            });
        });
    });
</script>