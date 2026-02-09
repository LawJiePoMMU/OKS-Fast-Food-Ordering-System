<?php
session_start();
include "../database.php";

if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

if (isset($_POST['ajax_update_status'])) {
    $order_id = mysqli_real_escape_string($conn, $_POST['order_id']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);

    if ($status == 'Preparing') {
        $sql = "UPDATE `order` SET order_status='$status', order_preparing_date=NOW() WHERE order_id='$order_id'";
    } elseif ($status == 'Out for Delivery') {
        $sql = "UPDATE `order` SET order_status='$status', order_delivery_date=NOW() WHERE order_id='$order_id'";
    } elseif ($status == 'Completed' || $status == 'Cancelled') {
        $sql = "UPDATE `order` SET order_status='$status', order_completed_date=NOW() WHERE order_id='$order_id'";
    } else {
        $sql = "UPDATE `order` SET order_status='$status' WHERE order_id='$order_id'";
    }

    if (mysqli_query($conn, $sql)) {
        echo "success";
    } else {
        echo "error: " . mysqli_error($conn);
    }
    exit();
}

if (isset($_POST['ajax_view_details'])) {
    $order_id = mysqli_real_escape_string($conn, $_POST['order_id']);
    $sql_items = "SELECT oi.*, p.product_name, p.product_image 
                  FROM order_items oi 
                  JOIN products p ON oi.product_id = p.product_id 
                  WHERE oi.order_id = '$order_id'";
    $res_items = mysqli_query($conn, $sql_items);

    $output = '<div class="table-responsive"><table class="table align-middle"><thead class="table-light"><tr><th>Image</th><th>Product</th><th>Qty</th><th>Price</th><th>Subtotal</th></tr></thead><tbody>';
    $grand_total = 0;
    while ($row = mysqli_fetch_assoc($res_items)) {
        $price = floatval($row['order_items_price']);
        $qty = intval($row['order_items_quantity']);
        $subtotal = $price * $qty;
        $grand_total += $subtotal;

        $img = !empty($row['product_image']) ? "../uploads/" . $row['product_image'] : "https://via.placeholder.com/50";
        $output .= '<tr>
                        <td><img src="' . $img . '" style="width:50px; height:50px; object-fit:cover; border-radius:5px;"></td>
                        <td>' . htmlspecialchars($row['product_name']) . '</td>
                        <td>x ' . $qty . '</td>
                        <td>RM ' . number_format($price, 2) . '</td>
                        <td class="fw-bold">RM ' . number_format($subtotal, 2) . '</td>
                    </tr>';
    }
    $output .= '</tbody><tfoot class="table-light"><tr><td colspan="4" class="text-end fw-bold">Grand Total:</td><td class="fw-bold text-danger fs-5">RM ' . number_format($grand_total, 2) . '</td></tr></tfoot></table></div>';
    echo $output;
    exit();
}

$search = "";
$where_search = "";
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search = mysqli_real_escape_string($conn, $_GET['search']);
    $where_search = "AND (o.order_id LIKE '%$search%' OR u.username LIKE '%$search%')";
}

$sql_pending = "SELECT o.*, u.username FROM `order` o JOIN users u ON o.user_id = u.user_id WHERE o.order_status = 'Pending' $where_search ORDER BY o.order_date ASC";
$result_pending = mysqli_query($conn, $sql_pending);

$sql_active = "SELECT o.*, u.username FROM `order` o JOIN users u ON o.user_id = u.user_id WHERE o.order_status = 'Preparing' $where_search ORDER BY o.order_preparing_date ASC";
$result_active = mysqli_query($conn, $sql_active);

$sql_delivering = "SELECT o.*, u.username FROM `order` o JOIN users u ON o.user_id = u.user_id WHERE o.order_status = 'Out for Delivery' $where_search ORDER BY o.order_delivery_date ASC";
$result_delivering = mysqli_query($conn, $sql_delivering);

$sql_history = "SELECT o.*, u.username FROM `order` o JOIN users u ON o.user_id = u.user_id WHERE (o.order_status = 'Completed' OR o.order_status = 'Cancelled') $where_search ORDER BY o.order_completed_date DESC";
$result_history = mysqli_query($conn, $sql_history);
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title> Manage Orders - OKS Admin </title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="Admin css folder/manage_orders.css">
</head>

