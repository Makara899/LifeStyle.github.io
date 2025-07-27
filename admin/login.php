<?php
session_start();

// Database connection with error handling
$conn = new mysqli("localhost", "root", "", "ten11-web");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize cart if not set
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Handle product addition to cart
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_to_cart'])) {
    $product = [
        'code' => $_POST['code'],
        'name' => $_POST['name'],
        'price' => floatval($_POST['price']),
        'quantity' => 1
    ];

    // Check if product already in cart
    $found = false;
    foreach ($_SESSION['cart'] as &$item) {
        if ($item['code'] === $product['code']) {
            $item['quantity']++;
            $found = true;
            break;
        }
    }
    if (!$found) {
        $_SESSION['cart'][] = $product;
    }
    header("Location: ?section=back-to-store");
    exit;
}

// Handle product deletion from cart
if (isset($_GET['delete_cart_item'])) {
    $index = $_GET['delete_cart_item'];
    if (isset($_SESSION['cart'][$index])) {
        unset($_SESSION['cart'][$index]);
        $_SESSION['cart'] = array_values($_SESSION['cart']); // Reindex array
    }
    header("Location: ?section=back-to-store");
    exit;
}

// Handle add product form submission (for both Products and Shops sections)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_product'])) {
    $section = $_POST['section'] ?? 'products';
    $productId = $conn->real_escape_string($_POST['productId']);
    $productName = $conn->real_escape_string($_POST['productName']);
    $productPrice = floatval($_POST['productPrice']);
    $productStock = intval($_POST['productStock']);
    $img_name = '';

    // Handle file upload
    if (isset($_FILES['productImage']) && $_FILES['productImage']['error'] === UPLOAD_ERR_OK) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        $max_size = 2 * 1024 * 1024; // 2MB
        $file_type = $_FILES['productImage']['type'];
        $file_size = $_FILES['productImage']['size'];

        if (in_array($file_type, $allowed_types) && $file_size <= $max_size) {
            $upload_dir = "img/";
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            $img_name = uniqid() . '_' . basename($_FILES['productImage']['name']);
            $target_path = $upload_dir . $img_name;
            
            if (!move_uploaded_file($_FILES['productImage']['tmp_name'], $target_path)) {
                $error = "Failed to upload image. Please try again.";
            }
        } else {
            $error = "Invalid file type or size. Please upload a JPEG, PNG, or GIF image under 2MB.";
        }
    }

    // Insert into database if no file upload error
    if (empty($error)) {
        if ($section === 'shops') {
            $sql = "INSERT INTO `shop` (`product_id`, `product_name`, `price`, `stock`, `img`) 
                    VALUES ('$productId', '$productName', $productPrice, $productStock, '$img_name')";
        } else {
            $sql = "INSERT INTO `add_product` (`product_id`, `name`, `price`, `stock`, `img`) 
                    VALUES ('$productId', '$productName', $productPrice, $productStock, '$img_name')";
        }
        if (!$conn->query($sql)) {
            $error = "Error saving product to database: " . $conn->error;
        }
    }
    
    if (empty($error)) {
        header("Location: ?section=$section");
        exit;
    }
}

// Handle update product form submission (for both Products and Shops sections)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_product'])) {
    $section = $_POST['section'] ?? 'products';
    $productId = $conn->real_escape_string($_POST['productId']);
    $productName = $conn->real_escape_string($_POST['productName']);
    $productPrice = floatval($_POST['productPrice']);
    $productStock = intval($_POST['productStock']);
    $img_name = $conn->real_escape_string($_POST['existingImage']);

    // Handle file upload for update
    if (isset($_FILES['productImage']) && $_FILES['productImage']['error'] === UPLOAD_ERR_OK) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        $max_size = 2 * 1024 * 1024; // 2MB
        $file_type = $_FILES['productImage']['type'];
        $file_size = $_FILES['productImage']['size'];

        if (in_array($file_type, $allowed_types) && $file_size <= $max_size) {
            $upload_dir = "img/";
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            $img_name = uniqid() . '_' . basename($_FILES['productImage']['name']);
            $target_path = $upload_dir . $img_name;
            
            if (!move_uploaded_file($_FILES['productImage']['tmp_name'], $target_path)) {
                $error = "Failed to upload image. Please try again.";
            }
        } else {
            $error = "Invalid file type or size. Please upload a JPEG, PNG, or GIF image under 2MB.";
        }
    }

    // Update database if no file upload error
    if (empty($error)) {
        if ($section === 'shops') {
            $sql = "UPDATE `shop` SET 
                    `product_name`='$productName', 
                    `price`=$productPrice, 
                    `stock`=$productStock, 
                    `img`='$img_name' 
                    WHERE `product_id`='$productId'";
        } else {
            $sql = "UPDATE `add_product` SET 
                    `name`='$productName', 
                    `price`=$productPrice, 
                    `stock`=$productStock, 
                    `img`='$img_name' 
                    WHERE `product_id`='$productId'";
        }
        if (!$conn->query($sql)) {
            $error = "Error updating product: " . $conn->error;
        }
    }
    
    if (empty($error)) {
        header("Location: ?section=$section");
        exit;
    }
}

