<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TEN11 Online Shop</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDjOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
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
        }
        @media (max-width: 768px) {
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
        @media (max-width: 360px) {
            .carousel-item {
                height: 250px; 
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
        .card {
            /* border: none; */
        }
        .search-input {
            background-color: #f0f0f0; 
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
        .button-container {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .btn-custom {
            margin-right: 10px;
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
            .discount-boxtxt {
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
        .contact-section {
            padding: 60px 0;
            background-color: #f8f9fa;
        }
        .contact-section h2 {
            text-align: center;
            margin-bottom: 50px;
            font-weight: bold;
            color: #343a40;
        }
        .contact-form, .contact-info {
            background: #ffffff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        .contact-info h4, .contact-form h4 {
            margin-bottom: 20px;
            font-weight: 600;
            padding-bottom: 10px;
        }
        .contact-form h4 {
            border-bottom: 2px solid #0D6EFD;
        }
        .contact-info h4 {
            border-bottom: 2px solid #28A745;
        }
        .contact-info p {
            font-size: 16px;
            color: #555;
            display: flex;
            align-items: center;
            margin-bottom: 15px;
        }
        .contact-info p i {
            font-size: 20px;
            margin-right: 15px;
            width: 30px;
            text-align: center;
            color: #28A745;
        }
        .form-control {
            height: 50px;
            border-radius: 5px;
        }
        textarea.form-control {
            height: auto;
        }
        .btn-submit {
            background-color: #0D6EFD;
            color: white;
            padding: 12px 30px;
            border-radius: 50px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .btn-submit:hover {
            background-color: #0B5ED7;
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(0,0,0,0.15);
        }
        .map-container {
            border-radius: 8px;
            overflow: hidden;
            margin-top: 20px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
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
                                <a class="nav-link active" aria-current="page" href="Project.php">Home</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link active" aria-current="page" href="about.php">About Us</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link active" aria-current="page" href="shop.php">Shop</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link active" aria-current="page" href="contact.php">Contact</a>
                            </li>
                        </ul>
                        <!-- <form class="d-flex" role="search">
                            <input class="form-control me-2" type="search" placeholder="Search product" aria-label="Search">
                        </form>
                        <button type="button" class="btn btn-outline-light ms-3" id="viewCartButton">
                            View Cart <i class="fas fa-shopping-cart"></i>
                        </button> -->
                    </div>
                </div>
            </nav>
        </div>
    </div>

    <section id="contact-section" class="contact-section">
        <div class="container">
            <h2>Contact Us</h2>
            <div class="row g-5">
                <div class="col-lg-6">

                <?php
                date_default_timezone_set('Asia/Phnom_Penh');
                $cn=new mysqli("localhost","root","","ten11-web");
                
               if ($_SERVER['REQUEST_METHOD'] == 'POST') {

                $name=$_POST['name'];
                $email=$_POST['email'];
                $subject=$_POST['subject'];
                $message=$_POST['message'];
                $created_at = date('Y-m-d H:i:s');

                $sql="INSERT INTO `contact`(`id`, `name`, `email`, `subject`, `message`, `date`) 
                VALUES (null,'$name','$email','$subject','$message','$created_at')";
                $cn->query($sql);
               }

                
                ?>
                    <div class="contact-form">
                        <h4><i class="fas fa-paper-plane me-2"></i>Send us a Message</h4>
                        <form action="" method="post">
                            <div class="mb-3">
                                <input type="text" class="form-control" name="name" placeholder="Your Name">
                            </div>
                            <div class="mb-3">
                                <input type="email" class="form-control" name="email" placeholder="Your Email">
                            </div>
                            <div class="mb-3">
                                <input type="text" class="form-control" name="subject" placeholder="Subject">
                            </div>
                            <div class="mb-3">
                                <textarea class="form-control" name="message" rows="5" placeholder="Your Message"></textarea>
                            </div>
                            <button type="submit" class="btn btn-submit">Send Message</button>
                        </form>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="contact-info">
                        <h4><i class="fas fa-info-circle me-2"></i>Contact Information</h4>
                        <p><i class="fas fa-map-marker-alt"></i><strong>Address:</strong> 123 Fashion St, Socheat City, TC 12345</p>
                        <p><i class="fas fa-phone-alt"></i><strong>Phone:</strong> +123 456 7890</p>
                        <p><i class="fas fa-envelope"></i><strong>Email:</strong> contact@ten11.com</p>
                        <p><i class="fas fa-clock"></i><strong>Working Hours:</strong> Mon - Fri: 9:00 AM - 8:00 PM</p>
                        <div class="map-container">
                             <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3908.851189798081!2d104.92113331478954!3d11.562479991790435!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3109513998a44b99%3A0x44a7a8f8a614d640!2sIndependence%20Monument!5e0!3m2!1sen!2skh!4v1626343363365!5m2!1sen!2skh" width="100%" height="220" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
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
                    <a href="https://www.facebook.com/login/?privacy_mutation_token=eyJ0eXBlIjowLCJjcmVhdGlvbl90aW1lIjoxNzUxODU4NTI1LCJjYWxsc2l0ZV9pZCI6MjY5NTQ4NDUzMDcyMDk1MX0%3D" class="text-white me-2">
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
            <p>&copy; 2024 TEN11. All rights reserved.</p>
        </div>
    </footer>

    </body>
<script>
   
</script>
</html>