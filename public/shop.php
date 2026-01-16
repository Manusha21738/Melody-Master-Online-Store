<?php
require_once '../config/db.php';
include 'includes/header.php';

// Filter by Category
$category_id = isset($_GET['category_id']) ? (int)$_GET['category_id'] : 0;
$where_clause = "";
if ($category_id > 0) {
    $where_clause = "WHERE category_id = $category_id";
}

// Fetch Products
$query = "SELECT * FROM products $where_clause ORDER BY name ASC";
$result = mysqli_query($conn, $query);

// Fetch Categories for Sidebar
$cat_query = "SELECT * FROM categories";
$cat_result = mysqli_query($conn, $cat_query);
?>

<div class="container mt-4">
    <div style="display: flex; gap: 30px;">
        
        <!-- Sidebar Filters -->
        <aside style="width: 250px; flex-shrink: 0;">
            <div class="card card-body">
                <h4>Categories</h4>
                <ul style="list-style: none; padding: 0;">
                    <li><a href="shop.php" style="<?php echo $category_id == 0 ? 'font-weight:bold; color:var(--primary-color);' : ''; ?>">All Products</a></li>
                    <?php while($cat = mysqli_fetch_assoc($cat_result)): ?>
                        <li>
                            <a href="shop.php?category_id=<?php echo $cat['category_id']; ?>" 
                               style="<?php echo $category_id == $cat['category_id'] ? 'font-weight:bold; color:var(--primary-color);' : ''; ?>">
                                <?php echo htmlspecialchars($cat['name']); ?>
                            </a>
                        </li>
                    <?php endwhile; ?>
                </ul>
            </div>
        </aside>

        <!-- Product Grid -->
        <main style="flex-grow: 1;">
            <h2>Shop Instruments</h2>
            <?php if(mysqli_num_rows($result) > 0): ?>
                <div class="grid">
                    <?php while($prod = mysqli_fetch_assoc($result)): ?>
                        <div class="card">
                            <img src="<?php echo $prod['image_url'] ? htmlspecialchars($prod['image_url']) : 'assets/images/placeholder.jpg'; ?>" class="card-img-top">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo htmlspecialchars($prod['name']); ?></h5>
                                <span class="price">£<?php echo number_format($prod['price'], 2); ?></span>
                                <a href="product.php?id=<?php echo $prod['product_id']; ?>" class="btn" style="width:100%; text-align:center;">View Details</a>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <div class="alert alert-danger">No products found in this category.</div>
            <?php endif; ?>
        </main>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
