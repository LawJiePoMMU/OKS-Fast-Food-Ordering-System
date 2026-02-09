<?php
session_start();
include 'database.php';

if (isset($_POST['submit'])) {

    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];
    $sql = "SELECT * FROM users WHERE user_email = '$email'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if (password_verify($password, $row['user_password'])) {
            $_SESSION['user_id'] = $row['user_id'];
            $_SESSION['username'] = $row['username'];
            $_SESSION['role'] = $row['user_role'];

            if ($row['user_role'] == 'admin') {
                echo "<script>alert('Welcome Admin!'); window.location.href='Admin /admin_dashboard.php';</script>";
            } else {
                echo "<script>alert('Login Successful! Welcome back.'); window.location.href='index.php';</script>";
            }

        } else {
            echo "<script>alert('Invalid email or password');</script>";
        }
    } else {
        echo "<script>alert(' Email not found! Please register first.');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - OKS Fast Food</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css folder/login.css">
</head>

<body>
    <div class="full-screen-container">
        <div class="login-box">
            <a href="index.php" class="home-link-logo">OKS</a>

            <div class="welcome-text">Welcome back! Please login.</div>

            <form action="" method="POST">

                <div class="input-group">
                    <input type="email" name="email" class="oks-input" placeholder="Email@gmail.com" required>
                </div>

                <div class="input-group">
                    <input type="password" name="password" id="pass" class="oks-input" placeholder="Password" required>
                    <span class="eye-icon" onclick="togglePass('pass')">👁️</span>
                </div>

                <button type="submit" name="submit" class="btn-submit">LOG IN</button>

                <div class="register-link">
                    <a href="forgot_password.php" class="small text-danger text-decoration-none">Forgot Password?</a>
                    New to OKS? <a href="register.php">Create Account</a>
                </div>
            </form>
        </div>
    </div>

    <script src="java script folder/login.js"></script>

</body>


</html>
