<?php
require_once '../config/db.php';
include 'includes/header.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    die("<div class='container mt-4 alert alert-danger'>Access Denied.</div>");
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $price = (float)$_POST['price'];
    $category_id = (int)$_POST['category_id'];
    $stock = (int)$_POST['stock'];
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $is_digital = isset($_POST['is_digital']) ? 1 : 0;
    
    $update_image_sql = "";
    
    // Handle File Upload if new image provided
    if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] == 0) {
        $upload_dir = 'assets/images/';
        $filename = uniqid() . '_' . basename($_FILES['product_image']['name']);
        $target_file = $upload_dir . $filename;
        $absolute_target = __DIR__ . '/' . $target_file;
        
        if (move_uploaded_file($_FILES['product_image']['tmp_name'], $absolute_target)) {
            $update_image_sql = ", image_url = '$target_file'";
        } else {
            $message = "File upload failed!";
        }
    }
    
    $query = "UPDATE products SET 
                name = '$name', 
                price = '$price', 
                category_id = '$category_id', 
                stock_quantity = '$stock', 
                description = '$description', 
                is_digital = '$is_digital'
                $update_image_sql 
              WHERE product_id = $id";
    
    if (empty($message) && mysqli_query($conn, $query)) {
        $message = "Product updated successfully!";
    } else if (empty($message)) {
        $message = "Error: " . mysqli_error($conn);
    }
}

// Fetch existing data
$prod_query = "SELECT * FROM products WHERE product_id = $id";
$prod_res = mysqli_query($conn, $prod_query);
$product = mysqli_fetch_assoc($prod_res);

if (!$product) {
    echo "<div class='container mt-4 alert alert-danger'>Product not found.</div>";
    include 'includes/footer.php';
    exit;
}

// Fetch Categories
$cats = mysqli_query($conn, "SELECT * FROM categories");
?>

<div class="container mt-4">
    <div class="auth-container" style="max-width: 600px;">
        <h2>Edit Product</h2>
        <a href="admin_manage_products.php">&larr; Back to Manage Products</a>
        <hr>
        
        <?php if($message): ?>
            <div class="alert alert-info"><?php echo $message; ?></div>
        <?php endif; ?>

        <form method="POST" action="edit_product.php?id=<?php echo $id; ?>" enctype="multipart/form-data">
            <div class="form-group">
                <label>Product Name</label>
                <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($product['name']); ?>" required>
            </div>
            <div style="display: flex; gap: 20px;">
                <div class="form-group" style="flex: 1;">
                    <label>Price (Rs. )</label>
                    <input type="number" step="0.01" name="price" class="form-control" value="<?php echo $product['price']; ?>" required>
                </div>
                <div class="form-group" style="flex: 1;">
                    <label>Stock</label>
                    <input type="number" name="stock" class="form-control" value="<?php echo $product['stock_quantity']; ?>" required>
                </div>
            </div>
            <div class="form-group">
                <label>Category</label>
                <select name="category_id" class="form-control">
                    <?php while($c = mysqli_fetch_assoc($cats)): ?>
                        <option value="<?php echo $c['category_id']; ?>" <?php echo $product['category_id'] == $c['category_id'] ? 'selected' : ''; ?>>
                            <?php echo $c['name']; ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Description</label>
                <textarea name="description" class="form-control" rows="3"><?php echo htmlspecialchars($product['description']); ?></textarea>
            </div>
            <div class="form-group">
                <label>Current Image</label><br>
                <?php if($product['image_url']): ?>
                    <img src="<?php echo htmlspecialchars($product['image_url']); ?>" style="width: 100px; border-radius: 4px; border: 1px solid #ddd; margin-bottom: 10px;">
                <?php endif; ?>
                <br>
                <label>Upload New Product Image (Optional)</label>
                <input type="file" name="product_image" class="form-control" accept="image/*">
                <small style="color: grey;">Leave blank to keep existing image.</small>
            </div>
            <div class="form-group">
                <label>
                    <input type="checkbox" name="is_digital" value="1" <?php echo $product['is_digital'] ? 'checked' : ''; ?>> Is Digital Product?
                </label>
            </div>
            <button type="submit" class="btn" style="width: 100%;">Update Product</button>
        </form>
    </div>
</div>

<?php include 'includes/footer.php'; ?>

