<?php
session_start();
require_once 'db.php';

$token = $_GET['token'] ?? '';
$error = "";
$success = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['token'];
    $newPassword = $_POST['password'];
    $confirmPassword = $_POST['confirm_password'];

    if ($newPassword !== $confirmPassword) {
        $error = "Passwords do not match.";
    } else {
        $stmt = $pdo->prepare("SELECT * FROM password_resets WHERE token = ? AND expires_at > NOW()");
        $stmt->execute([$token]);
        $reset = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($reset) {
            $hashed = password_hash($newPassword, PASSWORD_DEFAULT);
            $pdo->prepare("UPDATE users SET password = ? WHERE email = ?")
                ->execute([$hashed, $reset['email']]);
            $pdo->prepare("DELETE FROM password_resets WHERE email = ?")->execute([$reset['email']]);
            $success = "✅ Password reset successful! <a href='login.php'>Login</a>";
        } else {
            $error = "❌ Invalid or expired reset link.";
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Reset Password</title>
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
    <h2 class="text-2xl font-bold mb-4 text-center">Reset Password</h2>

    <?php if ($error): ?>
        <div class="bg-red-100 text-red-700 p-3 rounded mb-4"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div class="bg-green-100 text-green-700 p-3 rounded mb-4"><?= $success ?></div>
    <?php endif; ?>

    <?php if (!$success): ?>
    <form method="POST" class="space-y-3">
        <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">
        <label class="block">New Password</label>
        <input type="password" name="password" class="w-full border p-2 rounded" required>
        <label class="block">Confirm Password</label>
        <input type="password" name="confirm_password" class="w-full border p-2 rounded" required>
        <div class="flex justify-between mt-4 space-x-2">
            <button type="submit" class="flex-1 bg-blue-600 text-white p-2 rounded hover:bg-blue-700">Reset Password</button>
            <a href="login.php" class="flex-1 text-center bg-gray-400 hover:bg-gray-500 text-white p-2 rounded">Cancel</a>
        </div>
    </form>
    <?php endif; ?>
</div>
</body>
</html>
