<?php
include 'db.php';

$token = $_GET['token'] ?? '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $newPassword = $_POST['password'];
    $token = $_POST['token'];

    $stmt = $pdo->prepare("SELECT * FROM password_resets WHERE token = ? AND expires_at > NOW()");
    $stmt->execute([$token]);
    $reset = $stmt->fetch();

    if ($reset) {
        $hashed = password_hash($newPassword, PASSWORD_DEFAULT);
        $pdo->prepare("UPDATE users SET password = ? WHERE email = ?")->execute([$hashed, $reset['email']]);
        $pdo->prepare("DELETE FROM password_resets WHERE email = ?")->execute([$reset['email']]);
        echo "Password has been reset.";
    } else {
        echo "Invalid or expired token.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Set New Password</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="flex items-center justify-center min-h-screen bg-gray-100">
  <form method="POST" class="bg-white p-6 rounded shadow-md w-full max-w-md">
    <h2 class="text-2xl mb-4 font-bold text-center">Set New Password</h2>
    <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
    <input type="password" name="password" class="w-full border px-3 py-2 rounded mb-4" placeholder="New password" required>
    <button type="submit" class="w-full bg-blue-500 hover:bg-blue-600 text-white py-2 rounded">Reset Password</button>
  </form>
</body>
</html>
