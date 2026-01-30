<?php
session_start();
include "database.php";

if (!isset($_GET['order_id'])) {
    header("Location: ../menu.php");
    exit();
}

$order_id = $_GET['order_id'];
$u_id = $_SESSION['user_id'];
if (isset($_POST['confirm_payment'])) {
    mysqli_query($conn, "UPDATE `order` SET order_status='Pending' WHERE order_id='$order_id'");
    mysqli_query($conn, "DELETE FROM cart WHERE user_id='$u_id'");

    echo "<script>alert('Thank you! We will verify your payment shortly.'); window.location.href='my_orders.php';</script>";
    exit();
}

$sql = "SELECT order_total_price FROM `order` WHERE order_id = '$order_id'";
$result = mysqli_query($conn, $sql);
$order = mysqli_fetch_assoc($result);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Payment - OKS Fast Food</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css folder/checkout.css">
</head>
<body>
    <div class="container text-center mt-5">
        <div class="card shadow p-5 mx-auto" style="max-width: 500px; border-radius: 15px;">
            <h2 class="fw-bold mb-4">Payment</h2>
            <p class="text-muted">Order ID: #<?php echo $order_id; ?></p>
            <h3 class="text-danger fw-bold mb-4">RM <?php echo number_format($order['order_total_price'], 2); ?></h3>
            
            <div class="mb-4 p-3 border rounded bg-light">
                <h6>Scan to Pay (DuitNow QR)</h6>
                <img src="../image/TNG.jpeg" alt="QR Code" class="img-fluid my-2" style="width: 300px;">
                <p class="small text-muted mt-2">Please upload receipt after payment (optional) or notify us.</p>
            </div>

            <form method="POST" style="display: block;">
                <button type="submit" name="confirm_payment" class="btn btn-danger w-100 fw-bold py-3 rounded-pill">I Have Paid</button>
            </form>

            <a href="cart.php" class="btn btn-link text-muted mt-3">Back to Cart</a>
        </div>
    </div>
</body>
</html>