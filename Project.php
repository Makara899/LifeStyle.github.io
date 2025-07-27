<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

// Database connection function
function getDbConnection() {
    $cn = new mysqli("localhost", "root", "", "ten11-web");
    if ($cn->connect_error) {
        die('Connection failed: ' . $cn->connect_error);
    }
    return $cn;
}

// Handle registration form submission
if (isset($_GET['action']) && $_GET['action'] === 'register' && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $cn = getDbConnection();

    if (empty($_POST['username']) || empty($_POST['email']) || empty($_POST['password']) || empty($_POST['confirmPassword'])) {
        echo '<script>alert("All fields are required"); window.location.href="shop.php";</script>';
        $cn->close();
        exit;
    }

    $username = $cn->real_escape_string($_POST['username']);
    $email = $cn->real_escape_string($_POST['email']);
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirmPassword'];

    if ($password !== $confirmPassword) {
        echo '<script>alert("Passwords do not match!"); window.location.href="shop.php";</script>';
        $cn->close();
        exit;
    }

    // Check if email already exists
    $query = "SELECT email FROM `register` WHERE email = '$email'";
    $result = $cn->query($query);
    if ($result->num_rows > 0) {
        echo '<script>alert("Email already registered!"); window.location.href="shop.php";</script>';
        $cn->close();
        exit;
    }

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $date = date('Y-m-d');

    $query = "INSERT INTO `register` (`username`, `email`, `password`, `confirm_password`, `Joined_date`) VALUES ('$username', '$email', '$hashedPassword', '$hashedPassword', '$date')";
    if ($cn->query($query)) {
        echo '<script>alert("Registration successful!"); window.location.href="shop.php";</script>';
    } else {
        echo '<script>alert("Error: ' . $cn->error . '"); window.location.href="shop.php";</script>';
    }

    $cn->close();
    exit;
}

// Handle login form submission
if (isset($_GET['action']) && $_GET['action'] === 'login' && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $cn = getDbConnection();

    if (empty($_POST['email']) || empty($_POST['password'])) {
        echo '<script>alert("Email and password are required"); window.location.href="shop.php";</script>';
        $cn->close();
        exit;
    }

    $email = $cn->real_escape_string($_POST['email']);
    $password = $_POST['password'];

    $query = "SELECT * FROM `register` WHERE email = '$email'";
    $result = $cn->query($query);

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['loggedin'] = true;
            $_SESSION['email'] = $email;
            $_SESSION['username'] = $user['username'];
            echo '<script>alert("Login successful!"); window.location.href="shop.php";</script>';
        } else {
            echo '<script>alert("Invalid password"); window.location.href="shop.php";</script>';
        }
    } else {
        echo '<script>alert("Email not found"); window.location.href="shop.php";</script>';
    }

    $cn->close();
    exit;
}

// Handle confirm payment
if (isset($_GET['action']) && $_GET['action'] === 'confirm_payment' && $_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!isset($_SESSION['loggedin']) || !$_SESSION['loggedin']) {
        echo '<script>alert("Please log in to confirm payment"); window.location.href="shop.php";</script>';
        exit;
    }

    if (empty($_POST['payment_method']) || empty($_POST['cart'])) {
        echo '<script>alert("Payment method or cart is missing"); window.location.href="shop.php";</script>';
        exit;
    }

    $cn = getDbConnection();
    $username = $cn->real_escape_string($_SESSION['username']);
    $email = $cn->real_escape_string($_SESSION['email']);
    $payment_method = $cn->real_escape_string($_POST['payment_method']);
    $status = ($payment_method === 'cod') ? 'Pending' : 'Complete';
    $date = date('Y-m-d');
    $cart = json_decode($_POST['cart'], true);

    if (empty($cart)) {
        echo '<script>alert("Cart is empty"); window.location.href="shop.php";</script>';
        $cn->close();
        exit;
    }

    $success = true;
    foreach ($cart as $product) {
        $code = $cn->real_escape_string($product['code']);
        $product_name = $cn->real_escape_string($product['name']);
        $qty = (int)$product['qty'];
        $price = (float)$product['price'];
        $img = $cn->real_escape_string($product['img']);
        $total = $qty * $price;

        // Insert or update product in product_admin
        $query = "SELECT id, qty, total FROM `product_admin` WHERE email = '$email' AND code = '$code'";
        $result = $cn->query($query);

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $newQty = $row['qty'] + $qty;
            $newTotal = $row['total'] + $total;
            $query = "UPDATE `product_admin` SET qty = $newQty, total = $newTotal, status = '$status', payment_method = '$payment_method', date = '$date' WHERE id = {$row['id']}";
        } else {
            $query = "INSERT INTO `product_admin` (`name`, `email`, `code`, `product_name`, `qty`, `total`, `img`, `date`, `payment_method`, `status`) VALUES ('$username', '$email', '$code', '$product_name', $qty, $total, '$img', '$date', '$payment_method', '$status')";
        }

        if (!$cn->query($query)) {
            $success = false;
            echo '<script>alert("Error: ' . $cn->error . '"); window.location.href="shop.php";</script>';
            $cn->close();
            exit;
        }
    }

    if ($success) {
        echo '<script>alert("Payment confirmed and products added to order"); window.location.href="shop.php";</script>';
    }

    $cn->close();
    exit;
}

