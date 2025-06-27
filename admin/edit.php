<?php
include '../db.php';


session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}



$id = (int)$_GET['id']; // basic sanitization

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $conn->real_escape_string($_POST['name']);
    $cat = $conn->real_escape_string($_POST['category']);
    $price = (float)$_POST['price'];

    $conn->query("UPDATE jerseys SET name='$name', category='$cat', price='$price' WHERE id=$id");
    header('Location: index.php');
    exit;
}

$res = $conn->query("SELECT * FROM jerseys WHERE id=$id");
$item = $res->fetch_assoc();
if (!$item) {
    die("Jersey not found.");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Jersey</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, sans-serif;
            background-color: #f2f4f8;
            margin: 0;
            padding: 40px;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .form-container {
            background-color: #fff;
            padding: 40px 30px;
            border-radius: 10px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 500px;
        }

        h2 {
            text-align: center;
            color: #2c3e50;
            margin-bottom: 30px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: bold;
        }

        input[type="text"],
        input[type="number"] {
            width: 100%;
            padding: 12px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 16px;
        }

        button {
            width: 100%;
            padding: 12px;
            background-color: #27ae60;
            border: none;
            color: white;
            font-size: 16px;
            border-radius: 6px;
            cursor: pointer;
        }

        button:hover {
            background-color: #2ecc71;
        }
    </style>
</head>
<body>

<div class="form-container">
    <h2>✏️ Edit Jersey</h2>
    <form method="post">
        <label for="name">Jersey Name</label>
        <input type="text" name="name" id="name" value="<?= htmlspecialchars($item['name']) ?>" required>

        <label for="category">Category</label>
        <input type="text" name="category" id="category" value="<?= htmlspecialchars($item['category']) ?>" required>

        <label for="price">Price (Ksh)</label>
        <input type="number" name="price" id="price" value="<?= htmlspecialchars($item['price']) ?>" step="0.01" required>

        <button type="submit">Update Jersey</button>
    </form>
</div>

</body>
</html>
