<?php
session_start();
include "database.php";
if (isset($_POST['add_to_cart'])) {
    if (!isset($_SESSION['user_id'])) {
        echo "not_logged_in";
        exit();
    }

    $u_id = $_SESSION['user_id'];
    $p_id = $_POST['product_id'];
    $qty = isset($_POST['quantity']) ? intval($_POST['quantity']) : 1;

    $check_cart = mysqli_query($conn, "SELECT * FROM cart WHERE user_id='$u_id' AND product_id='$p_id'");

    if (mysqli_num_rows($check_cart) > 0) {
        mysqli_query($conn, "UPDATE cart SET cart_quantity = cart_quantity + $qty WHERE user_id='$u_id' AND product_id='$p_id'");
    } else {
        mysqli_query($conn, "INSERT INTO cart (user_id, product_id, cart_quantity) VALUES ('$u_id', '$p_id', '$qty')");
    }
    echo "success";
    exit();
}
$category_filter = isset($_GET['category']) ? $_GET['category'] : 'All';
$search_query = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
$sql_products = "SELECT * FROM products WHERE product_status = 'Active'";

if ($category_filter != 'All') {
    $safe_cat_id = mysqli_real_escape_string($conn, $category_filter);
    $sql_products .= " AND category_id = '$safe_cat_id'";
}

if ($search_query != '') {
    $sql_products .= " AND product_name LIKE '%$search_query%'";
}

$sql_products .= " ORDER BY product_id ASC";
$result_products = mysqli_query($conn, $sql_products);
$categories = mysqli_query($conn, "SELECT * FROM categories WHERE category_status='Active'");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu page - OKS Fast Food</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="css folder/menu.css">
</head>

<body>

    <?php include 'header.php'; ?>

    <div class="hero-banner">
        <h1 class="fw-bold display-4">Order Your Favorites</h1>
        <p class="fs-5">Click "Add Item" to customize your order!</p>

        <form class="d-flex justify-content-center mt-4" action="" method="GET"
            style="max-width: 500px; margin: 0 auto;">
            <input class="form-control rounded-pill px-4 py-2" type="text" name="search" placeholder="Search..."
                value="<?php echo htmlspecialchars($search_query); ?>">
            <button class="btn btn-warning rounded-pill ms-2 fw-bold px-4" type="submit">Search</button>
        </form>
    </div>

    <div class="container mb-5">
        <div class="d-flex flex-wrap justify-content-center mb-5">
            <a href="menu.php" class="cat-btn <?php echo ($category_filter == 'All') ? 'active' : ''; ?>">All</a>
            <?php
            if ($categories && mysqli_num_rows($categories) > 0) {
                while ($cat = mysqli_fetch_assoc($categories)): ?>
                    <a href="menu.php?category=<?php echo $cat['category_id']; ?>"
                        class="cat-btn <?php echo ($category_filter == $cat['category_id']) ? 'active' : ''; ?>">
                        <?php echo htmlspecialchars($cat['category_name']); ?>
                    </a>
                <?php endwhile;
            }
            ?>
        </div>

        <div class="row g-4">
            <?php if ($result_products && mysqli_num_rows($result_products) > 0): ?>
                <?php while ($row = mysqli_fetch_assoc($result_products)):
                    $img = !empty($row['product_image']) ? "uploads/" . $row['product_image'] : "https://via.placeholder.com/300x200?text=No+Image";
                    ?>
                    <div class="col-6 col-md-4 col-lg-3">
                        <div class="product-card" onclick="openProductModal(
                            '<?php echo $row['product_id']; ?>',
                            '<?php echo addslashes($row['product_name']); ?>',
                            '<?php echo $row['product_price']; ?>',
                            '<?php echo addslashes(str_replace(array("\r", "\n"), '', $row['product_description'])); ?>',
                            '<?php echo $img; ?>'
                         )">
                            <img src="<?php echo $img; ?>" class="product-img" alt="Food">
                            <div class="card-body">
                                <div>
                                    <h5 class="fw-bold mb-1 text-truncate"><?php echo htmlspecialchars($row['product_name']); ?>
                                    </h5>
                                    <div class="price-tag">RM <?php echo number_format($row['product_price'], 2); ?></div>
                                </div>
                                <button class="btn-add-item">
                                    <i class="fas fa-plus-circle me-1"></i> Add Item
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="col-12 text-center py-5">
                    <h3 class="text-muted">No products found.</h3>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="modal fade" id="productModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0 pb-0">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body pt-0">
                    <div class="text-center mb-3">
                        <img id="modal_img" src="" class="modal-img">
                    </div>

                    <h3 id="modal_name" class="fw-bold mb-1">Product Name</h3>
                    <h4 class="text-danger fw-bold mb-3">RM <span id="modal_price">0.00</span></h4>

                    <div id="modal_desc">Description...</div>

                    <div class="d-flex align-items-center mb-4 justify-content-center bg-light p-2 rounded">
                        <span class="fw-bold me-3">Quantity:</span>
                        <div class="input-group" style="width: 130px;">
                            <button class="btn btn-white border" type="button" onclick="changeQty(-1)">-</button>
                            <input type="text" id="modal_qty" class="form-control text-center bg-white" value="1"
                                readonly>
                            <button class="btn btn-white border" type="button" onclick="changeQty(1)">+</button>
                        </div>
                    </div>

                    <input type="hidden" id="modal_product_id">

                    <button class="btn btn-danger w-100 fw-bold py-3" style="background-color: #D50032;"
                        onclick="confirmAddToCart()">
                        Add to Cart
                    </button>
                </div>
            </div>
        </div>
    </div>

    <?php include 'footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="java script folder/menu.js"></script>
</body>


</html>
