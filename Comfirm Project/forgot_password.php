<?php
session_start();
include "database.php";

$message = "";
$error = "";

if (isset($_POST['reset_password'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $mobile_input = mysqli_real_escape_string($conn, $_POST['mobile']);
    $mobile = "+60" . $mobile_input;
    $new_password = $_POST['new_password'];
    $check = mysqli_query($conn, "SELECT user_id FROM users WHERE user_email = '$email' AND user_mobile = '$mobile'");

    if (mysqli_num_rows($check) > 0) {
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $sql_update = "UPDATE users SET user_password = '$hashed_password' WHERE user_email = '$email'";

        if (mysqli_query($conn, $sql_update)) {
            $message = "Password reset successfully!";
        } else {
            $error = "Database error. Please try again.";
        }
    } else {
        $error = "Invalid Email Address or Mobile Number. Records do not match.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Forgot Password - OKS Fast Food</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css folder/forgot_password.css">
</head>

<body>
    <div class="forgot-container">
        <h3 class="text-center fw-bold mb-4">Reset Password</h3>

        <form method="POST" id="forgotForm">
            <div class="mb-3">
                <label class="form-label fw-bold">Email Address</label>
                <input type="email" name="email" class="form-control" required placeholder="example@gmail.com">
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">Mobile Number</label>
                <div class="mobile-group">
                    <span class="mobile-prefix">+60</span>
                    <input type="text" name="mobile" class="mobile-input" required placeholder="123456789">
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label fw-bold">New Password</label>
                <input type="password" name="new_password" class="form-control" required minlength="6">
            </div>

            <button type="submit" name="reset_password" class="btn-reset">Update Password</button>
            <div class="text-center mt-3">
                <a href="login.php" class="text-decoration-none text-muted small">Back to Login</a>
            </div>
        </form>
    </div>

    <input type="hidden" id="php_error" value="<?php echo $error; ?>">
    <input type="hidden" id="php_success" value="<?php echo $message; ?>">

    <script src="java script folder/forgot_password.js"></script>
</body>


</html>
