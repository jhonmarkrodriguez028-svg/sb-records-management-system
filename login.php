<?php
session_start();
include "config.php";

// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$message = "";

/* ================= LOGIN ================= */
$email_value = "";

if(isset($_POST['login'])){
    $email_value = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    $query = mysqli_query($conn, "SELECT * FROM users WHERE email='$email_value'");

    if($query && mysqli_num_rows($query) > 0){
        $user = mysqli_fetch_assoc($query);
        if(password_verify($password, $user['password'])){
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['fullname'] = $user['fullname'];
            $_SESSION['role'] = $user['role'];
            header("Location: index.php");
            exit();
        } else {
            $message = "<div class='alert alert-danger'>Incorrect password!</div>";
        }
    } else {
        $message = "<div class='alert alert-danger'>Email not found!</div>";
    }
}

/* ================= SIGNUP ================= */
if(isset($_POST['signup'])){
    $fullname = mysqli_real_escape_string($conn, $_POST['fullname']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $check = mysqli_query($conn, "SELECT * FROM users WHERE email='$email'");

    if($check && mysqli_num_rows($check) > 0){
        $message = "<div class='alert alert-danger'>Email already exists!</div>";
    } else {
        mysqli_query($conn, "INSERT INTO users (fullname,email,password) VALUES ('$fullname','$email','$password')");
        $message = "<div class='alert alert-success'>Account created! You can now login.</div>";
    }
}

/* ================= FORGOT PASSWORD ================= */
if(isset($_POST['forgot'])){
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $new_password = $_POST['new_password'];

    $query = mysqli_query($conn, "SELECT * FROM users WHERE email='$email'");

    if($query && mysqli_num_rows($query) > 0){
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        mysqli_query($conn, "UPDATE users SET password='$hashed_password' WHERE email='$email'");
        $message = "<div class='alert alert-success'>Password has been successfully updated. You can now login.</div>";
    } else {
        $message = "<div class='alert alert-danger'>Email not found!</div>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>SB Records Management System - Login</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
html, body {
    height: 100%;
    margin: 0;
    overflow: hidden; /* disable scrolling */
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background-color: #f4f6f9;
}

/* Government Header */
.gov-header {
    background-color: #0b3d91;
    color: white;
    padding: 20px 0;
    text-align: center;
}

.gov-header h1 {
    font-family: 'Times New Roman', serif;
    font-weight: bold;
    margin: 0;
    letter-spacing: 1px;
}

/* Full height row */
.full-height-row {
    height: calc(100% - 80px); /* subtract header height */
}

/* Left Section */
.left-section {
    display: flex;
    flex-direction: column;
    justify-content: center;
    padding: 60px;
}

.left-section h2 {
    font-weight: 600;
    color: #0b3d91;
}

/* Card */
.login-card {
    background: white;
    border-radius: 8px;
    border: 1px solid #dcdcdc;
    padding: 35px;
    width: 100%;
    max-width: 420px;
    min-height: 420px; /* fixed height for all forms */
    display: flex;
    flex-direction: column;
    justify-content: center;
}

/* Inputs */
.form-control {
    height: 42px;
    border-radius: 6px;
}

/* Buttons */
.btn-primary-custom {
    background-color: #0b3d91;
    border: none;
    color: white;
}

.btn-primary-custom:hover {
    background-color: #072c66;
    color: white;
}

/* Password toggle */
.togglePassword {
    position: absolute;
    right: 10px;
    top: 50%;
    transform: translateY(-50%);
    cursor: pointer;
    color: #555;
}

/* Forms */
.form-box {
    display: none;
}

#loginForm {
    display: block;
}
</style>
</head>

<body>

<!-- HEADER -->
<div class="gov-header">
    <h1>SANGGUNIANG BAYAN</h1>
    <small>Records Management System</small>
</div>

<div class="container-fluid h-100">
    <div class="row full-height-row">

        <!-- LEFT SIDE -->
        <div class="col-md-7 left-section">
            <div class="d-flex align-items-center justify-content-center gap-3">
            <img src="images/logo.png" alt="Logo" style="height:30vh;">
            <div>
            <h2>Official Records Portal</h2>
              <p class="text-muted">
                This system is intended for authorized personnel only. 
                Please login using your official account credentials.
            </p>
            </div>
            </div> 
          
        </div>

        <!-- RIGHT SIDE -->
        <div class="col-md-5 d-flex align-items-center justify-content-center">
              <div class="login-card shadow-sm">
                <img src="images/SB.png" alt="Logo" style="height:15vh; width: 25%; align-self: center;">
                <h4 class="text-center mb-4 fw-semibold">Secure System Access</h4>

                <?php echo $message; ?>

                <!-- LOGIN FORM -->
                <div id="loginForm" class="form-box">
                    <form method="POST">
                        <input type="email" name="email" class="form-control mb-2"
                               placeholder="Email Address"
                               value="<?php echo htmlspecialchars($email_value); ?>" required>
                        <div class="position-relative mb-2">
                            <input type="password" name="password" id="loginPassword" class="form-control"
                                   placeholder="Password" required>
                            <span class="togglePassword" data-target="loginPassword">👁</span>
                        </div>
                        <button name="login" class="btn btn-primary-custom w-100">Login</button>
                    </form>
                    <div class="text-center mt-3">
                        <a href="#" onclick="showSignup()">Create Account</a> |
                        <a href="#" onclick="showForgot()">Forgot Password?</a>
                    </div>
                </div>

                <!-- SIGNUP FORM -->
                <div id="signupForm" class="form-box">
                    <form method="POST">
                        <input type="text" name="fullname" class="form-control mb-3" placeholder="Full Name" required>
                        <input type="email" name="email" class="form-control mb-3" placeholder="Email Address" required>
                        <div class="position-relative mb-3">
                            <input type="password" name="password" id="signupPassword" class="form-control"
                                   placeholder="Password" required>
                            <span class="togglePassword" data-target="signupPassword">👁</span>
                        </div>
                        <button name="signup" class="btn btn-primary-custom w-100">Register</button>
                    </form>
                    <div class="text-center mt-3">
                        <a href="#" onclick="showLogin()">Back to Login</a>
                    </div>
                </div>

                <!-- FORGOT PASSWORD FORM -->
                <div id="forgotForm" class="form-box">
                    <form method="POST">
                        <input type="email" name="email" class="form-control mb-3" placeholder="Email Address" required>
                        <div class="position-relative mb-3">
                            <input type="password" name="new_password" id="forgotPassword" class="form-control"
                                   placeholder="New Password" required>
                            <span class="togglePassword" data-target="forgotPassword">👁</span>
                        </div>
                        <button name="forgot" class="btn btn-primary-custom w-100">Reset Password</button>
                    </form>
                    <div class="text-center mt-3">
                        <a href="#" onclick="showLogin()">Back to Login</a>
                    </div>
                </div>

            </div>

        </div>
    </div>
</div>

<script>
const loginForm = document.getElementById("loginForm");
const signupForm = document.getElementById("signupForm");
const forgotForm = document.getElementById("forgotForm");

function showSignup(){
    loginForm.style.display="none";
    forgotForm.style.display="none";
    signupForm.style.display="block";
}

function showForgot(){
    loginForm.style.display="none";
    signupForm.style.display="none";
    forgotForm.style.display="block";
}

function showLogin(){
    signupForm.style.display="none";
    forgotForm.style.display="none";
    loginForm.style.display="block";
}

// Password toggle
const toggles = document.querySelectorAll('.togglePassword');
toggles.forEach(toggle => {
    const input = document.getElementById(toggle.dataset.target);
    if(!input) return;

    toggle.addEventListener('mouseenter', () => input.type = 'text');
    toggle.addEventListener('mouseleave', () => input.type = 'password');
});
</script>

</body>
</html>