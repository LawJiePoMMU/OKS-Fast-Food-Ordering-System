<?php
include 'database.php';
if (isset($_POST['submit'])) {
    $first_name = mysqli_real_escape_string($conn, $_POST['first_name']);
    $last_name = mysqli_real_escape_string($conn, $_POST['last_name']);
    $full_name = $first_name . " " . $last_name;
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    
    $mobile_input = mysqli_real_escape_string($conn, $_POST['mobile']);
    $mobile_input = ltrim($mobile_input, '0'); 
    if (substr($mobile_input, 0, 2) == "60") { $mobile_input = substr($mobile_input, 2); } 
    $mobile = "+60" . $mobile_input;
 

    $password = $_POST['password'];
    $confirm_pw = $_POST['confirm_password'];
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $birthday = $_POST['birthday'];
    $role = 'customer';

    if ($password !== $confirm_pw) {
        echo "<script> alert ('Passwords do not match!'); window.history.back(); </script>";
        exit();
    }

    $check_email = "SELECT * FROM users WHERE user_email='$email'";
    $result = $conn->query($check_email);
    if ($result->num_rows > 0) {
        echo "<script>alert('Email already registered! Please Log In.'); window.location='login.php';</script>";
        exit();
    }

    if ($birthday > date('Y-m-d')) {
        echo "<script> alert ('Invalid birthday! You cannot be born in the future.'); window.history.back(); </script>";
        exit();
    }

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $sql = "INSERT INTO users (username, user_email, user_password, user_mobile, user_address, user_birthday, user_role) 
            VALUES ('$full_name', '$email', '$hashed_password', '$mobile', '$address', '$birthday', '$role')";
            
    if ($conn->query($sql) === TRUE) {
        echo "<script>
                alert('Account Created Successfully! Welcome to OKS fastfood.');
                window.location.href = 'login.php';
              </script>";
    } else {
        $error_msg = addslashes($conn->error);
        echo "<script>alert('Error: $error_msg'); window.history.back();</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Account OKS Fast Food</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css folder/register.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
</head>

<body>
    <div class="full-screen-container">
        <div class="register-box">
            <a href="index.php" class="home-link-logo">OKS</a>
            <div class="reg-title">
                <h1>Create Account</h1>
                <p>Join us for exclusive deals & rewards!</p>
            </div>

            <form action="" method="POST">
                <div class="row-split">
                    <div class="col-half input-group">
                        <input type="text" name="first_name" class="oks-input" placeholder="First Name" required>
                    </div>
                    <div class="col-half input-group">
                        <input type="text" name="last_name" class="oks-input" placeholder="Last Name" required>
                    </div>
                </div>

                <div class="mobile-container">
                    <span class="prefix">+60</span>
                    <input type="text" name="mobile" class="mobile-input" placeholder="123456789" required
                        oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 10)">
                </div>

                <div class="input-group">
                    <input type="email" name="email" class="oks-input" placeholder="Email@gmail.com" required>
                </div>

                <div class="input-group">
                    <input type="password" name="password" id="pass" class="oks-input" placeholder="Password" required
                        minlength="6">
                    <span class="eye-icon" onclick="togglePass('pass')">👁️</span>
                </div>

                <div class="input-group">
                    <input type="password" name="confirm_password" id="confirm_pass" class="oks-input"
                        placeholder="Confirm Password" required>
                    <span class="eye-icon" onclick="togglePass('confirm_pass')">👁️</span>
                </div>

                <div class="input-group" style="text-align: left; margin-bottom: 20px;">
                    <label class="form-label fw-bold" style="font-size:14px; color:#555; font-weight:bold;">Detailed Address</label>
                    <textarea name="address" class="oks-input" rows="3"
                        style="height: auto; padding: 10px; line-height: 1.5;"
                        placeholder="e.g. No. 88, Jalan Pauh 1, Taman Pang" required></textarea>
                    <div class="form-text text-muted" style="font-size: 11px; color: #999; margin-top: 4px;">
                        <i class="bi bi-info-circle"></i> Please enter your house number, street name, and housing area.
                    </div>
                </div>
                <div class="input-group" style="text-align: left;">
                    <label style="font-size:12px; color:#999; display:block; margin-bottom:5px;">Date of Birth</label>
                    <input type="date" name="birthday" id="birthdayInput" class="oks-input" required>
                </div>

                <button type="submit" name="submit" class="btn-submit">SIGN UP NOW</button>

                <div class="login-link">
                    Already have an account? <a href="login.php">Log In here</a>
                </div>
            </form>
        </div>
    </div>

    <script src="java script folder/register.js"></script>
</body>

</html>