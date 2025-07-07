<?php
session_start();
ini_set('display_errors', 1);
class Action
{

	private $conn;

	public function __construct()
	{
		ob_start();
		include 'includes/connection.php';

		$this->conn = $conn;
	}
	function __destruct()
	{
		$this->conn->close();
		ob_end_flush();
	}



	// function login()
	// {
	// 	extract($_POST);
	// 	$qry = $this->conn->query("SELECT *,concat(firstname,' ',lastname) as name FROM register where email = '" . $email . "' and password = '" . md5($password) . "'");
	// 	if ($qry->num_rows > 0) {
	// 		foreach ($qry->fetch_array() as $key => $value) {
	// 			if ($key != 'password' && !is_numeric($key))
	// 				$_SESSION['login_' . $key] = $value;
	// 		}

	// 		return 1;
	// 	} else {
	// 		return 2;
	// 	}
	// }

	  function save_register() {
        $data = "";
        $register_id_to_update = null;

        // Process regular POST data (excluding the 'id' which is used for WHERE clause)
        foreach ($_POST as $k => $v) {
            if ($k === 'id') {
                $register_id_to_update = (int)$v;
                continue;
            }
            // Exclude 'img' and 'current_img_path' from direct data string as they are handled separately
            if (!is_numeric($k) && $k !== 'img' && $k !== 'current_img_path') {
                if (empty($data)) {
                    $data .= "`$k`='". $this->conn->real_escape_string($v) ."'";
                } else {
                    $data .= ", `$k`='". $this->conn->real_escape_string($v) ."'";
                }
            }
        }

        // --- Handle Image Upload ---
        if (isset($_FILES['img']) && $_FILES['img']['error'] == 0) {
            // Define your upload directory relative to the *project root*.
            // Assuming admin_class.php is in 'includes/' and 'uploads' is in the root.
            $uploadDir = '../assets/profile_imgs/'; 
            
            // Ensure the upload directory exists and is writable
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true); // Create directory recursively with permissions
            }

            $fileExtension = pathinfo($_FILES['img']['name'], PATHINFO_EXTENSION);
            $newFileName = uniqid('profile_') . '.' . $fileExtension; // Unique filename
            $destination = $uploadDir . $newFileName;

            if (move_uploaded_file($_FILES['img']['tmp_name'], $destination)) {
                // Path to store in the database (relative to web root or your base path)
                // Assuming your web server serves from the directory containing 'uploads'
                $imagePathForDb = 'assets/profile_imgs/' . $newFileName; 
                
                if (empty($data)) {
                    $data .= "`img`='". $this->conn->real_escape_string($imagePathForDb) ."'";
                } else {
                    $data .= ", `img`='". $this->conn->real_escape_string($imagePathForDb) ."'";
                }

                // Optional: Delete the old profile image
                // Only if current_img_path was sent AND it's not the default image AND it actually exists
                if (isset($_POST['current_img_path']) && !empty($_POST['current_img_path'])) {
                    $oldImgPath = "../" . $_POST['current_img_path']; // Adjust path
                    // Prevent deleting default image or images not in your uploads directory
                    if (file_exists($oldImgPath) && strpos($oldImgPath, '../assets/profile_imgs/') !== false && $oldImgPath !== "../assets/img/profile.jpg") {
                        unlink($oldImgPath);
                    }
                }

            } else {
                error_log("Failed to move uploaded file. Error: " . $_FILES['img']['error']);
                return "Error uploading image: Failed to move file.";
            }
        }
        // --- End Image Upload Handling ---

        // Check if there's data to update or insert
        if (empty($data)) {
            return "No data to update.";
        }

        if (!empty($register_id_to_update)) {
            $query = "UPDATE register SET $data WHERE id=$register_id_to_update";
        } else {
            // This case should ideally not happen if you're always updating an existing profile
            // If you intend to use this for new registrations too, adjust logic
            $query = $this->conn->query("INSERT INTO meeting_links set $data");
        }

        $save = $this->conn->query($query);

        if ($save) {
            return 1; // Success
        } else {
            error_log("MySQL Error: " . $this->conn->error . " Query: " . $query);
            return "Error: " . $this->conn->error;
        }
    }

	function save_meeting()
	{
		extract($_POST);
		$data = "";
		foreach ($_POST as $k => $v) {
			if (!in_array($k, array('id')) && !is_numeric($k)) {
				if (empty($data)) {
					$data .= " $k='$v' ";
				} else {
					$data .= ", $k='$v' ";
				}
			}
		}

		if (empty($id)) {
			$save = $this->conn->query("INSERT INTO meeting_links set $data");
		} else {
			$save = $this->conn->query("UPDATE meeting_links set  $data where id=$id");
		}


		if ($save) {

			return 1;
			// exit;
		}
	}

	function delete_meeting() {
    if (!isset($_POST['ids']) || !is_array($_POST['ids'])) {
        return 0;
    }  

    // Sanitize and build query
    $ids = array_map('intval', $_POST['ids']);
    $ids_str = implode(',', $ids);

    $delete = $this->conn->query("DELETE FROM meeting_links WHERE id IN ($ids_str)");

    return $delete ? 1 : 0;
}


}

?>