<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>About Us - TEN11 Shop</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
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

    .about-hero {
      background: linear-gradient(rgba(219, 121, 187, 0.7), rgba(35, 255, 138, 0.7)), url('https://via.placeholder.com/1920x600/db79bb/23ff8a?text=TEN11+Shop+About+Us') no-repeat center center/cover;
      color: #fff;
      text-align: center;
      padding: 100px 0;
      min-height: 50vh;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .about-hero h1 {
      font-size: 3.5rem;
      font-weight: bold;
      margin-bottom: 20px;
      text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
    }

    .about-hero p {
      font-size: 1.5rem;
      max-width: 800px;
      margin: 0 auto;
      text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.2);
    }

    .section-title {
      text-align: center;
      font-size: 2.5rem;
      font-weight: bold;
      margin-bottom: 40px;
      color: #333;
      position: relative;
    }

    .section-title::after {
      content: '';
      width: 80px;
      height: 4px;
      background: linear-gradient(90deg, rgb(219, 121, 187) 0%, rgba(35, 255, 138, 0.527) 100%);
      display: block;
      margin: 10px auto 0;
      border-radius: 5px;
    }

    .about-section {
      padding: 60px 0;
      background-color: #f8f9fa;
    }

    .about-section:nth-of-type(even) {
      background-color: #fff;
    }

    .about-section h3 {
      color: rgb(219, 121, 187);
      font-size: 2rem;
      margin-bottom: 20px;
    }

    .about-section p {
      font-size: 1.1rem;
      line-height: 1.8;
      color: #555;
    }

    .about-image {
      border-radius: 15px;
      box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
      transition: transform 0.3s ease-in-out;
    }

    .about-image:hover {
      transform: translateY(-5px);
    }

    .value-item {
      text-align: center;
      padding: 30px;
      border-radius: 10px;
      background-color: #fff;
      box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
      transition: all 0.3s ease;
      height: 100%; 
      display: flex;
      flex-direction: column;
      justify-content: center;
    }

    .value-item:hover {
      transform: translateY(-10px);
      box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
    }

    .value-item i {
      font-size: 3rem;
      color: rgba(35, 255, 138, 0.9);
      margin-bottom: 20px;
      transition: color 0.3s ease;
    }

    .value-item:hover i {
      color: rgb(219, 121, 187);
    }

    .value-item h4 {
      font-size: 1.5rem;
      color: #333;
      margin-bottom: 15px;
    }

    .value-item p {
      font-size: 1rem;
      color: #666;
      line-height: 1.6;
    }

    .team-member {
      text-align: center;
      margin-bottom: 30px;
    }

    .team-member img {
      width: 150px;
      height: 150px;
      border-radius: 50%;
      object-fit: cover;
      border: 5px solid rgb(219, 121, 187);
      margin-bottom: 15px;
      transition: transform 0.3s ease;
    }

    .team-member img:hover {
      transform: scale(1.05);
      border-color: rgba(35, 255, 138, 0.9);
    }

    .team-member h4 {
      font-size: 1.3rem;
      color: #333;
      margin-bottom: 5px;
    }

    .team-member p {
      color: #777;
      font-style: italic;
    }

    .cta-section {
      background: linear-gradient(90deg, rgba(35, 255, 138, 0.9) 0%, rgba(219, 121, 187, 0.9) 100%);
      color: #fff;
      text-align: center;
      padding: 80px 0;
    }

    .cta-section h2 {
      font-size: 2.8rem;
      margin-bottom: 25px;
    }

    .cta-section .btn-light {
      background-color: #fff;
      color: rgb(219, 121, 187);
      border: none;
      padding: 15px 40px;
      border-radius: 30px;
      font-size: 1.2rem;
      font-weight: bold;
      transition: all 0.3s ease;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
    }

    .cta-section .btn-light:hover {
      background-color: #f1c40f;
      color: #fff;
      transform: translateY(-3px);
      box-shadow: 0 6px 15px rgba(0, 0, 0, 0.3);
    }

  </style>
