<?php
include '../db.php';
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: ../index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $conn->real_escape_string($_POST['name']);
    $cat = $conn->real_escape_string($_POST['category']);
    $price = (float)$_POST['price'];

    // Handle image upload
    $targetDir = "../uploads/";
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0755, true);
    }

    $imageName = basename($_FILES["image"]["name"]);
    $targetFilePath = $targetDir . $imageName;
    $imageFileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));

    $allowedTypes = ['jpg', 'jpeg', 'png', 'gif','webp'];
    if (!in_array($imageFileType, $allowedTypes)) {
        die("Only JPG, JPEG, webp,PNG & GIF files are allowed.");
    }

    if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFilePath)) {
        $imagePath = "uploads/" . $imageName;

        $stmt = $conn->prepare("INSERT INTO jerseys (name, category, price, image_path) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssds", $name, $cat, $price, $imagePath);
        $stmt->execute();
        $stmt->close();

        header('Location: index.php');
        exit;
    } else {
        echo "Error uploading image.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Jersey</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, sans-serif;
            background-color: #f0f4f8;
            margin: 0;
            padding: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
        }

        .form-container {
            background-color: #ffffff;
            padding: 40px 30px;
            border-radius: 10px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 480px;
        }

        h2 {
            text-align: center;
            color: #34495e;
            margin-bottom: 25px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: #333;
        }

        input[type="text"],
        input[type="number"],
        input[type="file"] {
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
            background-color: #2980b9;
            border: none;
            color: white;
            font-size: 16px;
            border-radius: 6px;
            cursor: pointer;
        }

        button:hover {
            background-color: #3498db;
        }
    </style>
</head>
<body>

<div class="form-container">
    <h2>➕ Add New Jersey</h2>
    <form method="post" enctype="multipart/form-data">
        <label for="name">Jersey Name</label>
        <input type="text" name="name" id="name" placeholder="e.g. Manchester United Home Kit" required>

        <label for="category">Category</label>
        <input type="text" name="category" id="category" placeholder="e.g. Football" required>

        <label for="price">Price (Ksh)</label>
        <input type="number" name="price" id="price" placeholder="e.g. 1500" step="0.01" required>

        <label for="image">Jersey Image</label>
        <input type="file" name="image" id="image" accept="image/*" required>

        <button type="submit">Add Jersey</button>
    </form>
</div>

</body>
</html>
