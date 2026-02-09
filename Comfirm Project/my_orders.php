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
$alert_msg = "";

if (isset($_POST['update_status'])) {
    $order_id = mysqli_real_escape_string($conn, $_POST['order_id']);
    $action = $_POST['action'];
    $new_status = '';

    if ($action == 'cancel') {
        $new_status = 'Cancelled';
    } elseif ($action == 'complete') {
        $new_status = 'Completed';
    }

    if ($new_status != '') {
        $sql_update = "UPDATE `order` SET order_status = '$new_status', order_completed_date = NOW() WHERE order_id = '$order_id' AND user_id = '$u_id'";
        if (mysqli_query($conn, $sql_update)) {
            $alert_msg = "Order status updated successfully!";
        }
    }
}

$sql_active_orders = "SELECT * FROM `order` 
                      WHERE user_id = '$u_id' 
                      AND order_status IN ('Pending', 'Preparing', 'Out for Delivery') 
                      ORDER BY order_id DESC";
$result_orders = mysqli_query($conn, $sql_active_orders);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders - OKS Fast Food</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="css folder/my_orders.css">
</head>

<body>

    <?php include 'header.php'; ?>

    <div class="container">
        <div class="orders-container">
            <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
                <h2 class="fw-bold m-0"><i class="fas fa-utensils me-2"></i>My Active Orders</h2>
                <a href="orders_history.php" class="btn btn-outline-secondary btn-sm">View Order History</a>
            </div>

            <?php if (mysqli_num_rows($result_orders) > 0): ?>
                <div class="row g-4">
                    <?php while ($order = mysqli_fetch_assoc($result_orders)):
                        $oid = $order['order_id'];
                        $status = $order['order_status'];
                        $total = $order['order_total_price'];
                        $order_time = date('d M Y, h:i A', strtotime($order['order_date']));

                        $statusColor = 'bg-secondary';
                        $progressBar = 0;

                        if ($status == 'Pending') {
                            $statusColor = 'bg-warning text-dark';
                            $progressBar = 25;
                        } elseif ($status == 'Preparing') {
                            $statusColor = 'bg-info text-dark';
                            $progressBar = 50;
                        } elseif ($status == 'Out for Delivery') {
                            $statusColor = 'bg-primary';
                            $progressBar = 80;
                        }

                        $items_list = [];
                        $sql_items = "SELECT oi.order_items_quantity, p.product_name 
                                      FROM order_items oi 
                                      JOIN products p ON oi.product_id = p.product_id 
                                      WHERE oi.order_id = '$oid'";
                        $res_items = mysqli_query($conn, $sql_items);
                        if ($res_items) {
                            while ($item = mysqli_fetch_assoc($res_items)) {
                                $items_list[] = $item['order_items_quantity'] . "x " . $item['product_name'];
                            }
                        }
                        $items_display = implode(", ", $items_list);
                        ?>
                        <div class="col-12">
                            <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
                                <div
                                    class="card-header bg-white border-0 pt-4 px-4 d-flex justify-content-between align-items-center">
                                    <div>
                                        <h5 class="fw-bold m-0">Order #<?php echo $oid; ?></h5>
                                        <small class="text-muted"><i
                                                class="far fa-clock me-1"></i><?php echo $order_time; ?></small>
                                    </div>
                                    <span
                                        class="badge rounded-pill <?php echo $statusColor; ?> fs-6 px-3 py-2"><?php echo $status; ?></span>
                                </div>

                                <div class="card-body px-4">
                                    <div class="progress mb-4" style="height: 6px;">
                                        <div class="progress-bar progress-bar-striped progress-bar-animated bg-danger"
                                            role="progressbar" style="width: <?php echo $progressBar; ?>%"></div>
                                    </div>
                                    <div class="row align-items-center">
                                        <div class="col-md-8 mb-3 mb-md-0">
                                            <h6 class="text-muted text-uppercase small fw-bold mb-2">Items Ordered</h6>
                                            <p class="fs-5 fw-bold text-dark mb-0">
                                                <?php echo htmlspecialchars($items_display); ?>
                                            </p>
                                        </div>
                                        <div class="col-md-4 text-md-end">
                                            <h6 class="text-muted text-uppercase small fw-bold mb-1">Total Amount</h6>
                                            <h4 class="text-danger fw-bold">RM <?php echo number_format($total, 2); ?></h4>
                                        </div>
                                    </div>
                                </div>

                                <div class="card-footer bg-light border-0 px-4 py-3 d-flex justify-content-end gap-2">
                                    <?php if ($status == 'Pending'): ?>
                                        <form method="POST"
                                            onsubmit="return confirm('Are you sure you want to cancel this order?');">
                                            <input type="hidden" name="update_status" value="1">
                                            <input type="hidden" name="order_id" value="<?php echo $oid; ?>">
                                            <input type="hidden" name="action" value="cancel">
                                            <button type="submit" class="btn btn-outline-danger rounded-pill px-4 fw-bold">Cancel
                                                Order</button>
                                        </form>
                                    <?php elseif ($status == 'Preparing'): ?>
                                        <button class="btn btn-secondary rounded-pill px-4 fw-bold" disabled>
                                            <i class="fas fa-fire me-2"></i>Kitchen is Preparing...
                                        </button>
                                    <?php elseif ($status == 'Out for Delivery'): ?>
                                        <form method="POST">
                                            <input type="hidden" name="update_status" value="1">
                                            <input type="hidden" name="order_id" value="<?php echo $oid; ?>">
                                            <input type="hidden" name="action" value="complete">
                                            <button type="submit" class="btn btn-success rounded-pill px-4 fw-bold">
                                                <i class="fas fa-check-circle me-2"></i>Order Received
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <div class="text-center py-5">
                    <i class="fas fa-utensils fs-1 text-muted mb-3"></i>
                    <h3 class="fw-bold text-secondary">No active orders</h3>
                    <a href="menu.php" class="btn btn-danger rounded-pill px-5 fw-bold mt-2">Go to Menu</a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <?php include 'footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        <?php if (!empty($alert_msg)): ?>
            alert("<?php echo $alert_msg; ?>");
            window.location.href = "my_orders.php";
        <?php endif; ?>
    </script>
</body>


</html>
