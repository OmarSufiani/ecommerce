<?php
// Enable error reporting for development (disable in production)
ini_set('display_errors', 1);
error_reporting(E_ALL);

// DB connection
include 'db.php';

// PHPMailer setup
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'phpmailer/PHPMailer.php';
require 'phpmailer/SMTP.php';
require 'phpmailer/Exception.php';

// SMTP Configuration (Gmail)
$smtpHost = 'smtp.gmail.com';
$smtpUsername = 'hommiedelaco@gmail.com';
$smtpPassword = 'mkpjatvrhtcwafrr'; // 🔐 Gmail App Password
$smtpPort = 587;

// Initialize messages
$error = '';
$success = '';

// Check if form submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['email'])) {
    $email = trim($_POST['email']);

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email address.";
    } else {
        // Prepare and execute the query
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        // Check if email exists
        if ($result->num_rows > 0) {
            // Generate token and expiry
            $token = bin2hex(random_bytes(16));
            $expiry = date('Y-m-d H:i:s', time() + 3600); // 1 hour

            // Update database
            $update = $conn->prepare("UPDATE users SET reset_token = ?, reset_token_expiry = ? WHERE email = ?");
            $update->bind_param("sss", $token, $expiry, $email);

            if ($update->execute()) {
                // Prepare reset link and email content
                $resetLink = "https://jerzystore8.infinityfreeapp.com/reset_password.php?email=" . urlencode($email) . "&token=$token";
                $subject = "Password Reset Request";
                $message = "To reset your password, click this link:\n\n$resetLink\n\nThis link expires in 1 hour.";

                // Send email
                $mail = new PHPMailer(true);
                try {
                    $mail->isSMTP();
                    $mail->Host       = $smtpHost;
                    $mail->SMTPAuth   = true;
                    $mail->Username   = $smtpUsername;
                    $mail->Password   = $smtpPassword;
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                    $mail->Port       = $smtpPort;

                    $mail->setFrom($smtpUsername, 'JAIRO SPORT WEAR');
                    $mail->addAddress($email);

                    $mail->isHTML(false);
                    $mail->Subject = $subject;
                    $mail->Body    = $message;

                    $mail->send();
                    $success = "Reset link sent to your email.";
                } catch (Exception $e) {
                    $error = "Email sending failed: " . $mail->ErrorInfo;
                }
                $mail = null;
            } else {
                $error = "Failed to save token to database.";
            }

            $update->close();
        } else {
            $error = "Email not found.";
        }

        $stmt->close();
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Forgot Password</title>
</head>
<body>
    <h2>Forgot Password</h2>

    <?php if ($error): ?>
        <p style="color:red;"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>

    <?php if ($success): ?>
        <p style="color:green;"><?php echo htmlspecialchars($success); ?></p>
    <?php endif; ?>

    <form method="post" action="">
        <label>Email: <input type="email" name="email" required></label><br><br>
        <button type="submit">Send Reset Link</button>
    </form>
</body>
</html>
