<?php
require_once '../config/db.php';
session_start();

// Ensure the user actually came from the forgot password flow
if (!isset($_SESSION['reset_user_id'])) {
    header("Location: login.php");
    exit;
}

include 'includes/header.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    if ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    } else {
        $user_id = (int)$_SESSION['reset_user_id'];
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        $query = "UPDATE users SET password = '$hashed_password' WHERE user_id = $user_id";
        if (mysqli_query($conn, $query)) {
            $success = "Password has been successfully updated!";
            unset($_SESSION['reset_user_id']); // Clear the reset session
        } else {
            $error = "Failed to update password. Please try again later.";
        }
    }
}
?>

<div class="container">
    <div class="auth-container">
        <h2 class="text-center">Reset Password</h2>
        
        <?php if($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <?php if($success): ?>
            <div class="alert alert-success">
                <?php echo $success; ?>
                <br><br>
                <a href="login.php" class="btn" style="width: 100%; text-align: center;">Go to Login</a>
            </div>
        <?php else: ?>
            <form method="POST" action="">
                <div class="form-group">
                    <label>New Password</label>
                    <input type="password" name="password" class="form-control" required minlength="6">
                </div>
                <div class="form-group">
                    <label>Confirm New Password</label>
                    <input type="password" name="confirm_password" class="form-control" required minlength="6">
                </div>
                <button type="submit" class="btn" style="width: 100%;">Update Password</button>
            </form>
        <?php endif; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
