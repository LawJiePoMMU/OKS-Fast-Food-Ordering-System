<?php
session_start();
include "../database.php";

if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

if (isset($_POST['ajax_add_admin'])) {
    $first_name = mysqli_real_escape_string($conn, $_POST['first_name']);
    $last_name = mysqli_real_escape_string($conn, $_POST['last_name']);
    $full_name = $first_name . " " . $last_name;

    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $mobile_input = mysqli_real_escape_string($conn, $_POST['mobile']);
    $mobile = "+60" . $mobile_input;
    $password = $_POST['password'];
    $address = mysqli_real_escape_string($conn, $_POST['address']);

    $check = mysqli_query($conn, "SELECT user_id FROM users WHERE user_email = '$email'");
    if (mysqli_num_rows($check) > 0) {
        echo "error: Email already exists!";
        exit();
    }

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $sql = "INSERT INTO users (username, user_email, user_password, user_mobile, user_address, user_role, user_status) 
            VALUES ('$full_name', '$email', '$hashed_password', '$mobile', '$address', 'admin', 'Active')";

    if (mysqli_query($conn, $sql)) {
        echo "success";
    } else {
        echo "error: " . mysqli_error($conn);
    }
    exit();
}

if (isset($_POST['ajax_update'])) {
    $id = mysqli_real_escape_string($conn, $_POST['edit_user_id']);
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);

    $mobile_input = mysqli_real_escape_string($conn, $_POST['mobile']);
    $mobile = "+60" . $mobile_input;

    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $role = mysqli_real_escape_string($conn, $_POST['role']);

    if ($id == $_SESSION['user_id']) {
        $role = 'admin';
    }

    $check_email_sql = "SELECT user_id FROM users WHERE user_email = '$email' AND user_id != '$id'";
    $check_result = mysqli_query($conn, $check_email_sql);

    if (mysqli_num_rows($check_result) > 0) {
        echo "error: Email already exists!";
        exit();
    }

    $sql_update = "UPDATE users SET 
                   username='$username', 
                   user_email='$email', 
                   user_mobile='$mobile', 
                   user_address='$address', 
                   user_role='$role' 
                   WHERE user_id='$id'";

    if (mysqli_query($conn, $sql_update)) {
        echo "success";
    } else {
        echo "error: " . mysqli_error($conn);
    }
    exit();
}

if (isset($_POST['ajax_toggle_status'])) {
    $id = mysqli_real_escape_string($conn, $_POST['user_id']);
    $current_status = mysqli_real_escape_string($conn, $_POST['current_status']);

    if ($id == $_SESSION['user_id']) {
        echo "error: You cannot change your own status!";
        exit();
    }

    $new_status = ($current_status == 'Active') ? 'Inactive' : 'Active';

    $sql = "UPDATE users SET user_status='$new_status' WHERE user_id='$id'";
    if (mysqli_query($conn, $sql)) {
        echo "success";
    } else {
        echo "error: " . mysqli_error($conn);
    }
    exit();
}

$search = "";
$sql_users = "SELECT * FROM users ORDER BY user_id ASC";

