<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

include 'db.php'; // adjust path if needed
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'], $_POST['mpesa_message'])) {
    $order_id = intval($_POST['order_id']);
    $mpesa_message = trim($_POST['mpesa_message']);

    // Get current status of the order
    $stmt = $conn->prepare("SELECT status FROM orders WHERE id = ?");
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    $stmt->bind_result($status);
    $stmt->fetch();
    $stmt->close();

    if ($status) {
        // Insert into tracker
        $insert = $conn->prepare("INSERT INTO tracker (orders_id, orders_status, mpesa_message) VALUES (?, ?, ?)");
        $insert->bind_param("iss", $order_id,$status, $mpesa_message);

        if ($insert->execute()) {
            $success = "M-Pesa code added successfully for order ID $order_id.";
        } else {
            $error = "Failed to add M-Pesa code.";
        }
        $insert->close();
    } else {
        $error = "Order not found.";
    }
}

// Fetch submitted orders
$orders = $conn->query("SELECT id, status FROM orders WHERE status = 'submitted'");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add M-Pesa Code</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #f9f9f9;
            padding: 20px;
            color: #333;
        }
        h2 {
            text-align: center;
            color: #2a9d8f;
        }
        form {
            max-width: 500px;
            margin: 30px auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
        }
        select, input[type="text"], button {
            width: 100%;
            padding: 10px;
            margin-bottom: 16px;
            border: 1px solid #ccc;
            border-radius: 6px;
        }
        button {
            background: #27ae60;
            color: white;
            font-weight: bold;
            cursor: pointer;
            transition: background 0.3s;
        }
        button:hover {
            background: #219150;
        }
        .message {
            text-align: center;
            padding: 10px;
            font-weight: bold;
            border-radius: 5px;
            margin: 10px auto;
            max-width: 500px;
        }
        .success {
            background-color: #d4edda;
            color: #155724;
        }
        .error {
            background-color: #f8d7da;
            color: #721c24;
        }
    </style>
</head>
<body>

<h2>Add M-Pesa Code for Order</h2>

<?php if (isset($success)) echo "<div class='message success'>$success</div>"; ?>
<?php if (isset($error)) echo "<div class='message error'>$error</div>"; ?>

<form method="POST">
    <label for="order_id">Select Order</label>
    <select name="order_id" id="order_id" required>
        <option value="">-- Select Order ID --</option>
        <?php while ($row = $orders->fetch_assoc()): ?>
            <option value="<?= $row['id'] ?>">Order #<?= $row['id'] ?> (Status: <?= htmlspecialchars($row['status']) ?>)</option>
        <?php endwhile; ?>
    </select>

    <label for="mpesa_message">M-Pesa Code</label>
    <input type="text" name="mpesa_message" id="mpesa_message" placeholder="e.g. QJD1234567" required>

    <button type="submit">Submit Code</button>
</form>

</body>
</html>
