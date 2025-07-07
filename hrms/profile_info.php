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

$id = $_SESSION['register_id'];
$stmt = $conn->prepare("SELECT * FROM register WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$res = $stmt->get_result();
$emp = $res->fetch_assoc();

if (!$emp) {
    die("Employee not found.");
}

// Dummy user image if not available
$userImage = $emp['img'] ?? 'default.png';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Employee Profile</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        body {
            background: #f4f6f8;
        }

        .profile-card {
            border: none;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.06);
        }

        .profile-header {
            background: linear-gradient(29deg, #1a2035, #5c6bc0);
            color: white;
            padding: 2rem;
            text-align: center;
        }

        .profile-header img {
            width: 110px;
            height: 110px;
            object-fit: cover;
            border-radius: 50%;
            border: 4px solid white;
            margin-bottom: 1rem;
        }

        .profile-body {
            padding: 2rem;
        }

        .profile-info-label {
            font-size: 0.85rem;
            color: #6c757d;
            margin-bottom: 0;
        }

        .profile-info-value {
            font-size: 1rem;
            font-weight: 500;
            color: #343a40;
        }

        @media (max-width: 767.98px) {
            .profile-header {
                padding: 1.5rem;
            }

            .profile-body {
                padding: 1.5rem;
            }
        }

        /* Styles for the profile image and camera overlay */
        .profile-image-container {
            position: relative;
            /* This is crucial for positioning the camera icon */
            width: 110px;
            /* Same as img width */
            height: 110px;
            /* Same as img height */
            margin: 0 auto 1rem auto;
            /* Center the container and add bottom margin */
            border-radius: 50%;
            /* overflow: hidden; Ensures image stays within circular boundary */
            border: 4px solid white;
            /* Matches image border */
        }

        .profile-image-container img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
            /* Remove extra space below image */
        }

        .camera-overlay {
            position: absolute;
            bottom: 0;
            /* Adjust as needed */
            right: 0;
            /* Adjust as needed */
            transform: translate(25%, 25%);
            /* Fine-tune position */
            background-color: white;
            width: 40px;
            /* Smaller icon size for better fit */
            height: 40px;
            /* Smaller icon size for better fit */
            border-radius: 50%;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            z-index: 10;
            /* Ensure it's above the image */
        }

        .camera-overlay i {
            font-size: 1.2rem;
            /* Adjust icon size */
            color: #6c757d;
            /* Gray color for icon */
        }


        .profile-body {
            padding: 2rem;
        }

        .profile-info-label {
            font-size: 0.85rem;
            color: #6c757d;
            margin-bottom: 0;
        }

        .profile-info-value {
            font-size: 1rem;
            font-weight: 500;
            color: #343a40;
        }

        @media (max-width: 767.98px) {
            .profile-header {
                padding: 1.5rem;
            }

            .profile-body {
                padding: 1.5rem;
            }
        }
    </style>
</head>

