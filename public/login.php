<?php
require_once '../config/db.php';
include 'includes/header.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    $query = "SELECT * FROM users WHERE email = '$email'";
    $result = mysqli_query($conn, $query);

    if ($row = mysqli_fetch_assoc($result)) {
        if (password_verify($password, $row['password'])) {
            $_SESSION['user_id'] = $row['user_id'];
            $_SESSION['name'] = $row['name'];
            $_SESSION['role'] = $row['role'];
            
            // Redirect based on role
            if ($row['role'] == 'admin') {
                echo "<script>window.location.href='admin_dashboard.php';</script>";
            } else {
                echo "<script>window.location.href='index.php';</script>";
            }
            exit;
        } else {
            $error = "Invalid password.";
        }
    } else {
        $error = "No account found with this email.";
    }
}
?>

<div class="container">
    <div class="auth-container">
        <h2 class="text-center">Login</h2>
        
        <?php if($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <div class="form-group">
                <label>Email Address</label>
                <input type="email" name="email" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <button type="submit" class="btn" style="width: 100%; margin-bottom: 10px;">Login</button>
            <div style="text-align: right;">
                <a href="forgot_password.php" style="font-size: 0.9rem;">Forgot Password?</a>
            </div>
        </form>
        <p class="mt-4 text-center">Don't have an account? <a href="register.php">Register here</a></p>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
