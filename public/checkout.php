<?php
require_once '../config/db.php';
// We need to start session before header redirect check, but header include starts session too.
// We'll check login manually first.
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
// Including header now that we didn't redirect
// Note: header functions of PHP cannot be called after output. 
// Ideally includes/header.php shouldn't be outputting HTML if we might redirect, but for simplicity here we assume if we pass the check, we render.
// I'll re-include header after logic or ensure logic is separate. 
// For this simple project, I'll do logic first.

$user_id = $_SESSION['user_id'];
$cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];

if (empty($cart)) {
    header("Location: shop.php");
    exit;
}

// Calculate Totals again
$subtotal = 0;
$cart_items = [];
$ids = implode(',', array_keys($cart));
$query = "SELECT * FROM products WHERE product_id IN ($ids)";
$result = mysqli_query($conn, $query);
while($row = mysqli_fetch_assoc($result)) {
    $qty = $cart[$row['product_id']];
    $row['qty'] = $qty;
    $cart_items[] = $row;
    $subtotal += $row['price'] * $qty;
}
$shipping = ($subtotal > 100) ? 0 : 10;
$total = $subtotal + $shipping;

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $shipping_address = mysqli_real_escape_string($conn, $_POST['address']);
    
    // 1. Create Order
    $order_sql = "INSERT INTO orders (user_id, total_amount, shipping_cost, status, shipping_address) VALUES ('$user_id', '$total', '$shipping', 'pending', '$shipping_address')";
    
    if (mysqli_query($conn, $order_sql)) {
        $order_id = mysqli_insert_id($conn);
        
        // 2. Insert Order Items & Update Stock
        foreach ($cart_items as $item) {
            $pid = $item['product_id'];
            $qty = $item['qty'];
            $price = $item['price'];
            
            mysqli_query($conn, "INSERT INTO order_items (order_id, product_id, quantity, price) VALUES ('$order_id', '$pid', '$qty', '$price')");
            
            // Deduct Stock
            mysqli_query($conn, "UPDATE products SET stock_quantity = stock_quantity - $qty WHERE product_id = $pid");
        }
        
        // 3. Clear Cart
        unset($_SESSION['cart']);
        
        // 4. Redirect
        header("Location: orders.php?success=1");
        exit;
    } else {
        $error = "Error placing order: " . mysqli_error($conn);
    }
}

// End logic, start Output
// We must close and re-open session? No, just include header carefully. 
// But include 'header.php' already has session_start(). 
// It's safe to call session_start() twice if we check status, which I did in navbar.php.
// But header.php outputs HTML. So we must put it here.
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Checkout - Melody Masters</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<?php include 'includes/navbar.php'; ?>

<div class="container mt-4">
    <h2>Checkout</h2>
    
    <?php if($error): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>

    <div style="display: flex; gap: 40px;">
        <div style="flex: 1;">
            <div class="card card-body">
                <h4>Shipping Details</h4>
                <form method="POST" action="">
                    <div class="form-group">
                        <label>Shipping Address</label>
                        <textarea name="address" class="form-control" rows="4" required></textarea>
                    </div>
                     <!-- Mock Payment -->
                    <div class="form-group">
                        <label>Payment Method</label>
                        <select class="form-control">
                            <option>Credit Card (Mock)</option>
                            <option>PayPal (Mock)</option>
                        </select>
                    </div>
                    
                    <button type="submit" class="btn" style="width: 100%; margin-top: 20px;">Place Order</button>
                </form>
            </div>
        </div>
        
        <div style="flex: 1;">
            <div class="card card-body" style="background: #f9f9f9;">
                <h4>Order Summary</h4>
                <table style="width: 100%;">
                    <?php foreach($cart_items as $item): ?>
                        <tr>
                            <td><?php echo $item['name']; ?> x <?php echo $item['qty']; ?></td>
                            <td class="text-right">Rs. <?php echo number_format($item['price'] * $item['qty'], 2); ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <tr><td colspan="2"><hr></td></tr>
                    <tr>
                        <td>Subtotal</td>
                        <td class="text-right">Rs. <?php echo number_format($subtotal, 2); ?></td>
                    </tr>
                    <tr>
                        <td>Shipping</td>
                        <td class="text-right">Rs. <?php echo number_format($shipping, 2); ?></td>
                    </tr>
                    <tr>
                        <td><strong>Total</strong></td>
                        <td class="text-right"><strong>Rs. <?php echo number_format($total, 2); ?></strong></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>

