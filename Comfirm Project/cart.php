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

if (isset($_POST['action']) && $_POST['action'] == 'remove') {
    $cart_id = $_POST['cart_id'];
    mysqli_query($conn, "DELETE FROM cart WHERE cart_id='$cart_id' AND user_id='$u_id'");
    echo "success";
    exit();
}

if (isset($_POST['action']) && $_POST['action'] == 'update_qty') {
    $cart_id = $_POST['cart_id'];
    $qty = intval($_POST['quantity']);

    if ($qty >= 1) {
        mysqli_query($conn, "UPDATE cart SET cart_quantity='$qty' WHERE cart_id='$cart_id' AND user_id='$u_id'");
        echo "success";
    }
    exit();
}

$sql_cart = "SELECT c.cart_id, c.cart_quantity, p.product_name, p.product_price, p.product_image, p.product_id 
             FROM cart c 
             JOIN products p ON c.product_id = p.product_id 
             WHERE c.user_id = '$u_id'";
$result_cart = mysqli_query($conn, $sql_cart);

$grand_total = 0;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OKS Fast Food</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="css folder/cart.css">
</head>

<body>

    <?php include 'header.php'; ?>

    <div class="container">
        <div class="cart-container">
            <div class="cart-header d-flex justify-content-between align-items-center">
                <h2 class="fw-bold m-0"><i class="fas fa-shopping-cart me-2"></i>My Shopping Cart</h2>
                <span class="text-muted"><?php echo mysqli_num_rows($result_cart); ?> Items</span>
            </div>

            <?php if (mysqli_num_rows($result_cart) > 0): ?>
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead class="text-secondary small">
                            <tr>
                                <th style="width: 40%">Product</th>
                                <th style="width: 15%">Price</th>
                                <th style="width: 20%">Quantity</th>
                                <th style="width: 15%">Subtotal</th>
                                <th style="width: 10%">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = mysqli_fetch_assoc($result_cart)):
                                $img = !empty($row['product_image']) ? "uploads/" . $row['product_image'] : "https://via.placeholder.com/80?text=No+Img";
                                $subtotal = $row['product_price'] * $row['cart_quantity'];
                                $grand_total += $subtotal;
                                ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="<?php echo $img; ?>" class="product-img-small me-3">
                                            <div>
                                                <h6 class="fw-bold mb-1"><?php echo htmlspecialchars($row['product_name']); ?>
                                                </h6>
                                            </div>
                                        </div>
                                    </td>
                                    <td>RM <?php echo number_format($row['product_price'], 2); ?></td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <button class="btn-qty"
                                                onclick="updateQty(<?php echo $row['cart_id']; ?>, -1, <?php echo $row['cart_quantity']; ?>)">-</button>
                                            <span class="fw-bold"><?php echo $row['cart_quantity']; ?></span>
                                            <button class="btn-qty"
                                                onclick="updateQty(<?php echo $row['cart_id']; ?>, 1, <?php echo $row['cart_quantity']; ?>)">+</button>
                                        </div>
                                    </td>
                                    <td class="price-text">RM <?php echo number_format($subtotal, 2); ?></td>
                                    <td>
                                        <button class="btn btn-outline-danger btn-sm rounded-circle"
                                            onclick="removeItem(<?php echo $row['cart_id']; ?>)">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>

                <div class="row justify-content-end mt-4">
                    <div class="col-md-5 col-lg-4">
                        <div class="bg-light p-4 rounded-3">
                            <div class="d-flex justify-content-between mb-3">
                                <span class="fw-bold text-muted">Total Amount:</span>
                                <span class="fw-bold fs-4 text-dark">RM <?php echo number_format($grand_total, 2); ?></span>
                            </div>
                            <a href="checkout.php" class="btn btn-checkout w-100">
                                Proceed to Checkout <i class="fas fa-arrow-right ms-2"></i>
                            </a>
                            <a href="menu.php"
                                class="btn btn-link text-decoration-none text-muted w-100 mt-2 text-center small">
                                Continue Shopping
                            </a>
                        </div>
                    </div>
                </div>

            <?php else: ?>
                <div class="empty-cart-state">
                    <i class="fas fa-shopping-basket empty-icon"></i>
                    <h3>Your cart is empty</h3>
                    <p class="text-muted">Looks like you haven't made your choice yet.</p>
                    <a href="menu.php" class="btn btn-danger rounded-pill px-4 mt-3 fw-bold">Go to Menu</a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <?php include 'footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="java script folder/cart.js"></script>
</body>

</html>