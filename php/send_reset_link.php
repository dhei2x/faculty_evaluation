<?php
include 'db.php'; // include your DB connection
use PHPMailer\PHPMailer\PHPMailer;

require 'vendor/autoload.php'; // install PHPMailer via Composer

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];

    // Verify email exists
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user) {
        $token = bin2hex(random_bytes(32));
        $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));

        // Store token
        $pdo->prepare("INSERT INTO password_resets (email, token, expires_at) VALUES (?, ?, ?)")
            ->execute([$email, $token, $expires]);

       $resetLink = "http://yourdomain.com/reset_password.php?token=$token";

        // Send email
        $mail = new PHPMailer();
        $mail->isSMTP();
        $mail->Host = 'smtp.example.com'; // Replace
        $mail->SMTPAuth = true;
        $mail->Username = 'your@example.com';
        $mail->Password = 'yourpassword';
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        $mail->setFrom('noreply@example.com', 'Faculty Eval System');
        $mail->addAddress($email);
        $mail->Subject = 'Password Reset';
        $mail->Body = "Click the link to reset your password: $resetLink";

        if ($mail->send()) {
            echo "Reset link sent!";
        } else {
            echo "Failed to send email.";
        }
    } else {
        echo "Email not found.";
    }
}
?>

