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
    <div class="product-details">
        <!-- Product Image -->
        <div class="product-image-container">
            <img src="<?php echo $product['image_url'] ? htmlspecialchars($product['image_url']) : 'assets/images/placeholder.jpg'; ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
        </div>

        <!-- Product Details -->
        <div class="product-info-container">
            <h1><?php echo htmlspecialchars($product['name']); ?></h1>
            <p class="price">Rs. <?php echo number_format($product['price'], 2); ?></p>
            
            <p><?php echo nl2br(htmlspecialchars($product['description'])); ?></p>
            
            <div style="margin: 20px 0;">
                <?php if($product['stock_quantity'] > 0): ?>
                    <span class="stock-status in-stock">In Stock (<?php echo $product['stock_quantity']; ?> available)</span>
                <?php else: ?>
                    <span class="stock-status out-of-stock">Out of Stock</span>
                <?php endif; ?>
                
                <?php if($product['is_digital']): ?>
                    <span class="stock-status digital-badge">Digital Download Product</span>
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
        // [Business Rule]: The system shall allow verified customers to submit product reviews.
        // We verify purchase by checking the orders table for this user and product.
        $can_review = false;
        if (isset($_SESSION['user_id'])) {
            $user_id = $_SESSION['user_id'];
            $check_query = "SELECT COUNT(*) as count FROM order_items oi JOIN orders o ON oi.order_id = o.order_id WHERE o.user_id = $user_id AND oi.product_id = $id";
            $check_result = mysqli_query($conn, $check_query);
            if ($check_result) {
                $check_row = mysqli_fetch_assoc($check_result);
                if ($check_row['count'] > 0) {
                    $can_review = true;
                }
            }
        }
        ?>

        <?php if($can_review): ?>
            <div class="card card-body mb-4" style="background-color: #fcfcfc;">
                <h4>Leave a Review</h4>
                <form action="review.php" method="POST">
                    <input type="hidden" name="product_id" value="<?php echo $id; ?>">
                    <div class="form-group">
                        <label>Rating:</label>
                        <select name="rating" class="form-control" style="width: auto; min-width: 150px;">
                            <option value="5">5 ˜…˜…˜…˜…˜… (Excellent)</option>
                            <option value="4">4 ˜…˜…˜…˜…˜† (Good)</option>
                            <option value="3">3 ˜…˜…˜…˜†˜† (Average)</option>
                            <option value="2">2 ˜…˜…˜†˜†˜† (Poor)</option>
                            <option value="1">1 ˜…˜†˜†˜†˜† (Terrible)</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Comment:</label>
                        <textarea name="comment" class="form-control" rows="3" required placeholder="Write your experience here..."></textarea>
                    </div>
                    <button type="submit" class="btn">Submit Review</button>
                </form>
            </div>
        <?php endif; ?>

        <?php
        $rev_query = "SELECT r.*, u.name FROM reviews r JOIN users u ON r.user_id = u.user_id WHERE product_id = $id ORDER BY created_at DESC";
        $rev_result = mysqli_query($conn, $rev_query);
        if(mysqli_num_rows($rev_result) > 0) {
            while($review = mysqli_fetch_assoc($rev_result)) {
                echo "<div class='review-card'>";
                echo "<strong>" . htmlspecialchars($review['name']) . "</strong><br>";
                echo "<span class='stars'>" . str_repeat("˜…", $review['rating']) . str_repeat("˜†", 5 - $review['rating']) . "</span>";
                echo "<p style='margin-top: 10px;'>" . htmlspecialchars($review['comment']) . "</p>";
                echo "<small style='color: #94a3b8; font-size: 0.85rem;'>" . date('F j, Y, g:i a', strtotime($review['created_at'])) . "</small>";
                echo "</div>";
            }
        } else {
            echo "<p>No reviews yet. Be the first to share your thoughts!</p>";
        }
        ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>

