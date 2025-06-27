<?php
// view_orders.php
include '../db.php';
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: ../index.php");
    exit();
}

// Handle delete request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'])) {
    $order_id = intval($_POST['order_id']);
    $delete_stmt = $conn->prepare("DELETE FROM orders WHERE id = ?");
    $delete_stmt->bind_param("i", $order_id);
    $delete_stmt->execute();
    $delete_stmt->close();
}

// Fetch ordered jerseys with category and order ID
$sql = "SELECT 
            orders.id AS order_id,
            users.username,
            users.email,
            jerseys.name AS jersey_name,
            jerseys.category,
            orders.quantity,
            orders.price,
            jerseys.image_path,
            orders.created_at
        FROM orders
        JOIN users ON orders.users_id = users.id
        JOIN jerseys ON orders.jersey_id = jerseys.id
        ORDER BY orders.created_at DESC";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Ordered Jerseys</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f4f4;
            padding: 20px;
        }
        h1 {
            text-align: center;
        }
        .orders-table {
            width: 100%;
            max-width: 1000px;
            margin: 0 auto;
            border-collapse: collapse;
            background: #fff;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .orders-table th, .orders-table td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: center;
        }
        .orders-table th {
            background-color: #2a9d8f;
            color: white;
        }
        .top-bar {
            max-width: 900px;
            margin: 20px auto;
            display: flex;
            justify-content: flex-end;
            gap: 10px;
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
        img {
            height: 80px;
            object-fit: cover;
            border-radius: 6px;
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

<h1>Ordered Jerseys</h1>
<div class="top-bar">
    <a href="index.php">Go Back</a>
</div>

<?php if ($result && $result->num_rows > 0): ?>
    <table class="orders-table">
        <tr>
            <th>#</th>
            <th>Category</th>
            <th>User</th>
            <th>Email</th>
            <th>Jersey</th>
            <th>Quantity</th>
            <th>Total Price (KSH)</th>
            <th>Image</th>
            <th>Ordered On</th>
            <th>Action</th>
        </tr>
        <?php $count = 1; ?>
        <?php while($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?php echo $count++; ?></td>
            <td><?php echo htmlspecialchars($row['category']); ?></td>
            <td><?php echo htmlspecialchars($row['username']); ?></td>
            <td><?php echo htmlspecialchars($row['email']); ?></td>
            <td><?php echo htmlspecialchars($row['jersey_name']); ?></td>
            <td><?php echo htmlspecialchars($row['quantity']); ?></td>
            <td><?php echo number_format($row['price'] * $row['quantity'], 2); ?></td>
            <td><img src="<?php echo '../' . htmlspecialchars($row['image_path']); ?>" alt="jersey image"></td>
            <td><?php echo date('d M Y, H:i', strtotime($row['created_at'])); ?></td>
            <td>
                <form method="POST" onsubmit="return confirm('Are you sure you want to delete this order?');">
                    <input type="hidden" name="order_id" value="<?php echo $row['order_id']; ?>">
                    <button type="submit" class="delete-btn">Delete</button>
                </form>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
<?php else: ?>
    <p style="text-align:center;">No orders have been placed yet.</p>
<?php endif; ?>

<?php $conn->close(); ?>
</body>
</html>
