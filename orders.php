<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch summarized orders per jersey for the user
$stmt = $conn->prepare("
    SELECT 
        jersey_id,
        name,
        image_path,
        SUM(price) AS total_price,
        COUNT(*) AS quantity,
        MAX(created_at) AS last_ordered
    FROM orders
    WHERE users_id = ?
    GROUP BY jersey_id, name, image_path
    ORDER BY last_ordered DESC
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
    </style>
</head>
<body>

<h2>🛒 My Jersey Orders Summary</h2>

<div class="top-bar">
    <a href="dashboard.php">Go Back</a>
</div>

<?php if ($result->num_rows > 0): ?>
    <table>
        <tr>
            <th>#</th>
            <th>Jersey</th>
            <th>Image</th>
            <th>Quantity</th>
            <th>Total Price (KSH)</th>
            <th>Last Ordered</th>
        </tr>
        <?php $count = 1; ?>
        <?php while ($order = $result->fetch_assoc()): ?>
        <tr>
            <td><?php echo $count++; ?></td>
            <td><?php echo htmlspecialchars($order['name']); ?></td>
            <td><img src="<?php echo htmlspecialchars($order['image_path']); ?>" alt="<?php echo htmlspecialchars($order['name']); ?>"></td>
            <td><?php echo $order['quantity']; ?></td>
            <td><?php echo number_format($order['total_price'], 2); ?></td>
            <td><?php echo date("F j, Y, g:i a", strtotime($order['last_ordered'])); ?></td>
        </tr>
        <?php endwhile; ?>
    </table>
<?php else: ?>
    <div class="empty-message">You haven't ordered any jerseys yet.</div>
<?php endif; ?>

</body>
</html>
