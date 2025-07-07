<?php

    include 'includes/connection.php';
// Initialize variables
$name = $email = $employee_id = $department = $role = $joining_date = $mobile = $address = $gender = $account_number = $ifsc_code = $salary_per_month = "";

// Get the record ID from the URL
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Fetch data for the specific ID
    $sql = "SELECT * FROM register WHERE id = $id";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // Fetch the existing values into variables
        $row = $result->fetch_assoc();
        $name = $row['name'];
        $email = $row['email'];
        $employee_id = $row['employee_id'];
        $department = $row['department'];
        $role = $row['role'];
        $joining_date = $row['joining_date'];
        $mobile = $row['mobile'];
        $address = $row['address'];
        $gender = $row['gender'];
        $account_number = $row['account_number'];
        $ifsc_code = $row['ifsc_code'];
        $salary_per_month = $row['salary_per_month'];

        

    }
}

// Close the connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Form</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="index.css">
     <!-- SweetAlert CDN -->
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <style>
         /* General Styling */
        body {
            background-color: #f8f9fa;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .signup-box {
            background-color: #fff;
            padding: 40px;
            border-radius: 10px;
              box-shadow: 0 5px 15px rgb(231 45 60 / 73%);
            width: 100%;
            max-width: 700px;
        }

        .signup-box h2 {
            text-align: center;
            margin-bottom: 15px;
            font-weight: bold;
            color: #f2167a;
            font-size:20px;
        }

        .form-control {
            margin-bottom: 20px;
            border-radius: 30px;
            padding: 10px 20px;
            border: 1px solid #ddd;
        }

        .btn-primary {
            background-color: #ff0080;
            border-color: #ff0080;
            width: 60%;
            border-radius: 30px;
            padding: 10px;
            margin-left: 20%;
        }

        .btn-primary:hover {
            background-color: #ff4da6;
            border-color: #ff4da6;
        }

        .logo {
            display: block;
            margin: auto 5px 30px;
            width: 40%;
          /*margin-top: 15%;*/
          /*margin-left:15%;*/
        }

        /* Responsive Adjustments */
        @media (min-width: 768px) {
            .logo {
                width: 40%;
            }

            .login-box {
                width: 30%;
            }
        }

        .form-group.error input,
        .form-group.error select {
            border-color: red;
        }

        .error-message {
            color: red;
            font-size: 0.9em;
            margin-top: 5px;
        }

       

        .row-input {
            margin-bottom: 15px; /* Space between rows */
        }

        /* Style for the input fields */
        .form-control {
            border: 1px solid #ced4da; /* Default border color */
            border-radius: 50px; /* Rounded corners */
            height: 40px; /* Set height */
        }
    </style>
</head>

