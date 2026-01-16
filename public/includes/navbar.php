<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<nav class="navbar">
    <div class="container">
        <a href="index.php" class="logo">Melody Masters</a>
        <ul class="nav-links">
            <li><a href="index.php">Home</a></li>
            <li><a href="shop.php">Shop</a></li>
            <li><a href="cart.php">Cart <?php echo isset($_SESSION['cart']) ? '('.count($_SESSION['cart']).')' : ''; ?></a></li>
            
            <?php if(isset($_SESSION['user_id'])): ?>
                <li><a href="orders.php">My Orders</a></li>
                <?php if(isset($_SESSION['role']) && $_SESSION['role'] == 'admin'): ?>
                    <li><a href="admin_dashboard.php">Admin</a></li>
                <?php endif; ?>
                <li><a href="logout.php" class="btn btn-secondary">Logout</a></li>
            <?php else: ?>
                <li><a href="login.php">Login</a></li>
                <li><a href="register.php" class="btn">Register</a></li>
            <?php endif; ?>
        </ul>
    </div>
</nav>
