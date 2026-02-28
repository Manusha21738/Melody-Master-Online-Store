<?php
require_once '../config/db.php';
include 'includes/header.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    die("<div class='container mt-4 alert alert-danger'>Access Denied. Admins Only.</div>");
}

$message = isset($_GET['msg']) ? htmlspecialchars($_GET['msg']) : '';

// Fetch all products
$query = "SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.category_id ORDER BY p.product_id DESC";
$products_res = mysqli_query($conn, $query);
?>

<div class="container mt-4">
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <h2>Manage Products</h2>
        <div>
            <a href="admin_products.php" class="btn">Add New Product</a>
            <a href="admin_dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
        </div>
    </div>
    <hr>
    
    <?php if($message): ?>
        <div class="alert alert-success"><?php echo $message; ?></div>
    <?php endif; ?>

    <table style="width: 100%; border-collapse: collapse; margin-top: 10px;">
        <tr style="background: #333; color: white;">
            <th style="padding: 10px;">ID</th>
            <th style="padding: 10px;">Name</th>
            <th style="padding: 10px;">Category</th>
            <th style="padding: 10px;">Price</th>
            <th style="padding: 10px;">Stock</th>
            <th style="padding: 10px;">Actions</th>
        </tr>
        <?php while($p = mysqli_fetch_assoc($products_res)): ?>
            <tr style="border-bottom: 1px solid #ddd;">
                <td style="padding: 10px;"><?php echo $p['product_id']; ?></td>
                <td style="padding: 10px;">
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <?php if($p['image_url']): ?>
                            <img src="<?php echo htmlspecialchars($p['image_url']); ?>" style="width:40px; height:40px; border-radius:4px; object-fit:cover;">
                        <?php endif; ?>
                        <?php echo htmlspecialchars($p['name']); ?>
                    </div>
                </td>
                <td style="padding: 10px;"><?php echo htmlspecialchars($p['category_name']); ?></td>
                <td style="padding: 10px;">Rs. <?php echo number_format($p['price'], 2); ?></td>
                <td style="padding: 10px;"><?php echo $p['stock_quantity']; ?></td>
                <td style="padding: 10px;">
                    <a href="edit_product.php?id=<?php echo $p['product_id']; ?>" class="btn" style="padding: 5px 10px; font-size: 0.8rem;">Edit</a>
                    <a href="delete_product.php?id=<?php echo $p['product_id']; ?>" onclick="return confirm('Are you sure you want to delete this product?');" class="btn btn-secondary" style="padding: 5px 10px; font-size: 0.8rem; background-color: #dc3545;">Delete</a>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>
</div>

<?php include 'includes/footer.php'; ?>

