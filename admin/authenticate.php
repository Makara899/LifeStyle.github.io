<?php
session_start();
$conn = new mysqli("localhost", "root", "", "ten11-web");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $conn->real_escape_string($_POST['email']);
    $password = $_POST['password']; // You should hash passwords in production

    $sql = "SELECT * FROM admin_login WHERE email = '$email'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        // Verify password (use password_verify() if passwords are hashed)
        if ($password === $row['password']) { // Replace with password_verify() in production
            $_SESSION['user'] = $row['email'];
            header("Location: dashboard.php");
            exit;
        } else {
            echo "<script>alert('Invalid password'); window.location.href='login.php';</script>";
        }
    } else {
        echo "<script>alert('User not found'); window.location.href='login.php';</script>";
    }
}
$conn->close();
?>