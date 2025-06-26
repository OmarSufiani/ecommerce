<?php
session_start();
include 'functions.php';
if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}


$id = $_GET['id'] ?? 0;
$product = getJersey($conn, $id);
if (!$product) die("Jersey not found.");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($product['name']) ?></title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 40px;
        }
        .product-container {
            background-color: #fff;
            max-width: 600px;
            margin: auto;
            padding: 30px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            border-radius: 8px;
        }
        h2 {
            color: #2c3e50;
            margin-bottom: 10px;
        }
        p {
            font-size: 16px;
            color: #555;
            margin: 8px 0;
        }
        .price {
            font-size: 18px;
            color: #27ae60;
            font-weight: bold;
        }
        form {
            margin-top: 20px;
        }
        input[type="number"] {
            width: 60px;
            padding: 6px;
            font-size: 16px;
            margin-right: 10px;
        }
        button {
            padding: 8px 16px;
            background-color: #2980b9;
            color: #fff;
            border: none;
            font-size: 16px;
            border-radius: 4px;
            cursor: pointer;
        }
        button:hover {
            background-color: #3498db;
        }
    </style>
</head>
<body>
<div class="product-container">
    <h2><?= htmlspecialchars($product['name']) ?></h2>
    <p><strong>Category:</strong> <?= htmlspecialchars($product['category']) ?></p>
    <p class="price">Ksh <?= number_format($product['price'], 2) ?></p>

    <form method="post" action="cart.php">
        <input type="hidden" name="id" value="<?= $product['id'] ?>">
        <label for="qty">Quantity:</label>
        <input type="number" name="qty" id="qty" value="1" min="1">
        <button type="submit" name="add">Add to Cart</button>
    </form>
</div>
</body>
</html>
