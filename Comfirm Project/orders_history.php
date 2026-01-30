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
$sql_orders = "SELECT * FROM `order` WHERE user_id = '$u_id' AND order_status IN ('Completed', 'Cancelled') ORDER BY order_date DESC";
$result_orders = mysqli_query($conn, $sql_orders);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order History - OKS Fast Food</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="css folder/orders_history.css">
</head>

<body>

    <?php include 'header.php'; ?>

    <div class="container">
        <div class="history-container">
            <div class="history-header d-flex justify-content-between align-items-center">
                <h2 class="fw-bold m-0"><i class="fas fa-history me-2"></i>Order History</h2>
                <span class="text-muted"><?php echo mysqli_num_rows($result_orders); ?> Orders</span>
            </div>

            <?php if (mysqli_num_rows($result_orders) > 0): ?>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Order ID</th>
                                <th>Date & Time</th>
                                <th>Delivery Address</th>
                                <th>Total Amount</th>
                                <th class="text-center">Status</th>
                                <th class="text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($order = mysqli_fetch_assoc($result_orders)):
                                $status = $order['order_status'];
                                $total_price = $order['order_total_price'];
                                $address = $order['order_delivery_address'];

                                $badgeClass = 'bg-secondary';
                                if ($status == 'Completed')
                                    $badgeClass = 'status-completed';
                                else if ($status == 'Cancelled')
                                    $badgeClass = 'status-cancelled';
                                ?>
                                <tr>
                                    <td class="fw-bold text-danger">#<?php echo $order['order_id']; ?></td>
                                    <td><?php echo date('d M Y, h:i A', strtotime($order['order_date'])); ?></td>
                                    <td
                                        style="max-width: 250px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                        <?php echo htmlspecialchars($address); ?>
                                    </td>
                                    <td class="fw-bold">RM <?php echo number_format($total_price, 2); ?></td>
                                    <td class="text-center">
                                        <span class="badge badge-status <?php echo $badgeClass; ?>">
                                            <?php echo $status; ?>
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <button class="btn btn-view" data-bs-toggle="modal"
                                            data-bs-target="#orderModal<?php echo $order['order_id']; ?>">
                                            View Details
                                        </button>
                                    </td>
                                </tr>

                                <div class="modal fade" id="orderModal<?php echo $order['order_id']; ?>" tabindex="-1"
                                    aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered modal-lg">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Order Details #<?php echo $order['order_id']; ?></h5>
                                                <button type="button" class="btn-close btn-close-white"
                                                    data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="row mb-3">
                                                    <div class="col-md-6">
                                                        <p class="text-muted mb-1">Order Date</p>
                                                        <h6 class="fw-bold">
                                                            <?php echo date('d F Y, h:i A', strtotime($order['order_date'])); ?>
                                                        </h6>
                                                    </div>
                                                    <div class="col-md-6 text-md-end">
                                                        <p class="text-muted mb-1">Current Status</p>
                                                        <span
                                                            class="badge badge-status <?php echo $badgeClass; ?>"><?php echo $status; ?></span>
                                                    </div>
                                                </div>
                                                <div class="row mb-4">
                                                    <div class="col-12">
                                                        <p class="text-muted mb-1">Delivery Address</p>
                                                        <h6 class="fw-bold"><?php echo htmlspecialchars($address); ?></h6>
                                                    </div>
                                                </div>

                                                <h6 class="border-bottom pb-2 mb-3 fw-bold">Items Ordered</h6>

                                                <?php
                                                $oid = $order['order_id'];
                                                $sql_items = "SELECT oi.*, p.product_name, p.product_image 
                                                              FROM order_items oi 
                                                              JOIN products p ON oi.product_id = p.product_id 
                                                              WHERE oi.order_id = '$oid'";
                                                $res_items = mysqli_query($conn, $sql_items);

                                                while ($item = mysqli_fetch_assoc($res_items)):
                                                    $img = !empty($item['product_image']) ? "uploads/" . $item['product_image'] : "https://via.placeholder.com/60";
                                                    $unit_price = $item['order_items_price'];
                                                    $qty = $item['order_items_quantity'];
                                                    $itemTotal = $qty * $unit_price;
                                                    ?>
                                                    <div class="item-row d-flex align-items-center justify-content-between">
                                                        <div class="d-flex align-items-center">
                                                            <img src="<?php echo $img; ?>" class="item-img me-3">
                                                            <div>
                                                                <h6 class="mb-0 fw-bold">
                                                                    <?php echo htmlspecialchars($item['product_name']); ?></h6>
                                                                <small class="text-muted">RM
                                                                    <?php echo number_format($unit_price, 2); ?> x
                                                                    <?php echo $qty; ?></small>
                                                            </div>
                                                        </div>
                                                        <span class="fw-bold">RM <?php echo number_format($itemTotal, 2); ?></span>
                                                    </div>
                                                <?php endwhile; ?>

                                                <div class="price-summary mt-4 text-end">
                                                    <h4 class="fw-bold text-danger m-0">Total: RM
                                                        <?php echo number_format($total_price, 2); ?></h4>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-clipboard-list empty-icon"></i>
                    <h3>No orders yet</h3>
                    <p class="mb-4">It looks like you haven't placed any orders yet.</p>
                    <a href="menu.php" class="btn btn-danger rounded-pill px-4 fw-bold">Order Now</a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <?php include 'footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="java script folder/orders_history.js"></script>
</body>

</html>