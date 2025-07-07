<?php
include 'includes/connection.php';
include 'includes/header.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Login Page</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <style>
        body {
            background: rgb(247, 244, 243);
            font-family: 'Poppins', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
        }

        .login-card {
            background-color:   rgb(247, 244, 243);
            /* border-radius: 20px; */
            padding: 2rem;
            max-width: 900px;
            width: 100%;
            /* box-shadow: 0 10px 30px rgba(0,0,0,0.1); */
            display: flex;
            align-items: center;
            justify-content: space-between;
            animation: fadeIn 1s ease-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .login-form {
            flex: 1;
            padding-right: 2rem;
        }

        .login-form h2 {
            font-weight: 600;
            margin-bottom: 1.5rem;
        }

        .form-control {
            border-radius: 10px;
            padding: 0.75rem 1rem;
        }

        .form-control:focus {
            border-color: #1a2035;
            box-shadow: 0 0 5px #1a2035;
        }

        .login-btn {
            background: #1a2035;
            color: #fff;
            border: none;
            border-radius: 10px;
            padding: 0.75rem;
            width: 100%;
            transition: 0.3s;
        }

        .login-btn:hover {
            background: #1a2035;
        }

        .social-icons i {
            font-size: 1.5rem;
            margin: 0 10px;
            color: #555;
            transition: 0.3s;
            cursor: pointer;
        }

        .social-icons i:hover {
            color: #ff9a76;
        }

        .illustration {
            flex: 1;
            text-align: center;
            position: relative;
        }

        .bg-shape {
            position: absolute;
            top: 63%;
            left: 70%;
            transform: translate(-50%, -50%);
            width: 450px;
            height: 530px;
            background: #1a2035 !important;
            border-radius: 50% 50% 0% 0%;
            z-index: 1;
        }

        .illustration img {
            margin-top: 40%;
            position: relative;
            z-index: 2;
            max-width: 100%;
            max-height: 300px;
            animation: float 3s ease-in-out infinite;
        }

        @keyframes float {

            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-10px);
            }
        }

        @media(max-width: 768px) {
            .login-card {
                flex-direction: column;
                padding: 1rem;
            }

            .illustration {
                order: -1;
                /* move illustration on top of form */
                margin-bottom: 1rem;
            }

            .login-form {
                padding-right: 0;
                width: 100%;
            }

            .bg-shape {
                position: absolute;
                top: -60%;
                left: 50%;
                transform: translate(-50%, -50%);
                width: 390px;
                height: 293px;
                border-radius: 0% 0% 50% 50%;
            }

            .illustration img {
                max-height: 260px;
                margin-top: -57%;
            }

        }

        .textOnInput {
            position: relative;
        }

        .textOnInput input {
            padding: 1rem 0.75rem 0.25rem 0.75rem;
            /* give space for label */
            background-color: #e8e4e3;
            border: #1a2035;
            border-radius: 20px 20px 20px 20px;
                line-height: 2.5;
        }
        .from-control{
            height:60%;
        }

        .textOnInput label {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            background: #f7f4f3;
            padding: 0 4px;
            color: #aaa;
            font-size: 1rem;
            transition: 0.2s ease all;
            opacity: 0;
            pointer-events: none;
        }

        .textOnInput input:focus+label,
        .textOnInput input:not(:placeholder-shown)+label {
            top: 0;
            transform: translateY(-50%) scale(0.85);
            opacity: 1;
            color: #ff9a76;
            font-size: 0.8rem;

        }

        .form-control {
            box-shadow: none !important;
        }
    </style>
</head>

<body>
    <div class="login-card">
        <div class="login-form">
            <!-- <img src="assets/img/crawlers/Crawlers_ai_logo.png" alt="sadasdasd" style="width: 50%;"> -->


            <form>
                <div class="mb-5">
                    <div class="input-icon textOnInput">
                        <span class="input-icon-addon">
                            <i class="fa fa-user"></i>
                        </span>
                        <input class="form-control" type="text" name="email" id="email" placeholder="Email " required>
                        <label for="email">Email</label>
                    </div>
                </div>

                <div class="mb-3">
                    <div class="input-icon textOnInput">
                        <span class="input-icon-addon">
                            <i class="fa fa-lock"></i>
                        </span>
                        <input class="form-control" type="password" name="password" id="password" placeholder="password " required>
                        <label for="password">Password</label>
                    </div>
                </div>

                <div class="mb-3 text-end">
                    <a href="#" style="font-size: 0.9rem; color: #1a2035;">Forgot Password?</a>
                </div>
                <button class="login-btn" type="button" onclick="validateForm1()">Login</button>
            </form>
            <div class="text-center my-3">- or -</div>
            <div class="social-icons text-center">
                <i class="fab fa-google"></i>
                <i class="fab fa-facebook"></i>
                <i class="fab fa-apple"></i>
            </div>
            <div class="text-center mt-3" style="font-size: 0.9rem;">
                Don’t have an account? <a href="#" style="color:#1a2035;">Sign up</a>
            </div>
        </div>
        <div class="illustration">
            <div class="bg-shape"></div>
            <img src="Sky_Associate-removebg-preview.png" alt="3D Illustration" class="img-fluid">
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        document.getElementById("togglePassword").addEventListener("click", function() {
            const passwordField = document.getElementById("password");
            const type = passwordField.type === "password" ? "text" : "password";
            passwordField.type = type;

            // Toggle icon class
            this.classList.toggle("fa-eye-slash");
        });


        function validateForm1() {
            $.ajax({
                url: "login.php",
                type: "post",
                data: {
                    login: true,
                    email: $('#email').val(),
                    password: $('#password').val()
                },
                success: function(response) {
                    if (response == 1) {
                        // Redirect admin to dashboard with SweetAlert
                        swal({
                            title: "Login Successful!",
                            text: "Login to hr dashboard",
                            icon: "success",
                            button: false,
                            timer: 1500
                        }).then(() => {
                            window.location.href = "dashboard1.php";
                        });
                    } else if (response == 5) {
                        // Redirect user to table page with SweetAlert
                        swal({
                            title: "admin Login Successful!",
                            text: "Login to admin dashboard",
                            icon: "success",
                            button: false,
                            timer: 1500
                        }).then(() => {
                            window.location.href = "dashboard1.php";
                        });
                    } else if (response == 2) {
                        // Redirect user to table page with SweetAlert
                        swal({
                            title: "Login Successful!",
                            text: "Login to employee dashboard",
                            icon: "success",
                            button: false,
                            timer: 1500
                        }).then(() => {
                            window.location.href = "dashboard1.php";
                        });
                    } else if (response == 7) {
                        swal({
                            title: "Login Successful!",
                            text: "Login to client dashboard",
                            icon: "success",
                            button: false,
                            timer: 1500
                        }).then(() => {
                            window.location.href = "client_dashboard.php";
                        });

                    } else {

                        // Wrong credentials with SweetAlert
                        swal({
                            title: "Login Failed!",
                            text: "Incorrect email or password. Please try again.",
                            icon: "error",
                            button: "Retry"
                        });
                    }
                }
            });
        }
    </script>
</body>

</html>