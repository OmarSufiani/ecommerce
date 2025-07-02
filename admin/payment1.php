<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

include '../db.php'; // adjust if needed
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: ../index.php");
    exit();
}

// Fetch orders that are either 'submitted' or have M-Pesa code linked
$sql = "
    SELECT 
        o.id AS order_id,
        u.username,
        o.status,
        t.mpesa_message,
        o.created_at
    FROM orders o
    JOIN users u ON o.users_id = u.id
    LEFT JOIN tracker t ON o.id = t.orders_id
    WHERE o.status = 'submitted' OR t.mpesa_message IS NOT NULL
    ORDER BY o.created_at DESC
";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Order + M-Pesa Check</title>
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
            margin-bottom: 30px;
        }
        table {
            width: 90%;
            max-width: 1100px;
            margin: 0 auto;
            border-collapse: collapse;
            background: white;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            border-radius: 8px;
            overflow: hidden;
        }
        th, td {
            padding: 14px;
            text-align: center;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #2a9d8f;
            color: white;
        }
        tr:hover {
            background-color: #f1f1f1;
        }
        .mpesa {
            color: green;
            font-weight: bold;
        }
        .none {
            color: gray;
            font-style: italic;
        }
        .top-bar {
            text-align: center;
            margin-bottom: 20px;
        }
        .top-bar a {
            background-color: #3498db;
            color: white;
            padding: 10px 20px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: bold;
        }
        .top-bar a:hover {
            background-color: #2980b9;
        }
    </style>
</head>
<body>

<div class="top-bar">
    <a href="index.php">🏠 Back to Dashboard</a>
</div>

<h2>Submitted & M-Pesa Confirmed Orders</h2>

<?php if ($result && $result->num_rows > 0): ?>
    <table>
        <tr>
            <th>Order ID</th>
            <th>User</th>
            <th>Status</th>
            <th>M-Pesa Code</th>
            <th>Ordered On</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($row['order_id']) ?></td>
                <td><?= htmlspecialchars($row['username']) ?></td>
                <td><?= htmlspecialchars($row['status']) ?></td>
                <td class="<?= $row['mpesa_message'] ? 'mpesa' : 'none' ?>">
                    <?= $row['mpesa_message'] ? htmlspecialchars($row['mpesa_message']) : 'Not Confirmed' ?>
                </td>
                <td><?= date('d M Y, H:i', strtotime($row['created_at'])) ?></td>
            </tr>
        <?php endwhile; ?>
    </table>
<?php else: ?>
    <p style="text-align:center; font-size: 1.1em;">No submitted or M-Pesa-confirmed orders found.</p>
<?php endif; ?>

</body>
</html>
