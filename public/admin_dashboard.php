<?php
require_once '../config/db.php';
include 'includes/header.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    die("<div class='container mt-4 alert alert-danger'>Access Denied. Admins Only.</div>");
}

// Stats
$order_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM orders"))['c'];
$revenue = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(total_amount) as s FROM orders"))['s'];
$product_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM products"))['c'];
$low_stock = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM products WHERE stock_quantity < 5"))['c'];
?>

<div class="container mt-4">
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <h2>Admin Dashboard</h2>
        <div>
            <a href="admin_manage_products.php" class="btn btn-secondary">Manage Products</a>
            <a href="admin_products.php" class="btn">Add New Product</a>
        </div>
    </div>
    
    <div class="grid mt-4">
        <div class="card card-body text-center" style="background: #e3f2fd;">
            <h3><?php echo $order_count; ?></h3>
            <p>Total Orders</p>
        </div>
        <div class="card card-body text-center" style="background: #e8f5e9;">
            <h3>Rs. <?php echo number_format($revenue ?? 0, 2); ?></h3>
            <p>Total Revenue</p>
        </div>
        <div class="card card-body text-center" style="background: #fff3e0;">
            <h3><?php echo $product_count; ?></h3>
            <p>Products</p>
        </div>
        <div class="card card-body text-center" style="background: #ffebee;">
            <h3 style="color: red;"><?php echo $low_stock; ?></h3>
            <p>Low Stock Items</p>
        </div>
    </div>

    <!-- Recent Orders Table -->
    <h3 class="mt-4">Recent Orders</h3>
    <table style="width: 100%; border-collapse: collapse; margin-top: 10px;">
        <tr style="background: #333; color: white;">
            <th style="padding: 10px;">ID</th>
            <th style="padding: 10px;">User</th>
            <th style="padding: 10px;">Total</th>
            <th style="padding: 10px;">Status</th>
            <th style="padding: 10px;">Date</th>
        </tr>
        <?php
        $orders_query = "SELECT o.*, u.name FROM orders o JOIN users u ON o.user_id = u.user_id ORDER BY created_at DESC LIMIT 10";
        $orders_res = mysqli_query($conn, $orders_query);
        while($o = mysqli_fetch_assoc($orders_res)):
        ?>
            <tr style="border-bottom: 1px solid #ddd;">
                <td style="padding: 10px;">#<?php echo $o['order_id']; ?></td>
                <td style="padding: 10px;"><?php echo htmlspecialchars($o['name']); ?></td>
                <td style="padding: 10px;">Rs. <?php echo number_format($o['total_amount'], 2); ?></td>
                <td style="padding: 10px;"><?php echo ucfirst($o['status']); ?></td>
                <td style="padding: 10px;"><?php echo $o['created_at']; ?></td>
            </tr>
        <?php endwhile; ?>
    </table>
</div>

<?php include 'includes/footer.php'; ?>

