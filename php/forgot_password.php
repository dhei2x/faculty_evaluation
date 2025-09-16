<?php
session_start();
require_once 'db.php';

// ✅ Load PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../PHPMailer/src/Exception.php';
require '../PHPMailer/src/PHPMailer.php';
require '../PHPMailer/src/SMTP.php';

$error = "";
$success = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');

    if ($email === '') {
        $error = "Email is required.";
    } else {
        // Check if email exists in users table
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // Generate reset token
            $token = bin2hex(random_bytes(16));
            $expires = date("Y-m-d H:i:s", strtotime("+1 hour"));

            // Store token in password_resets table
            $pdo->prepare("DELETE FROM password_resets WHERE email = ?")->execute([$email]);
            $stmt = $pdo->prepare("INSERT INTO password_resets (email, token, expires_at) VALUES (?, ?, ?)");
            $stmt->execute([$email, $token, $expires]);

            // Send reset email
            $resetLink = "http://localhost/faculty_eval/php/reset_password.php?token=$token";

            $mail = new PHPMailer(true);
            try {
                // Server settings
                $mail->isSMTP();
                $mail->Host       = 'smtp.gmail.com';
                $mail->SMTPAuth   = true;
                $mail->Username   = 'wendreipayad@gmail.com';   // your Gmail
                $mail->Password   = 'yucs cpfd rfxz bpwn';   // Gmail App Password
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port       = 587;

                // Recipients
                $mail->setFrom('wendreipayad@gmail.com', 'Faculty Evaluation System');
                $mail->addAddress($email);

                // Content
                $mail->isHTML(true);
                $mail->Subject = "Password Reset Request";
                $mail->Body    = "
                    <p>Hello,</p>
                    <p>You requested a password reset. Click the link below to reset your password:</p>
                    <p><a href='$resetLink'>$resetLink</a></p>
                    <p>This link will expire in 1 hour.</p>
                ";

                $mail->send();
                $success = "✅ A reset link has been sent to your email.";
            } catch (Exception $e) {
                $error = "Mailer Error: " . $mail->ErrorInfo;
            }
        } else {
            $error = "❌ No account found with that email.";
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>Forgot Password</title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet" />
</head>
<body class="bg-gray-100 flex justify-center items-center min-h-screen">
  <div class="bg-white p-6 rounded shadow-md w-full max-w-md">
    <h2 class="text-xl font-bold mb-4">Forgot Password</h2>
    <?php if ($error): ?><p class="text-red-600 mb-4"><?= htmlspecialchars($error) ?></p><?php endif; ?>
    <?php if ($success): ?><p class="text-green-600 mb-4"><?= $success ?></p><?php endif; ?>

    <form method="POST" class="space-y-4">
      <label>Email Address</label>
      <input type="email" name="email" required class="w-full border p-2 rounded">
      <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Send Reset Link</button>
    </form>
  </div>
</body>
</html>
