<?php
session_start();
require_once 'db.php';
use PHPMailer\PHPMailer\PHPMailer;
require 'vendor/autoload.php'; // Composer PHPMailer

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');

    // Check if user exists
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user) {
        $token = bin2hex(random_bytes(32));
        $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));

        // Save reset token
        $pdo->prepare("INSERT INTO password_resets (email, token, expires_at) VALUES (?, ?, ?)")
            ->execute([$email, $token, $expires]);

        $resetLink = "http://yourdomain.com/reset_password.php?token=$token";

        // Send email
        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->Host = 'smtp.yourdomain.com'; // ✅ Your hosting SMTP
        $mail->SMTPAuth = true;
        $mail->Username = 'noreply@yourdomain.com';
        $mail->Password = 'yourpassword';
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        $mail->setFrom('noreply@yourdomain.com', 'Faculty Eval System');
        $mail->addAddress($email);
        $mail->Subject = 'Password Reset';
        $mail->Body = "Click here to reset your password:\n\n$resetLink";

        if ($mail->send()) {
            $message = "✅ Reset link has been sent to your email.";
        } else {
            $message = "❌ Failed to send email. " . $mail->ErrorInfo;
        }
    } else {
        $message = "❌ Email not found.";
    }
}
?>
<!DOCTYPE html>
<html>
<head><title>Forgot Password</title></head>
<body>
  <h2>Forgot Password</h2>
  <?php if ($message) echo "<p>$message</p>"; ?>
  <form method="post">
    <label>Email:</label>
    <input type="email" name="email" required>
    <button type="submit">Send Reset Link</button>
  </form>
</body>
</html>