</head>
<body>
  <div class="container-fluid bg-secondary ">
    <div class="container ">
        <nav class="navbar navbar-expand-lg bg-secondary   border-bottom border-body" data-bs-theme="dark">
            <div class="container-fluid p-1">
              <a class="navbar-brand" href=""><img src="img/TEN11.png" alt="TEN11 Logo"></a>
              <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarColor01" aria-controls="navbarColor01" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
              </button>
              <div class="collapse navbar-collapse" id="navbarColor01">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                  <li class="nav-item">
                    <a class="nav-link" aria-current="page" href="Project.php">Home</a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link active" aria-current="page" href="about.php">About Us</a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" aria-current="page" href="shop.php">Shop</a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" aria-current="page" href="contact.php">Contact</a>
                  </li>
                </ul>
                <form class="d-flex" role="search">
                  <input class="form-control me-2 search-input" type="search" placeholder="Search Products" aria-label="Search">
                </form>
                   <button type="button" class="btn btn-outline-light ms-3" id="viewCartButton">
                                View Cart <i class="fas fa-shopping-cart"></i>
                            </button>
              </div>
            </div>
          </nav>
    </div>
</div>

<section class="about-hero">
  <div class="container">
    <h1>About TEN11 Shop</h1>
    <p>We bring you the most unique and stylish fashion for every taste.</p>
  </div>
</section>

<section class="about-section">
  <div class="container">
    <h2 class="section-title">Our Story</h2>
    <div class="row align-items-center">
      <div class="col-md-4">
        <img src="img/ZD2.jpg" class="img-fluid about-image" alt="Our Story" style="border-radius: 15px; box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);" height="100" >
      </div>
     <div class="col-md-8" style="text-align: justify;">
  <h3>From a Passion...</h3>
  <p>TEN11 Shop was founded from a deep love for fashion and a desire to share unique styles that can express your true personality. We believe that clothing is not just about dressing up, but also a way to express your emotions and creativity.For many years, we have strived to find and select the highest quality products from talented designers and renowned brands worldwide.</p>
  <!-- <p>For many years, we have strived to find and select the highest quality products from talented designers and renowned brands worldwide. We pay attention to every detail, from fabric quality to tailoring, to ensure our customers receive the very best. We understand that fashion is constantly evolving, which is why we always update our collections with the latest trends to cater to your unique tastes.</p> -->
  <p>At TEN11 Shop, we take pride in offering a wide selection of clothing and accessories that cater to different occasions and personal preferences. Whether you are looking for an outfit for work, a night out, or simple everyday wear, you will find something that suits your style here. Our mission is to help you feel confident and comfortable in your own skin, through the clothes you choose. We don't just sell clothes; we sell an experience — an experience where you can express yourself freely and creatively.</p>
  <p>We believe that fashion should be accessible to everyone, and that's why we strive to offer high-quality products at affordable prices. Our team is dedicated to providing excellent customer service and is always ready to assist you in finding the perfect fit. Thank you for choosing TEN11 Shop as your fashion destination. We hope to serve you soon!</p>
</div>
    </div>
  </div>
</section>

<section class="about-section bg-light">
  <div class="container">
    <h2 class="section-title">Our Mission & Values</h2>
    <div class="row">
      <div class="col-md-6 mb-4">
        <h3>Mission</h3>
       <p>Our mission is to provide the best shopping experience, connecting our customers to the latest and most unique fashion. We are committed to offering high-quality products, reasonable prices, and excellent customer service. We want everyone to feel confident and beautiful when wearing our products.</p>

       <p>We believe that fashion is more than just clothing. It's a form of self-expression and a way to showcase your unique personality. That's why we meticulously curate each item, reflecting current trends while maintaining timeless appeal. From chic designs to comfortable everyday wear, our selections are crafted to cater to diverse tastes and needs.</p>

       <p>At TEN11 Shop, we value the relationships we build with our customers. We are here to assist you in finding the perfect styles that complement your look and elevate your confidence. Your shopping experience is our priority, and we strive to make it easy, enjoyable, and fulfilling.Join us on this fashion journey and let us help you express your unique style to the world.</p>
      </div>
      <div class="col-md-6 mb-4">
        <h3>Core Values</h3>
        <div class="row g-4">
          <div class="col-sm-6">
            <div class="value-item">
              <i class="fas fa-gem"></i>
              <h4>Quality</h4>
              <p>We are committed to providing products of excellent quality.</p>
            </div>
          </div>
          <div class="col-sm-6">
            <div class="value-item">
              <i class="fas fa-lightbulb"></i>
              <h4>Innovation</h4>
              <p>We constantly seek new and leading styles.</p>
            </div>
          </div>
          <div class="col-sm-6">
            <div class="value-item">
              <i class="fas fa-heart"></i>
              <h4>Customer Service</h4>
              <p>We care and provide warm service.</p>
            </div>
          </div>
          <div class="col-sm-6">
            <div class="value-item">
              <i class="fas fa-handshake"></i>
              <h4>Responsibility</h4>
              <p>We adhere to honest business practices.</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<section class="cta-section">
  <div class="container">
    <h2>Ready for a New Style?</h2>
    <a href="shop.php" class="btn btn-light">Shop Now <i class="fas fa-arrow-right ms-2"></i></a>
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