// Handle product deletion (for both Products and Shops sections)
if (isset($_GET['delete_product'])) {
    $section = $_GET['section'] ?? 'products';
    $productId = $conn->real_escape_string($_GET['delete_product']);
    if ($section === 'shops') {
        $sql = "DELETE FROM `shop` WHERE `product_id`='$productId'";
    } else {
        $sql = "DELETE FROM `add_product` WHERE `product_id`='$productId'";
    }
    if (!$conn->query($sql)) {
        $error = "Error deleting product: " . $conn->error;
    }
    
    if (empty($error)) {
        header("Location: ?section=$section");
        exit;
    }
}

// Fetch products from database
$products = [];
$shop_products = [];
// Fetch products for Products section
$sql = "SELECT * FROM add_product";
$result = $conn->query($sql);
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
}
// Fetch products for Shops section
$sql = "SELECT * FROM shop";
$result = $conn->query($sql);
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $shop_products[] = $row;
    }
}

// Handle delete request for feedback
if (isset($_POST['delete_id'])) {
    $delete_id = $conn->real_escape_string($_POST['delete_id']);
    $delete_sql = "DELETE FROM contact WHERE id = '$delete_id'";
    if ($conn->query($delete_sql)) {
        header("Location: ?section=feedback");
        exit;
    } else {
        echo "<script>alert('Error deleting message: " . $conn->error . "');</script>";
    }
}

// Handle mark as read request for feedback
if (isset($_POST['mark_read_id'])) {
    $read_id = $conn->real_escape_string($_POST['mark_read_id']);
    $update_sql = "UPDATE contact SET read_status = 1 WHERE id = '$read_id'";
    if (!$conn->query($update_sql)) {
        echo "<script>alert('Error marking message as read: " . $conn->error . "');</script>";
    }
}

// Fetch all contact messages
$contact_messages = [];
$sql = "SELECT id, name, email, subject, message, date, read_status FROM contact ORDER BY date DESC";
$result = $conn->query($sql);
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $contact_messages[] = $row;
    }
}

// Fetch data for charts
$monthly_sales = [];
$monthly_orders = [];
$product_sales = [];

$cn = new mysqli("localhost", "root", "", "ten11-web");
if (!$cn->connect_error) {
    // Monthly Sales Data
    $sql = "SELECT DATE_FORMAT(date, '%Y-%m') as month, SUM(total) as total_sales 
            FROM product_admin 
            GROUP BY DATE_FORMAT(date, '%Y-%m') 
            ORDER BY month DESC LIMIT 6";
    $result = $cn->query($sql);
    while ($row = $result->fetch_assoc()) {
        $monthly_sales[] = ['month' => $row['month'], 'sales' => $row['total_sales']];
    }

    // Monthly Orders Data
    $sql = "SELECT DATE_FORMAT(date, '%Y-%m') as month, COUNT(*) as total_orders 
            FROM product_admin 
            GROUP BY DATE_FORMAT(date, '%Y-%m') 
            ORDER BY month DESC LIMIT 6";
    $result = $cn->query($sql);
    while ($row = $result->fetch_assoc()) {
        $monthly_orders[] = ['month' => $row['month'], 'orders' => $row['total_orders']];
    }

    // Top Products by Sales
    $sql = "SELECT product_name, SUM(qty) as total_qty 
            FROM product_admin 
            GROUP BY product_name 
            ORDER BY total_qty DESC LIMIT 5";
    $result = $cn->query($sql);
    while ($row = $result->fetch_assoc()) {
        $product_sales[] = ['name' => $row['product_name'], 'quantity' => $row['total_qty']];
    }

    $cn->close();
}

