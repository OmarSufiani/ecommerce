<?php
// Enable error reporting for debugging (disable in production)
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Include MySQLi OOP connection
include 'db.php';

$error = '';
$success = '';

// Get email and token from GET or POST
$email = $_GET['email'] ?? $_POST['email'] ?? '';
$token = $_GET['token'] ?? $_POST['token'] ?? '';

// Escape input using MySQLi object
$email = $conn->real_escape_string($email);
$token = $conn->real_escape_string($token);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newPassword = $_POST['password'] ?? '';

    if (strlen($newPassword) < 6) {
        $error = "Password must be at least 6 characters.";
    } elseif (empty($email) || empty($token)) {
        $error = "Missing reset token or email.";
    } else {
        // Validate token and expiry
        $stmt = $conn->prepare("SELECT reset_token_expiry FROM users WHERE email = ? AND reset_token = ?");
        $stmt->bind_param("ss", $email, $token);
        $stmt->execute();
        $stmt->bind_result($expiry);

        if ($stmt->fetch()) {
            if (strtotime($expiry) < time()) {
                $error = "Reset link has expired.";
                $stmt->close();
            } else {
                $stmt->close();

                // Hash new password
                $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

                // Update password and clear token
                $update = $conn->prepare("UPDATE users SET password = ?, reset_token = NULL, reset_token_expiry = NULL WHERE email = ?");
                $update->bind_param("ss", $hashedPassword, $email);

                if ($update->execute()) {
                    $success = "✅ Password reset successful. You may now log in.";
                } else {
                    $error = " Failed to update password.";
                }

                $update->close();
            }
        } else {
            $error = "Invalid or expired reset link.";
            $stmt->close();
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Reset Password</title>
</head>
<body>
    <h2>Reset Your Password</h2>

    <?php if ($error): ?>
        <p style="color: red;"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>

    <?php if ($success): ?>
        <p style="color: green;"><?php echo htmlspecialchars($success); ?></p>
    <?php endif; ?>

    <?php if (!$success): ?>
    <form method="post" action="">
        <label>Email:
            <input type="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>
        </label><br><br>

        <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">

        <label>New Password:
            <input type="password" name="password" required>
        </label><br><br>

        <button type="submit">Reset Password</button>
    </form>
    <?php endif; ?>
</body>
</html>
