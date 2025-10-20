<?php
session_start();
require_once 'db.php';

// PHPMailer
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
        // Check if email exists
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // Generate token
            $token = bin2hex(random_bytes(16));
            $expires = date("Y-m-d H:i:s", strtotime("+1 hour"));

            // Store token
            $pdo->prepare("DELETE FROM password_resets WHERE email = ?")->execute([$email]);
            $stmt = $pdo->prepare("INSERT INTO password_resets (email, token, expires_at) VALUES (?, ?, ?)");
            $stmt->execute([$email, $token, $expires]);

            // Send email
            $resetLink = "http://localhost/faculty_eval/php/reset_password.php?token=$token";

            $mail = new PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host       = 'smtp.gmail.com';
                $mail->SMTPAuth   = true;
                $mail->Username   = 'wendreipayad@gmail.com';
                $mail->Password   = 'yucs cpfd rfxz bpwn';
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port       = 587;

                $mail->setFrom('wendreipayad@gmail.com', 'Faculty Evaluation System');
                $mail->addAddress($email);

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
<meta charset="UTF-8">
<title>Forgot Password</title>
<link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
<style>
body {
    position: relative;
    background-color: #f3f4f6;
    min-height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
    padding: 1rem;
}
body::before {
    content: "";
    position: fixed;
    top: 0; left: 0;
    width: 100%; height: 100%;
    background: url('logo.png') no-repeat center center;
    background-size: 900px 900px;
    opacity: 0.09;
    pointer-events: none;
    z-index: 0;
}
.content { position: relative; z-index: 1; width: 100%; max-width: 400px; }
.card { background-color: #ffffff; padding: 2rem; border-radius: 0.5rem; box-shadow: 0 2px 6px rgba(0,0,0,0.1);}
</style>
</head>
<body>
<div class="content card">
    <h2 class="text-2xl font-bold mb-4 text-center">Forgot Password</h2>

    <?php if ($error): ?>
        <div class="bg-red-100 text-red-700 p-3 rounded mb-4"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div class="bg-green-100 text-green-700 p-3 rounded mb-4"><?= $success ?></div>
    <?php endif; ?>

    <?php if (!$success): ?>
    <form method="POST" class="space-y-3">
        <label class="block">Email Address</label>
        <input type="email" name="email" class="w-full border p-2 rounded" required>
        <div class="flex justify-between mt-4 space-x-2">
            <button type="submit" class="flex-1 bg-blue-600 text-white p-2 rounded hover:bg-blue-700">Send Reset Link</button>
            <a href="login.php" class="flex-1 text-center bg-gray-400 hover:bg-gray-500 text-white p-2 rounded">Cancel</a>
        </div>
    </form>
    <?php endif; ?>
</div>
</body>
</html>