<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">Add Product</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="popup">
                        <label for="code" class="label-control">Enter Code</label>
                        <input type="text" id="code" class="form-control" placeholder="Enter Code">
                        <label for="name" class="label-control">Enter Product Name</label>
                        <input type="text" id="name" class="form-control" placeholder="Enter Product Name">
                        <label for="qty" class="label-control">Enter Quantity</label>
                        <input type="number" id="qty" class="form-control" placeholder="Enter Quantity" min="1" value="1">
                        <label for="price" class="label-control">Enter Price</label>
                        <input type="number" id="price" class="form-control" placeholder="Enter Price" step="0.01">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-success" id="enterButton">Add</button>
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
                    <th>Quantity</th>
                    <th>Total</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody id="tblBody">
            </tbody>
        </table>
        <div class="text-center p-3">
    <button type="button" class="btn btn-secondary" id="closeTableButton">Close Cart</button>
</div>
    </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
  <script>
   
    document.addEventListener('DOMContentLoaded', function() {
        var tbltable = document.getElementById('tbltable');
        var viewCartButton = document.getElementById('viewCartButton');
        var closeTableButton = document.getElementById('closeTableButton'); 
        var enterButton = document.getElementById('enterButton');
        var tblBody = document.getElementById('tblBody'); 

       
        enterButton.addEventListener('click', function() {
            var code = document.getElementById("code").value;
            var name = document.getElementById("name").value;
            var qty = parseFloat(document.getElementById("qty").value);z
            var price = parseFloat(document.getElementById("price").value);
            var total = qty * price;

            
            if (!code || !name || isNaN(qty) || isNaN(price) || qty <= 0 || price <= 0) {
                alert("Please enter correct and complete information!");
                return;
            }

            var newRow = `
                <tr>
                    <td>${code}</td>
                    <td>${name}</td>
                    <td>${qty}</td>
                    <td>$${total.toFixed(2)}</td>
                    <td><button type="button" class="btn btn-danger btn-sm delete-btn"><i class="fas fa-trash-alt"></i></button></td>
                </tr>`;

            tblBody.innerHTML += newRow; 

            
            document.getElementById("code").value = "";
            document.getElementById("name").value = "";
            document.getElementById("qty").value = "1";
            document.getElementById("price").value = "";
            document.getElementById("code").focus();

            tbltable.style.display = "block";

          
            var modalElement = document.getElementById('exampleModal');
            var modal = bootstrap.Modal.getInstance(modalElement);
            if (modal) {
                modal.hide();
            }
        });

   
        viewCartButton.addEventListener('click', function() {
            if (tbltable.style.display === 'block') {
                tbltable.style.display = 'none';
            } else {
                tbltable.style.display = 'block';
            }
        });

        
        closeTableButton.addEventListener('click', function() {
            tbltable.style.display = 'none';
        });

        tblBody.addEventListener('click', function(event) {
            if (event.target.classList.contains('delete-btn') || event.target.closest('.delete-btn')) {
                const button = event.target.closest('.delete-btn');
                const row = button.closest('tr');
                if (row) {
                    row.remove(); 
                }
             
                event.stopPropagation(); 
            }
        });

        document.addEventListener('click', function(event) {
            var isClickInsideTable = tbltable.contains(event.target);
            var isAddToCartButton = event.target.classList.contains('btn-custom');
            var isModalContent = event.target.closest('.modal-content');
            var isViewCartButton = event.target === viewCartButton || viewCartButton.contains(event.target); 
            
            var isDeleteButton = event.target.classList.contains('delete-btn') || event.target.closest('.delete-btn');

            if (!isClickInsideTable && !isAddToCartButton && !isModalContent && !isViewCartButton && !isDeleteButton) {
                tbltable.style.display = 'none';
            }
        });
    });
  </script>
</body>
</html>