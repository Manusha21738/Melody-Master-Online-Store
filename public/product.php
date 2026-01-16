<?php
require_once '../config/db.php';
include 'includes/header.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$query = "SELECT * FROM products WHERE product_id = $id";
$result = mysqli_query($conn, $query);
$product = mysqli_fetch_assoc($result);

if (!$product) {
    echo "<div class='container mt-4'><div class='alert alert-danger'>Product not found.</div></div>";
    include 'includes/footer.php';
    exit;
}
?>

<div class="container mt-4">
    <div style="display: flex; gap: 40px; flex-wrap: wrap;">
        <!-- Product Image -->
        <div style="flex: 1; min-width: 300px;">
            <img src="<?php echo $product['image_url'] ? htmlspecialchars($product['image_url']) : 'assets/images/placeholder.jpg'; ?>" 
                 style="width: 100%; border-radius: 8px; border: 1px solid #ddd;">
        </div>

        <!-- Product Details -->
        <div style="flex: 1; min-width: 300px;">
            <h1><?php echo htmlspecialchars($product['name']); ?></h1>
            <p class="price" style="font-size: 2rem;">£<?php echo number_format($product['price'], 2); ?></p>
            
            <p><?php echo nl2br(htmlspecialchars($product['description'])); ?></p>
            
            <div style="margin: 20px 0; padding: 15px; background: #eee; border-radius: 5px;">
                <?php if($product['stock_quantity'] > 0): ?>
                    <span style="color: green; font-weight: bold;">In Stock (<?php echo $product['stock_quantity']; ?> available)</span>
                <?php else: ?>
                    <span style="color: red; font-weight: bold;">Out of Stock</span>
                <?php endif; ?>
                
                <?php if($product['is_digital']): ?>
                    <br><span style="color: blue;">Digital Download Product</span>
                <?php endif; ?>
            </div>

            <?php if($product['stock_quantity'] > 0): ?>
                <form action="cart.php" method="POST">
                    <input type="hidden" name="action" value="add">
                    <input type="hidden" name="product_id" value="<?php echo $product['product_id']; ?>">
                    <div class="form-group" style="display: flex; gap: 10px; align-items: center;">
                        <label>Quantity:</label>
                        <input type="number" name="quantity" value="1" min="1" max="<?php echo $product['stock_quantity']; ?>" class="form-control" style="width: 80px;">
                        <button type="submit" class="btn">Add to Cart</button>
                    </div>
                </form>
            <?php endif; ?>
        </div>
    </div>

    <!-- Reviews / Specs Tabs (Simple Implementation) -->
    <div class="mt-4">
        <h3>Customer Reviews</h3>
        <hr>
        <?php
        $rev_query = "SELECT r.*, u.name FROM reviews r JOIN users u ON r.user_id = u.user_id WHERE product_id = $id ORDER BY created_at DESC";
        $rev_result = mysqli_query($conn, $rev_query);
        if(mysqli_num_rows($rev_result) > 0) {
            while($review = mysqli_fetch_assoc($rev_result)) {
                echo "<div class='card card-body mb-4'>";
                echo "<strong>" . htmlspecialchars($review['name']) . "</strong> ";
                echo str_repeat("★", $review['rating']) . str_repeat("☆", 5 - $review['rating']);
                echo "<p>" . htmlspecialchars($review['comment']) . "</p>";
                echo "<small  class='text-muted'>" . $review['created_at'] . "</small>";
                echo "</div>";
            }
        } else {
            echo "<p>No reviews yet.</p>";
        }
        ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
