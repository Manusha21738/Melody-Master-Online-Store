<?php
require_once '../config/db.php';
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    die("Access Denied.");
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id > 0) {
    // Optionally delete the image file if it's not a default placeholder
    $query = "SELECT image_url FROM products WHERE product_id = $id";
    $res = mysqli_query($conn, $query);
    if ($row = mysqli_fetch_assoc($res)) {
        $img = $row['image_url'];
        if ($img && strpos($img, 'placeholder.jpg') === false && file_exists(__DIR__ . '/' . $img)) {
            unlink(__DIR__ . '/' . $img);
        }
    }
    
    // Delete from DB (Order items handle Cascade/Set Null based on DB schema)
    $del_query = "DELETE FROM products WHERE product_id = $id";
    if (mysqli_query($conn, $del_query)) {
        header("Location: admin_manage_products.php?msg=" . urlencode("Product deleted successfully."));
    } else {
        header("Location: admin_manage_products.php?msg=" . urlencode("Failed to delete product."));
    }
} else {
    header("Location: admin_manage_products.php");
}
exit;
