<?php
session_start();
include "../database.php";

if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

if (isset($_POST['ajax_add_product'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $price = floatval($_POST['price']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $cat_id = mysqli_real_escape_string($conn, $_POST['category_id']);
    $image = "";
    if (isset($_FILES['image']['name']) && $_FILES['image']['name'] != "") {
        $target_dir = "uploads/";
        if (!is_dir($target_dir))
            mkdir($target_dir, 0777, true);
        $image_name = time() . "_" . basename($_FILES["image"]["name"]);
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_dir . $image_name))
            $image = $image_name;
    }
    if ($price < 0) {
        echo "error: Price cannot be negative!";
        exit();
    }
    $sql = "INSERT INTO products (product_name, product_price, category_id, product_description, product_image, product_status) VALUES ('$name', '$price', '$cat_id', '$description', '$image', 'Active')";
    echo mysqli_query($conn, $sql) ? "success" : "error: " . mysqli_error($conn);
    exit();
}

if (isset($_POST['ajax_update_product'])) {
    $id = mysqli_real_escape_string($conn, $_POST['edit_product_id']);
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $price = floatval($_POST['price']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $cat_id = mysqli_real_escape_string($conn, $_POST['category_id']);
    $image_update_query = "";
    if (isset($_FILES['image']['name']) && $_FILES['image']['name'] != "") {
        $image_name = time() . "_" . basename($_FILES["image"]["name"]);
        if (move_uploaded_file($_FILES["image"]["tmp_name"], "../uploads/" . $image_name))
            $image_update_query = ", product_image='$image_name'";
    }
    $sql = "UPDATE products SET product_name='$name', product_price='$price', category_id='$cat_id', product_description='$description' $image_update_query WHERE product_id='$id'";
    echo mysqli_query($conn, $sql) ? "success" : "error: " . mysqli_error($conn);
    exit();
}

if (isset($_POST['ajax_update_status'])) {
    $id = mysqli_real_escape_string($conn, $_POST['product_id']);
    $current_status = mysqli_real_escape_string($conn, $_POST['current_status']);
    $new_status = ($current_status == 'Active') ? 'Inactive' : 'Active';
    $sql = "UPDATE products SET product_status='$new_status' WHERE product_id='$id'";
    echo mysqli_query($conn, $sql) ? "success" : "error: " . mysqli_error($conn);
    exit();
}

$category_options = "";
$res_cat = mysqli_query($conn, "SELECT * FROM categories WHERE category_status = 'Active'");
if ($res_cat)
    while ($row_cat = mysqli_fetch_assoc($res_cat))
        $category_options .= "<option value='{$row_cat['category_id']}'>{$row_cat['category_name']}</option>";

$where_sql = "";
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search = mysqli_real_escape_string($conn, $_GET['search']);
    $where_sql = "WHERE p.product_id LIKE '%$search%' OR p.product_name LIKE '%$search%' OR c.category_name LIKE '%$search%'";
}
$result_products = mysqli_query($conn, "SELECT p.*, c.category_name FROM products p LEFT JOIN categories c ON p.category_id = c.category_id $where_sql ORDER BY p.product_id ASC");
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title> Manage Products - OKS Admin </title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="Admin css folder/manage_products.css">
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
                    <li class="nav-item"><a class="nav-link" href="admin_dashboard.php">Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="manage_orders.php">Manage Orders</a></li>
                    <li class="nav-item"><a class="nav-link active" href="manage_products.php">Manage Product</a></li>
                    <li class="nav-item"><a class="nav-link" href="manage_categories.php">Manage Categories</a></li>
                    <li class="nav-item"><a class="nav-link" href="manage_users.php">Manage Users</a></li>
                </ul>
                <div class="mt-auto border-top border-secondary pt-3">
                    <a href="../logout.php" class="logout-link"><i class="bi bi-box-arrow-left me-2"></i>Logout</a>
                </div>
            </nav>

            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between align-items-center mb-4 pt-3">
                    <h2 class="fw-bold" style="color: #1e293b;">Manage Products</h2>
                    <span class="text-muted">Welcome, <strong><?php echo $_SESSION['username']; ?></strong></span>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <form action="" method="GET" class="d-flex"><input type="text" name="search"
                                class="form-control me-2" placeholder="Search by ID, Name, Category..."
                                value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>"><button
                                class="btn btn-danger"
                                type="submit">Search</button><?php if (!empty($_GET['search'])): ?><a
                                    href="manage_products.php" class="btn btn-secondary ms-2">Reset</a><?php endif; ?>
                        </form>
                    </div>
                    <div class="col-md-6 text-end"><button class="btn btn-danger" data-bs-toggle="modal"
                            data-bs-target="#addProductModal"><i class="bi bi-plus-lg me-1"></i> Add New
                            Product</button></div>
                </div>

                <div class="card shadow-sm border-0 rounded-3">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>ID</th>
                                        <th>Image</th>
                                        <th>Product Name</th>
                                        <th>Category</th>
                                        <th>Description</th>
                                        <th>Price (RM)</th>
                                        <th>Status</th>
                                        <th class="text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if ($result_products && mysqli_num_rows($result_products) > 0) {
                                        while ($row = mysqli_fetch_assoc($result_products)) {
                                            $imgSrc = !empty($row['product_image']) ? "../uploads/" . $row['product_image'] : "https://via.placeholder.com/60?text=No+Img";
                                            $catName = !empty($row['category_name']) ? $row['category_name'] : "<span class='text-muted'>-</span>";
                                            $statusBtnClass = ($row['product_status'] == 'Active') ? 'btn-success' : 'btn-secondary';
                                            ?>
                                            <tr>
                                                <td class="text-muted fw-bold">P<?php echo $row['product_id']; ?></td>
                                                <td><img src="<?php echo $imgSrc; ?>" class="product-img-thumb" alt="Product">
                                                </td>
                                                <td class="fw-bold text-dark">
                                                    <?php echo htmlspecialchars($row['product_name']); ?>
                                                </td>
                                                <td><span class="badge bg-secondary"><?php echo $catName; ?></span></td>
                                                <td class="desc-cell"
                                                    title="<?php echo htmlspecialchars($row['product_description']); ?>">
                                                    <?php echo htmlspecialchars($row['product_description']); ?>
                                                </td>
                                                <td class="text-danger fw-bold">RM
                                                    <?php echo number_format($row['product_price'], 2); ?>
                                                </td>
                                                <td>
                                                    <button type="button"
                                                        class="btn btn-sm <?php echo $statusBtnClass; ?> toggle-status-btn"
                                                        data-id="<?php echo $row['product_id']; ?>"
                                                        data-status="<?php echo $row['product_status']; ?>">
                                                        <?php echo $row['product_status']; ?>
                                                    </button>
                                                </td>
                                                <td class="text-end">
                                                    <button class="btn btn-sm btn-outline-primary btn-action edit-btn"
                                                        data-bs-toggle="modal" data-bs-target="#editProductModal"
                                                        data-id="<?php echo $row['product_id']; ?>"
                                                        data-name="<?php echo htmlspecialchars($row['product_name']); ?>"
                                                        data-price="<?php echo $row['product_price']; ?>"
                                                        data-cat="<?php echo $row['category_id']; ?>"
                                                        data-desc="<?php echo htmlspecialchars($row['product_description']); ?>"
                                                        data-img="<?php echo $imgSrc; ?>"><i class="fas fa-edit"></i>
                                                        Edit
                                                    </button>
                                                </td>
                                            </tr>
                                        <?php }
                                    } else {
                                        echo "<tr><td colspan='8' class='text-center py-4 text-muted'>No products found.</td></tr>";
                                    } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <div class="modal fade" id="addProductModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h6 class="modal-title fw-bold">Add New Product</h6><button type="button"
                        class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form id="addProductForm" enctype="multipart/form-data">
                    <div class="modal-body"><input type="hidden" name="ajax_add_product" value="1">
                        <div class="mb-3"><label class="form-label fw-bold small">Product Name</label><input type="text"
                                name="name" class="form-control" required></div>
                        <div class="row g-2 mb-3">
                            <div class="col-6"><label class="form-label fw-bold small">Price (RM)</label><input
                                    type="number" step="0.01" min="0" name="price" class="form-control" required></div>
                            <div class="col-6"><label class="form-label fw-bold small">Category</label><select
                                    name="category_id" class="form-select" required>
                                    <option value="" disabled selected>Select...</option>
                                    <?php echo $category_options; ?>
                                </select></div>
                        </div>
                        <div class="mb-3"><label class="form-label fw-bold small">Description</label><textarea
                                name="description" class="form-control" rows="2"></textarea></div>
                        <div class="mb-3"><label class="form-label fw-bold small">Image</label><input type="file"
                                name="image" class="form-control" accept="image/*" required></div>
                    </div>
                    <div class="modal-footer"><button type="button" class="btn btn-secondary"
                            data-bs-dismiss="modal">Close</button><button type="submit" class="btn btn-danger"
                            id="addBtn">Add Product</button></div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editProductModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h6 class="modal-title fw-bold">Edit Product</h6><button type="button"
                        class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form id="editProductForm" enctype="multipart/form-data">
                    <div class="modal-body"><input type="hidden" name="ajax_update_product" value="1"><input
                            type="hidden" name="edit_product_id" id="edit_id">
                        <div class="text-center mb-3"><img id="edit_img_preview" src="" class="modal-img-preview"
                                style="display:block; width:120px; margin:0 auto;"></div>
                        <div class="mb-3"><label class="form-label fw-bold small">Product Name</label><input type="text"
                                name="name" id="edit_name" class="form-control" required></div>
                        <div class="row g-2 mb-3">
                            <div class="col-6"><label class="form-label fw-bold small">Price (RM)</label><input
                                    type="number" step="0.01" min="0" name="price" id="edit_price" class="form-control"
                                    required></div>
                            <div class="col-6"><label class="form-label fw-bold small">Category</label><select
                                    name="category_id" id="edit_category"
                                    class="form-select"><?php echo $category_options; ?></select></div>
                        </div>
                        <div class="mb-3"><label class="form-label fw-bold small">Description</label><textarea
                                name="description" id="edit_desc" class="form-control" rows="2"></textarea></div>
                        <div class="mb-3"><label class="form-label fw-bold small">Change Image (Optional)</label><input
                                type="file" name="image" class="form-control" accept="image/*"></div>
                    </div>
                    <div class="modal-footer"><button type="button" class="btn btn-secondary"
                            data-bs-dismiss="modal">Close</button><button type="submit" class="btn btn-danger"
                            id="updateBtn">Save Changes</button></div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="Admin java script folder/manage_products.js"></script>
</body>


</html>
