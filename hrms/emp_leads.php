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

$resignedCrms = $conn->query("SELECT id FROM register WHERE resign = 1 AND crm = 1");

while ($resigned = $resignedCrms->fetch_assoc()) {
    $resignedEmpId = $resigned['id'];

    // 2. Get all leads assigned to the resigned employee
    $leads = $conn->query("SELECT id FROM customer WHERE emp_id = '$resignedEmpId'");
    $leadIds = [];
    while ($lead = $leads->fetch_assoc()) {
        $leadIds[] = $lead['id'];
    }

    $totalLeads = count($leadIds);
    if ($totalLeads == 0) continue;

    // 3. Get active CRM employees with their current lead counts
    $activeCrms = $conn->query("
        SELECT r.id AS emp_id, COUNT(c.id) AS lead_count 
        FROM register r
        LEFT JOIN customer c ON r.id = c.emp_id
        WHERE r.resign = 0 AND r.crm = 1
        GROUP BY r.id
        ORDER BY lead_count ASC
    ");

    $empDistribution = [];
    while ($emp = $activeCrms->fetch_assoc()) {
        $empDistribution[] = [
            'emp_id' => $emp['emp_id'],
            'lead_count' => $emp['lead_count'],
        ];
    }

    if (count($empDistribution) == 0) continue;

    // 4. Distribute leads to employees with fewer leads first
    $empIndex = 0;
    foreach ($leadIds as $leadId) {
        // Assign to the employee with the least current leads
        usort($empDistribution, fn($a, $b) => $a['lead_count'] - $b['lead_count']);

        $selectedEmp = $empDistribution[0];
        $newEmpId = $selectedEmp['emp_id'];

        // Update customer lead to new employee
        $conn->query("UPDATE customer SET emp_id = '$newEmpId' WHERE id = '$leadId'");

        // Update the local counter
        $empDistribution[0]['lead_count']++;
    }
}
?>
<style>
  .card:hover {
    transform: scale(1.01);
    transition: 0.2s ease;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
  }

  .filter-bar {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    justify-content: center;
    gap: 30px;
    padding: 15px 20px;
    background-color: #f4f4f8;
    border-radius: 12px;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
    margin-bottom: 20px;
  }

  .filter-group {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
  }

  .filter-group label {
    font-weight: 600;
    margin-bottom: 5px;
    font-size: 14px;
    color: #333;
  }

  .filter-group select {
    width: 220px;
    padding: 6px 10px;
    border-radius: 8px;
    border: 1px solid #ccc;
    font-size: 14px;
  }

  #categoryCountDisplay {
    margin-left: 17%;
    font-weight: bold;
    font-size: 16px;
    color: #444;
    background-color: #e7e7f7;
    padding: 5px 16px;
    border-radius: 8px;
    box-shadow: inset 0 0 4px rgba(0, 0, 0, 0.05);
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
  <!-- Page end  -->
  <!-- Add User Modal -->
  <div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel"
    aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="addUserModalLabel">Add New User</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <form id="add-user-form">
            <div class="form-group">
              <label for="user-name">Name</label>
              <input type="text" id="user-name" name="name" class="form-control"
                placeholder="Enter name" pattern="^[A-Za-z\s]{3,50}$"
                title="Name should be 3 to 50 characters long and contain only letters and spaces."
                required>
            </div>

            <div class="form-group">
              <select class="form-control" id="categories" name="categories" required>
                <option selected disabled value="">Select Business</option>
                <option value="Website Development" data-color="#629ddc99">Website Development
                </option>
                <option value="App Development" data-color="#8bcf9b">App Development</option>
                <option value="Software Development" data-color="#d7b85999">Software Development
                </option>
                <option value="Social Media Marketing" data-color="#e9714ba3">Social Media
                  Marketing</option>
              </select>
            </div>
            <div class="form-group">
              <label for="user-number">Mobile Number</label>
              <input type="text" id="user-number" name="number" class="form-control"
                placeholder="Enter mobile number" pattern=^[5-9]{1}\d{9}$
                title="Mobile number must start with 5 and be exactly 10 digits long." required>
            </div>
            <div class="form-group">
              <label for="user-password">Password</label>
              <input type="password" id="user-password" name="password" class="form-control"
                placeholder="Enter password" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}"
                title="Password must contain at least one number,
                                         one uppercase and lowercase letter, and at least 8 or more characters."
                required>
            </div>
            <button type="submit" class="btn btn-primary">Submit</button>
          </form>

        </div>
      </div>
    </div>
  </div>

  <div class="filter-bar">
    <!-- Business Type -->
    <div class="filter-group">
      <label for="scategories">Business Type:</label>
      <select id="scategories" class="form-control" name="categories">
        <option value="All">All Categories</option>
        <option value="Website Development">Website Development</option>
        <option value="App Development">App Development</option>
        <option value="Software Development">Software Development</option>
        <option value="Social Media Marketing">Social Media Marketing</option>
      </select>
    </div>

    <!-- Status Type -->
    <div class="filter-group">
      <label for="sstatus">Status Type:</label>
      <select id="sstatus" class="form-control" name="status">
        <option value="All">All Status</option>
        <option value="Pending">Pending</option>
        <option value="Accepted">Accepted</option>
        <option value="Rejected">Rejected</option>
        <option value="In Progress">In Progress</option>
      </select>
    </div>

    <!-- Count Display -->
    <div id="categoryCountDisplay">
      <!-- Count text will be inserted dynamically here -->
    </div>
  </div>


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
  //  // Toggle sidebar
  //  document.getElementById('toggleSidebar').addEventListener('click', function() {
  //   const sidebar = document.getElementById('sidebar');
  //   sidebar.classList.toggle('active');
  //   });

  const searchInputs = [document.querySelector('#searchInput'), document.querySelector('#searchBarMobile')];
  const userDashboard = document.querySelector('#user-dashboard'); // Container to display user cards

  // Loop over both search inputs (desktop + mobile)
  searchInputs.forEach(input => {
    if (input) {
      input.addEventListener('input', function() {
        const query = this.value.toLowerCase().trim(); // Normalize input

        const userCards = userDashboard.querySelectorAll('.card');
        userCards.forEach(card => {
          const name = card.querySelector('h5').textContent.toLowerCase();
          const number = card.querySelector('p').textContent.toLowerCase();

          // Show/hide based on match
          if (name.includes(query) || number.includes(query)) {
            card.style.display = 'block';
          } else {
            card.style.display = 'none';
          }
        });
      });
    }
  });
  function getQueryParam(param) {
  const urlParams = new URLSearchParams(window.location.search);
  return urlParams.get(param);
}
const emp_id = getQueryParam('emp_id');
// console.log("Fetched emp_id:", emp_id); // ✅ Should show the correct emp_id from URL


  // Fetch users when the page loads
  $(document).ready(function() {

    $('#add-user-form').on('submit', function(e) {
      e.preventDefault(); // ✅ Prevent default form submission

      const formData = {
        name: $('#user-name').val(),
        categories: $('#categories').val(),
        number: $('#user-number').val(),
        password: $('#user-password').val()
      };

      $.ajax({
        url: 'user_form.php',
        type: 'POST',
        data: formData,
        success: function(response) {
          const result = JSON.parse(response);
          if (result.success) {
            Swal.fire({
              icon: 'success',
              title: 'uploaded Successfully!',
              showConfirmButton: false,
              timer: 2000
            }).then(() => {
              window.location.href = "text.php";
            });
            $('#add-user-form')[0].reset();

            // location.
          } else {
            Swal.fire("Error", result.message, "error");
          }
        },
        error: function() {
          Swal.fire("Error", "Something went wrong!", "error");
        }
      });
    });

    fetchUsers();


    // fetchUsers(); // Initial call to fetch and display users


    $('#scategories, #sstatus').on('change', function() {
      const selectedCategory = $('#scategories').val();
      const selectedStatus = $('#sstatus').val();
      fetchUsers(selectedCategory, selectedStatus);
    });


    // Define category-to-color mapping
    const categoryColors = {
      "Website Development": "#629ddc99", // Blue
      "App Development": "#8bcf9b", // Green
      "Software Development": "#d7b85999", // Yellow
      "Social Media Marketing": "#e9714ba3" // Orange
    };

    const statusMap = {
      "Accepted": {
        color: "#adebb0",
        icon: "✔️"
      },
      "Rejected": {
        color: "#f0857d",
        icon: "X"
      },
      "In Progress": {
        color: "#eed27f",
        icon: "⏳"
      },
      "Pending": {
        color: "#FFFFFF",
        icon: "⚪",
        border: "1px solid #ccc"
      }
    };

    // Optional: normalize raw DB value into matching key
    function normalizeStatus(status) {
      switch ((status || "").toLowerCase()) {
        case "accept":
        case "accepted":
          return "Accepted";
        case "reject":
        case "rejected":
          return "Rejected";
        case "inprogress":
        case "in_progress":
          return "In Progress";
        case "pending":
        default:
          return "Pending";
      }
    }
    // Add this inside success: function(response) { ... }
    const totalCount = filteredUsers.length;
    const displayName = selectedCategory === "All" ? "All Categories" : selectedCategory;
    $('#categoryCountDisplay').html(`Showing <span style="color:#3e64ff">${totalCount}</span> from <span style="color:#000;font-weight:600">${displayName}</span>`);


    // console.log(categoryColors);
    // Function to fetch users and display them
    function fetchUsers(selectedCategory = "All", selectedStatus = "All") {


     
      $.ajax({
        url: `emp_cust_data.php?emp_id=${emp_id}`, // PHP script to fetch users with conversation counts
        type: 'GET',
        success: function(response) {
          const users = JSON.parse(response);
          $('#user-dashboard').empty(); // Clear the dashboard

          const filteredUsers = users.filter(user => {
            const matchCategory = selectedCategory === "All" || user.categories.trim() === selectedCategory.trim();
            const normalizedStatus = normalizeStatus(user.status);
            const matchStatus = selectedStatus === "All" || normalizedStatus === selectedStatus;
            return matchCategory && matchStatus;
          });

          // Count display
          const totalCount = filteredUsers.length;
          const displayCategory = selectedCategory === "All" ? "All Categories" : selectedCategory;
          const displayStatus = selectedStatus === "All" ? "All Statuses" : selectedStatus;

          $('#categoryCountDisplay').html(`
        <div style="display: flex; align-items: center; gap: 10px;">
          <div style="
            width: 40px;
            height: 40px;
            background-color: #785dc8;
            color: white;
            display: flex;
            justify-content: center;
            align-items: center;
            border-radius: 50%;
            font-size: 18px;
            font-weight: bold;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
            ${totalCount}
          </div>
          <span style="font-weight: 600; font-size: 16px;">from ${displayCategory}</span>
        </div>
      `);

          if (filteredUsers.length > 0) {
            filteredUsers.forEach(function(user) {
              const bgColor = categoryColors[user.categories.trim()] || "#d3d3d3"; // Default gray for unknown categories
              // Get status icon and color
              const normalizedStatus = normalizeStatus(user.status);
              const statusData = statusMap[normalizedStatus];
              const statusIcon = `
                    <div style="
                      width: 40px;
                      height: 40px;
                      background-color: ${statusData.color};
                      color: ${normalizedStatus === 'Pending' ? '#333' : '#785dc8'};
                      font-weight: 900;
                      border: ${statusData.border || 'none'};
                      display: flex;
                      justify-content: center;
                      align-items: center;
                      border-radius: 50%;
                      font-size: 20px;
                      box-shadow: 0 2px 4px rgba(0,0,0,0.1);" 
                      title="${normalizedStatus}">
                      ${statusData.icon}
                    </div>
                  `;

<?php

?>

              const userCard = `
                            <div class="col-md-4 mb-3">
                             
                               <a href="emp-forms.php?cust_id=${encodeURIComponent(user.id)}&emp_id=${encodeURIComponent(emp_id)}" style="text-decoration: none;">
                                    <div class="card" style="border-radius: 17px;background-color: #f3ffff;    box-shadow: 2px 3px 8px rgb(0 0 0 / 52%);">
                                    <div class="card-body d-flex justify-content-between align-items-center">
                                            <!-- User data on the left -->
                                            <div>
                                                <h5 class="mb-1">${user.name}</h5>
                                                <p class="mb-0">${user.number}</p>
                                                <p class="mb-0" style="
                                                    background-color: ${bgColor}; 
                                                    color: black; 
                                                    padding: 5px; 
                                                    border-radius:5px; 
                                                    font-size: 11px;
                                                    display: inline-block;">
                                                    ${user.categories}
                                                </p>
                                            </div>
                                            <!-- Circular background for the conversation count on the right -->
                                              ${statusIcon}
                                        </div>
                                    </div>
                                    
                                </a>
                            </div>
                            

                        `;
              $('#user-dashboard').append(userCard);
            });
          } else {
            $('#user-dashboard').append('<p>No users found.</p>');
          }
        },
        error: function() {
          alert('Failed to fetch users.');
        }
      });
    }

  });





  document.getElementById('categories').addEventListener('change', function() {
    const selectedOption = this.options[this.selectedIndex];
    const category = selectedOption.value; // Get the selected category
    const colorClass = selectedOption.getAttribute('data-color'); // Get the data-color attribute

    const categoryDisplay = document.getElementById('selected-category');
    categoryDisplay.textContent = category; // Update the text content
    categoryDisplay.className = `mb-0 ${colorClass}`; // Apply the background color class
  });
</script>