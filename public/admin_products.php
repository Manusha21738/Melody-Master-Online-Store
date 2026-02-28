<?php
require_once '../config/db.php';
include 'includes/header.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    die("<div class='container mt-4 alert alert-danger'>Access Denied.</div>");
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $price = (float)$_POST['price'];
    $category_id = (int)$_POST['category_id'];
    $stock = (int)$_POST['stock'];
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $image_url_to_save = 'assets/images/placeholder.jpg'; // default
    
    // Handle File Upload
    if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] == 0) {
        $upload_dir = 'assets/images/';
        $filename = uniqid() . '_' . basename($_FILES['product_image']['name']);
        $target_file = $upload_dir . $filename;
        
        // Ensure absolute path mapping if needed, but local relative works for moves in standard setups
        $absolute_target = __DIR__ . '/' . $target_file;
        
        if (move_uploaded_file($_FILES['product_image']['tmp_name'], $absolute_target)) {
            $image_url_to_save = $target_file;
        } else {
            $message = "File upload failed!";
        }
    }
    
    $query = "INSERT INTO products (name, price, category_id, stock_quantity, description, image_url, is_digital) 
              VALUES ('$name', '$price', '$category_id', '$stock', '$description', '$image_url_to_save', '$is_digital')";
    
    if (empty($message) && mysqli_query($conn, $query)) {
        $message = "Product added successfully!";
    } else if (empty($message)) {
        $message = "Error: " . mysqli_error($conn);
    }
}

// Fetch Categories
$cats = mysqli_query($conn, "SELECT * FROM categories");
?>

<div class="container mt-4">
    <div class="auth-container" style="max-width: 600px;">
        <h2>Add New Product</h2>
        <a href="admin_dashboard.php">&larr; Back to Dashboard</a>
        <hr>
        
        <?php if($message): ?>
            <div class="alert alert-info"><?php echo $message; ?></div>
        <?php endif; ?>

        <form method="POST" action="" enctype="multipart/form-data">
            <div class="form-group">
                <label>Product Name</label>
                <input type="text" name="name" class="form-control" required>
            </div>
            <div style="display: flex; gap: 20px;">
                <div class="form-group" style="flex: 1;">
                    <label>Price (Rs. )</label>
                    <input type="number" step="0.01" name="price" class="form-control" required>
                </div>
                <div class="form-group" style="flex: 1;">
                    <label>Stock</label>
                    <input type="number" name="stock" class="form-control" required>
                </div>
            </div>
            <div class="form-group">
                <label>Category</label>
                <select name="category_id" class="form-control">
                    <?php while($c = mysqli_fetch_assoc($cats)): ?>
                        <option value="<?php echo $c['category_id']; ?>"><?php echo $c['name']; ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Description</label>
                <textarea name="description" class="form-control" rows="3"></textarea>
            </div>
            <div class="form-group">
                <label>Upload Product Image</label>
                <input type="file" name="product_image" class="form-control" accept="image/*">
                <small style="color: grey;">Leaves blank to use default placeholder.</small>
            </div>
            <div class="form-group">
                <label>
                    <input type="checkbox" name="is_digital" value="1"> Is Digital Product?
                </label>
            </div>
            <button type="submit" class="btn" style="width: 100%;">Add Product</button>
        </form>
    </div>
</div>

<?php include 'includes/footer.php'; ?>

