<?php
require_once '../config/db.php';
include 'includes/header.php';

if (!isset($_SESSION['user_id'])) {
    echo "<script>window.location.href='login.php';</script>";
    exit;
}

$user_id = $_SESSION['user_id'];
$query = "SELECT * FROM orders WHERE user_id = '$user_id' ORDER BY created_at DESC";
$result = mysqli_query($conn, $query);
?>

<div class="container mt-4">
    <h2>My Orders</h2>
    <?php if(isset($_GET['success'])): ?>
        <div class="alert alert-success">Order placed successfully!</div>
    <?php endif; ?>

    <?php if(mysqli_num_rows($result) > 0): ?>
        <?php while($order = mysqli_fetch_assoc($result)): ?>
            <div class="card mb-4">
                <div class="card-body">
                    <div style="display: flex; justify-content: space-between; background: #eee; padding: 10px; border-radius: 4px;">
                        <span><strong>Order #<?php echo $order['order_id']; ?></strong></span>
                        <span>Date: <?php echo $order['created_at']; ?></span>
                        <span>Status: <?php echo ucfirst($order['status']); ?></span>
                        <span>Total: <strong>£<?php echo number_format($order['total_amount'], 2); ?></strong></span>
                    </div>
                    
                    <div style="margin-top: 15px;">
                        <table style="width: 100%;">
                            <?php
                            $oid = $order['order_id'];
                            $items_query = "SELECT oi.*, p.name, p.is_digital, p.file_path FROM order_items oi JOIN products p ON oi.product_id = p.product_id WHERE oi.order_id = $oid";
                            $items_result = mysqli_query($conn, $items_query);
                            while($item = mysqli_fetch_assoc($items_result)):
                            ?>
                                <tr>
                                    <td style="padding: 5px;"><?php echo htmlspecialchars($item['name']); ?></td>
                                    <td style="padding: 5px;">x <?php echo $item['quantity']; ?></td>
                                    <td style="padding: 5px;">£<?php echo number_format($item['price'], 2); ?></td>
                                    <td style="padding: 5px;">
                                        <?php if($item['is_digital']): ?>
                                            <a href="download.php?file=<?php echo urlencode($item['file_path']); ?>" class="btn btn-secondary" style="padding: 2px 8px; font-size: 0.8rem;">Download</a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </table>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p>You have not placed any orders yet.</p>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>
