<?php
require_once '../config/db.php';
include 'includes/header.php';

// Fetch Categories
$cat_query = "SELECT * FROM categories LIMIT 6";
$cat_result = mysqli_query($conn, $cat_query);

// Fetch Featured Products (Just random/latest 4 for now)
$prod_query = "SELECT * FROM products ORDER BY created_at DESC LIMIT 4";
$prod_result = mysqli_query($conn, $prod_query);
?>

<!-- Hero Section -->
<section class="hero">
    <div class="container">
        <h1>Welcome to Melody Masters</h1>
        <p>Your one-stop shop for professional musical instruments and accessories.</p>
        <a href="shop.php" class="btn">Shop Now</a>
    </div>
</section>

<!-- Categories Section -->
<section class="container mt-4">
    <h2 class="text-center">Browse Categories</h2>
    <div class="grid">
        <?php while($cat = mysqli_fetch_assoc($cat_result)): ?>
            <div class="card text-center" style="padding: 20px;">
                <h3><?php echo htmlspecialchars($cat['name']); ?></h3>
                <p><?php echo htmlspecialchars($cat['description']); ?></p>
                <a href="shop.php?category_id=<?php echo $cat['category_id']; ?>" class="btn btn-secondary">View</a>
            </div>
        <?php endwhile; ?>
    </div>
</section>

<!-- Featured Products Section -->
<section class="container mt-4 mb-4">
    <h2 class="text-center">Featured Products</h2>
    <div class="grid">
        <?php while($prod = mysqli_fetch_assoc($prod_result)): ?>
            <div class="card">
                <?php if($prod['image_url']): ?>
                    <img src="<?php echo htmlspecialchars($prod['image_url']); ?>" alt="<?php echo htmlspecialchars($prod['name']); ?>" class="card-img-top">
                <?php else: ?>
                    <img src="assets/images/placeholder.jpg" class="card-img-top">
                <?php endif; ?>
                <div class="card-body">
                    <h5 class="card-title"><?php echo htmlspecialchars($prod['name']); ?></h5>
                    <span class="price">£<?php echo number_format($prod['price'], 2); ?></span>
                    <a href="product.php?id=<?php echo $prod['product_id']; ?>" class="btn">View Details</a>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
