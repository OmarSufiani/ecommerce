<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Handle delete request for a single record
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'])) {
    $order_id = intval($_POST['order_id']);
    $delete_stmt = $conn->prepare("DELETE FROM orders WHERE id = ? AND users_id = ?");
    $delete_stmt->bind_param("ii", $order_id, $user_id);
    $delete_stmt->execute();
    $delete_stmt->close();
    header("Location: orders.php");
    exit();
}

// Fetch all individual orders for this user with category info
$stmt = $conn->prepare("
    SELECT o.id, o.jersey_id, o.name, o.price, o.image_path, o.created_at, j.category
    FROM orders o
    JOIN jerseys j ON o.jersey_id = j.id
    WHERE o.users_id = ?
    ORDER BY o.created_at DESC
");
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
        }

        .delete-btn:hover {
            background-color: #c0392b;
        }
    </style>
</head>
<body>

<h2>🛒 My Jersey Orders</h2>

<div class="top-bar">
    <a href="dashboard.php">Go Back</a>
</div>

<?php if ($result->num_rows > 0): ?>
    <table>
        <tr>
            <th>#</th>
            <th>S/No.</th>
            <th>Jersey</th>
            <th>Image</th>
            <th>Price</th>
            <th>Ordered At</th>
            <th>Action</th>
        </tr>
        <?php $count = 1; ?>
        <?php while ($order = $result->fetch_assoc()): ?>
            <tr>
                <td><?= $count++ ?></td>
                <td><?= htmlspecialchars($order['category']) ?></td>
                <td><?= htmlspecialchars($order['name']) ?></td>
                <td><img src="<?= htmlspecialchars($order['image_path']) ?>" alt="<?= htmlspecialchars($order['name']) ?>"></td>
                <td>KSH <?= number_format($order['price'], 2) ?></td>
                <td><?= date("F j, Y, g:i a", strtotime($order['created_at'])) ?></td>
                <td>
                    <form method="POST" onsubmit="return confirm('Delete this order?');">
                        <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                        <button type="submit" class="delete-btn">Delete</button>
                    </form>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>
<?php else: ?>
    <div class="empty-message">You haven't placed any orders yet.</div>
<?php endif; ?>

</body>
</html>
