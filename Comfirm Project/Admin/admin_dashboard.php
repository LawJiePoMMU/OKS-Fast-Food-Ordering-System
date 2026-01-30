<?php
session_start();
include '../database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo "<script>alert('Access Denied! Admins only.'); window.location.href='login.php';</script>";
    exit();
}

if (isset($_POST['ajax_view_details'])) {
    $order_id = mysqli_real_escape_string($conn, $_POST['order_id']);
    $res_items = mysqli_query($conn, "SELECT oi.*, p.product_name, p.product_image FROM order_items oi JOIN products p ON oi.product_id = p.product_id WHERE oi.order_id = '$order_id'");
    $output = '<div class="table-responsive"><table class="table align-middle"><thead class="table-light"><tr><th>Image</th><th>Product</th><th>Qty</th><th>Price</th><th>Subtotal</th></tr></thead><tbody>';
    $grand_total = 0;
    while ($row = mysqli_fetch_assoc($res_items)) {
        $price = floatval($row['order_items_price']);
        $qty = intval($row['order_items_quantity']);
        $subtotal = $price * $qty;
        $grand_total += $subtotal;
        $img = !empty($row['product_image']) ? "../uploads/" . $row['product_image'] : "https://via.placeholder.com/50";
        $output .= '<tr><td><img src="' . $img . '" style="width:50px; height:50px; object-fit:cover; border-radius:5px;"></td><td>' . htmlspecialchars($row['product_name']) . '</td><td>x ' . $qty . '</td><td>RM ' . number_format($price, 2) . '</td><td class="fw-bold">RM ' . number_format($subtotal, 2) . '</td></tr>';
    }
    $output .= '</tbody><tfoot class="table-light"><tr><td colspan="4" class="text-end fw-bold">Grand Total:</td><td class="fw-bold text-danger fs-5">RM ' . number_format($grand_total, 2) . '</td></tr></tfoot></table></div>';
    echo $output;
    exit();
}

$sql_sales = "SELECT SUM(order_total_price) AS total_sales FROM `order` WHERE order_status = 'Completed'";
$res_sales = $conn->query($sql_sales);
$row_sales = $res_sales->fetch_assoc();
$total_sales = $row_sales['total_sales'] ?? 0;

$sql_orders = "SELECT COUNT(*) AS total_orders FROM `order`";
$res_orders = $conn->query($sql_orders);
$total_orders = $res_orders->fetch_assoc()['total_orders'];

$sql_products = "SELECT COUNT(*) AS total_products FROM products";
$res_products = $conn->query($sql_products);
$total_products = $res_products->fetch_assoc()['total_products'];

$sql_users = "SELECT COUNT(*) AS total_users FROM users WHERE user_role = 'customer'";
$res_users = $conn->query($sql_users);
$total_users = $res_users->fetch_assoc()['total_users'];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OKS Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="Admin css folder/admin_dashboard.css">
</head>

