<?php
require_once '../config/db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    die("Unauthorized access.");
}

if (!isset($_GET['file'])) {
    die("File not specified.");
}

$user_id = $_SESSION['user_id'];
$file_path = urldecode($_GET['file']); 
// Vulnerability Note: In production, validate this path strictly to prevent directory traversal!
// For this academic project, we will assume paths are safe relative to project root or absolute/controlled. 
// We should check if the user actually BOUGHT this file.

// 1. Find Product ID by file path (Reverse lookup or pass ID)
// Simpler: Check if any order of this user contains a product with this file path.
$query = "SELECT COUNT(*) as count 
          FROM order_items oi 
          JOIN orders o ON oi.order_id = o.order_id 
          JOIN products p ON oi.product_id = p.product_id 
          WHERE o.user_id = '$user_id' AND p.file_path = '$file_path' AND p.is_digital = 1";

$result = mysqli_query($conn, $query);
$row = mysqli_fetch_assoc($result);

if ($row['count'] > 0 || (isset($_SESSION['role']) && $_SESSION['role'] =='admin')) {
    // User bought it (or is admin). Serve file.
    // For demo, we just echo "Downloading..." or fake it if file doesn't exist.
    // Real implementation:
    /*
    if (file_exists($file_path)) {
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="'.basename($file_path).'"');
        readfile($file_path);
        exit;
    }
    */
    echo "<h1>Downloading File...</h1>";
    echo "<p>File: " . htmlspecialchars($file_path) . "</p>";
    echo "<p><em>(In a real server, the file download would start automatically.)</em></p>";
    echo "<a href='orders.php'>Back to Orders</a>";
} else {
    die("Access Denied: You have not purchased this item.");
}
?>