// Determine active section
$section = isset($_GET['section']) ? $_GET['section'] : 'overview';
$valid_sections = ['overview', 'products', 'shops', 'orders', 'customers', 'settings', 'feedback', 'back-to-store'];
if (!in_array($section, $valid_sections)) {
    $section = 'overview';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TEN11 Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            overflow-x: hidden;
            background-color: #f0f2f5;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
        }
        .sidebar {
            height: 100vh;
            width: 250px;
            position: fixed;
            top: 0;
            left: 0;
            background-color: #343a40;
            padding-top: 20px;
            color: white;
            z-index: 1000;
        }
        .sidebar .nav-link {
            color: #adb5bd;
            padding: 10px 15px;
            transition: background-color 0.3s;
            text-decoration: none;
        }
        .sidebar .nav-link:hover {
            background-color: #495057;
            color: white;
        }
        .sidebar .nav-link.active {
            background-color: #0d6efd;
            color: white;
        }
        .content {
            margin-left: 250px;
            padding: 20px;
            flex-grow: 1;
        }
        .card-dashboard {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            padding: 20px;
            margin-bottom: 20px;
        }
        .card-dashboard h5 {
            color: #343a40;
            margin-bottom: 15px;
        }
        .card-dashboard .display-4 {
            font-size: 2.5rem;
            font-weight: bold;
            color: #0d6efd;
        }
        .container-fluid.bg-secondary, .carousel, .discount-box, .container.mt-5, footer {
            margin-left: 250px;
            width: calc(100% - 250px);
            box-sizing: border-box;
        }
        .main-store-content {
            padding: 20px;
        }
        .tbltable {
            position: fixed;
            bottom: 20px;
            right: 20px;
            width: 450px;
            max-height: 400px;
            overflow-y: auto;
            background-color: rgba(255, 255, 255, 0.95);
            border-radius: 12px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.25);
            z-index: 1000;
            display: <?php echo isset($_GET['show_cart']) ? 'block' : 'none'; ?>;
            backdrop-filter: blur(5px);
            -webkit-backdrop-filter: blur(5px);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }
        .tbltable table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
        }
        .tbltable th, .tbltable td {
            border: none;
            padding: 12px 15px;
            text-align: center;
            font-size: 1rem;
            color: #333;
            vertical-align: middle;
        }
        .tbltable thead {
            background-color: rgb(219, 121, 187);
            color: #fff;
            position: sticky;
            top: 0;
            z-index: 10;
        }
        .tbltable th:first-child { border-top-left-radius: 10px; }
        .tbltable th:last-child { border-top-right-radius: 10px; }
        .tbltable tbody tr:nth-child(even) {
            background-color: rgba(0, 0, 0, 0.05);
        }
        .tbltable tbody tr:hover {
            background-color: rgba(255, 165, 0, 0.2);
        }
        .tbltable .delete-btn {
            background-color: #dc3545;
            border: none;
            color: white;
            padding: 6px 10px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 0.9em;
            transition: background-color 0.3s ease;
        }
        .tbltable .delete-btn:hover {
            background-color: #c82333;
        }
        .carousel-item {
            width: 100%;
            height: 100vh;
        }
        .carousel-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .navbar {
            background: linear-gradient(90deg, rgb(219, 121, 187) 0%, rgba(35, 255, 138, 0.527) 100%);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            position: fixed;
            top: 0;
            left: 250px;
            width: calc(100% - 250px);
            z-index: 111;
        }
        .navbar-brand img {
            width: 50px;
            height: 30px;
        }
        .navbar-nav .nav-link {
            color: #fff;
            transition: color 0.3s, transform 0.3s;
            position: relative;
            padding: 15px 20px;
        }
        .navbar-nav .nav-link:hover {
            color: #f1c40f;
            transform: scale(1.05);
        }
        .navbar-nav .nav-link::after {
            content: '';
            display: block;
            width: 0;
            height: 2px;
            background-color: #f1c40f;
            position: absolute;
            bottom: -5px;
            left: 0;
            transition: width 0.3s;
        }
        .navbar-nav .nav-link:hover::after {
            width: 100%;
        }
        .form-control {
            border-radius: 50px;
        }
        body {
            padding-top: 70px;
        }
        .card img {
            transition: transform 0.3s, box-shadow 0.1s;
            cursor: pointer;
        }
        .card img:hover {
            transform: scale(1.05);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
        .card {
            border: none;
        }
        .btn-custom {
            background-color: orange;
            width: 100%;
            border: none;
            color: #fff;
            padding: 10px 20px;
            border-radius: 20px;
            font-weight: bold;
            text-transform: uppercase;
            transition: all 0.4s ease;
            position: relative;
            overflow: hidden;
        }
        .btn-custom::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 300%;
            height: 300%;
            background: rgba(255, 255, 255, 0.2);   
            transition: all 0.4s ease;
            transform: translate(-50%, -50%) scale(0);
            border-radius: 50%;
        }
        .btn-custom:hover::before {
            transform: translate(-50%, -50%) scale(1);
        }
        .btn-custom:hover {
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
            transform: translateY(-3px);
        }
        .discount-box1 {
            background-color: #dc3545;
            color: #fff;
            padding: 5px 10px;
            border-radius: 12px;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 0.9rem;
            position: absolute;
            top: 10px;
            right: 10px;
        }
        .discount-box {
            width: 100%;
            height: 50px;
            background-color: rgb(225, 225, 41);
            display: flex;
            font-size: 1.2rem;
            align-items: center;
            justify-content: center;
            white-space: nowrap;
        }
        .discount-boxtxt {
            width: 100%;
            height: 100%;
            align-items: center;
            justify-content: center;
            text-align: center;
            margin-top: 15px;
            animation: scroll 19s linear infinite;
        }
        @keyframes scroll {
            100% { transform: translateX(100%); }
            0% { transform: translateX(-100%); }
        }
        footer a {
            display: inline-flex;
            align-items: center;
            text-decoration: none;
            color: #f8f9fa;
        }
        footer a i {
            margin-right: 8px;
        }
        footer a:hover {
            text-decoration: underline;
        }
        .modal-content {
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        }
        .modal-header {
            background: linear-gradient(90deg, rgb(219, 121, 187) 0%, rgba(35, 255, 138, 0.527) 100%);
            color: #fff;
            border-bottom: none;
            border-top-left-radius: 10px;
            border-top-right-radius: 10px;
            padding: 20px;
        }
        .modal-title {
            font-weight: bold;
            font-size: 1.75rem;
        }
        .modal-body {
            padding: 30px;
        }
        .modal-body .form-control {
            margin-bottom: 15px;
            border-radius: 8px;
            border: 1px solid #ccc;
            padding: 10px 15px;
            background-color: #fff;
            color: #333;
        }
        .modal-body .label-control {
            font-weight: 600;
            margin-bottom: 5px;
            display: block;
            color: #333;
        }
        .modal-footer {
            border-top: none;
            padding: 20px;
            justify-content: center;
        }
        .modal-footer .btn-danger, .modal-footer .btn-success {
            padding: 10px 30px;
            border-radius: 25px;
            font-weight: bold;
            transition: all 0.3s ease;
        }
        .modal-footer .btn-danger {
            background-color: #dc3545;
            border-color: #dc3545;
        }
        .modal-footer .btn-danger:hover {
            background-color: #c82333;
            border-color: #bd2130;
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        }
        .modal-footer .btn-success {
            background-color: #28a745;
            border-color: #28a745;
        }
        .modal-footer .btn-success:hover {
            background-color: #218838;
            border-color: #1e7e34;
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        }
        .messenger-container {
            display: flex;
            height: calc(100vh - 80px);
            margin: 20px;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }
        .feedback-sidebar {
            width: 350px;
            background: #fff;
            border-right: 1px solid #ddd;
            transition: all 0.3s ease;
            overflow-y: auto;
        }
        .feedback-sidebar.hidden {
            width: 0;
            overflow: hidden;
        }
        .sidebar-header {
            padding: 15px;
            background: #0084ff;
            color: #fff;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .sidebar-header h4 {
            margin: 0;
            font-size: 1.2rem;
        }
        .search-input {
            margin: 15px;
            border-radius: 20px;
            border: 1px solid #ccc;
            padding: 8px 15px;
            font-size: 0.9rem;
        }
        .search-input:focus {
            border-color: #0084ff;
            box-shadow: 0 0 5px rgba(0, 132, 255, 0.3);
        }
        .message-list .message-item {
            padding: 10px 15px;
            border-bottom: 1px solid #eee;
            cursor: pointer;
            transition: background 0.2s ease;
        }
        .message-item:hover, .message-item.active {
            background: #e8f0fe;
        }
        .message-item .avatar {
            width: 50px;
            height: 40px;
            background: #0084ff;
            color: #fff;
            border-radius: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            margin-right: 10px;
        }
        .message-item .name {
            font-weight: 600;
            font-size: 0.95rem;
        }
        .message-item .preview {
            color: #666;
            font-size: 0.85rem;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .message-item .time {
            font-size: 0.8rem;
            color: #999;
        }
        .message-item .badge {
            font-size: 0.7rem;
            padding: 4px 8px;
            border-radius: 10px;
        }
        .main-panel {
            flex: 1;
            background: #fff;
            padding: 20px;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }
        .main-panel.empty {
            color: #666;
            font-size: 1.2rem;
            text-align: center;
        }
        .message-content {
            width: 100%;
            max-width: 600px;
        }
        .message-header {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }
        .message-header .avatar {
            width: 50px;
            height: 50px;
            background: #0084ff;
            color: #fff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-right: 15px;
        }
        .message-bubble {
            background: #e8f0fe;
            border-radius: 15px;
            padding: 15px;
            margin-bottom: 20px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        .message-bubble h5 {
            font-size: 1.1rem;
            margin-bottom: 10px;
        }
        .message-bubble p {
            margin: 5px 0;
            font-size: 0.95rem;
        }
        .message-actions {
            display: flex;
            gap: 10px;
        }
        .btn-custom {
            background: #0084ff;
            color: #fff;
            border-radius: 20px;
            padding: 8px 15px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .btn-custom:hover {
            background: #0066cc;
            transform: translateY(-2px);
        }
        .btn-danger {
            border-radius: 20px;
            padding: 8px 15px;
        }
        .btn-danger:hover {
            transform: translateY(-2px);
        }
        .toggle-btn {
            position: fixed;
            top: 20px;
            left: 20px;
            z-index: 1000;
            display: none;
        }
        .chart-container {
            width: 100%;
            max-width: 600px;
            margin: 20px auto;
        }
        @media (max-width: 768px) {
            .carousel-item { height: 250px; }
            .card { margin-bottom: 20px; }
            .row { display: flex; flex-wrap: wrap; justify-content: center; }
            .col-xl-3, .col-lg-4, .col-md-6, .col-sm-12 { display: flex; justify-content: center; }
            .discount-boxtxt { font-size: 14px; }
            .messenger-container {
                flex-direction: column;
            }
            .feedback-sidebar {
                width: 100%;
                height: 50%;
            }
            .feedback-sidebar.hidden {
                height: 0;
            }
            .main-panel {
                height: 50%;
            }
            .toggle-btn {
                display: block;
            }
        }
        @media (max-width: 884px) {
            .carousel-item { height: 50vh; }
        }
        @media (max-width: 576px) {
            .discount-boxtxt { font-size: 12px; }
        }
    </style>
</head>
<body>
    <div class="d-flex">
        <div class="sidebar">
            <h4 class="text-center mb-4">TEN11 Admin</h4>
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link <?php echo $section === 'overview' ? 'active' : ''; ?>" href="?section=overview">
                        <i class="fas fa-tachometer-alt me-2"></i> Overview
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $section === 'products' ? 'active' : ''; ?>" href="?section=products">
                        <i class="fas fa-box me-2"></i> Products
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $section === 'shops' ? 'active' : ''; ?>" href="?section=shops">
                        <i class="fas fa-box me-2"></i> Shops
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $section === 'orders' ? 'active' : ''; ?>" href="?section=orders">
                        <i class="fas fa-shopping-bag me-2"></i> Orders
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $section === 'customers' ? 'active' : ''; ?>" href="?section=customers">
                        <i class="fas fa-users me-2"></i> Customers
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $section === 'feedback' ? 'active' : ''; ?>" href="?section=feedback">
                        <i class="fas fa-comment me-2"></i> Feedback
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $section === 'settings' ? 'active' : ''; ?>" href="?section=settings">
                        <i class="fas fa-cog me-2"></i> Settings
                    </a>
                </li>
            </ul>
        </div>

        <div class="content">
            <h2 class="mb-4">Dashboard</h2>

            <?php if ($section === 'overview'): ?>
            <div id="overview-section" class="dashboard-section">
                <div class="row">
                    <div class="col-md-4">
                        <div class="card-dashboard text-center">
                            <h5>Total Sales</h5>
                            <?php
                            $cn = new mysqli("localhost", "root", "", "ten11-web");
                            if ($cn->connect_error) {
                                echo "<p class='display-4'>$0</p>";
                            } else {
                                $sql = "SELECT SUM(total) as total_sum FROM `product_admin`";
                                $result = $cn->query($sql);
                                if ($result && $result->num_rows > 0) {
                                    $row = $result->fetch_assoc();
                                    $total_sum = $row['total_sum'] ? $row['total_sum'] : 0;
                                    echo "<p class='display-4'>$" . number_format($total_sum, 2) . "</p>";
                                } else {
                                    echo "<p class='display-4'>$0</p>";
                                }
                                $cn->close();
                            }
                            ?>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card-dashboard text-center">
                            <h5>Total Orders</h5>
                            <?php
                            $cn = new mysqli("localhost", "root", "", "ten11-web");
                            if ($cn->connect_error) {
                                echo "<p class='display-4'>0</p>";
                            } else {
                                $sql = "SELECT COUNT(*) as total FROM `product_admin`";
                                $result = $cn->query($sql);
                                if ($result) {
                                    $row = $result->fetch_assoc();
                                    echo "<p class='display-4'>" . $row['total'] . "</p>";
                                } else {
                                    echo "<p class='display-4'>0</p>";
                                }
                                $cn->close();
                            }
                            ?>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card-dashboard text-center">
                            <h5>New Customers</h5>
                            <?php
                            $cn = new mysqli("localhost", "root", "", "ten11-web");
                            if ($cn->connect_error) {
                                echo "<p class='display-4'>0</p>";
                            } else {
                                $sql = "SELECT COUNT(*) as total FROM `register`";
                                $result = $cn->query($sql);
                                if ($result) {
                                    $row = $result->fetch_assoc();
                                    echo "<p class='display-4'>" . $row['total'] . "</p>";
                                } else {
                                    echo "<p class='display-4'>0</p>";
                                }
                                $cn->close();
                            }
                            ?>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="card-dashboard">
                            <h5>Monthly Sales</h5>
                            <div class="chart-container">
                                <canvas id="monthlySalesChart"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card-dashboard">
                            <h5>Monthly Orders</h5>
                            <div class="chart-container">
                                <canvas id="monthlyOrdersChart"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="card-dashboard">
                            <h5>Top Selling Products</h5>
                            <div class="chart-container">
                                <canvas id="productSalesChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <?php if ($section === 'products'): ?>
            <div id="products-section" class="dashboard-section">
                <h3>Product Management</h3>
                <p>Manage your products here.</p>
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>
                <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#addProductModal"><i class="fas fa-plus-circle me-2"></i>Add New Product</button>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Price</th>
                            <th>Stock</th>
                            <th>Image</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($products as $product): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($product['product_id']); ?></td>
                            <td><?php echo htmlspecialchars($product['name']); ?></td>
                            <td>$<?php echo number_format($product['price'], 2); ?></td>
                            <td><?php echo htmlspecialchars($product['stock']); ?></td>
                            <td>
                                <?php if (!empty($product['img'])): ?>
                                    <img src="img/<?php echo htmlspecialchars($product['img']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="img-thumbnail" style="width: 80px; height: 80px;">
                                <?php else: ?>
                                    <span class="text-muted">No image</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <button class="btn btn-sm btn-info me-2" data-bs-toggle="modal" data-bs-target="#editProductModal" 
                                        data-id="<?php echo htmlspecialchars($product['product_id']); ?>" 
                                        data-name="<?php echo htmlspecialchars($product['name']); ?>" 
                                        data-price="<?php echo number_format($product['price'], 2); ?>" 
                                        data-stock="<?php echo htmlspecialchars($product['stock']); ?>" 
                                        data-img="<?php echo htmlspecialchars($product['img']); ?>">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <a href="?section=products&delete_product=<?php echo urlencode($product['product_id']); ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this product?');">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>

            <?php if ($section === 'shops'): ?>
            <div id="shops-section" class="dashboard-section">
                <h3>Shops Management</h3>
                <p>Manage your shop products here.</p>
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>
                <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#addProductModal"><i class="fas fa-plus-circle me-2"></i>Add New Product</button>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Price</th>
                            <th>Stock</th>
                            <th>Image</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($shop_products as $product): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($product['product_id']); ?></td>
                            <td><?php echo htmlspecialchars($product['product_name']); ?></td>
                            <td>$<?php echo number_format($product['price'], 2); ?></td>
                            <td><?php echo htmlspecialchars($product['stock']); ?></td>
                            <td>
                                <?php if (!empty($product['img'])): ?>
                                    <img src="img/<?php echo htmlspecialchars($product['img']); ?>" alt="<?php echo htmlspecialchars($product['product_name']); ?>" class="img-thumbnail" style="width: 80px; height: 80px;">
                                <?php else: ?>
                                    <span class="text-muted">No image</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <button class="btn btn-sm btn-info me-2" data-bs-toggle="modal" data-bs-target="#editProductModal" 
                                        data-id="<?php echo htmlspecialchars($product['product_id']); ?>" 
                                        data-name="<?php echo htmlspecialchars($product['product_name']); ?>" 
                                        data-price="<?php echo number_format($product['price'], 2); ?>" 
                                        data-stock="<?php echo htmlspecialchars($product['stock']); ?>" 
                                        data-img="<?php echo htmlspecialchars($product['img']); ?>">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <a href="?section=shops&delete_product=<?php echo urlencode($product['product_id']); ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this product?');">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>

            <?php if ($section === 'orders'): ?>
            <div id="orders-section" class="dashboard-section">
                <h3>Order Management</h3>
                <p>Track and manage customer orders.</p>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Customer</th>
                            <th>Product</th>
                            <th>Date</th>
                            <th>Qty</th>
                            <th>Total</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $cn = new mysqli("localhost", "root", "", "ten11-web");
                        $sql = "SELECT * FROM `product_admin`";
                        $result = $cn->query($sql);
                        while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td>ORD<?php echo $row["id"]; ?></td>
                                <td><?php echo $row["name"]; ?></td>
                                <td><?php echo $row["product_name"]; ?></td>
                                <td><?php echo $row["date"]; ?></td>
                                <td><?php echo $row["qty"]; ?></td>
                                <td><?php echo $row["total"]; ?></td>
                                <td><span class="badge bg-success">Completed</span></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>

            <?php if ($section === 'customers'): ?>
            <div id="customers-section" class="dashboard-section">
                <h3>Customer Management</h3>
                <p>View and manage customer accounts.</p>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Customer ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Joined Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $cn = new mysqli("localhost", "root", "", "ten11-web");
                        $sql = "SELECT * FROM `register`";
                        $result = $cn->query($sql);
                        while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td>CUST00<?php echo $row["id"]; ?></td>
                                <td><?php echo $row["username"]; ?></td>
                                <td><?php echo $row["email"]; ?></td>
                                <td><?php echo $row["Joined_date"]; ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>

            <?php if ($section === 'settings'): ?>
            <div id="settings-section" class="dashboard-section">
                <h3>Settings</h3>
                <p>Adjust dashboard and store settings.</p>
                <form method="POST" action="?section=settings">
                    <div class="mb-3">
                        <label for="storeName" class="form-label">Store Name</label>
                        <input type="text" class="form-control" id="storeName" name="storeName" value="TEN11">
                    </div>
                    <div class="mb-3">
                        <label for="currency" class="form-label">Currency</label>
                        <select class="form-select" id="currency" name="currency">
                            <option selected>$ USD</option>
                            <option>KHR Riel</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary" name="save_settings">Save Settings</button>
                </form>
            </div>
            <?php endif; ?>

            <?php if ($section === 'feedback'): ?>
            <div id="feedback-section" class="dashboard-section">
                <button type="button" class="btn btn-custom toggle-btn" id="toggleSidebarButton">
                    <i class="fas fa-bars"></i>
                </button>
                <div class="messenger-container">
                    <div class="feedback-sidebar" id="feedbackSidebar">
                        <div class="sidebar-header">
                            <h4>Contact Messages</h4>
                        </div>
                        <input type="text" id="searchInput" class="form-control search-input" placeholder="Search messages...">
                        <div class="message-list">
                            <?php if (!empty($contact_messages)): ?>
                                <?php foreach ($contact_messages as $row): ?>
                                    <div class="message-item" 
                                         data-id="<?php echo htmlspecialchars($row['id']); ?>" 
                                         data-name="<?php echo htmlspecialchars($row['name']); ?>" 
                                         data-email="<?php echo htmlspecialchars($row['email']); ?>" 
                                         data-subject="<?php echo htmlspecialchars($row['subject']); ?>" 
                                         data-message="<?php echo htmlspecialchars($row['message']); ?>" 
                                         data-created_at="<?php echo date('Y-m-d H:i', strtotime($row['date'])); ?>"
                                         data-read_status="<?php echo $row['read_status']; ?>">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar"><?php echo strtoupper(substr($row['name'], 0, 1)); ?></div>
                                            <div class="flex-grow-1">
                                                <div class="d-flex justify-content-between">
                                                    <div class="name"><?php echo htmlspecialchars($row['name']); ?></div>
                                                    <div class="time"><?php echo date('H:i', strtotime($row['date'])); ?></div>
                                                </div>
                                                <div class="preview"><?php echo htmlspecialchars(substr($row['message'], 0, 50)) . (strlen($row['message']) > 50 ? '...' : ''); ?></div>
                                                <?php if ($row['read_status'] == 0): ?>
                                                    <span class="badge bg-primary">Unread</span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="text-center p-3">No messages found.</div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="main-panel empty" id="mainPanel">
                        <p>Select a message to view details</p>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="modal fade" id="addProductModal" tabindex="-1" aria-labelledby="addProductModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="addProductModalLabel">Add New Product</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <?php if (isset($error)): ?>
                    <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                    <?php endif; ?>
                    <form method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="section" value="<?php echo htmlspecialchars($section); ?>">
                        <div class="mb-3">
                            <label for="productId" class="label-control">Product ID</label>
                            <input type="text" class="form-control" name="productId" id="productId" placeholder="Enter Product ID (e.g., P003)" required>
                        </div>
                        <div class="mb-3">
                            <label for="productName" class="label-control">Product Name</label>
                            <input type="text" class="form-control" name="productName" id="productName" placeholder="Enter Product Name" required>
                        </div>
                        <div class="mb-3">
                            <label for="productPrice" class="label-control">Price ($)</label>
                            <input type="number" step="0.01" class="form-control" name="productPrice" id="productPrice" placeholder="Enter Price" required>
                        </div>
                        <div class="mb-3">
                            <label for="productStock" class="label-control">Stock</label>
                            <input type="number" class="form-control" name="productStock" id="productStock" placeholder="Enter Stock Quantity" required>
                        </div>
                        <div class="mb-3">
                            <label for="productImage" class="label-control">Product Image</label>
                            <input type="file" class="form-control" name="productImage" id="productImage" accept="image/*">
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" name="add_product" class="btn btn-success">Save Product</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editProductModal" tabindex="-1" aria-labelledby="editProductModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="editProductModalLabel">Edit Product</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <?php if (isset($error)): ?>
                    <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                    <?php endif; ?>
                    <form method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="section" value="<?php echo htmlspecialchars($section); ?>">
                        <input type="hidden" name="productId" id="editProductId">
                        <div class="mb-3">
                            <label for="editProductName" class="label-control">Product Name</label>
                            <input type="text" class="form-control" name="productName" id="editProductName" required>
                        </div>
                        <div class="mb-3">
                            <label for="editProductPrice" class="label-control">Price ($)</label>
                            <input type="number" step="0.01" class="form-control" name="productPrice" id="editProductPrice" required>
                        </div>
                        <div class="mb-3">
                            <label for="editProductStock" class="label-control">Stock</label>
                            <input type="number" class="form-control" name="productStock" id="editProductStock" required>
                        </div>
                        <div class="mb-3">
                            <label for="editProductImage" class="label-control">Product Image</label>
                            <input type="file" class="form-control" name="productImage" id="editProductImage" accept="image/*">
                            <input type="hidden" name="existingImage" id="editExistingImage">
                            <small class="form-text text-muted">Current image: <span id="currentImageName"></span></small>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" name="update_product" class="btn btn-success">Update Product</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="tbltable" id="tbltable">
        <table>
            <thead>
                <tr>
                    <th>Code</th>
                    <th>Name</th>
                    <th>Qty</th>
                    <th>Total</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($_SESSION['cart'] as $index => $item): ?>
                <tr>
                    <td><?php echo htmlspecialchars($item['code']); ?></td>
                    <td><?php echo htmlspecialchars($item['name']); ?></td>
                    <td><?php echo $item['quantity']; ?></td>
                    <td>$<?php echo number_format($item['price'] * $item['quantity'], 2); ?></td>
                    <td>
                        <a href="?section=back-to-store&delete_cart_item=<?php echo $index; ?>" class="delete-btn">Delete</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <div class="text-center p-3">
            <a href="?section=back-to-store" class="btn btn-secondary">Close Cart</a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script>
        // Populate edit modal with product data
        document.querySelectorAll('[data-bs-target="#editProductModal"]').forEach(button => {
            button.addEventListener('click', () => {
                document.getElementById('editProductId').value = button.dataset.id;
                document.getElementById('editProductName').value = button.dataset.name;
                document.getElementById('editProductPrice').value = button.dataset.price;
                document.getElementById('editProductStock').value = button.dataset.stock;
                document.getElementById('editExistingImage').value = button.dataset.img;
                document.getElementById('currentImageName').textContent = button.dataset.img || 'No image';
            });
        });

        // Feedback section: Toggle sidebar
        document.getElementById('toggleSidebarButton')?.addEventListener('click', function() {
            const sidebar = document.getElementById('feedbackSidebar');
            sidebar.classList.toggle('hidden');
        });

        // Feedback section: Select message
        document.querySelectorAll('.message-item')?.forEach(item => {
            item.addEventListener('click', function() {
                document.querySelectorAll('.message-item').forEach(i => i.classList.remove('active'));
                this.classList.add('active');
                const mainPanel = document.getElementById('mainPanel');
                mainPanel.classList.remove('empty');
                mainPanel.innerHTML = `
                    <div class="message-content">
                        <div class="message-header">
                            <div class="avatar">${this.dataset.name.charAt(0).toUpperCase()}</div>
                            <h4>${this.dataset.name}</h4>
                        </div>
                        <div class="message-bubble">
                            <h5>${this.dataset.subject}</h5>
                            <p><strong>ID:</strong> ${this.dataset.id}</p>
                            <p><strong>Email:</strong> ${this.dataset.email}</p>
                            <p><strong>Message:</strong> ${this.dataset.message}</p>
                            <p><strong>Date:</strong> ${this.dataset.created_at}</p>
                            <p><strong>Status:</strong> ${this.dataset.read_status == '0' ? 'Unread' : 'Read'}</p>
                        </div>
                        <div class="message-actions">
                            <form method="POST" style="display:${this.dataset.read_status == '0' ? 'inline' : 'none'};">
                                <input type="hidden" name="mark_read_id" value="${this.dataset.id}">
                                <button type="submit" class="btn btn-custom">
                                    <i class="fas fa-check"></i> Mark as Read
                                </button>
                            </form>
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="delete_id" value="${this.dataset.id}">
                                <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this message?');">
                                    <i class="fas fa-trash"></i> Delete
                                </button>
                            </form>
                        </div>
                    </div>
                `;
            });
        });

        // Feedback section: Search functionality
        document.getElementById('searchInput')?.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const items = document.querySelectorAll('.message-item');
            items.forEach(item => {
                const text = item.textContent.toLowerCase();
                item.style.display = text.includes(searchTerm) ? '' : 'none';
            });
        });

        // Chart.js for Monthly Sales
        const monthlySalesData = <?php echo json_encode(array_reverse($monthly_sales)); ?>;
        const monthlySalesChart = new Chart(document.getElementById('monthlySalesChart'), {
            type: 'line',
            data: {
                labels: monthlySalesData.map(item => item.month),
                datasets: [{
                    label: 'Sales ($)',
                    data: monthlySalesData.map(item => item.sales),
                    borderColor: '#0d6efd',
                    backgroundColor: 'rgba(13, 110, 253, 0.2)',
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        title: { display: true, text: 'Sales ($)' }
                    },
                    x: {
                        title: { display: true, text: 'Month' }
                    }
                }
            }
        });

        // Chart.js for Monthly Orders
        const monthlyOrdersData = <?php echo json_encode(array_reverse($monthly_orders)); ?>;
        const monthlyOrdersChart = new Chart(document.getElementById('monthlyOrdersChart'), {
            type: 'bar',
            data: {
                labels: monthlyOrdersData.map(item => item.month),
                datasets: [{
                    label: 'Orders',
                    data: monthlyOrdersData.map(item => item.orders),
                    backgroundColor: '#28a745',
                    borderColor: '#218838',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        title: { display: true, text: 'Number of Orders' }
                    },
                    x: {
                        title: { display: true, text: 'Month' }
                    }
                }
            }
        });

        // Chart.js for Top Selling Products
        const productSalesData = <?php echo json_encode($product_sales); ?>;
        const productSalesChart = new Chart(document.getElementById('productSalesChart'), {
            type: 'pie',
            data: {
                labels: productSalesData.map(item => item.name),
                datasets: [{
                    label: 'Units Sold',
                    data: productSalesData.map(item => item.quantity),
                    backgroundColor: [
                        '#0d6efd',
                        '#28a745',
                        '#dc3545',
                        '#ffc107',
                        '#17a2b8'
                    ],
                    borderColor: '#fff',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { position: 'right' },
                    title: { display: true, text: 'Top Selling Products' }
                }
            }
        });
    </script>
</body>
</html>
<?php $conn->close(); ?>