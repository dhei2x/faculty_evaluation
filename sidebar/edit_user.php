<?php
session_start();
require_once '../php/db.php';
require_once '../php/auth.php';
require_role('admin');

// Check if ID is present
if (!isset($_GET['id'])) {
    header("Location: users.php");
    exit();
}

$id = $_GET['id'];

// Fetch the user to edit
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = :id");
$stmt->execute(['id' => $id]);
$user = $stmt->fetch();

if (!$user) {
    echo "User not found.";
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $username = trim($_POST['username']);
  $role = $_POST['role'];
  $password = $_POST['password'];

  // ✅ Check for duplicate username (excluding the current user)
  $check = $pdo->prepare("SELECT id FROM users WHERE username = :username AND id != :id");
  $check->execute(['username' => $username, 'id' => $id]);
  if ($check->fetch()) {
      echo "Username already taken.";
      exit();
  }

  if (!empty($password)) {
      // Update with new password
      $stmt = $pdo->prepare("UPDATE users SET username = :username, role = :role, password = :password WHERE id = :id");
      $stmt->execute([
          'username' => $username,
          'role' => $role,
          'password' => password_hash($password, PASSWORD_DEFAULT),
          'id' => $id
      ]);
  } else {
      // Update without changing password
      $stmt = $pdo->prepare("UPDATE users SET username = :username, role = :role WHERE id = :id");
      $stmt->execute([
          'username' => $username,
          'role' => $role,
          'id' => $id
      ]);
  }

  header("Location: users.php");
  exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Edit User</title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 p-6 min-h-screen">
  <div class="max-w-xl mx-auto bg-white p-6 rounded shadow">
    <h1 class="text-2xl font-bold mb-4">Edit User</h1>
    <form method="POST" class="space-y-4">
      <div>
        <label class="block text-sm font-medium text-gray-700">Username</label>
        <input type="text" name="username" required value="<?= htmlspecialchars($user['username']) ?>" class="mt-1 block w-full border border-gray-300 rounded p-2">
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700">New Password (leave blank to keep current)</label>
        <input type="password" name="password" class="mt-1 block w-full border border-gray-300 rounded p-2">
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700">Role</label>
        <select name="role" required class="mt-1 block w-full border border-gray-300 rounded p-2">
          <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
          <option value="faculty" <?= $user['role'] === 'faculty' ? 'selected' : '' ?>>Faculties</option>
          <option value="students" <?= $user['role'] === 'students' ? 'selected' : '' ?>>Students</option>
        </select>
      </div>
      <div class="flex space-x-4">
        <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">Save Changes</button>
        <a href="users.php" class="bg-gray-400 text-white px-4 py-2 rounded hover:bg-gray-500">Cancel</a>
      </div>
    </form>
  </div>
</body>
</html>
