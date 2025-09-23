<?php 
session_start();
require_once '../php/db.php';
require_once '../php/auth.php';
require_role('admin');

$message = "";

// Add User
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'add') {
    $username = trim($_POST['username']);
    $role     = $_POST['role'];
    $password = $_POST['password'];

    // Duplicate check
    $check = $pdo->prepare("SELECT id FROM users WHERE username = ?");
    $check->execute([$username]);
    if ($check->fetch()) {
        $message = "❌ Username already exists.";
    } else {
        $stmt = $pdo->prepare("INSERT INTO users (username, password, role, created_at) VALUES (?, ?, ?, NOW())");
        $stmt->execute([$username, password_hash($password, PASSWORD_DEFAULT), $role]);
        header("Location: users.php");
        exit();
    }
}

// Fetch all users
$stmt = $pdo->query("SELECT id, username, role FROM users ORDER BY id ASC");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Manage Users</title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
 <style>
        body {
            position: relative;
            background-color: #f3f4f6; /* Tailwind gray-100 */
        }

        /* Transparent logo watermark */
        body::before {
            content: "";
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: url('../php/logo.png') no-repeat center center;
            background-size: 900px 900px; /* adjust size */
            opacity: 0.09; /* 👈 controls transparency (lower = more transparent) */
            pointer-events: none; /* so it won’t block clicks */
            z-index: 0;
        }

        /* Keep content above background */
        .content {
            position: relative;
            z-index: 1;
        }
    </style>
</head>
<body class="min-h-screen p-6">
  <div class="max-w-4xl mx-auto p-6 glass-card content">
    <h1 class="text-2xl font-bold mb-4">Manage Users</h1>
    <a href="../php/admin_dashboard.php" 
       class="inline-block mb-4 bg-blue-200 hover:bg-gray-300 text-gray-800 px-4 py-2 rounded">
      ← Back to Dashboard
    </a>

    <?php if ($message): ?>
      <div class="mb-4 text-red-600 font-semibold"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <h2 class="text-xl font-semibold mb-2">User List</h2>
    <ul class="space-y-3">
      <?php foreach ($users as $user): ?>
        <li class="p-3 border rounded flex justify-between items-center glass-card">
          <span>
            <?= htmlspecialchars($user['username']) ?> - <?= htmlspecialchars($user['role']) ?>
            <?php if ($user['id'] == 1): ?>
              <span class="ml-2 px-2 py-1 bg-yellow-200 text-yellow-800 rounded text-xs font-semibold">
                Super Admin
              </span>
            <?php endif; ?>
          </span>
          <span class="space-x-2">
            <?php if ($user['id'] != 1): ?>
              <a href="edit_user.php?id=<?= $user['id'] ?>" class="text-blue-600 hover:underline">Edit</a>
              <a href="delete_user.php?id=<?= $user['id'] ?>" 
                 onclick="return confirm('Are you sure you want to delete this user?');" 
                 class="text-red-600 hover:underline">Delete</a>
            <?php else: ?>
              <span class="text-gray-500 italic">Locked</span>
            <?php endif; ?>
          </span>
        </li>
      <?php endforeach; ?>
      <?php if (empty($users)): ?>
        <li class="text-gray-500 glass-card p-3 rounded">No users found.</li>
      <?php endif; ?>
    </ul>
  </div>
</body>
</html>
