<?php
session_start();
require_once '../php/db.php';
require_once '../php/auth.php';
require_role(['admin', 'superadmin']);


if (!isset($_GET['id'])) {
    header("Location: users.php");
    exit();
}
$id = (int) $_GET['id'];

// Prevent editing super admin
if ($id === 1) {
    echo "<div style='padding:20px; color:red; font-weight:bold;'>⚠️ You cannot edit the Super Admin account.</div>";
    echo "<a href='users.php' style='color:blue; text-decoration:underline;'>Back to Users</a>";
    exit();
}

$stmt = $pdo->prepare("SELECT * FROM users WHERE id = :id");
$stmt->execute(['id' => $id]);
$user = $stmt->fetch();

if (!$user) {
    echo "User not found.";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $role = $_POST['role'];
    $password = $_POST['password'];

    $check = $pdo->prepare("SELECT id FROM users WHERE username = :username AND id != :id");
    $check->execute(['username' => $username, 'id' => $id]);
    if ($check->fetch()) {
        echo "Username already taken.";
        exit();
    }

    if (!empty($password)) {
        $stmt = $pdo->prepare("UPDATE users SET username = :username, role = :role, password = :password WHERE id = :id");
        $stmt->execute([
            'username' => $username,
            'role' => $role,
            'password' => password_hash($password, PASSWORD_DEFAULT),
            'id' => $id
        ]);
    } else {
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
            background-size: 900px 900px;
            opacity: 0.09;
            pointer-events: none;
            z-index: 0;
        }

        /* Keep content above background */
        .content {
            position: relative;
            z-index: 1;
        }

        /* Card effect to allow bg to peek in gaps */
        .card {
            background-color: #ffffff; /* solid for readability */
            padding: 1.5rem;
            border-radius: 0.5rem;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body class="min-h-screen p-6">
  <div class="max-w-xl mx-auto card content">
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
          <option value="faculty" <?= $user['role'] === 'faculty' ? 'selected' : '' ?>>Faculty</option>
          <option value="students" <?= $user['role'] === 'students' ? 'selected' : '' ?>>Students</option>z
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
