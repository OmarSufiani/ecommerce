<?php
include 'db.php'; // Make sure the path is correct

// Fetch jerseys from database
$result = $conn->query("SELECT name, price, image_path FROM jerseys ORDER BY id DESC");

$jerseys = [];

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $jerseys[] = [
            'name' => $row['name'],
            'price' => $row['price'],
            'image' => $row['image_path'], // Should be like "uploads/image.jpg"
        ];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Jersey Store</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 0;
            margin: 0;
            background: #f9f9f9;
        }
        header {
            position: sticky;
            top: 0;
            background-color: #2a9d8f;
            color: white;
            padding: 20px 40px;
            font-size: 1.8em;
            font-weight: bold;
            box-shadow: 0 2px 8px rgba(0,0,0,0.15);
            z-index: 1000;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .logout-btn {
            background-color:rgb(46, 14, 223);
            color: white;
            border: none;
            padding: 10px 16px;
            border-radius: 6px;
            font-size: 1em;
            cursor: pointer;
            text-decoration: none;
            font-weight: bold;
            transition: background-color 0.3s ease;
        }
        .logout-btn:hover {
            background-color: #d62828;
        }
        .store {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            padding: 20px 40px;
            justify-content: center;
        }
        .jersey {
            border: 1px solid #ddd;
            border-radius: 6px;
            width: 180px;
            padding: 10px;
            text-align: center;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
            background: white;
            transition: transform 0.3s ease;
        }
        .jersey:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 12px rgba(0,0,0,0.15);
        }
        .jersey img {
            width: 100%;
            height: 160px;
            object-fit: cover;
            border-radius: 4px;
        }
        .jersey h3 {
            margin: 10px 0 5px;
            font-size: 1.1em;
        }
        .price {
            color: #2a9d8f;
            font-weight: bold;
            font-size: 1.2em;
        }
        .buttons {
            margin-top: 10px;
            display: flex;
            justify-content: space-between;
            gap: 6px;
        }
        .cart-btn,
        .buy-btn {
            flex: 1;
            padding: 8px;
            font-size: 0.9em;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
            transition: background-color 0.3s ease;
        }
        .cart-btn {
            background-color: #3498db;
            color: white;
        }
        .cart-btn:hover:not(:disabled) {
            background-color: #2980b9;
        }
        .cart-btn:disabled {
            background-color: #95a5a6;
            cursor: not-allowed;
            opacity: 0.6;
        }
        .buy-btn {
            background-color: #e67e22;
            color: white;
        }
        .buy-btn:hover {
            background-color: #d35400;
        }
    </style>
</head>
<body>

<header>
    <div>⚽️ European Football Jersey Store</div>
    <a href="login.php" class="logout-btn">Login</a>
</header>

<div class="store">
    <?php if (!empty($jerseys)): ?>
        <?php foreach ($jerseys as $jersey): ?>
            <div class="jersey">
                <img src="<?php echo htmlspecialchars($jersey['image']); ?>" alt="<?php echo htmlspecialchars($jersey['name']); ?>" />
                <h3><?php echo htmlspecialchars($jersey['name']); ?></h3>
                <div class="price">KSH <?php echo number_format($jersey['price'], 2); ?></div>
                <div class="buttons">
                    <button class="cart-btn" disabled>Add to Cart</button>
                    <button class="buy-btn">Buy Now</button>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p style="text-align:center; padding: 50px; font-size: 1.2em;">No jerseys found in the store.</p>
    <?php endif; ?>
</div>

</body>
</html>
