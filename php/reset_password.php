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
        // Check token
        $stmt = $pdo->prepare("SELECT * FROM password_resets WHERE token = ? AND expires_at > NOW()");
        $stmt->execute([$token]);
        $reset = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($reset) {
            $hashed = password_hash($newPassword, PASSWORD_DEFAULT);

            // Update user password
            $pdo->prepare("UPDATE users SET password = ? WHERE email = ?")
                ->execute([$hashed, $reset['email']]);

            // Remove used token
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
<head><title>Reset Password</title></head>
<body>
  <h2>Reset Password</h2>
  <?php if ($error) echo "<p style='color:red'>$error</p>"; ?>
  <?php if ($success) echo "<p style='color:green'>$success</p>"; ?>

  <?php if (!$success): ?>
    <form method="POST">
      <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">
      <label>New Password:</label>
      <input type="password" name="password" required><br>
      <label>Confirm Password:</label>
      <input type="password" name="confirm_password" required><br>
      <button type="submit">Reset Password</button>
    </form>
  <?php endif; ?>
</body>
</html>
