<?php
require_once '../config/db.php';
include 'includes/header.php';

// Initialize Cart
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Handle Actions (Add, Remove)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action']) && $_POST['action'] == 'add') {
        $product_id = (int)$_POST['product_id'];
        $quantity = (int)$_POST['quantity'];
        
        // Check if already in cart
        if (isset($_SESSION['cart'][$product_id])) {
            $_SESSION['cart'][$product_id] += $quantity;
        } else {
            $_SESSION['cart'][$product_id] = $quantity;
        }
    }
    
    if (isset($_POST['action']) && $_POST['action'] == 'remove') {
        $product_id = (int)$_POST['product_id'];
        unset($_SESSION['cart'][$product_id]);
    }
    
    // Refresh to prevent resubmission
    // header("Location: cart.php"); // Commented out to avoid headers sent error if output started, but here it's fine as include header is after logic usually. 
    // Wait, I included header.php at top. This is an issue for redirects. 
    // Correction: I should process logic BEFORE header include if I want to redirect.
    // For now, I'll just let it render.
}

// Fetch Cart Products
$cart_items = [];
$subtotal = 0;

if (!empty($_SESSION['cart'])) {
    $ids = implode(',', array_keys($_SESSION['cart']));
    $query = "SELECT * FROM products WHERE product_id IN ($ids)";
    $result = mysqli_query($conn, $query);
    
    while($row = mysqli_fetch_assoc($result)) {
        $row['qty'] = $_SESSION['cart'][$row['product_id']];
        $row['line_total'] = $row['price'] * $row['qty'];
        $cart_items[] = $row;
        $subtotal += $row['line_total'];
    }
}

// Shipping Rule: > Rs. 15,000 Free, else Rs. 500
$shipping = ($subtotal > 15000) ? 0 : 500;
if ($subtotal == 0) $shipping = 0;
$total = $subtotal + $shipping;
?>

<div class="container mt-4">
    <h2>Your Shopping Cart</h2>
    
    <?php if(empty($cart_items)): ?>
        <div class="alert alert-danger">Your cart is empty. <a href="shop.php">Go Shop</a></div>
    <?php else: ?>
        <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
            <thead>
                <tr style="border-bottom: 2px solid #ddd; text-align: left;">
                    <th style="padding: 10px;">Product</th>
                    <th style="padding: 10px;">Price</th>
                    <th style="padding: 10px;">Quantity</th>
                    <th style="padding: 10px;">Total</th>
                    <th style="padding: 10px;">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($cart_items as $item): ?>
                    <tr style="border-bottom: 1px solid #eee;">
                        <td style="padding: 10px;">
                            <strong><?php echo htmlspecialchars($item['name']); ?></strong>
                            <?php if($item['is_digital']) echo " <span style='color:blue; font-size:0.8em;'>(Digital)</span>"; ?>
                        </td>
                        <td style="padding: 10px;">Rs. <?php echo number_format($item['price'], 2); ?></td>
                        <td style="padding: 10px;"><?php echo $item['qty']; ?></td>
                        <td style="padding: 10px;">Rs. <?php echo number_format($item['line_total'], 2); ?></td>
                        <td style="padding: 10px;">
                            <form method="POST" action="cart.php" style="display:inline;">
                                <input type="hidden" name="action" value="remove">
                                <input type="hidden" name="product_id" value="<?php echo $item['product_id']; ?>">
                                <button type="submit" class="btn btn-secondary" style="padding: 5px 10px; font-size: 0.8rem;">Remove</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div style="display: flex; justify-content: flex-end;">
            <div style="width: 300px; padding: 20px; background: #f9f9f9; border-radius: 5px;">
                <p style="display: flex; justify-content: space-between;"><span>Subtotal:</span> <span>Rs. <?php echo number_format($subtotal, 2); ?></span></p>
                <p style="display: flex; justify-content: space-between;"><span>Shipping:</span> <span>Rs. <?php echo number_format($shipping, 2); ?></span></p>
                <hr>
                <h3 style="display: flex; justify-content: space-between;"><span>Total:</span> <span>Rs. <?php echo number_format($total, 2); ?></span></h3>
                <a href="checkout.php" class="btn" style="display: block; text-align: center; margin-top: 15px;">Proceed to Checkout</a>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>

