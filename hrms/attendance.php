<?php
session_start();
include 'includes/connection.php';
include './includes/header.php';
include './includes/footer.php';

// Check if user is logged in
if (!isset($_SESSION['email'])) {
    header("Location: index.php");
    exit();
}

// Get employee details
$email = $_SESSION['email'];
$stmt = $conn->prepare("SELECT id, name, employee_id FROM register WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$employee = $result->fetch_assoc();
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance System</title>
    <!-- Include your existing CSS files -->
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .photo-container {
            width: 100%;
            max-width: 500px;
            margin: 0 auto;
            text-align: center;
            padding: 20px;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        #photoCanvas {
            width: 100%;
            max-width: 400px;
            border: 2px dashed #ccc;
            margin: 15px 0;
        }
        .btn-capture {
            margin: 5px;
        }
        .location-info {
            margin-top: 15px;
            padding: 10px;
            background: #f8f9fa;
            border-radius: 5px;
        }
        .hidden {
            display: none;
        }
    </style>
</head>
<body>
    <?php include 'includes/emp_sidebar.php'; ?>
    
    <div class="main-panel">
        <div class="content">
            <div class="page-inner">
                <div class="page-header">
                    <h4 class="page-title">Attendance System</h4>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <div class="card-title">Mark Your Attendance</div>
                            </div>
                            <div class="card-body">
                                <div class="photo-container">
                                    <h3>Capture Your Photo</h3>
                                    <p>Please take a clear photo of yourself to mark your attendance</p>
                                    
                                    <div id="cameraControls">
                                        <video id="video" width="400" height="300" autoplay class="hidden"></video>
                                        <canvas id="photoCanvas" class="hidden"></canvas>
                                        
                                        <div id="imagePreview" class="text-center">
                                            <img id="uploadedImage" src="" alt="Preview" class="img-fluid hidden">
                                        </div>
                                        
                                        <div class="btn-group">
                                            <button id="startCamera" class="btn btn-primary btn-capture">
                                                <i class="fas fa-camera"></i> Open Camera
                                            </button>
                                            <button id="captureBtn" class="btn btn-success btn-capture hidden">
                                                <i class="fas fa-camera"></i> Capture Photo
                                            </button>
                                            <button id="retakeBtn" class="btn btn-warning btn-capture hidden">
                                                <i class="fas fa-sync-alt"></i> Retake
                                            </button>
                                        </div>
                                        
                                        <div class="mt-3">
                                            <button id="uploadBtn" class="btn btn-secondary btn-capture">
                                                <i class="fas fa-upload"></i> Upload From Device
                                            </button>
                                            <input type="file" id="fileInput" accept="image/*" class="hidden">
                                        </div>
                                        
                                        <div class="location-info">
                                            <p><strong>Location:</strong> <span id="locationText">Fetching location...</span></p>
                                            <input type="hidden" id="latitude" name="latitude">
                                            <input type="hidden" id="longitude" name="longitude">
                                        </div>
                                        
                                        <div class="mt-3">
                                            <button id="submitBtn" class="btn btn-success btn-lg hidden">
                                                <i class="fas fa-check-circle"></i> Submit Attendance
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- jQuery -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<!-- Toastr -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastr/build/toastr.min.css" />
    
    <script>
        // Global variables
        let stream = null;
        let capturedPhoto = null;
        let currentLocation = null;
        
        // DOM elements
        const video = document.getElementById('video');
        const photoCanvas = document.getElementById('photoCanvas');
        const startCameraBtn = document.getElementById('startCamera');
        const captureBtn = document.getElementById('captureBtn');
        const retakeBtn = document.getElementById('retakeBtn');
        // const uploadBtn = document.getElementById('uploadBtn');
        const fileInput = document.getElementById('fileInput');
        const submitBtn = document.getElementById('submitBtn');
        const locationText = document.getElementById('locationText');
        const latitudeInput = document.getElementById('latitude');
        const longitudeInput = document.getElementById('longitude');
        const uploadedImage = document.getElementById('uploadedImage');
        
        // Start camera
        startCameraBtn.addEventListener('click', async () => {
            try {
                stream = await navigator.mediaDevices.getUserMedia({ 
                    video: { 
                        facingMode: 'user', // Front camera
                        width: { ideal: 1280 },
                        height: { ideal: 720 }
                    }, 
                    audio: false 
                });
                video.srcObject = stream;
                video.classList.remove('hidden');
                startCameraBtn.classList.add('hidden');
                captureBtn.classList.remove('hidden');
                photoCanvas.classList.add('hidden');
                uploadedImage.classList.add('hidden');
                
                // Get location when camera starts
                getLocation();
            } catch (err) {
                console.error("Error accessing camera: ", err);
                Swal.fire({
                    icon: 'error',
                    title: 'Camera Error',
                    text: 'Could not access the camera. Please check permissions.',
                });
            }
        });
        
        // Capture photo
        captureBtn.addEventListener('click', () => {
            photoCanvas.width = video.videoWidth;
            photoCanvas.height = video.videoHeight;
            photoCanvas.getContext('2d').drawImage(video, 0, 0, photoCanvas.width, photoCanvas.height);
            
            capturedPhoto = photoCanvas.toDataURL('image/png');
            photoCanvas.classList.remove('hidden');
            video.classList.add('hidden');
            captureBtn.classList.add('hidden');
            retakeBtn.classList.remove('hidden');
            submitBtn.classList.remove('hidden');
            
            // Stop camera stream
            if (stream) {
                stream.getTracks().forEach(track => track.stop());
            }
        });
        
        // Retake photo
        retakeBtn.addEventListener('click', () => {
            photoCanvas.classList.add('hidden');
            uploadedImage.classList.add('hidden');
            startCameraBtn.click();
        });
        
        // Upload from device
        uploadBtn.addEventListener('click', () => {
            fileInput.click();
        });
        
        fileInput.addEventListener('change', (e) => {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = (event) => {
                    capturedPhoto = event.target.result;
                    uploadedImage.src = capturedPhoto;
                    uploadedImage.classList.remove('hidden');
                    photoCanvas.classList.add('hidden');
                    video.classList.add('hidden');
                    startCameraBtn.classList.remove('hidden');
                    captureBtn.classList.add('hidden');
                    retakeBtn.classList.remove('hidden');
                    submitBtn.classList.remove('hidden');
                    
                    // Get location when photo is uploaded
                    getLocation();
                };
                reader.readAsDataURL(file);
            }
        });
        
        // Get current location
        function getLocation() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    (position) => {
                        currentLocation = {
                            latitude: position.coords.latitude,
                            longitude: position.coords.longitude
                        };
                        latitudeInput.value = currentLocation.latitude;
                        longitudeInput.value = currentLocation.longitude;
                        
                        // Reverse geocoding to get address (using Nominatim API)
                        fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${currentLocation.latitude}&lon=${currentLocation.longitude}`)
                            .then(response => response.json())
                            .then(data => {
                                let address = '';
                                if (data.address) {
                                    if (data.address.road) address += data.address.road + ', ';
                                    if (data.address.suburb) address += data.address.suburb + ', ';
                                    if (data.address.city) address += data.address.city + ', ';
                                    if (data.address.state) address += data.address.state + ', ';
                                    if (data.address.country) address += data.address.country;
                                }
                                locationText.textContent = address || 'Location captured (lat: ' + currentLocation.latitude.toFixed(6) + ', lng: ' + currentLocation.longitude.toFixed(6) + ')';
                            })
                            .catch(() => {
                                locationText.textContent = 'Location captured (lat: ' + currentLocation.latitude.toFixed(6) + ', lng: ' + currentLocation.longitude.toFixed(6) + ')';
                            });
                    },
                    (error) => {
                        console.error("Geolocation error: ", error);
                        locationText.textContent = 'Unable to retrieve location. Please enable location services.';
                        latitudeInput.value = '';
                        longitudeInput.value = '';
                    },
                    { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
                );
            } else {
                locationText.textContent = 'Geolocation is not supported by this browser.';
            }
        }
        
        // Submit attendance
        submitBtn.addEventListener('click', () => {
            if (!capturedPhoto) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Please capture or upload a photo first.',
                });
                return;
            }
            
            if (!currentLocation && !latitudeInput.value) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Location Missing',
                    text: 'We could not get your location. Attendance will be recorded without location data.',
                });
            }
            
            Swal.fire({
                title: 'Submit Attendance?',
                text: 'Are you sure you want to submit your attendance with this photo?',
                icon: 'question',
                showCancelButton: true,
                            timer: 3000,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, submit!'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Create FormData to send to server
                    const formData = new FormData();
                    formData.append('employee_id', '<?php echo $employee['employee_id']; ?>');
                    formData.append('emp_name', '<?php echo $employee['name']; ?>');
                    formData.append('session_email', '<?php echo $email; ?>');
                    formData.append('latitude', latitudeInput.value);
                    formData.append('longitude', longitudeInput.value);
                    
                    // Convert data URL to blob for file upload
                    if (capturedPhoto.startsWith('data:')) {
                        const blob = dataURItoBlob(capturedPhoto);
                        formData.append('user_photo', blob, 'attendance_photo.png');
                    } else {
                        // This handles the case when uploaded from device
                        formData.append('user_photo', fileInput.files[0]);
                    }
                    
                    // Send to server
                    $.ajax({
                        url: 'submit_attendance.php',
                        type: 'POST',
                        data: formData,
                        contentType: false,
                        processData: false,
                        success: function(response) {
                            const res = JSON.parse(response);
                            if (res.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Success!',
                                    text: 'Your attendance has been recorded.',
                            timer: 3000

                                }).then(() => {
                                    // window.location.reload();
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                            timer: 3000,
                                    
                                    text: res.message || 'Failed to record attendance.',
                                });
                            }
                        },
                        error: function() {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                            timer: 3000,
                                text: 'Failed to connect to server.',
                            });
                        }
                    });
                }
            });
        });
        
        // Helper function to convert data URI to Blob
        function dataURItoBlob(dataURI) {
            const byteString = atob(dataURI.split(',')[1]);
            const mimeString = dataURI.split(',')[0].split(':')[1].split(';')[0];
            const ab = new ArrayBuffer(byteString.length);
            const ia = new Uint8Array(ab);
            for (let i = 0; i < byteString.length; i++) {
                ia[i] = byteString.charCodeAt(i);
            }
            return new Blob([ab], { type: mimeString });
        }
    </script>

</body>
</html>