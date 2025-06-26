<?php




session_start();
include 'functions.php';
if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
    $name = $_POST['name'] ?? '';
    $price = $_POST['price'] ?? 0;
    $image = $_POST['image'] ?? '';
    $user_id = $_SESSION['user_id'];

    // Fetch jersey ID based on name (assuming names are unique)
    $stmt = $conn->prepare("SELECT id FROM jerseys WHERE name = ?");
    $stmt->bind_param("s", $name);
    $stmt->execute();
    $result = $stmt->get_result();
    $jersey = $result->fetch_assoc();

    if ($jersey && $price > 0) {
        $jersey_id = $jersey['id'];

        // Insert order into `orders` table
        $stmt = $conn->prepare("INSERT INTO orders (users_id, jersey_id, name, price, image_path) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("iisss", $user_id, $jersey_id, $name, $price, $image);
        $stmt->execute();

        // Redirect to view orders for that user
        header("Location: orders.php?user_id=" . $user_id);
        exit();
    } else {
        $message = "Invalid item or jersey not found.";
    }
}





?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Your Cart</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f6f9;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 800px;
            margin: 40px auto;
            padding: 20px;
            background: #fff;
            box-shadow: 0 8px 20px rgba(0,0,0,0.1);
            border-radius: 10px;
        }

        h1 {
            text-align: center;
            color: #333;
        }

        .cart-items {
            list-style: none;
            padding: 0;
            margin: 20px 0;
        }

        .cart-items li {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 20px;
            border-bottom: 1px solid #eee;
        }

        .cart-items li:last-child {
            border-bottom: none;
        }

        .item-name {
            font-weight: bold;
            color: #444;
        }

        .item-details {
            font-size: 14px;
            color: #666;
        }

        .checkout-btn {
            display: block;
            width: 100%;
            padding: 15px;
            text-align: center;
            background: #4CAF50;
            color: white;
            font-size: 16px;
            border: none;
            border-radius: 8px;
            text-decoration: none;
            margin-top: 30px;
            transition: background 0.3s ease;
        }

        .checkout-btn:hover {
            background: #45a049;
        }

        .empty-message {
            text-align: center;
            color: #999;
            font-size: 18px;
            margin-top: 40px;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Your Cart</h1>

    <?php if (empty($cart)): ?>
        <p class="empty-message">🛒 Your cart is currently empty.</p>
    <?php else: ?>
        <ul class="cart-items">
            <?php foreach ($cart as $id => $qty):
                $item = getJersey($conn, $id); ?>
                <li>
                    <div>
                        <span class="item-name"><?= htmlspecialchars($item['name']) ?></span><br>
                        <span class="item-details"><?= $qty ?> pcs × Ksh <?= number_format($item['price']) ?></span>
                    </div>
                    <div>
                        <strong>Ksh <?= number_format($item['price'] * $qty) ?></strong>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
        <a href="checkout.php" class="checkout-btn">Proceed to Checkout</a>
    <?php endif; ?>
</div>

</body>
</html>