<body>
    <div class="container-fluid">
        <div class="row">
            <nav class="col-md-3 col-lg-2 d-none d-md-flex sidebar p-3 fixed-top"
                style="z-index: 100; height: 100vh; overflow-y: auto;">
                <div class="brand-wrapper pt-2">
                    <div class="d-flex align-items-center text-white"><i class="bi bi-layers-fill fs-4 me-2"></i>
                        <h4 class="m-0 fw-bold">OKS ADMIN</h4>
                    </div>
                </div>
                <ul class="nav flex-column mb-auto">
                    <li class="nav-item"><a class="nav-link" href="admin_dashboard.php">Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link active" href="manage_orders.php">Manage Orders</a></li>
                    <li class="nav-item"><a class="nav-link" href="manage_products.php">Manage Product</a></li>
                    <li class="nav-item"><a class="nav-link" href="manage_categories.php">Manage Categories</a></li>
                    <li class="nav-item"><a class="nav-link" href="manage_users.php">Manage Users</a></li>
                </ul>
                <div class="mt-auto border-top border-secondary pt-3"><a href="../logout.php" class="logout-link"><i
                            class="bi bi-box-arrow-left me-2"></i>Logout</a></div>
            </nav>

            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between align-items-center mb-4 pt-3">
                    <h2 class="fw-bold" style="color: #1e293b;">Manage Orders</h2>
                    <span class="text-muted">Welcome, <strong><?php echo $_SESSION['username']; ?></strong></span>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <form action="" method="GET" class="d-flex">
                            <input type="text" name="search" class="form-control me-2"
                                placeholder="Search Order ID or Customer..."
                                value="<?php echo htmlspecialchars($search); ?>">
                            <button class="btn btn-danger" type="submit">Search</button>
                            <?php if (!empty($search)): ?><a href="manage_orders.php"
                                    class="btn btn-secondary ms-2">Reset</a><?php endif; ?>
                        </form>
                    </div>
                </div>

                <h4 class="section-header text-warning"><i class="bi bi-hourglass-split me-2"></i>New Orders (Pending)
                </h4>
                <div class="card shadow-sm border-0 rounded-3 mb-5">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>ID</th>
                                        <th>Customer</th>
                                        <th>Order Time</th>
                                        <th>Address</th>
                                        <th>Payment</th>
                                        <th>Total</th>
                                        <th>Status</th>
                                        <th class="text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if ($result_pending && mysqli_num_rows($result_pending) > 0) {
                                        while ($row = mysqli_fetch_assoc($result_pending)) {
                                            $date = ($row['order_date']) ? date("d M, h:i A", strtotime($row['order_date'])) : "-";
                                            ?>
                                            <tr>
                                                <td class="fw-bold text-danger">#<?php echo $row['order_id']; ?></td>
                                                <td class="fw-bold"><?php echo htmlspecialchars($row['username']); ?></td>
                                                <td><?php echo $date; ?></td>
                                                <td><?php echo htmlspecialchars($row['order_delivery_address']); ?></td>
                                                <td><span
                                                        class="badge bg-secondary"><?php echo htmlspecialchars($row['order_payment_method']); ?></span>
                                                </td>
                                                <td class="fw-bold">RM
                                                    <?php echo number_format($row['order_total_price'], 2); ?>
                                                </td>
                                                <td><span class="badge rounded-pill badge-pending">Pending</span></td>
                                                <td class="text-end">
                                                    <button class="btn btn-sm btn-outline-primary view-btn"
                                                        data-bs-toggle="modal" data-bs-target="#viewOrderModal"
                                                        data-id="<?php echo $row['order_id']; ?>">View</button>
                                                    <button class="btn btn-sm btn-outline-success status-btn"
                                                        data-id="<?php echo $row['order_id']; ?>"
                                                        data-status="Pending">Update</button>
                                                </td>
                                            </tr>
                                        <?php }
                                    } else {
                                        echo "<tr><td colspan='8' class='text-center py-4 text-muted'>No pending orders.</td></tr>";
                                    } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <h4 class="section-header text-info"><i class="bi bi-fire me-2"></i>Kitchen (Preparing)</h4>
                <div class="card shadow-sm border-0 rounded-3 mb-5">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>ID</th>
                                        <th>Customer</th>
                                        <th>Preparing Time</th>
                                        <th>Address</th>
                                        <th>Payment</th>
                                        <th>Total</th>
                                        <th>Status</th>
                                        <th class="text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if ($result_active && mysqli_num_rows($result_active) > 0) {
                                        while ($row = mysqli_fetch_assoc($result_active)) {
                                            $date = ($row['order_preparing_date']) ? date("d M, h:i A", strtotime($row['order_preparing_date'])) : "-";
                                            ?>
                                            <tr>
                                                <td class="fw-bold text-danger">#<?php echo $row['order_id']; ?></td>
                                                <td class="fw-bold"><?php echo htmlspecialchars($row['username']); ?></td>
                                                <td><?php echo $date; ?></td>
                                                <td><?php echo htmlspecialchars($row['order_delivery_address']); ?></td>
                                                <td><span
                                                        class="badge bg-secondary"><?php echo htmlspecialchars($row['order_payment_method']); ?></span>
                                                </td>
                                                <td class="fw-bold">RM
                                                    <?php echo number_format($row['order_total_price'], 2); ?>
                                                </td>
                                                <td><span class="badge rounded-pill badge-preparing">Preparing</span></td>
                                                <td class="text-end">
                                                    <button class="btn btn-sm btn-outline-primary view-btn"
                                                        data-bs-toggle="modal" data-bs-target="#viewOrderModal"
                                                        data-id="<?php echo $row['order_id']; ?>">View</button>
                                                    <button class="btn btn-sm btn-outline-info status-btn"
                                                        data-id="<?php echo $row['order_id']; ?>"
                                                        data-status="Preparing">Update</button>
                                                </td>
                                            </tr>
                                        <?php }
                                    } else {
                                        echo "<tr><td colspan='8' class='text-center py-4 text-muted'>No orders preparing.</td></tr>";
                                    } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <h4 class="section-header text-primary"><i class="bi bi-truck me-2"></i>Out for Delivery</h4>
                <div class="card shadow-sm border-0 rounded-3 mb-5">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>ID</th>
                                        <th>Customer</th>
                                        <th>Delivery Time</th>
                                        <th>Address</th>
                                        <th>Payment</th>
                                        <th>Total</th>
                                        <th>Status</th>
                                        <th class="text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if ($result_delivering && mysqli_num_rows($result_delivering) > 0) {
                                        while ($row = mysqli_fetch_assoc($result_delivering)) {
                                            $date = ($row['order_delivery_date']) ? date("d M, h:i A", strtotime($row['order_delivery_date'])) : "-";
                                            ?>
                                            <tr>
                                                <td class="fw-bold text-danger">#<?php echo $row['order_id']; ?></td>
                                                <td class="fw-bold"><?php echo htmlspecialchars($row['username']); ?></td>
                                                <td><?php echo $date; ?></td>
                                                <td><?php echo htmlspecialchars($row['order_delivery_address']); ?></td>
                                                <td><span
                                                        class="badge bg-secondary"><?php echo htmlspecialchars($row['order_payment_method']); ?></span>
                                                </td>
                                                <td class="fw-bold">RM
                                                    <?php echo number_format($row['order_total_price'], 2); ?>
                                                </td>
                                                <td><span class="badge rounded-pill badge-delivering">Out for Delivery</span>
                                                </td>
                                                <td class="text-end">
                                                    <button class="btn btn-sm btn-outline-primary view-btn"
                                                        data-bs-toggle="modal" data-bs-target="#viewOrderModal"
                                                        data-id="<?php echo $row['order_id']; ?>">View Details</button>
                                                </td>
                                            </tr>
                                        <?php }
                                    } else {
                                        echo "<tr><td colspan='8' class='text-center py-4 text-muted'>No orders out for delivery.</td></tr>";
                                    } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <h4 class="section-header text-secondary"><i class="bi bi-clock-history me-2"></i>Order History</h4>
                <div class="card shadow-sm border-0 rounded-3">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>ID</th>
                                        <th>Customer</th>
                                        <th>Completion Time</th>
                                        <th>Address</th>
                                        <th>Payment</th>
                                        <th>Total</th>
                                        <th>Status</th>
                                        <th class="text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if ($result_history && mysqli_num_rows($result_history) > 0) {
                                        while ($row = mysqli_fetch_assoc($result_history)) {
                                            $status = $row['order_status'];
                                            $badgeClass = ($status == 'Completed') ? 'badge-completed' : 'badge-cancelled';
                                            $date = ($row['order_completed_date']) ? date("d M, h:i A", strtotime($row['order_completed_date'])) : "-";
                                            ?>
                                            <tr>
                                                <td class="fw-bold text-danger">#<?php echo $row['order_id']; ?></td>
                                                <td class="fw-bold"><?php echo htmlspecialchars($row['username']); ?></td>
                                                <td><?php echo $date; ?></td>
                                                <td><?php echo htmlspecialchars($row['order_delivery_address']); ?></td>
                                                <td><span
                                                        class="badge bg-secondary"><?php echo htmlspecialchars($row['order_payment_method']); ?></span>
                                                </td>
                                                <td class="fw-bold">RM
                                                    <?php echo number_format($row['order_total_price'], 2); ?>
                                                </td>
                                                <td><span
                                                        class="badge rounded-pill <?php echo $badgeClass; ?>"><?php echo $status; ?></span>
                                                </td>
                                                <td class="text-end">
                                                    <button class="btn btn-sm btn-outline-secondary view-btn"
                                                        data-bs-toggle="modal" data-bs-target="#viewOrderModal"
                                                        data-id="<?php echo $row['order_id']; ?>">View</button>
                                                </td>
                                            </tr>
                                        <?php }
                                    } else {
                                        echo "<tr><td colspan='8' class='text-center py-4 text-muted'>No order history found.</td></tr>";
                                    } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <div class="modal fade" id="viewOrderModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h6 class="modal-title fw-bold">Order Details</h6><button type="button"
                        class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="order_details_content">
                    <div class="text-center py-3">
                        <div class="spinner-border text-danger"></div>
                    </div>
                </div>
                <div class="modal-footer"><button type="button" class="btn btn-secondary"
                        data-bs-dismiss="modal">Close</button></div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="updateStatusModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h6 class="modal-title fw-bold">Update Order Status</h6><button type="button"
                        class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body"><input type="hidden" id="status_order_id"><label
                        class="form-label fw-bold">Select New Status:</label><select id="new_status"
                        class="form-select"></select></div>
                <div class="modal-footer"><button type="button" class="btn btn-secondary"
                        data-bs-dismiss="modal">Close</button><button type="button" class="btn btn-danger"
                        onclick="saveStatus()">Update Status</button></div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="Admin java script folder/manage_orders.js"></script>
</body>


</html>
