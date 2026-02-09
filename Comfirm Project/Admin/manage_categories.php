<?php
session_start();
include "../database.php";

if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

if (isset($_POST['ajax_add_category'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);
    $check = mysqli_query($conn, "SELECT category_id FROM categories WHERE category_name = '$name'");
    if (mysqli_num_rows($check) > 0) {
        echo "error: Category already exists!";
        exit();
    }
    $sql = "INSERT INTO categories (category_name, category_status) VALUES ('$name', '$status')";
    echo mysqli_query($conn, $sql) ? "success" : "error: " . mysqli_error($conn);
    exit();
}

if (isset($_POST['ajax_toggle_status'])) {
    $id = mysqli_real_escape_string($conn, $_POST['category_id']);
    $current_status = mysqli_real_escape_string($conn, $_POST['current_status']);
    $new_status = ($current_status == 'Active') ? 'Inactive' : 'Active';
    $sql = "UPDATE categories SET category_status='$new_status' WHERE category_id='$id'";
    echo mysqli_query($conn, $sql) ? "success" : "error: " . mysqli_error($conn);
    exit();
}

if (isset($_POST['ajax_update_category'])) {
    $id = mysqli_real_escape_string($conn, $_POST['category_id']);
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $check = mysqli_query($conn, "SELECT category_id FROM categories WHERE category_name = '$name' AND category_id != '$id'");
    if (mysqli_num_rows($check) > 0) {
        echo "error: Category name already exists!";
        exit();
    }
    $sql = "UPDATE categories SET category_name='$name' WHERE category_id='$id'";
    echo mysqli_query($conn, $sql) ? "success" : "error: " . mysqli_error($conn);
    exit();
}

$result_cat = mysqli_query($conn, "SELECT * FROM categories ORDER BY category_id ASC");
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title> Manage Categories - OKS Admin </title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="Admin css folder/manage_categories.css">
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
                    <li class="nav-item"><a class="nav-link" href="manage_orders.php">Manage Orders</a></li>
                    <li class="nav-item"><a class="nav-link" href="manage_products.php">Manage Product</a></li>
                    <li class="nav-item"><a class="nav-link active" href="manage_categories.php">Manage Categories</a>
                    </li>
                    <li class="nav-item"><a class="nav-link" href="manage_users.php">Manage Users</a></li>
                </ul>
                <div class="mt-auto border-top border-secondary pt-3"><a href="../logout.php" class="logout-link"><i
                            class="bi bi-box-arrow-left me-2"></i>Logout</a></div>
            </nav>

            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between align-items-center mb-4 pt-3">
                    <h2 class="fw-bold" style="color: #1e293b;">Manage Categories</h2>
                    <span class="text-muted">Welcome, <strong><?php echo $_SESSION['username']; ?></strong></span>
                </div>
                <div class="d-flex justify-content-end mb-3"><button class="btn btn-danger" data-bs-toggle="modal"
                        data-bs-target="#addCategoryModal"><i class="bi bi-plus-lg me-1"></i> Add Category</button>
                </div>

                <div class="card shadow-sm border-0 rounded-3">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 15%;">ID</th>
                                        <th style="width: 45%;">Category Name</th>
                                        <th style="width: 20%;" class="text-center">Status</th>
                                        <th style="width: 20%;" class="text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if ($result_cat && mysqli_num_rows($result_cat) > 0) {
                                        while ($row = mysqli_fetch_assoc($result_cat)) {
                                            $status = $row['category_status'] ?? 'Active';
                                            $statusBadgeClass = ($status == 'Active') ? 'status-active' : 'status-inactive';
                                            ?>
                                            <tr>
                                                <td class="text-muted fw-bold">C<?php echo $row['category_id']; ?></td>
                                                <td class="fw-bold cell-name">
                                                    <?php echo htmlspecialchars($row['category_name']); ?></td>
                                                <td class="text-center"><span
                                                        class="badge rounded-pill status-btn <?php echo $statusBadgeClass; ?> toggle-status-btn"
                                                        data-id="<?php echo $row['category_id']; ?>"
                                                        data-status="<?php echo $status; ?>"><?php echo $status; ?></span></td>
                                                <td class="text-end"><button type="button"
                                                        class="btn btn-sm btn-outline-danger btn-action edit-btn"
                                                        data-bs-toggle="modal" data-bs-target="#editCategoryModal"
                                                        data-id="<?php echo $row['category_id']; ?>"
                                                        data-name="<?php echo htmlspecialchars($row['category_name']); ?>"><i
                                                            class="bi bi-pencil-square"></i> Edit</button></td>
                                            </tr>
                                        <?php }
                                    } else {
                                        echo "<tr><td colspan='4' class='text-center py-4'>No categories found.</td></tr>";
                                    } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <div class="modal fade" id="addCategoryModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h6 class="modal-title fw-bold"><i class="bi bi-tag me-2"></i>Add New Category</h6><button
                        type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form id="addCategoryForm">
                    <div class="modal-body"><input type="hidden" name="ajax_add_category" value="1">
                        <div class="mb-3"><label class="form-label fw-bold small">Category Name</label><input
                                type="text" name="name" class="form-control" placeholder="e.g. Set Meal" required></div>
                        <div class="mb-2"><label class="form-label fw-bold small">Status</label><select name="status"
                                class="form-select">
                                <option value="Active">Active (Show in Menu)</option>
                                <option value="Inactive">Inactive (Hide from Menu)</option>
                            </select></div>
                    </div>
                    <div class="modal-footer"><button type="button" class="btn btn-secondary"
                            data-bs-dismiss="modal">Close</button><button type="submit" class="btn btn-danger"
                            id="addBtn">Add Category</button></div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editCategoryModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h6 class="modal-title fw-bold"><i class="bi bi-pencil-square me-2"></i>Edit Category</h6><button
                        type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form id="editCategoryForm">
                    <div class="modal-body"><input type="hidden" name="ajax_update_category" value="1"><input
                            type="hidden" name="category_id" id="edit_id">
                        <div class="mb-3"><label class="form-label fw-bold small">Category Name</label><input
                                type="text" name="name" id="edit_name" class="form-control" required></div>
                    </div>
                    <div class="modal-footer"><button type="button" class="btn btn-secondary"
                            data-bs-dismiss="modal">Close</button><button type="submit" class="btn btn-danger"
                            id="updateBtn">Save Changes</button></div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="Admin java script folder/manage_categories.js"></script>
</body>


</html>
