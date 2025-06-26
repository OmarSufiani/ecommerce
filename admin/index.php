<?php
include '../db.php';

session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}

// Pagination setup
$limit = 6;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int) $_GET['page'] : 1;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $limit;

// Get total count to check if there is a next page
$total_result = $conn->query("SELECT COUNT(*) as total FROM jerseys");
$total_row = $total_result->fetch_assoc();
$total = $total_row['total'];

// Fetch jerseys with limit and offset
$stmt = $conn->prepare("SELECT * FROM jerseys ORDER BY id ASC LIMIT ? OFFSET ?");

$stmt->bind_param("ii", $limit, $offset);
$stmt->execute();
$result = $stmt->get_result();

// Calculate total pages
$total_pages = ceil($total / $limit);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, sans-serif;
            background-color: #f4f6f9;
            margin: 0;
            padding: 40px;
        }

        h1 {
            text-align: center;
            color: #2c3e50;
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

        table {
            width: 90%;
            margin: 20px auto;
            border-collapse: collapse;
            background-color: #fff;
            box-shadow: 0 4px 8px rgba(0,0,0,0.05);
            border-radius: 8px;
            overflow: hidden;
        }

        th, td {
            padding: 14px 20px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }

        th {
            background-color: #2980b9;
            color: white;
            font-weight: normal;
        }

        tr:hover {
            background-color: #f1f1f1;
        }

        .action-links a {
            margin-right: 10px;
            text-decoration: none;
            color: #2980b9;
            font-weight: bold;
        }

        .action-links a:hover {
            text-decoration: underline;
        }

        .pagination {
            max-width: 900px;
            margin: 20px auto;
            text-align: center;
        }

        .pagination a {
            padding: 8px 14px;
            margin: 0 5px;
            background-color: #2980b9;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            font-weight: bold;
            transition: background-color 0.3s ease;
        }

        .pagination a:hover {
            background-color: #3498db;
        }

        .logout-container {
            max-width: 900px;
            margin: 30px auto;
            text-align: center;
        }

        .logout-container a {
            background-color: #c0392b;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 6px;
            font-weight: bold;
            transition: background-color 0.3s ease;
        }

        .logout-container a:hover {
            background-color: #e74c3c;
        }
    </style>
</head>
<body>

<h1>⚙️ Admin Dashboard</h1>

<div class="top-bar">
    <a href="add.php">➕ Add Jersey</a>
    <a href="orders1.php">All orders</a>
</div>

<table>
    <tr>
        <th>#</th>
        <th>Name</th>
        <th>Category</th>
        <th>Price (Ksh)</th>
        <th>Action</th>
    </tr>
   <?php $serial = ($page - 1) * $limit + 1; ?>
<?php while ($row = $result->fetch_assoc()): ?>
<tr>
    <td><?= $serial++ ?></td>
    <td><?= htmlspecialchars($row['name']) ?></td>
    <td><?= htmlspecialchars($row['category']) ?></td>
    <td><?= number_format($row['price'], 2) ?></td>
    <td class="action-links">
        <a href="edit.php?id=<?= $row['id'] ?>">Edit</a>
        <a href="delete.php?id=<?= $row['id'] ?>" onclick="return confirm('Are you sure you want to delete this jersey?');">Delete</a>
    </td>
</tr>
<?php endwhile; ?>

</table>

<div class="pagination">
    <?php if ($page > 1): ?>
        <a href="?page=<?= $page - 1 ?>">« Previous</a>
    <?php endif; ?>

    <?php if ($page < $total_pages): ?>
        <a href="?page=<?= $page + 1 ?>">Next »</a>
    <?php endif; ?>
</div>

<div class="logout-container">
    <a href="../logout.php">Logout</a>
</div>

</body>
</html>
