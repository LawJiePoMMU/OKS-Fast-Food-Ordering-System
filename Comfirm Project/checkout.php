<?php
session_start();
include "database.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$u_id = $_SESSION['user_id'];
$check_cart = mysqli_query($conn, "SELECT * FROM cart WHERE user_id='$u_id'");
if (mysqli_num_rows($check_cart) == 0) {
    echo "<script>alert('Your cart is empty!'); window.location.href='../menu.php';</script>";
    exit();
}

$sql_user = "SELECT user_address, username, user_mobile FROM users WHERE user_id='$u_id'";
$res_user = mysqli_query($conn, $sql_user);
$user_row = mysqli_fetch_assoc($res_user);
$db_address = $user_row['user_address'];

if (isset($_POST['place_order'])) {
    $raw_address = mysqli_real_escape_string($conn, $_POST['address']);
    $city = mysqli_real_escape_string($conn, $_POST['city']);
    $state = mysqli_real_escape_string($conn, $_POST['state']);
    $final_address = $raw_address . ", " . $city . ", " . $state;

    $payment = mysqli_real_escape_string($conn, $_POST['payment_method']);
    $grand_total = $_POST['grand_total'];

    $status = ($payment == 'Online Banking') ? 'Awaiting Payment' : 'Pending';

    $sql_order = "INSERT INTO `order` (user_id, order_total_price, order_status, order_payment_method, order_delivery_address) 
                  VALUES ('$u_id', '$grand_total', '$status', '$payment', '$final_address')";

    if (mysqli_query($conn, $sql_order)) {
        $new_order_id = mysqli_insert_id($conn);

        $cart_items = mysqli_query($conn, "SELECT c.*, p.product_price FROM cart c JOIN products p ON c.product_id = p.product_id WHERE user_id='$u_id'");
        while ($item = mysqli_fetch_assoc($cart_items)) {
            $p_id = $item['product_id'];
            $qty = $item['cart_quantity'];
            $price = $item['product_price'];
            mysqli_query($conn, "INSERT INTO order_items (order_id, product_id, order_items_quantity, order_items_price) 
                                 VALUES ('$new_order_id', '$p_id', '$qty', '$price')");
        }

        if ($payment != 'Online Banking') {
            mysqli_query($conn, "DELETE FROM cart WHERE user_id='$u_id'");
        }

        if ($payment == 'Online Banking') {
            header("Location: payment.php?order_id=" . $new_order_id);
        } else {
            echo "<script>alert('Order Placed Successfully!'); window.location.href='my_orders.php';</script>";
        }
        exit();

    } else {
        echo "Error: " . mysqli_error($conn);
    }
}

$cart_query = mysqli_query($conn, "SELECT c.cart_quantity, p.product_price FROM cart c JOIN products p ON c.product_id = p.product_id WHERE user_id='$u_id'");
$grand_total_display = 0;
while ($c = mysqli_fetch_assoc($cart_query)) {
    $grand_total_display += ($c['product_price'] * $c['cart_quantity']);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - OKS Fast Food</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="css folder/checkout.css">
</head>

<body>

    <?php include 'header.php'; ?>

    <div class="container">
        <div class="checkout-container">
            <h2 class="fw-bold mb-4 text-center">Checkout</h2>

            <form action="" method="POST">
                <input type="hidden" id="db_address_hidden" value="<?php echo htmlspecialchars($db_address); ?>">

                <div class="mb-5">
                    <h4 class="section-title"><i class="fas fa-map-marker-alt text-danger me-2"></i>Delivery Details
                    </h4>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Name</label>
                            <input type="text" class="form-control"
                                value="<?php echo htmlspecialchars($user_row['username']); ?>" readonly
                                style="background-color: #e9ecef;">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Phone Number</label>
                            <input type="text" class="form-control"
                                value="<?php echo htmlspecialchars($user_row['user_mobile']); ?>" readonly
                                style="background-color: #e9ecef;">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Delivery Address</label>

                        <?php if (!empty($db_address)): ?>
                            <div class="address-checkbox-card">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="useRegAddress">
                                    <label class="form-check-label fw-bold" for="useRegAddress">
                                        Use my registered address
                                    </label>
                                    <div class="text-muted small ps-1 mt-1">
                                        <i class="fas fa-home me-1"></i> <?php echo htmlspecialchars($db_address); ?>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Detailed Address</label>
                            <textarea name="address" id="addrInput" class="form-control mb-1" rows="3"
                                placeholder="e.g. No. 88, Jalan Pauh 1, Taman Pang" required></textarea>
                            <div class="form-text text-muted small">
                                <i class="bi bi-info-circle"></i> Please enter your house number, street name, and
                                housing area.
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">State</label>
                                <div class="select-wrapper">
                                    <select name="state" id="stateSelect" class="form-select form-select-scroll"
                                        required>
                                        <option value="" disabled selected>Select State</option>
                                        <option value="Johor">Johor</option>
                                        <option value="Kedah">Kedah</option>
                                        <option value="Kelantan">Kelantan</option>
                                        <option value="Melaka">Melaka</option>
                                        <option value="Negeri Sembilan">Negeri Sembilan</option>
                                        <option value="Pahang">Pahang</option>
                                        <option value="Penang">Penang</option>
                                        <option value="Perak">Perak</option>
                                        <option value="Perlis">Perlis</option>
                                        <option value="Sabah">Sabah</option>
                                        <option value="Sarawak">Sarawak</option>
                                        <option value="Selangor">Selangor</option>
                                        <option value="Terengganu">Terengganu</option>
                                        <option value="Kuala Lumpur">Kuala Lumpur</option>
                                        <option value="Putrajaya">Putrajaya</option>
                                        <option value="Labuan">Labuan</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">City</label>
                                <div class="select-wrapper">
                                    <select name="city" id="citySelect" class="form-select form-select-scroll" required
                                        disabled>
                                        <option value="" disabled selected>Select State First</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mb-5">
                    <h4 class="section-title"><i class="fas fa-credit-card text-danger me-2"></i>Payment Method</h4>

                    <div class="form-check mb-3 p-3 border rounded">
                        <input class="form-check-input" type="radio" name="payment_method" id="pay1"
                            value="Cash On Delivery" checked>
                        <label class="form-check-label fw-bold d-flex align-items-center" for="pay1">
                            <i class="fas fa-money-bill-wave text-success fs-4 me-3"></i>
                            Cash On Delivery (COD)
                        </label>
                    </div>

                    <div class="form-check mb-3 p-3 border rounded">
                        <input class="form-check-input" type="radio" name="payment_method" id="pay2"
                            value="Online Banking">
                        <label class="form-check-label fw-bold d-flex align-items-center" for="pay2">
                            <i class="fas fa-university text-primary fs-4 me-3"></i>
                            QR Pay
                        </label>
                    </div>
                </div>

                <div class="summary-box mb-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="fs-5 text-muted">Total Amount to Pay:</span>
                        <span class="fs-2 fw-bold text-danger">RM
                            <?php echo number_format($grand_total_display, 2); ?></span>
                    </div>
                    <input type="hidden" name="grand_total" value="<?php echo $grand_total_display; ?>">
                </div>

                <button type="submit" name="place_order" class="btn-place-order">
                    Place Order Now
                </button>
            </form>
        </div>
    </div>

    <?php include 'footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="java script folder/checkout.js"></script>
</body>

</html>