// Handle logout
if (isset($_GET['action']) && $_GET['action'] === 'logout' && $_SERVER['REQUEST_METHOD'] == 'POST') {
    session_unset();
    session_destroy();
    echo '<script>alert("Logged out successfully"); window.location.href="shop.php";</script>';
    exit;
}

// Fetch products from the database
$cn = getDbConnection();
$query = "SELECT * FROM `add_product`";
$result = $cn->query($query);
$products = [];
while ($row = $result->fetch_assoc()) {
    $products[] = $row;
}
$cn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TEN11 - Shop</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
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
            left: 0;
            width: 100%;
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
        .navbar-toggler {
            border: none;
            outline: none;
        }
        .navbar-toggler-icon {
            background-image: url('data:image/svg+xml;charset=utf8,%3Csvg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"%3E%3Cpath stroke="%23f1c40f" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/%3E%3C/svg%3E');
        }
        .form-control {
            border-radius: 50px;
        }
        body {
            padding-top: 70px;
        }
        @media (max-width: 768px) {
            .navbar-nav {
                text-align: center;
            }
            .navbar-nav .nav-link {
                padding: 10px;
            }
            .carousel-item {
                height: 250px;
            }
            .card {
                margin-bottom: 20px;
            }
            .row {
                display: flex;
                flex-wrap: wrap;
                justify-content: center;
            }
            .col-xl-3, .col-lg-4, .col-md-6, .col-sm-12 {
                display: flex;
                justify-content: center;
            }
        }
        @media (max-width: 884px) {
            .carousel-item {
                height: 50vh;
            }
        }
        @media (max-width: 428px) {
            .carousel-item {
                height: 250px;
            }
        }
        .card img {
            transition: transform 0.3s, box-shadow 0.1s;
            cursor: pointer;
        }
        .card img:hover {
            transform: scale(1.05);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
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
        .card-body {
            position: relative;
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
            100% {
                transform: translateX(100%);
            }
            0% {
                transform: translateX(-100%);
            }
        }
        @media (max-width: 768px) {
            .discount-boxtxt {
                font-size: 14px;
            }
        }
        @media (max-width: 576px) {
            .discount-box {
                font-size: 12px;
            }
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
        .modal-body .form-control::placeholder {
            color: #aaa;
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
            display: none;
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
        .tbltable td:nth-child(2) {
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .loading::after {
            content: '';
            display: inline-block;
            width: 16px;
            height: 16px;
            border: 2px solid #fff;
            border-radius: 50%;
            border-top-color: transparent;
            animation: spin 1s linear infinite;
            margin-left: 10px;
        }
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        .payment-option {
            cursor: pointer;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 8px;
            margin-bottom: 10px;
            transition: all 0.3s ease;
        }
        .payment-option:hover {
            background-color: #f8f9fa;
            border-color: #28a745;
        }
        .payment-option.selected {
            border-color: #28a745;
            background-color: #e9f7ef;
        }
        .qr-code-img {
            max-width: 200px;
            margin: 20px auto;
            display: block;
        }
    </style>
</head>
<body>
    <div class="container-fluid bg-secondary">
        <div class="container">
            <nav class="navbar navbar-expand-lg bg-secondary border-bottom border-body" data-bs-theme="dark">
                <div class="container-fluid p-1">
                    <a class="navbar-brand" href="#"><img src="img/TEN11.png" alt="TEN11 Logo"></a>
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarColor01" aria-controls="navbarColor01" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <div class="collapse navbar-collapse" id="navbarColor01">
                        <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                            <li class="nav-item">
                                <a class="nav-link" href="Project.php">Home</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="about.php">About Us</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="shop.php">Shop</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="contact.php">Contact</a>
                            </li>
                        </ul>
                        <form class="d-flex" role="search" id="searchForm">
                            <input class="form-control me-2" type="search" placeholder="Search product" aria-label="Search" id="searchInput">
                        </form>
                        <div class="d-flex ms-3" id="authButtons">
                            <?php if (isset($_SESSION['loggedin']) && $_SESSION['loggedin']): ?>
                                <button type="button" class="btn btn-outline-light me-2" id="logoutButton">LOGOUT</button>
                            <?php else: ?>
                                <button type="button" class="btn btn-outline-light me-2" data-bs-toggle="modal" data-bs-target="#loginModal">LOGIN</button>
                                <button type="button" class="btn btn-outline-light me-2" data-bs-toggle="modal" data-bs-target="#registerModal">REGISTER</button>
                            <?php endif; ?>
                        </div>
                        <button type="button" class="btn btn-outline-light ms-3" id="viewCartButton">
                            View Cart <i class="fas fa-shopping-cart"></i>
                        </button>
                    </div>
                </div>
            </nav>
        </div>
    </div>

    <div id="carouselExample" class="carousel slide" data-bs-ride="carousel" data-bs-interval="3500">
        <div class="carousel-inner">
            <div class="carousel-item active">
                <img src="img/slide6.jpg" class="d-block w-100" alt="Slide 1">
            </div>
            <div class="carousel-item">
                <img src="img/slide8.jpg" class="d-block w-100" alt="Slide 2">
            </div>
            <div class="carousel-item">
                <img src="img/slide5.jpg" class="d-block w-100" alt="Slide 3">
            </div>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#carouselExample" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Previous</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#carouselExample" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Next</span>
        </button>
    </div>

    <div class="discount-box">
        <div class="discount-boxtxt">
            <p>🎉 Enjoy 50% Off 🛒 on your 1st order only in TEN11 Mobile App and Website. Valid from today onward.</p>
        </div>
    </div>

    <div class="container mt-5">
        <div class="row g-3" id="productRow1">
            <?php
            $count = 0;
            foreach ($products as $row) {
                if ($count < 4) {
            ?>
                <div class="col-xl-3 col-lg-4 col-md-6 col-sm-12 product-card" data-name="<?php echo htmlspecialchars($row['name']); ?>">
                    <div class="card" style="width: 18rem;">
                        <img src="./admin/img/<?php echo htmlspecialchars($row['img']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($row['name']); ?>">
                        <div class="card-body">
                            <p><strong>$<?php echo number_format($row['price'], 2); ?></strong><br><span style="color: gray;"><?php echo htmlspecialchars($row['name']); ?></span></p>
                            <button type="button" class="btn btn-custom add-to-cart-btn" 
                                    data-code="<?php echo htmlspecialchars($row['product_id']); ?>" 
                                    data-name="<?php echo htmlspecialchars($row['name']); ?>" 
                                    data-price="<?php echo number_format($row['price'], 2); ?>" 
                                    data-img="<?php echo htmlspecialchars($row['img']); ?>">
                                Add to cart
                            </button>
                            <div class="discount-box1">50% off</div>
                        </div>
                    </div>
                </div>
            <?php
                    $count++;
                } else {
                    break;
                }
            }
            ?>
        </div>
    </div>

    <div class="discount-box mt-3">
        <div class="discount-boxtxt">
            <p>Enjoy 30% Off 🛒 on your 1st order only in TEN11 Mobile App and Website. Valid from today onward.</p>
        </div>
    </div>

    <div class="container mt-5">
        <div class="row g-3" id="productRow2">
            <?php
            $count = 0;
            foreach ($products as $row) {
                if ($count >= 4) {
            ?>
                <div class="col-xl-3 col-lg-4 col-md-6 col-sm-12 product-card" data-name="<?php echo htmlspecialchars($row['name']); ?>">
                    <div class="card" style="width: 18rem;">
                        <img src="./admin/img/<?php echo htmlspecialchars($row['img']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($row['name']); ?>">
                        <div class="card-body">
                            <p><strong>$<?php echo number_format($row['price'], 2); ?></strong><br><span style="color: gray;"><?php echo htmlspecialchars($row['name']); ?></span></p>
                            <button type="button" class="btn btn-custom add-to-cart-btn" 
                                    data-code="<?php echo htmlspecialchars($row['product_id']); ?>" 
                                    data-name="<?php echo htmlspecialchars($row['name']); ?>" 
                                    data-price="<?php echo number_format($row['price'], 2); ?>" 
                                    data-img="<?php echo htmlspecialchars($row['img']); ?>">
                                Add to cart
                            </button>
                            <div class="discount-box1">30% off</div>
                        </div>
                    </div>
                </div>
            <?php
                }
                $count++;
            }
            ?>
        </div>
    </div>

    <footer class="bg-dark text-white py-4 mt-5">
        <div class="container text-center">
            <div class="row">
                <div class="col-md-4">
                    <h5>About Us</h5>
                    <p>TEN11 Shop brings you trendy and high-quality fashion.</p>
                </div>
                <div class="col-md-4">
                    <h5>Contact</h5>
                    <p>Email: contact@ten11.com</p>
                    <p>Phone: +123 456 7890</p>
                    <p>Address: 123 Fashion St, Socheat City, TC 12345</p>
                </div>
                <div class="col-md-4">
                    <h5>Follow Us</h5>
                    <a href="https://www.facebook.com/login/" class="text-white me-2">
                        <i class="fab fa-facebook-f"></i> Facebook
                    </a>
                    <a href="https://twitter.com/login" class="text-white me-2">
                        <i class="fab fa-twitter"></i> Twitter
                    </a>
                    <a href="https://www.instagram.com/accounts/login/" class="text-white">
                        <i class="fab fa-instagram"></i> Instagram
                    </a>
                </div>
            </div>
            <hr class="my-3">
            <p>© 2024 TEN11. All rights reserved.</p>
        </div>
    </footer>

    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">Adding Product to Cart</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Product is being added to your cart...</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">OK</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="loginModalLabel">Login</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="loginForm" action="?action=login" method="POST">
                        <div class="mb-3">
                            <label for="loginEmail" class="form-label">Email address</label>
                            <input type="email" class="form-control" id="loginEmail" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label for="loginPassword" class="form-label">Password</label>
                            <input type="password" class="form-control" id="loginPassword" name="password" required>
                        </div>
                        <button type="submit" class="btn btn-success w-100">Login</button>
                    </form>
                </div>
                <div class="modal-footer justify-content-center">
                    <p class="mb-0">Don't have an account? <a href="#" data-bs-toggle="modal" data-bs-target="#registerModal" data-bs-dismiss="modal">Register here</a></p>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="registerModal" tabindex="-1" aria-labelledby="registerModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="registerModalLabel">Register</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="registerForm" action="?action=register" method="POST">
                        <div class="mb-3">
                            <label for="registerUsername" class="form-label">Username</label>
                            <input type="text" class="form-control" id="registerUsername" name="username" required>
                        </div>
                        <div class="mb-3">
                            <label for="registerEmail" class="form-label">Email address</label>
                            <input type="email" class="form-control" id="registerEmail" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label for="registerPassword" class="form-label">Password</label>
                            <input type="password" class="form-control" id="registerPassword" name="password" required>
                        </div>
                        <div class="mb-3">
                            <label for="confirmPassword" class="form-label">Confirm Password</label>
                            <input type="password" class="form-control" id="confirmPassword" name="confirmPassword" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Register</button>
                    </form>
                </div>
                <div class="modal-footer justify-content-center">
                    <p class="mb-0">Already have an account? <a href="#" data-bs-toggle="modal" data-bs-target="#loginModal" data-bs-dismiss="modal">Login here</a></p>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="paymentModal" tabindex="-1" aria-labelledby="paymentModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="paymentModalLabel">Select Payment Method</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="paymentForm" action="?action=confirm_payment" method="POST">
                        <input type="hidden" name="cart" id="cartInput">
                        <div class="payment-option" data-method="cod">
                            <h5>Cash on Delivery</h5>
                            <p>Pay when you receive your order</p>
                            <input type="radio" name="payment_method" value="cod" required>
                        </div>
                        <div class="payment-option" data-method="qr">
                            <h5>QR Code Payment</h5>
                            <p>Scan the QR code to pay</p>
                            <input type="radio" name="payment_method" value="qr" required>
                            <img src="img/qr.jpg" class="qr-code-img" alt="QR Code" style="display: none;">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-success" id="confirmPaymentButton" disabled>Confirm Payment</button>
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
            <tbody id="tblBody"></tbody>
        </table>
        <div class="text-center p-3">
            <button type="button" class="btn btn-success" id="proceedToPaymentButton">Proceed to Payment</button>
            <button type="button" class="btn btn-secondary" id="closeTableButton">Close Cart</button>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', () => {
        const tblBody = document.getElementById('tblBody');
        const viewCartButton = document.getElementById('viewCartButton');
        const tblTable = document.getElementById('tbltable');
        const closeTableButton = document.getElementById('closeTableButton');
        const loginForm = document.getElementById('loginForm');
        const registerForm = document.getElementById('registerForm');
        const authButtons = document.getElementById('authButtons');
        const logoutButton = document.getElementById('logoutButton');
        const searchForm = document.getElementById('searchForm');
        const searchInput = document.getElementById('searchInput');
        const productRow1 = document.getElementById('productRow1');
        const productRow2 = document.getElementById('productRow2');
        const proceedToPaymentButton = document.getElementById('proceedToPaymentButton');
        const paymentModal = document.getElementById('paymentModal');
        const confirmPaymentButton = document.getElementById('confirmPaymentButton');
        const paymentOptions = document.querySelectorAll('.payment-option');
        const paymentForm = document.getElementById('paymentForm');
        const cartInput = document.getElementById('cartInput');

        let cart = [];
        let isLoggedIn = <?php echo isset($_SESSION['loggedin']) && $_SESSION['loggedin'] ? 'true' : 'false'; ?>;
        let pendingProduct = null;
        let isCartOpen = false;

        tblTable.style.display = 'none';

        function showLoading(button) {
            button.classList.add('loading');
            button.disabled = true;
        }

        function hideLoading(button) {
            button.classList.remove('loading');
            button.disabled = false;
        }

        function renderTable() {
            tblBody.innerHTML = '';
            cart.forEach(product => {
                const row = tblBody.insertRow();
                row.insertCell(0).textContent = product.code;
                const nameCell = row.insertCell(1);
                const img = document.createElement('img');
                img.src = `./admin/img/${product.img}`;
                img.style.width = '50px';
                img.style.height = '50px';
                img.style.objectFit = 'cover';
                img.style.marginRight = '10px';
                nameCell.appendChild(img);
                nameCell.append(product.name);
                row.insertCell(2).textContent = product.qty;
                row.insertCell(3).textContent = `$${parseFloat(product.total).toFixed(2)}`;
                const actionCell = row.insertCell(4);
                const deleteButton = document.createElement('button');
                deleteButton.innerHTML = '<i class="fas fa-trash-alt"></i>';
                deleteButton.classList.add('btn', 'btn-danger', 'btn-sm', 'delete-btn');
                deleteButton.addEventListener('click', () => {
                    showLoading(deleteButton);
                    removeProduct(product.code, deleteButton);
                });
                actionCell.appendChild(deleteButton);
            });
            proceedToPaymentButton.style.display = cart.length > 0 ? 'inline-block' : 'none';
        }

        viewCartButton.addEventListener('click', () => {
            if (!isLoggedIn) {
                const loginModal = new bootstrap.Modal(document.getElementById('loginModal'));
                loginModal.show();
                return;
            }
            isCartOpen = true;
            tblTable.style.display = 'block';
            renderTable();
        });

        closeTableButton.addEventListener('click', () => {
            isCartOpen = false;
            tblTable.style.display = 'none';
        });

        document.querySelectorAll('.add-to-cart-btn').forEach(button => {
            button.addEventListener('click', () => {
                if (!isLoggedIn) {
                    pendingProduct = {
                        code: button.dataset.code,
                        name: button.dataset.name,
                        price: parseFloat(button.dataset.price),
                        qty: 1,
                        img: button.dataset.img
                    };
                    const loginModal = new bootstrap.Modal(document.getElementById('loginModal'));
                    loginModal.show();
                    return;
                }
                showLoading(button);
                addProduct(button.dataset.code, button.dataset.name, 1, parseFloat(button.dataset.price), button.dataset.img, button);
            });
        });

        function addProduct(code, name, qty, price, img, button) {
            const addModal = new bootstrap.Modal(document.getElementById('exampleModal'));
            addModal.show();
            setTimeout(() => addModal.hide(), 1000);

            const existingProduct = cart.find(product => product.code === code);
            if (existingProduct) {
                existingProduct.qty += qty;
                existingProduct.total = existingProduct.qty * existingProduct.price;
            } else {
                cart.push({
                    code,
                    name,
                    qty,
                    price,
                    total: qty * price,
                    img
                });
            }

            hideLoading(button);
            alert('Product added to cart');
            renderTable();
        }

        function removeProduct(code, button) {
            cart = cart.filter(product => product.code !== code);
            hideLoading(button);
            alert('Product removed from cart');
            renderTable();
        }

        proceedToPaymentButton.addEventListener('click', () => {
            if (cart.length === 0) {
                alert('Your cart is empty');
                return;
            }
            cartInput.value = JSON.stringify(cart);
            const paymentModalInstance = new bootstrap.Modal(paymentModal);
            paymentModalInstance.show();
        });

        paymentOptions.forEach(option => {
            option.addEventListener('click', () => {
                paymentOptions.forEach(opt => {
                    opt.classList.remove('selected');
                    const qrImg = opt.querySelector('.qr-code-img');
                    if (qrImg) qrImg.style.display = 'none';
                });
                option.classList.add('selected');
                option.querySelector('input[type="radio"]').checked = true;
                confirmPaymentButton.disabled = false;
                if (option.dataset.method === 'qr') {
                    const qrImg = option.querySelector('.qr-code-img');
                    qrImg.style.display = 'block';
                }
            });
        });

        confirmPaymentButton.addEventListener('click', () => {
            if (!paymentForm.querySelector('input[name="payment_method"]:checked')) {
                alert('Please select a payment method');
                return;
            }
            showLoading(confirmPaymentButton);
            paymentForm.submit();
        });

        if (loginForm) {
            loginForm.addEventListener('submit', (event) => {
                event.preventDefault();
                showLoading(loginForm.querySelector('button[type="submit"]'));
                loginForm.submit();
            });
        }

        if (registerForm) {
            registerForm.addEventListener('submit', (event) => {
                event.preventDefault();
                const submitButton = registerForm.querySelector('button[type="submit"]');
                showLoading(submitButton);

                const password = document.getElementById('registerPassword').value;
                const confirmPassword = document.getElementById('confirmPassword').value;
                if (password !== confirmPassword) {
                    hideLoading(submitButton);
                    alert('Passwords do not match!');
                    return;
                }

                registerForm.submit();
            });
        }

        if (logoutButton) {
            logoutButton.addEventListener('click', () => {
                showLoading(logoutButton);
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '?action="logout"';
                document.body.appendChild(form);
                form.submit();
            });
        }

        searchInput.addEventListener('input', () => {
            const searchTerm = searchInput.value.trim().toLowerCase();
            const productCards1 = productRow1.querySelectorAll('.product-card');
            const productCards2 = productRow2.querySelectorAll('.product-card');

            productCards1.forEach(card => {
                const productName = card.dataset.name.toLowerCase();
                card.style.display = productName.includes(searchTerm) ? '' : 'none';
            });

            productCards2.forEach(card => {
                const productName = card.dataset.name.toLowerCase();
                card.style.display = productName.includes(searchTerm) ? '' : 'none';
            });
        });
    });
    </script>
</body>
</html>