<?php

 // ensure session is active
include 'db.php';
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}
// clear cart after successful checkout
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Checkout</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f0f2f5;
            margin: 0;
            padding: 40px;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .checkout-container {
            background-color: #fff;
            padding: 40px 30px;
            max-width: 500px;
            text-align: center;
            border-radius: 10px;
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        h1 {
            color: #27ae60;
            font-size: 28px;
            margin-bottom: 20px;
        }
        p {
            font-size: 18px;
            color: #555;
            margin-bottom: 30px;
        }
        a {
            text-decoration: none;
            color: #fff;
            background-color: #2980b9;
            padding: 12px 24px;
            border-radius: 5px;
            font-size: 16px;
            transition: background-color 0.3s ease;
        }
        a:hover {
            background-color: #3498db;
        }
    </style>
</head>
<body>
<div class="checkout-container">
    <h1>✅ Checkout Complete</h1>
    <p>Thank you for shopping with us!<br>Your order has been processed successfully.</p>
    <a href=" index.php">🏠 Back to Home</a>
    <a href=" logout.php">🏠 logout</a>
     
</div>
</body>
</html>
