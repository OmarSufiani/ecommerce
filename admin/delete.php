<?php
include '../db.php';
session_start();

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Check for a valid jersey ID
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $jersey_id = (int) $_GET['id'];

    // Use prepared statement to avoid SQL injection
    $stmt = $conn->prepare("DELETE FROM jerseys WHERE id = ?");
    $stmt->bind_param("i", $jersey_id);

    if ($stmt->execute()) {
        $stmt->close();
        $conn->close();
        header("Location: index.php?message=deleted");
        exit();
    } else {
        $error = "Failed to delete jersey. Please try again.";
    }

    $stmt->close();
} else {
    $error = "Invalid jersey ID.";
}

// Optional error handling
if (isset($error)) {
    echo "<p style='color: red; text-align: center;'>$error</p>";
    echo "<p style='text-align: center;'><a href='index.php'>Go back to dashboard</a></p>";
}
?>
