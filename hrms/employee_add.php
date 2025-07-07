<header class="page-title-bar">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item active">
                <a href="#">
                    <i class="breadcrumb-icon fa fa-angle-left mr-2"></i>Tables
                </a>
            </li>

        </ol>
    </nav>

    <div class="d-md-flex align-items-md-start">
        <h1 class="page-title mr-sm-auto">Add New Employee</h1><!-- .btn-toolbar -->
        <div class="btn-toolbar">


</header>


<div class="col-lg-12">
    <div class="card card-outline card-primary">
        <div class="card-body">
            <form id="addClientForm">
                <input type="hidden" name="id" value="<?php echo isset($id) ? $id : '' ?>">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-6 form-group">
                            <label for="joining_date" class="control-label"> Date</label>
                            <input type="date" name="dt" id="dt" class="form-control" value="<?php echo isset($dt) ? $dt : ''; ?>" required>
                        </div>

                        <div class="col-sm-6 form-group ">
                            <label for="" class="control-label">Employee Name</label>
                            <input type="text" name="emp_name" id="emp_name" cols="30" rows="2" class="form-control" value="<?php echo isset($emp_name) ?  $emp_name  : '' ?>">
                        </div>


                        <div class="col-sm-6 form-group ">
                            <label for="" class="control-label">Employee Phone</label>
                            <input type="text" name="phone" id="phone" cols="30" rows="2" class="form-control" value="<?php echo isset($phone) ?  $phone  : '' ?>">
                        </div>


                        <div class="col-sm-6 form-group ">
                            <label for="" class="control-label">Employee Email</label>
                            <input type="text" name="email" id="email" cols="30" rows="2" class="form-control" value="<?php echo isset($email) ?  $email  : '' ?>">
                        </div>



                        <div class="col-sm-6 form-group">
                            <label for="role" class="control-label">Employee Role</label>
                            <select name="role" id="role" class="form-control" required>
                                <option value="">-- Select Role --</option>
                                <option value="Booking Person" <?php echo (isset($role) && $role == 'Booking Person') ? 'selected' : ''; ?>>Booking Person</option>
                                <option value="Consignment Manager" <?php echo (isset($role) && $role == 'Consignment Manager') ? 'selected' : ''; ?>>Consignment Manager</option>
                                <option value="Delivery Manager" <?php echo (isset($role) && $role == 'Delivery Manager') ? 'selected' : ''; ?>>Delivery Manager</option>
                                <option value="Payment Manager" <?php echo (isset($role) && $role == 'Payment Manager') ? 'selected' : ''; ?>>Payment Manager</option>
                            </select>
                        </div>

                    </div>

                </div>
                <div class="modal-footer d-flex justify-content-center">
                    <button type="submit" class="btn btn-primary" id="addClientForm">Save</button>
                    <a href="./index.php?page=employee_list" class="btn btn-secondary ml-2">Close</a>
                </div>

            </form>
        </div>
    </div>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script>
    $(document).ready(function() {
        $('#addClientForm').submit(function(e) {
            e.preventDefault(); // Prevent default form submission

            let formData = new FormData(this);

            $.ajax({
                url: 'ajax.php?action=save_employee', // Backend PHP file
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