<body>
    <div class="signup-box">
        <img src="crawlers" alt="Logo" class="logo"> <!-- Add your logo here -->
        <form action="submitUpdate.php?id=<?= $id ?>" method="post">
            <h2>Registration</h2>
            <div class="row row-input">
                <div class="col-md-6">
                    <div class="form-group">
                        <!-- <label for="name">Name</label> -->
                        <input type="text" class="form-control" id="name" name="name" value="<?= $name ?>" placeholder="Enter name" required
                            minlength="3" maxlength="50" pattern="[A-Za-z\s]+"
                            title="Name should only contain letters and spaces.">
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="form-group">
                        <!-- <label for="email">Email address</label> -->
                        <input type="email" class="form-control" id="email" value="<?= $email ?>" name="email" aria-describedby="emailHelp"
                            placeholder="Enter email" required>
                    </div>
                </div>
            </div>
            
            <div class="row row-input">
                <div class="col-md-6">
                    <div class="form-group">
                        <!-- <label for="employee_id">Employee Id</label> -->
                        <input type="text" class="form-control" id="employee_id" value="<?= $employee_id ?>" name="employee_id" placeholder="Employee Id" required>
                    </div>
                </div>
            
            
                <div class="col-md-6">
                    <div class="form-group">
                        <!-- <label for="department">Department</label> -->
                        <select class="form-control" id="department" name="department" value="<?= $department ?>" required>
                            <option selected disabled value="">Select Department</option>
                            <option value="Development">Development</option>
                            <option value="Testing">Testing</option>
                            <option value="Management">Management</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="row row-input">
                    <div class="col-md-6">
                    <div class="form-group">
                        <!-- <label for="role">Role</label> -->
                        <select class="form-control" id="role" name="role" value="<?= $role ?>" required>
                            <option selected disabled value="">Select Role</option>
                            <option value="Software Developer">Software Developer</option>
                            <option value="Software Engineer">Software Engineer</option>
                            <option value="Junior Developer">Junior Developer</option>
                            <option value="UI UX Designer">UI UX Designer</option>
                            <option value="HR Manager">HR Manager</option>
                            <option value="Digital Marketing Intern">Digital Marketing Intern</option>
                            <option value="Full Stack Developer">Full Stack Developer</option>
                            <option value="Cloud Engineer">Cloud Engineer</option>
                            <option value="Software Tester">Software Tester</option>
                            <option value="Manual Testing">Manual Testing</option>
                            <option value="DevOps Engineer">DevOps Engineer</option>
                            <option value="Fullstack Developer Engineer">Fullstack Developer Engineer</option>
                            <option value="Testing Engineer">Testing Engineer</option>
                        </select>
                    </div>
                </div>
                
           
                
                <div class="col-md-6">
                    <div class="form-group">
                        <!-- <label for="joining_date">Joining Date</label> -->
                        <input type="date" class="form-control" id="joining_date" name="joining_date" value="<?= $joining_date ?>" required>
                    </div>
                </div>
             </div>
             <div class="row row-input">
                <div class="col-md-6">
                    <div class="form-group">
                        <!-- <label for="mobile">Mobile Number</label> -->
                        <input type="text" class="form-control" id="mobile" name="mobile" value="<?= $mobile ?>" maxlength="10" placeholder="Mobile Number" required pattern="^[6-9]\d{9}$" title="Please enter a valid 10-digit mobile number">
                    </div>
                </div>
            
                <div class="col-md-6">
                    <div class="form-group">
                        <!-- <label for="address">Address</label> -->
                        <input type="text" class="form-control" id="address" name="address" value="<?= $address ?>" placeholder="Enter Address" required>
                    </div>
                </div>
            </div>
            <div class="row row-input">
                <div class="col-md-6">
                    <div class="form-group">
                        <!-- <label for="gender">Gender</label> -->
                        <select class="form-control" id="gender" name="gender" value="<?= $gender ?>" required>
                            <option selected disabled value="">Select Gender</option>
                            <option value="male">Male</option>
                            <option value="female">Female</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <!-- <label for="joining_date">Joining Date</label> -->
                        <input type="text" class="form-control" id="account_number" name="account_number"  value="<?= $account_number ?>"  placeholder="Account Number" required>
                    </div>
                </div>
              
            </div>
        
              <div class="row row-input">
               <div class="col-md-6">
                    <div class="form-group">
                        <!-- <label for="employee_id">Employee Id</label> -->
                        <input type="text" class="form-control" id="ifsc_code" name="ifsc_code"  value="<?= $ifsc_code ?>" placeholder="IFSC Code" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <!-- <label for="employee_id">Employee Id</label> -->
                        <input type="text" class="form-control" id="salary_per_month" name="salary_per_month"   value="<?= $salary_per_month ?>" placeholder="Salary Per Month" required>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                    <div class="form-group">
                <button type="submit" class="btn btn-primary submit">Register</button>
                </div>
                </div>
            </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.js" integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4=" crossorigin="anonymous"></script>
    <script>
$(document).ready(function () {
    // Check if status exists in the URL
    const urlParams = new URLSearchParams(window.location.search);
    const status = urlParams.get('status');

    // Show SweetAlert based on the status
    if (status === 'success') {
        swal({
            title: "Updated Successful!",
            text: "Data updated successfully!",
            icon: "success",
            button: "OK",
        }).then(() => {
                            window.location.href = "allData.php";
                        });
    } else if (status === 'error') {
        swal({
            title: "Error!",
            text: "Failed to update data. Please try again.",
            icon: "error",
            button: "OK",
        });
    }
});
</script>

</body>
</html>
