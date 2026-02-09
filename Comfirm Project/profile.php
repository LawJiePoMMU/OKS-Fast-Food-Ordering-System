<?php
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

session_start();
include "database.php";
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$u_id = $_SESSION['user_id'];

if (isset($_POST['update_profile'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);

    $mobile_input = mysqli_real_escape_string($conn, $_POST['mobile']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    if (!ctype_digit($mobile_input)) {
        echo "Error: Mobile number must contain only digits.";
        exit();
    }
    $final_mobile = '0' . $mobile_input;

    $sql_update = "UPDATE users SET username='$username', user_mobile='$final_mobile', user_address='$address' WHERE user_id='$u_id'";

    if (mysqli_query($conn, $sql_update)) {
        $_SESSION['username'] = $username;
        echo "success";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
    exit();
}

$sql_user = "SELECT * FROM users WHERE user_id='$u_id'";
$res_user = mysqli_query($conn, $sql_user);
$user = mysqli_fetch_assoc($res_user);
$db_mobile = $user['user_mobile'];
$display_mobile = preg_replace('/^(\+?60|0)/', '', $db_mobile);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - OKS Fast Food</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="css folder/profile.css">
</head>

<body>

    <?php include 'header.php'; ?>

    <div class="container">
        <div class="profile-container">
            <div class="profile-header">
                <div class="profile-avatar">
                    <i class="fas fa-user"></i>
                </div>
                <h2 class="profile-title"><?php echo htmlspecialchars($user['username']); ?></h2>
                <p class="profile-subtitle">Manage your account information</p>
            </div>

            <div class="profile-body">
                <div id="profileAlert" class="alert" role="alert" style="display: none;"></div>

                <form id="profileForm">
                    <div class="row g-3">

                        <div class="col-md-6">
                            <label class="form-label">Full Name</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="fas fa-user"></i></span>
                                <input type="text" class="form-control" name="username"
                                    value="<?php echo htmlspecialchars($user['username']); ?>" required>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Email Address <small class="text-danger">(Not
                                    editable)</small></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="fas fa-envelope"></i></span>
                                <input type="email" class="form-control" name="email"
                                    value="<?php echo htmlspecialchars($user['user_email']); ?>" readonly>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Mobile Number</label>
                            <div class="input-group">
                                <span class="input-group-text bg-secondary text-white fw-bold"
                                    style="user-select: none;">+60</span>

                                <input type="text" class="form-control" name="mobile"
                                    value="<?php echo htmlspecialchars($display_mobile); ?>" required maxlength="10"
                                    minlength="9" placeholder="123456789"
                                    oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 10)">
                            </div>
                            <small class="text-muted" style="font-size: 0.8rem;">* Enter 9-10 digits (e.g.
                                123456789)</small>
                        </div>

                        <div class="col-12">
                            <label class="form-label">Delivery Address</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="fas fa-map-marker-alt"></i></span>
                                <textarea class="form-control" name="address" rows="3"
                                    required><?php echo htmlspecialchars($user['user_address']); ?></textarea>
                            </div>
                        </div>

                        <div class="col-12">
                            <button type="submit" class="btn btn-save">
                                <i class="fas fa-save me-2"></i>Save Changes
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <?php include 'footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="java script folder/profile.js"></script>
</body>


</html>