<body>

    <div class="container-fluid">
        <div class="row">

            <nav class="col-md-3 col-lg-2 d-none d-md-flex sidebar p-3 fixed-top"
                style="z-index: 100; height: 100vh; overflow-y: auto;">
                <div class="brand-wrapper pt-2">
                    <div class="d-flex align-items-center text-white">
                        <i class="bi bi-layers-fill fs-4 me-2"></i>
                        <h4 class="m-0 fw-bold">OKS ADMIN</h4>
                    </div>
                </div>
                <ul class="nav flex-column mb-auto">
                    <li class="nav-item"><a class="nav-link active" href="admin_dashboard.php">Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="manage_orders.php">Manage Orders</a></li>
                    <li class="nav-item"><a class="nav-link" href="manage_products.php">Manage Product</a></li>
                    <li class="nav-item"><a class="nav-link" href="manage_categories.php">Manage Categories</a></li>
                    <li class="nav-item"><a class="nav-link" href="manage_users.php">Manage Users</a></li>
                </ul>
                <div class="mt-auto border-top border-secondary pt-3">
                    <a href="../logout.php" class="logout-link"><i class="bi bi-box-arrow-left me-2"></i>Logout</a>
                </div>
            </nav>

            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">

                <div class="d-flex justify-content-between align-items-center mb-4 pt-3">
                    <div>
                        <h2 class="fw-bold" style="color: #1e293b;">Dashboard Overview</h2>
                        <p class="text-muted">Welcome back, <?php echo $_SESSION['username']; ?>!</p>
                    </div>
                </div>

                <div class="row g-4 mb-5">
                    <div class="col-12 col-md-6 col-xl-3">
                        <div class="card stat-card h-100">
                            <div class="card-body d-flex align-items-center">
                                <div class="icon-square bg-danger bg-opacity-10 text-danger me-3">
                                    <i class="bi bi-currency-exchange"></i>
                                </div>
                                <div>
                                    <h6 class="text-muted mb-1">Total Sales</h6>
                                    <h4 class="mb-0 fw-bold">RM <?php echo number_format($total_sales, 2); ?></h4>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-6 col-xl-3">
                        <div class="card stat-card h-100">
                            <div class="card-body d-flex align-items-center">
                                <div class="icon-square bg-success bg-opacity-10 text-success me-3">
                                    <i class="bi bi-cart-check"></i>
                                </div>
                                <div>
                                    <h6 class="text-muted mb-1">Total Orders</h6>
                                    <h4 class="mb-0 fw-bold"><?php echo $total_orders; ?></h4>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-6 col-xl-3">
                        <div class="card stat-card h-100">
                            <div class="card-body d-flex align-items-center">
                                <div class="icon-square bg-warning bg-opacity-10 text-warning me-3">
                                    <i class="bi bi-box-seam"></i>
                                </div>
                                <div>
                                    <h6 class="text-muted mb-1">Total Products</h6>
                                    <h4 class="mb-0 fw-bold"><?php echo $total_products; ?></h4>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-6 col-xl-3">
                        <div class="card stat-card h-100">
                            <div class="card-body d-flex align-items-center">
                                <div class="icon-square bg-info bg-opacity-10 text-info me-3">
                                    <i class="bi bi-people"></i>
                                </div>
                                <div>
                                    <h6 class="text-muted mb-1">Customers</h6>
                                    <h4 class="mb-0 fw-bold"><?php echo $total_users; ?></h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm rounded-3">
                    <div class="card-header bg-white py-3 border-bottom">
                        <h5 class="mb-0 fw-bold">Completed & Cancelled Orders</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover table-custom align-middle">
                                <thead>
                                    <tr>
                                        <th>Order ID</th>
                                        <th>Customer</th>
                                        <th>Date</th>
                                        <th>Status</th>
                                        <th>Total</th>
                                        <th class="text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $sql_recent = "SELECT o.order_id, u.username, o.order_date, o.order_status, o.order_total_price 
                                                   FROM `order` o 
                                                   JOIN users u ON o.user_id = u.user_id 
                                                   WHERE o.order_status IN ('Completed', 'Cancelled')
                                                   ORDER BY o.order_date DESC";
                                    $res_recent = $conn->query($sql_recent);

                                    if ($res_recent && $res_recent->num_rows > 0) {
                                        while ($row = $res_recent->fetch_assoc()) {
                                            $badge_color = 'bg-secondary';
                                            if ($row['order_status'] == 'Completed')
                                                $badge_color = 'bg-success';
                                            if ($row['order_status'] == 'Cancelled')
                                                $badge_color = 'bg-danger';

                                            $date = date("Y-m-d", strtotime($row['order_date']));
                                            echo "<tr>
                                                <td class='fw-bold text-danger'>#OKS-{$row['order_id']}</td>
                                                <td class='fw-bold'>{$row['username']}</td>
                                                <td>{$date}</td>
                                                <td><span class='badge {$badge_color}'>{$row['order_status']}</span></td>
                                                <td class='fw-bold'>RM {$row['order_total_price']}</td>
                                                <td class='text-end'>
                                                    <button class='btn btn-sm btn-outline-secondary view-btn' 
                                                        data-bs-toggle='modal' 
                                                        data-bs-target='#viewOrderModal' 
                                                        data-id='{$row['order_id']}'>View</button>
                                                </td>
                                            </tr>";
                                        }
                                    } else {
                                        echo "<tr><td colspan='6' class='text-center py-4 text-muted'>No completed or cancelled orders yet.</td></tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </main>
        </div>
    </div>

    <div class="offcanvas offcanvas-start" tabindex="-1" id="mobileSidebar" style="background-color: #1e293b;">
    </div>

    <div class="modal fade" id="viewOrderModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h6 class="modal-title fw-bold">Order Details</h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="order_details_content">
                    <div class="text-center py-3">
                        <div class="spinner-border text-danger"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="Admin java script folder/admin_dashboard.js"></script>
</body>

</html>