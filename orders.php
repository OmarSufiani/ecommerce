<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Handle delete and make order requests
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'])) {
    $order_id = intval($_POST['order_id']);

    if (isset($_POST['delete_order'])) {
        $delete_stmt = $conn->prepare("DELETE FROM orders WHERE id = ? AND users_id = ?");
        if (!$delete_stmt) {
            die("Delete prepare failed: " . $conn->error);
        }
        $delete_stmt->bind_param("ii", $order_id, $user_id);
        $delete_stmt->execute();
        $delete_stmt->close();

        header("Location: orders.php");
        exit();
    }

    if (isset($_POST['make_order'])) {
        $update_stmt = $conn->prepare("UPDATE orders SET status = 'submitted' WHERE id = ? AND users_id = ?");
        if (!$update_stmt) {
            die("Update prepare failed: " . $conn->error);
        }
        $update_stmt->bind_param("ii", $order_id, $user_id);
        $update_stmt->execute();
        $update_stmt->close();

        header("Location: orders.php?success=1");
        exit();
    }
}

// Fetch all orders for this user with category info
$stmt = $conn->prepare("
    SELECT o.id, o.jersey_id, o.name, o.price, o.image_path, o.created_at, o.status, j.category
    FROM orders o
    JOIN jerseys j ON o.jersey_id = j.id
    WHERE o.users_id = ?
    ORDER BY o.created_at DESC
");
if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Orders</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f0f2f5;
            padding: 20px;
        }
        h2 {
            text-align: center;
            color: #2a9d8f;
        }
        .top-bar {
            max-width: 900px;
            margin: 20px auto;
            display: flex;
            justify-content: flex-end;
        }
        .top-bar a {
            background-color: #27ae60;
            color: #fff;
            padding: 10px 16px;
            text-decoration: none;
            border-radius: 4px;
            font-size: 15px;
            transition: background-color 0.3s ease;
        }
        .top-bar a:hover {
            background-color: #2ecc71;
        }
        table {
            width: 100%;
            max-width: 1000px;
            margin: auto;
            border-collapse: collapse;
            background: #fff;
            box-shadow: 0 0 10px rgba(0,0,0,0.05);
        }
        th, td {
            padding: 12px;
            text-align: center;
            border: 1px solid #ddd;
        }
        th {
            background-color: #2a9d8f;
            color: white;
        }
        img {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 6px;
        }
        .empty-message {
            text-align: center;
            margin-top: 80px;
            color: #777;
        }
        .delete-btn {
            background-color: #e74c3c;
            color: white;
            border: none;
            padding: 6px 12px;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
            transition: background-color 0.3s ease;
        }
        .delete-btn:hover {
            background-color: #c0392b;
        }
        .make-order-btn {
            background-color: #3498db;
            color: white;
            border: none;
            padding: 8px 14px;
            border-radius: 2px;
            cursor: pointer;
            font-weight: bold;
            transition: background-color 0.3s ease;
        }
        .make-order-btn:hover {
            background-color: #2980b9;
        }
        .status-pending {
            color: #e67e22; /* orange */
            font-weight: bold;
        }
        .status-submitted {
            color: #2980b9; /* blue */
            font-weight: bold;
        }
        .status-confirmed {
            color: #27ae60; /* green */
            font-weight: bold;
        }
    </style>
</head>
<body>

<h2>🛒 My Cart</h2>

<div class="top-bar">
    <a href="dashboard.php">Go Back</a>
</div>

<?php if ($result->num_rows > 0): ?>
    <table>
        <tr>
            <th>#</th>
            <th>Category</th>
            <th>Jersey</th>
            <th>Image</th>
            <th>Price</th>
            <th>Status</th>
            <th>Action</th>
            <th>Make Order</th>
        </tr>
        <?php $count = 1; ?>
        <?php while ($order = $result->fetch_assoc()): ?>
            <tr>
                <td><?= $count++ ?></td>
                <td><?= htmlspecialchars($order['category']) ?></td>
                <td><?= htmlspecialchars($order['name']) ?></td>
                <td><img src="<?= htmlspecialchars($order['image_path']) ?>" alt="<?= htmlspecialchars($order['name']) ?>"></td>
                <td>KSH <?= number_format($order['price'], 2) ?></td>
                <td class="status-<?= strtolower(htmlspecialchars($order['status'])) ?>">
                    <?= ucfirst(htmlspecialchars($order['status'])) ?>
                </td>
                <td>
                    <form method="POST" onsubmit="return confirm('Delete this order?');">
                        <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                        <button type="submit" name="delete_order" class="delete-btn">Delete</button>
                    </form>
                </td>
                <td>
                    <?php if ($order['status'] === 'pending'): ?>
                        <form method="POST" onsubmit="return confirm('Do You want to make  order?');">
                            <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                            <button type="submit" name="make_order" class="make-order-btn">Make Order</button>
                        </form>
                    <?php else: ?>
                        <em>Order <?= htmlspecialchars($order['status']) ?></em>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>
<?php else: ?>
    <div class="empty-message">You haven't added anything yet.</div>
<?php endif; ?>

</body>
</html>