if (isset($_GET['search'])) {
    $search = mysqli_real_escape_string($conn, $_GET['search']);
    if (!empty($search)) {
        $sql_users = "SELECT * FROM users WHERE 
                      user_id = '$search' OR 
                      username LIKE '%$search%'";
    }
}
$result_users = mysqli_query($conn, $sql_users);
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Manage Users - OKS Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="Admin css folder/manage_users.css">
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
                    <li class="nav-item"><a class="nav-link" href="manage_products.php">Manage Product</a></li>
                    <li class="nav-item"><a class="nav-link" href="manage_categories.php">Manage Categories</a></li>
                    <li class="nav-item"><a class="nav-link active" href="manage_users.php">Manage Users</a></li>
                </ul>
                <div class="mt-auto border-top border-secondary pt-3">
                    <a href="../logout.php" class="logout-link"><i class="bi bi-box-arrow-left me-2"></i>Logout</a>
                </div>
            </nav>

            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between align-items-center mb-4 pt-3">
                    <h2 class="fw-bold" style="color: #1e293b;">Manage Users</h2>
                    <span class="text-muted">Welcome, <strong><?php echo $_SESSION['username']; ?></strong></span>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <form action="" method="GET" class="d-flex">
                            <input type="text" name="search" class="form-control me-2"
                                placeholder="Search ID or Name..." value="<?php echo htmlspecialchars($search); ?>">
                            <button class="btn btn-danger" type="submit">Search</button>
                            <?php if (!empty($search)): ?>
                                <a href="manage_users.php" class="btn btn-secondary ms-2">Reset</a>
                            <?php endif; ?>
                        </form>
                    </div>
                    <div class="col-md-6 text-end">
                        <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#addAdminModal">
                            <i class="bi bi-person-plus-fill me-1"></i> Add New Admin
                        </button>
                    </div>
                </div>

                <div class="card shadow-sm border-0 rounded-3">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>ID</th>
                                        <th>Username</th>
                                        <th>Email</th>
                                        <th>Mobile</th>
                                        <th>Address</th>
                                        <th>Role</th>
                                        <th class="text-center">Status</th>
                                        <th class="text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if ($result_users && mysqli_num_rows($result_users) > 0) {
                                        while ($row = mysqli_fetch_assoc($result_users)) {
                                            $status = $row['user_status'] ?? 'Active';
                                            $statusBadgeClass = ($status == 'Active') ? 'status-active' : 'status-inactive';
                                            $isCurrentUser = ($row['user_id'] == $_SESSION['user_id']);
                                            ?>
                                            <tr id="row-<?php echo $row['user_id']; ?>">
                                                <td class="text-muted fw-bold">#<?php echo $row['user_id']; ?></td>
                                                <td class="fw-bold cell-name"><?php echo htmlspecialchars($row['username']); ?>
                                                </td>
                                                <td class="cell-email"><?php echo htmlspecialchars($row['user_email']); ?></td>
                                                <td class="cell-mobile"><?php echo htmlspecialchars($row['user_mobile']); ?>
                                                </td>
                                                <td class="cell-address address-col"
                                                    title="<?php echo htmlspecialchars($row['user_address'] ?? ''); ?>">
                                                    <?php echo htmlspecialchars($row['user_address'] ?? '-'); ?>
                                                </td>
                                                <td class="cell-role">
                                                    <?php if ($row['user_role'] == 'admin'): ?>
                                                        <span class="badge badge-admin">Admin</span>
                                                    <?php else: ?>
                                                        <span class="badge badge-user">Customer</span>
                                                    <?php endif; ?>
                                                </td>

                                                <td class="text-center">
                                                    <?php if ($isCurrentUser): ?>
                                                        <span class="badge rounded-pill status-active status-disabled">Active</span>
                                                    <?php else: ?>
                                                        <span
                                                            class="badge rounded-pill status-btn <?php echo $statusBadgeClass; ?> toggle-status-btn"
                                                            data-id="<?php echo $row['user_id']; ?>"
                                                            data-status="<?php echo $status; ?>">
                                                            <?php echo $status; ?>
                                                        </span>
                                                    <?php endif; ?>
                                                </td>

                                                <td class="text-end">
                                                    <?php if ($isCurrentUser): ?>
                                                        <span class="text-muted small fst-italic me-2">Current User</span>
                                                    <?php else: ?>
                                                        <button type="button"
                                                            class="btn btn-sm btn-outline-primary btn-action edit-btn"
                                                            data-bs-toggle="modal" data-bs-target="#editUserModal"
                                                            data-id="<?php echo $row['user_id']; ?>"
                                                            data-username="<?php echo htmlspecialchars($row['username']); ?>"
                                                            data-email="<?php echo htmlspecialchars($row['user_email']); ?>"
                                                            data-mobile="<?php echo htmlspecialchars($row['user_mobile']); ?>"
                                                            data-address="<?php echo htmlspecialchars($row['user_address'] ?? ''); ?>"
                                                            data-role="<?php echo $row['user_role']; ?>">
                                                            <i class="fas fa-edit"></i> Edit
                                                        </button>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php }
                                    } else {
                                        echo "<tr><td colspan='8' class='text-center py-4'>No users found.</td></tr>";
                                    } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <div class="modal fade" id="editUserModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white py-2">
                    <h6 class="modal-title fw-bold"><i class="fas fa-user-edit me-2"></i>Edit User</h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form id="editUserForm">
                    <div class="modal-body">
                        <input type="hidden" name="edit_user_id" id="modal_user_id">
                        <input type="hidden" name="ajax_update" value="1">

                        <div class="row g-2 mb-2">
                            <div class="col-6">
                                <label class="form-label fw-bold small">Username</label>
                                <input type="text" name="username" id="modal_username"
                                    class="form-control form-control-sm" required>
                            </div>
                            <div class="col-6">
                                <label class="form-label fw-bold small text-danger">Role</label>
                                <select name="role" id="modal_role" class="form-select form-select-sm border-danger">
                                    <option value="customer">Customer</option>
                                    <option value="admin">Admin</option>
                                </select>
                            </div>
                        </div>
                        <div class="row g-2 mb-2">
                            <div class="col-6">
                                <label class="form-label fw-bold small">Email</label>
                                <input type="email" name="email" id="modal_email" class="form-control form-control-sm"
                                    required>
                            </div>
                            <div class="col-6">
                                <label class="form-label fw-bold small">Mobile</label>
                                <div class="mobile-group">
                                    <span class="mobile-prefix">+60</span>
                                    <input type="text" name="mobile" id="modal_mobile" class="mobile-input" required
                                        oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 10)">
                                </div>
                            </div>
                        </div>
                        <div class="mb-2">
                            <label class="form-label fw-bold small">Address</label>
                            <textarea name="address" id="modal_address" class="form-control form-control-sm" rows="2"
                                required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer py-1">
                        <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-sm btn-danger" id="saveBtn">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="addAdminModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white py-2">
                    <h6 class="modal-title fw-bold"><i class="bi bi-shield-lock-fill me-2"></i>Add New Admin</h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form id="addAdminForm">
                    <div class="modal-body">
                        <input type="hidden" name="ajax_add_admin" value="1">

                        <div class="row g-2 mb-2">
                            <div class="col-6">
                                <label class="form-label fw-bold small">First Name</label>
                                <input type="text" name="first_name" class="form-control form-control-sm" required>
                            </div>
                            <div class="col-6">
                                <label class="form-label fw-bold small">Last Name</label>
                                <input type="text" name="last_name" class="form-control form-control-sm" required>
                            </div>
                        </div>

                        <div class="row g-2 mb-2">
                            <div class="col-6">
                                <label class="form-label fw-bold small">Email</label>
                                <input type="email" name="email" class="form-control form-control-sm"
                                    placeholder="email@gmail.com" required>
                            </div>
                            <div class="col-6">
                                <label class="form-label fw-bold small">Mobile</label>
                                <div class="mobile-group">
                                    <span class="mobile-prefix">+60</span>
                                    <input type="text" name="mobile" class="mobile-input" placeholder="123456789"
                                        required oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 10)">
                                </div>
                            </div>
                        </div>

                        <div class="mb-2 position-relative">
                            <label class="form-label fw-bold small">Password</label>
                            <input type="password" name="password" id="new_pass" class="form-control form-control-sm"
                                required minlength="6">
                            <i class="bi bi-eye-slash position-absolute top-50 end-0 translate-middle-y me-3 cursor-pointer"
                                style="cursor: pointer; margin-top: 10px;" onclick="togglePass('new_pass')"></i>
                        </div>

                        <div class="mb-2">
                            <label class="form-label fw-bold small">Address (Optional)</label>
                            <textarea name="address" class="form-control form-control-sm" rows="2"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer py-1">
                        <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-sm btn-danger" id="addAdminBtn">Create Admin</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="Admin java script folder/manage_users.js"></script>

</body>

</html>