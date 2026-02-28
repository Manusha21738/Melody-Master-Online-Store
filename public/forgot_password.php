<?php
require_once '../config/db.php';
include 'includes/header.php';

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    
    // Check if email exists
    $query = "SELECT user_id, name FROM users WHERE email = '$email'";
    $result = mysqli_query($conn, $query);
    
    if ($row = mysqli_fetch_assoc($result)) {
        // In a real application, you would generate a unique token, save it to a password_resets table 
        // with an expiration time, and email a link to the user.
        // For this academic project without SMTP, we will mock the email sending process
        // and allow direct reset via session.
        
        session_start();
        $_SESSION['reset_user_id'] = $row['user_id'];
        
        $message = "Mock Email Sent! In a real system, you would check your inbox. <br><br> <a href='reset_password.php' class='btn'>Click here to proceed to Reset Form (Mock Email Link)</a>";
    } else {
        $error = "No account found with that email address.";
    }
}
?>

<div class="container">
    <div class="auth-container">
        <h2 class="text-center">Forgot Password</h2>
        
        <?php if($message): ?>
            <div class="alert alert-success"><?php echo $message; ?></div>
        <?php endif; ?>
        
        <?php if($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <?php if(!$message): ?>
            <p class="text-center" style="margin-bottom: 20px;">Enter your email address and we'll send you a link to reset your password.</p>
            <form method="POST" action="">
                <div class="form-group">
                    <label>Email Address</label>
                    <input type="email" name="email" class="form-control" required>
                </div>
                <button type="submit" class="btn" style="width: 100%;">Send Reset Link</button>
            </form>
        <?php endif; ?>
        
        <p class="mt-4 text-center"><a href="login.php">&larr; Back to Login</a></p>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
