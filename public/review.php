<?php
// Handles the submission of a product review by a verified customer.
require_once '../config/db.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
    $rating = isset($_POST['rating']) ? (int)$_POST['rating'] : 5;
    $comment = mysqli_real_escape_string($conn, trim($_POST['comment']));
    $user_id = $_SESSION['user_id'];

    // Assignment constraint: Customer must be verified (have purchased the product)
    if ($product_id > 0 && $rating >= 1 && $rating <= 5 && !empty($comment)) {
        // Query to check if the user has an order item containing this product
        $check_query = "SELECT COUNT(*) as count FROM order_items oi JOIN orders o ON oi.order_id = o.order_id WHERE o.user_id = $user_id AND oi.product_id = $product_id";
        $check_result = mysqli_query($conn, $check_query);
        $check_row = mysqli_fetch_assoc($check_result);
        
        if ($check_row['count'] > 0) {
            // User bought it - allow inserting review.
            // Using parameterized queries would be better for security,
            // but we stick to the existing style (with mysqli_real_escape_string) to match overall student codebase pattern.
            $insert_query = "INSERT INTO reviews (product_id, user_id, rating, comment) VALUES ($product_id, $user_id, $rating, '$comment')";
            mysqli_query($conn, $insert_query);
        }
    }
    // Redirect back to product page after execution
    header("Location: product.php?id=" . $product_id);
    exit;
} else {
    // If accessed without form submission
    header("Location: index.php");
    exit;
}