<body>
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
                        <a href="#">Profile</a>
                    </li>
                </ul>
            </div>
            <div class="container my-5">
                <div class="row justify-content-center">
                    <div class="col-md-10 col-xl-8">
                        <div class="card profile-card">
                            <div class="profile-header">
                                <button id="editProfileBtn" class="btn btn-sm btn-outline-light float-end" title="Edit Profile">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="bi bi-pencil" viewBox="0 0 16 16">
                                        <path d="M12.146.854a.5.5 0 0 1 .708 0l2.292 2.292a.5.5 0 0 1 0 .708l-9.439 9.439a.5.5 0 0 1-.168.11l-4 1.5a.5.5 0 0 1-.65-.65l1.5-4a.5.5 0 0 1 .11-.168l9.439-9.439zm1.415 2.121L13 2.414 3.5 11.914l-.793 2.378 2.378-.793L13.586 3.5l-.025-.025zm-1.768-.354L2 10.414V13h2.586l9.793-9.793-2.586-2.586z" />
                                    </svg>
                                </button>


                                <div class="profile-image-container">
                                    <?php
                                    $profileImg = !empty($emp['img']) && file_exists("../" . $emp['img'])
                                        ? "../" . $emp['img']
                                        : "assets/img/profile.jpg"; // path to your default image
                                    ?>
                                    <img id="profileImageDisplay" src="<?php echo $profileImg; ?>" alt="Profile" class="img-fluid">

                                    <div class="camera-overlay" onclick="document.getElementById('profileImageInput').click()">
                                        <i class="fas fa-camera"></i>
                                    </div>
                                    <input type="file" id="profileImageInput" name="profile_image" accept="image/*" style="display: none;">
                                </div>
                                <h4 class="fw-bold"><?php echo htmlspecialchars($emp['name']); ?></h4>
                                <div><?php echo htmlspecialchars($emp['role']); ?></div>
                                <div class="text-white-50"><?php echo htmlspecialchars($emp['email']); ?></div>
                            </div>


                        <div class="profile-body">
                            <div class="row g-4">
                                <?php
                                $fields = [
                                    'Employee ID' => 'employee_id',
                                    'Department' => 'department',
                                    'Joining Date' => 'joining_date',
                                    'Mobile' => 'mobile',
                                    'Address' => 'address',
                                    'Gender' => 'gender',
                                    'Branch' => 'branch',
                                    'Account Number' => 'account_number',
                                    'IFSC Code' => 'ifsc_code',
                                    'Salary / Month' => 'salary_per_month',
                                    'Consultancy' => 'consultency',
                                    'Password' => 'password',
                                    'Email' => 'email', // Add email to fields for editing
                                    'Name' => 'name', // Add name for editing
                                    'Role' => 'role' // Add role for editing
                                ];
                                foreach ($fields as $label => $key):
                                    // Ensure the 'name' and 'role' are accessible from $emp
                                    $value = $emp[$key] ?? 'N/A';
                                    // if ($key === 'password' && !empty($value)) {
                                    //     $value = '********'; // Mask password for display
                                    // }
                                ?>
                                    <div class="col-md-6">
                                        <p class="profile-info-label mb-1"><?php echo $label; ?></p>
                                        <p class="profile-info-value" data-key="<?php echo $key; ?>"><?php echo htmlspecialchars($value); ?></p>
                                    </div>
                                <?php endforeach; ?>
                                <button id="saveProfileBtn" class="btn btn-sm text-white float-end me-2 d-none" style="background-color:#17234d;" type="submit">Save</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
    <script>
        // AJAX and Edit/Save Script
        document.addEventListener('DOMContentLoaded', function() {
            const editBtn = document.getElementById('editProfileBtn');
            const saveBtn = document.getElementById('saveProfileBtn');
            const infoValues = document.querySelectorAll('.profile-info-value');
            let backupValues = []; // To store original values if you implement a cancel button

            editBtn.addEventListener('click', function() {
                backupValues = []; // Clear backup for a fresh edit session
                infoValues.forEach(div => {
                    const key = div.getAttribute('data-key'); // Get the data-key
                    const value = div.textContent.trim();
                    backupValues.push({
                        div,
                        value
                    }); // Store for potential revert

                    let inputType = 'text';
                    if (key === 'email') inputType = 'email';
                    if (key === 'password') inputType = 'password';
                    if (key === 'joining_date') inputType = 'date';
                    if (key === 'salary_per_month') inputType = 'number';

                    let inputValue = value.replace(/"/g, '&quot;');
                    // if (key === 'password' && value === '********') {
                    //     inputValue = ''; // Don't pre-fill masked password for security reasons
                    // }

                    // Special handling for Gender and Role as dropdowns
                    if (key === 'gender') {
                        div.innerHTML = `
                            <select class="form-control form-control-sm" name="${key}">
                                <option value="Male" ${value === 'Male' ? 'selected' : ''}>Male</option>
                                <option value="Female" ${value === 'Female' ? 'selected' : ''}>Female</option>
                                <option value="Other" ${value === 'Other' ? 'selected' : ''}>Other</option>
                            </select>`;
                    } else if (key === 'role') {
                        const roleOptions = [
                            "Software Developer", "Software Engineer", "Junior Developer", "UI UX Designer",
                            "HR Manager", "HR Head", "Backend Developer", "Frontend Developer", "Full Stack Developer", "Flutter Developer",
                            "Java Developer", "Cloud Engineer", "Software Tester", "Manual Testing",
                            "DevOps Engineer", "Fullstack Developer Engineer", "Testing Engineer",
                            "Digital Marketing Intern", "Digital Marketing Manager1", "Digital Marketing Executive",
                            "Customer Service Representative", "business developer", "Outbound Sales Executive",
                            "Technical Head", "UI/UX Designer - intern", "PHP Developer", "Team Lead"
                        ];
                        let optionsHtml = `<option selected disabled value="">Select Role</option>`;

                        roleOptions.forEach(optionRole => {
                            const isSelected = (value === optionRole) ? 'selected' : '';
                            optionsHtml += `<option value="${optionRole}" ${isSelected}>${optionRole}</option>`;
                        });

                        div.innerHTML = `
                    <select class="form-control form-control-sm" name="${key}" required>
                        ${optionsHtml}
                    </select>`;
                    } else {
                        // All other fields become text inputs
                        div.innerHTML = `<input type="${inputType}" class="form-control form-control-sm" name="${key}" value="${inputValue}" />`;
                    }
                });
                editBtn.classList.add('d-none');
                saveBtn.classList.remove('d-none');
            });

            saveBtn.addEventListener('click', function() {
                const formData = new FormData();

                // Add the employee's ID to the form data for the UPDATE query
                const userId = <?php echo json_encode($id); ?>; // Get ID from PHP session variable
                formData.append('id', userId); // This 'id' will be used in the WHERE clause in PHP

                // Collect data from all input/select fields
                document.querySelectorAll('.profile-info-value input, .profile-info-value select').forEach(input => {
                    formData.append(input.name, input.value);
                });

                // Optional: Log formData for debugging
                // for (let pair of formData.entries()) {
                //     console.log(pair[0]+ ': ' + pair[1]);
                // }

                $.ajax({
                    url: 'ajax.php?action=save_register', // Your AJAX endpoint
                    type: 'POST',
                    data: formData,
                    contentType: false, // Important for FormData
                    processData: false, // Important for FormData
                    success: function(response) {
                        console.log('Server response:', response); // Always log the raw response

                        if (response.trim() === '1') {
                            // Use Bootstrap Notify for success
                            $.notify({
                                icon: 'fas fa-check-circle', // Font Awesome icon for success
                                message: 'Profile updated successfully!',
                            }, {
                                type: 'success', // Bootstrap class for styling
                                placement: {
                                    from: "top",
                                    align: "right"
                                },
                                timer: 1000, // How long the notification stays (in ms)
                                z_index: 9999 // Ensure it's on top
                            });

                            setTimeout(function() {
                                location.reload();
                            }, 1500); // Give time for notification to show, then reload
                        } else {
                            // Use Bootstrap Notify for error
                            $.notify({
                                icon: 'fas fa-times-circle', // Font Awesome icon for error
                                message: 'Failed to update profile. Server said: ' + response,
                            }, {
                                type: 'danger', // Bootstrap class for styling
                                placement: {
                                    from: "top",
                                    align: "right"
                                },
                                timer: 3000, // Error messages usually stay longer
                                z_index: 9999
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX Error:', status, error, xhr.responseText);
                        toastr.error('A network or server error occurred. Please try again.');
                    }
                });
            });
            
        });





    </script>
    <!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script> -->
    <!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script> -->

</body>

</html>