<?php
session_start();
include '../php/db.php';
include '../php/auth.php';
require_role('admin');

$message = "";

// Add User
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'add') {
  $username = trim($_POST['username']);
  $role = $_POST['role'];
  $password = $_POST['password'];
  
  // ✅ Check for duplicate username
  $check = $pdo->prepare("SELECT id FROM users WHERE username = :username");
  $check->execute(['username' => $username]);
  if ($check->fetch()) {
      $message = "❌ Username already exists.";
  } else {
      $stmt = $pdo->prepare("INSERT INTO users (username, password, role) VALUES (:username, :password, :role)");
      $stmt->execute([
          'username' => $username,
          'password' => password_hash($password, PASSWORD_DEFAULT),
          'role' => $role
      ]);
      header("Location: users.php");
      exit();
  }
  
}

// Edit User
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'edit') {
    $stmt = $pdo->prepare("UPDATE users SET username = :username, role = :role WHERE id = :id");
    $stmt->execute([
        'username' => $_POST['username'],
        'role' => $_POST['role'],
        'id' => $_POST['id']
    ]);
    header("Location: users.php");
    exit();
}

// Delete User
if (isset($_GET['delete'])) {
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = :id");
    $stmt->execute(['id' => $_GET['delete']]);
    header("Location: users.php");
    exit();
}

// Fetch all users
$stmt = $pdo->query("SELECT id, username, role FROM users ORDER BY username ASC");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Manage Users</title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 min-h-screen p-6">
  <div class="max-w-4xl mx-auto bg-white p-6 rounded shadow">
    <h1 class="text-2xl font-bold mb-4">Manage Users</h1>
    <a href="../php/admin_dashboard.php" class="inline-block mb-4 bg-gray-200 hover:bg-gray-300 text-gray-800 px-4 py-2 rounded">
  ← Back to Dashboard
</a>
    <?php if ($message): ?>
      <div class="mb-4 text-red-600 font-semibold"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <!-- Add User Form -->
    <form method="POST" class="mb-6 space-y-4">
      <input type="hidden" name="action" value="add">
      <div>
        <label class="block text-sm font-medium text-gray-700">Username</label>
        <input type="text" name="username" required class="mt-1 block w-full border rounded p-2" />
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700">Password</label>
        <input type="password" name="password" required class="mt-1 block w-full border rounded p-2" />
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700">Role</label>
        <select name="role" required class="mt-1 block w-full border rounded p-2">
          <option value="admin">Admin</option>
          <option value="faculty">Faculties</option>
          <option value="students">Student</option>
        </select>
      </div>
      <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Add User</button>
    </form>

    <!-- User List -->
    <h2 class="text-xl font-semibold mb-2">User List</h2>
<ul class="space-y-2">
  <?php foreach ($users as $user): ?>
    <li class="p-2 bg-gray-50 border rounded flex justify-between items-center">
      <span>
        <?= htmlspecialchars($user['username']) ?> - <?= htmlspecialchars($user['role']) ?>
      </span>
      <span class="space-x-2">
        <a href="edit_user.php?id=<?= $user['id'] ?>" class="text-blue-600 hover:underline">Edit</a>
        <a href="delete_user.php?id=<?= $user['id'] ?>" onclick="return confirm('Are you sure you want to delete this user?');" class="text-red-600 hover:underline">Delete</a>
      </span>
    </li>
  <?php endforeach; ?>
  <?php if (empty($users)): ?>
    <li class="text-gray-500">No users found.</li>
  <?php endif; ?>
</ul>
  </div>
</body>
</html>
