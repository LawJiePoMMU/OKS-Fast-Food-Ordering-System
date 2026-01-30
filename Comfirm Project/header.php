<?php
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'database.php';

$current_page = basename($_SERVER['PHP_SELF']);
$cart_count = 0;
if (isset($_SESSION['user_id'])) {
    $uid = $_SESSION['user_id'];
    $cart_sql = "SELECT SUM(cart_quantity) as total FROM cart WHERE user_id = '$uid'";
    $cart_res = mysqli_query($conn, $cart_sql);
    if ($cart_res) {
        $row = mysqli_fetch_assoc($cart_res);
        $cart_count = $row['total'] ?? 0;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OKS Fast Food</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="css folder/header.css">
</head>

<body>

    <header class="main-header">
        <a href="index.php" class="logo">OKS</a>

        <nav class="nav-links">
            <a href="index.php"
                class="nav-item <?php echo ($current_page == 'index.php') ? 'active' : ''; ?>">Home</a>
                <a href="menu.php" class="nav-item <?php echo ($current_page == 'menu.php') ? 'active' : ''; ?>">Menu</a>
                
                <a href="about us.php" class="nav-item <?php echo ($current_page == 'about us.php') ? 'active' : ''; ?>">About Us</a>

                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="my_orders.php"
                    class="nav-item <?php echo ($current_page == 'my_orders.php') ? 'active' : ''; ?>">My Orders</a>
                    
            <?php endif; ?>
        </nav>

        <div class="auth-actions">

            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="cart.php" class="cart-wrapper" title="View Cart">
                    <i class="fas fa-shopping-cart fa-lg"></i>
                    <?php if ($cart_count > 0): ?>
                        <span class="cart-badge"><?php echo $cart_count; ?></span>
                    <?php endif; ?>
                </a>

                <div class="user-menu">
                    <i class="fas fa-user-circle fa-lg" style="color: #D50032;"></i>
                    <span><?php echo htmlspecialchars($_SESSION['username']); ?></span>
                    <i class="fas fa-chevron-down"></i>

                    <div class="dropdown-content">
                        <a href="profile.php"><i class="fas fa-user"></i> My Profile</a>
                        <a href="orders_history.php"><i class="fas fa-receipt"></i> Order History</a>
                        <hr style="margin:5px 0; border:0; border-top:1px solid #eee;">
                        <a href="logout.php" style="color: #D50032;"><i class="fas fa-sign-out-alt"></i> Logout</a>
                    </div>
                </div>

            <?php else: ?>
                <a href="login.php" class="btn-login-only">Log In</a>
            <?php endif; ?>

        </div>
    </header>

    <script src="java script folder/header.js"></script>