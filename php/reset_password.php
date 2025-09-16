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
        // Verify token
        $stmt = $pdo->prepare("SELECT * FROM password_resets WHERE token = ? AND expires_at > NOW()");
        $stmt->execute([$token]);
        $reset = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($reset) {
            $hashed = password_hash($newPassword, PASSWORD_DEFAULT);

            // Update password
            $pdo->prepare("UPDATE users SET password = ? WHERE email = ?")
                ->execute([$hashed, $reset['email']]);

            // Delete token
            $pdo->prepare("DELETE FROM password_resets WHERE email = ?")->execute([$reset['email']]);

            $success = "✅ Password reset successful! <a href='login.php' class='text-blue-600'>Login here</a>";
        } else {
            $error = "Invalid or expired token.";
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>Reset Password</title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet" />
</head>
<body class="bg-gray-100 flex justify-center items-center min-h-screen">
  <div class="bg-white p-6 rounded shadow-md w-full max-w-md">
    <h2 class="text-xl font-bold mb-4">Reset Password</h2>
    <?php if ($error): ?><p class="text-red-600 mb-4"><?= htmlspecialchars($error) ?></p><?php endif; ?>
    <?php if ($success): ?><p class="text-green-600 mb-4"><?= $success ?></p><?php endif; ?>
    <?php if (!$success): ?>
      <form method="POST">
        <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">
        <label>New Password</label>
        <input type="password" name="password" required class="w-full border p-2 mb-3 rounded">
        <label>Confirm Password</label>
        <input type="password" name="confirm_password" required class="w-full border p-2 mb-3 rounded">
        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Reset Password</button>
      </form>
    <?php endif; ?>
  </div>
</body>
</html>
