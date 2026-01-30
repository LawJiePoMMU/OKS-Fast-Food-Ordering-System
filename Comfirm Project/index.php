<?php
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

if (session_status() === PHP_SESSION_NONE) {
  session_start();
}
include 'database.php';
$sql_categories = "SELECT * FROM categories WHERE category_status = 'Active' ORDER BY category_id ASC";
$res_categories = mysqli_query($conn, $sql_categories);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Home - OKS Fast Food</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <link rel="stylesheet" href="css folder/index.css">
</head>
<body>

  <?php include 'header.php'; ?>

  <section class="hero-section">
    <div class="hero-content">
      <h1 class="reveal">TASTE THE <span class="text-danger">PERFECTION</span></h1>
      <p class="reveal">Freshly prepared, fastly delivered. Experience the OKS standard.</p>
      <a href="menu.php" class="btn-hero">ORDER NOW</a>
    </div>
  </section>

  <div class="container my-5">
    <?php if ($res_categories && mysqli_num_rows($res_categories) > 0): ?>
      <?php while ($cat = mysqli_fetch_assoc($res_categories)): 
          $current_cat_id = $cat['category_id'];
          $current_cat_name = $cat['category_name']; 
          $sql_products = "SELECT * FROM products WHERE category_id = '$current_cat_id' AND product_status = 'Active' LIMIT 4";
          $res_products = mysqli_query($conn, $sql_products);

          if (mysqli_num_rows($res_products) > 0):
      ?>
        
        <div class="category-block mb-5 reveal">
          <h2 class="category-title text-uppercase"><?php echo htmlspecialchars($current_cat_name); ?></h2>
          
          <div class="row g-4 mt-2"> <?php while ($product = mysqli_fetch_assoc($res_products)): 
                $img = !empty($product['product_image']) ? "uploads/" . $product['product_image'] : "images/no-image.png";
            ?>
              <div class="col-6 col-lg-3"> <a href="menu.php" class="text-decoration-none">
                  <div class="product-card-simple">
                    <div class="img-container">
                      <img src="<?php echo $img; ?>" alt="<?php echo htmlspecialchars($product['product_name']); ?>">
                    </div>
                    <div class="product-info mt-3">
                      <h5 class="product-name text-truncate"><?php echo htmlspecialchars($product['product_name']); ?></h5>
                      <div class="product-tags">
                          <span class="badge bg-light text-muted border">NEW</span>
                      </div>
                      <div class="product-price-row mt-2">
                        <span class="price-now">RM <?php echo number_format($product['product_price'], 2); ?></span>
                      </div>
                    </div>
                  </div>
                </a>
              </div>
            <?php endwhile; ?>
          </div>
        </div>

      <?php endif; ?>
    <?php endwhile; ?>
    <?php endif; ?>
  </div>

  <section class="features-section">
    <div class="container">
      <div class="row g-4 justify-content-center text-center">
        <div class="col-md-4 reveal">
            <div class="feature-icon-box mb-3">
                <i class="fas fa-shipping-fast fa-3x text-danger"></i>
            </div>
            <h3 class="feature-title">Fast Delivery</h3>
            <p class="feature-text">We promise to deliver your food within 30 minutes, hot and fresh right to your doorstep.</p>
        </div>
        <div class="col-md-4 reveal">
            <div class="feature-icon-box mb-3">
                <i class="fas fa-hamburger fa-3x text-danger"></i>
            </div>
            <h3 class="feature-title">Fresh Ingredients</h3>
            <p class="feature-text">Our chefs use only the finest and freshest ingredients to craft your perfect meal.</p>
        </div>
        <div class="col-md-4 reveal">
            <div class="feature-icon-box mb-3">
                <i class="fas fa-headset fa-3x text-danger"></i>
            </div>
            <h3 class="feature-title">Customer Support</h3>
            <p class="feature-text">Got a question? Our support team is here to help you in day only</p>
        </div>
      </div>
    </div>
  </section>

  <?php include 'footer.php'; ?>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="java script folder/index.js"></script>
</body>
</html>