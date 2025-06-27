<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);


session_start();
if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}
include 'db.php'; // Make sure this connects using $conn

$message = '';

// Handle Add to Cart submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
    $name = $_POST['name'] ?? '';
    $price = $_POST['price'] ?? 0;
    $image = $_POST['image'] ?? '';

    if ($name && $price > 0) {
        // Find jersey ID from DB
        $stmt = $conn->prepare("SELECT id FROM jerseys WHERE name = ?");
        $stmt->bind_param("s", $name);
        $stmt->execute();
        $result = $stmt->get_result();
        $jersey = $result->fetch_assoc();

        if ($jersey) {
            $jersey_id = $jersey['id'];
            $user_id = $_SESSION['user_id'] ?? null;

            if ($user_id) {
                // Insert into orders table
                $stmt = $conn->prepare("INSERT INTO orders (users_id, jersey_id, name, price, image_path) VALUES (?, ?, ?, ?, ?)");
                $stmt->bind_param("iisss", $user_id, $jersey_id, $name, $price, $image);
                $stmt->execute();

                // Redirect to orders page
                header("Location: orders.php?user_id=$user_id");
                exit();
            } else {
                $message = "User not logged in.";
            }
        } else {
            $message = "Jersey not found in database.";
        }
    } else {
        $message = "Invalid item details.";
    }
}

// Fetch jerseys from database
$result = $conn->query("SELECT name, price,category, image_path FROM jerseys ORDER BY id DESC");

$jerseys = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $jerseys[] = [
            'name' => $row['name'],
            'price' => $row['price'],
            'category' => $row['category'],
            'image' => $row['image_path']
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
        /* Your existing styles */
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
            background-color: rgb(245, 35, 35);
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
        .cart-btn:hover {
            background-color: #2980b9;
        }
        .buy-btn {
            background-color: #e67e22;
            color: white;
        }
        .buy-btn:hover {
            background-color: #d35400;
        }
        .message {
            text-align: center;
            background: #27ae60;
            color: white;
            padding: 10px;
            margin: 10px 0;
            font-weight: bold;
            border-radius: 4px;
            max-width: 400px;
            margin-left: auto;
            margin-right: auto;
        }
        .cart-count {
            font-weight: normal;
            font-size: 0.9em;
            background: #e76f51;
            padding: 4px 10px;
            border-radius: 20px;
            margin-left: 10px;
            color: white;
        }
    </style>
</head>
<body>

<header>
    <div>
        ⚽️ JAIRO SPORTS WEAR
        <?php if (!empty($_SESSION['cart'])): ?>
            <span class="cart-count"><?php echo count($_SESSION['cart']); ?> in Cart</span>
        <?php endif; ?>
    </div>
    <a href="logout.php" class="logout-btn">Logout</a>
</header>

<?php if (!empty($message)): ?>
    <div class="message"><?php echo htmlspecialchars($message); ?></div>
<?php endif; ?>

<div class="store">
    <?php if (!empty($jerseys)): ?>
        <?php foreach ($jerseys as $jersey): ?>
            <div class="jersey">
                <img src="<?php echo htmlspecialchars($jersey['image']); ?>" alt="<?php echo htmlspecialchars($jersey['name']); ?>" />
                <h3><?php echo htmlspecialchars($jersey['name']); ?></h3>
                <div class="price">KSH <?php echo number_format($jersey['price'], 2); ?></div>
                 <h3><?php echo htmlspecialchars($jersey['category']); ?></h3>
                <div class="buttons">
                    <form method="post" style="flex: 1; margin-right: 6px;">
                        <input type="hidden" name="name" value="<?php echo htmlspecialchars($jersey['name']); ?>">
                        <input type="hidden" name="price" value="<?php echo htmlspecialchars($jersey['price']); ?>">
                        <input type="hidden" name="image" value="<?php echo htmlspecialchars($jersey['image']); ?>">
                        <button type="submit" name="add_to_cart" class="cart-btn">Add to Cart</button>
                    </form>
                    
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p style="text-align:center; padding: 50px; font-size: 1.2em;">No jerseys found in the store.</p>
    <?php endif; ?>
</div>

</body>
</html>
