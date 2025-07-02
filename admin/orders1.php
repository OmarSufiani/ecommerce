<?php 

ini_set('display_errors', 1);
error_reporting(E_ALL);


use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;


require_once '../phpmailer/Exception.php';
require_once '../phpmailer/PHPMailer.php';
require_once '../phpmailer/SMTP.php';



include '../db.php';
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: ../index.php");
    exit();
}

// Mark as shipped & send email
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ship_order'], $_POST['email'])) {
    $order_id = intval($_POST['order_id']);
    $email = $_POST['email'];
    $status = 'shipped';

    $stmt = $conn->prepare("UPDATE orders SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $status, $order_id);

    if ($stmt->execute()) {
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'hommiedelaco@gmail.com';
            $mail->Password = 'mkpjatvrhtcwafrr'; // App password
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;

            $mail->setFrom('hommiedelaco@gmail.com', 'JAIRO SORTS WEAR');
            $mail->addAddress($email);

            $mail->isHTML(true);
            $mail->Subject = 'Order Shipped!';
            $mail->Body = 'Hello! Your jersey order is now <strong>being shipped</strong>. Thank you for shopping with us.For more infor contact +254703805049';

            $mail->send();
            $success = "Shipping email sent successfully!";
        } catch (Exception $e) {
            $error = "Mailer Error: {$mail->ErrorInfo}";
        }
    }
    $stmt->close();
}

// Delete order
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_order'])) {
    $order_id = intval($_POST['order_id']);
    $del_stmt = $conn->prepare("DELETE FROM orders WHERE id = ?");
    $del_stmt->bind_param("i", $order_id);
    if ($del_stmt->execute()) {
        $success = "Order deleted successfully!";
    } else {
        $error = "Failed to delete the order.";
    }
    $del_stmt->close();
}

// Fetch orders
$sql = "SELECT orders.id AS order_id, users.username, users.email, jerseys.name AS jersey_name, jerseys.category,
        orders.quantity, orders.price, jerseys.image_path, orders.created_at
        FROM orders
        JOIN users ON orders.users_id = users.id
        JOIN jerseys ON orders.jersey_id = jerseys.id
        
        WHERE orders.status = 'submitted'
        ORDER BY orders.created_at DESC";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Submitted Orders</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #f9f9f9;
            padding: 20px;
            color: #333;
        }

        h2 {
            text-align: center;
            margin-bottom: 30px;
            color: #2a9d8f;
        }

        .message {
            text-align: center;
            padding: 10px 20px;
            margin: 10px auto;
            max-width: 600px;
            font-weight: bold;
            border-radius: 6px;
        }

        .success {
            background-color: #d4edda;
            color: #155724;
        }

        .error {
            background-color: #f8d7da;
            color: #721c24;
        }

        table {
            width: 100%;
            max-width: 1100px;
            margin: 0 auto;
            border-collapse: collapse;
            background: #fff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }

        th, td {
            padding: 14px;
            border-bottom: 1px solid #ddd;
            text-align: center;
        }

        th {
            background-color: #2a9d8f;
            color: white;
        }

        tr:hover {
            background-color: #f1f1f1;
        }

        .btn {
            background-color: #27ae60;
            color: white;
            border: none;
            padding: 7px 14px;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
            margin: 3px;
            transition: background-color 0.3s ease;
        }

        .btn:hover {
            background-color: #219150;
        }

        .btn.delete {
            background-color: #e74c3c;
        }

        .btn.delete:hover {
            background-color: #c0392b;
        }

        .top-bar {
            display: flex;
            justify-content: center;
            margin-bottom: 20px;
        }

        .top-bar a {
            background-color: #3498db;
            color: white;
            padding: 10px 20px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: bold;
            transition: background-color 0.3s;
        }

        .top-bar a:hover {
            background-color: #2980b9;
        }

        .actions {
            display: flex;
            flex-direction: column;
            gap: 5px;
            align-items: center;
        }

        @media (min-width: 768px) {
            .actions {
                flex-direction: row;
            }
        }
    </style>
</head>
<body>

<!-- Existing PHP and HTML up to closing </table>... -->

<?php if ($result && $result->num_rows > 0): ?>
    <!-- ... your table code remains here -->
<?php endif; ?>

<!-- Auto-disappearing message -->
<script>
    setTimeout(() => {
        const messages = document.querySelectorAll('.message');
        messages.forEach(msg => {
            msg.style.transition = 'opacity 0.5s ease';
            msg.style.opacity = '0';
            setTimeout(() => msg.remove(), 500); // Remove after fade
        });
    }, 3000); // 3 seconds
</script>

</body>
</html>


<div class="top-bar">
    <a href="index.php">🏠 Back to Dashboard</a>
</div>

<h2>Submitted Orders</h2>

<?php if (isset($success)) echo "<div class='message success'>$success</div>"; ?>
<?php if (isset($error)) echo "<div class='message error'>$error</div>"; ?>

<?php if ($result && $result->num_rows > 0): ?>
    <table>
        <tr>
            <th>Order ID</th>
            <th>User</th>
            <th>Email</th>
            <th>Jersey</th>
             <th>Image</th>
            <th>Serial/No.</th>
            <th>Quantity</th>
            <th>Total Price (KSH)</th>
            <th>Ordered On</th>
            <th>Actions</th>
        </tr>
        <?php $i = 1; while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= htmlspecialchars($row['order_id']) ?></td>
            <td><?= htmlspecialchars($row['username']) ?></td>
            <td><?= htmlspecialchars($row['email']) ?></td>
            <td><?= htmlspecialchars($row['jersey_name']) ?></td>
            
           <td>
    <img src="<?= '../images/' . htmlspecialchars(basename($row['image_path'])) ?>" 
         alt="<?= htmlspecialchars($row['jersey_name']) ?>" 
         width="70">
</td>

            <td><?= htmlspecialchars($row['category']) ?></td>
            <td><?= $row['quantity'] ?></td>
            <td><?= number_format($row['price'] * $row['quantity'], 2) ?></td>
            
            <td><?= date('d M Y, H:i', strtotime($row['created_at'])) ?></td>
            <td>
                <div class="actions">
                    <form method="POST" style="display:inline;">
                        <input type="hidden" name="order_id" value="<?= $row['order_id'] ?>">
                        <input type="hidden" name="email" value="<?= $row['email'] ?>">
                        <button type="submit" name="ship_order" class="btn">Ship & Notify</button>
                    </form>
                    <form method="POST" onsubmit="return confirm('Are you sure you want to delete this order?');" style="display:inline;">
                        <input type="hidden" name="order_id" value="<?= $row['order_id'] ?>">
                        <button type="submit" name="delete_order" class="btn delete">Delete</button>
                    </form>
                </div>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
<?php else: ?>
    <p style="text-align:center; font-size: 1.1em;">No submitted orders found.</p>
<?php endif; ?>

</body>
</html>
