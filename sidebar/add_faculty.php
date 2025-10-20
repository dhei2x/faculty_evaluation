<?php
require_once '../php/db.php';
$success = "";
$errors = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $last_name = trim($_POST['last_name']);
    $first_name = trim($_POST['first_name']);
    $middle_name = trim($_POST['middle_name']);
    $department = trim($_POST['department']);
    $position = trim($_POST['position']);

    try {
        $pdo->beginTransaction();

        // Insert into users table
        $stmt = $pdo->prepare("INSERT INTO users (username, email, password, role, created_at) VALUES (?, ?, ?, 'faculty', NOW())");
        $stmt->execute([$username, $email, $password]);
        $user_id = $pdo->lastInsertId();

        // Insert into faculties table (no full_name)
        $stmt = $pdo->prepare("
            INSERT INTO faculties (id, faculty_id, last_name, first_name, middle_name, department, position, created_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, NOW())
        ");
        $stmt->execute([$user_id, $username, $last_name, $first_name, $middle_name, $department, $position]);

        $pdo->commit();
        $success = "✅ Faculty added successfully!";
    } catch (PDOException $e) {
        $pdo->rollBack();
        $errors[] = "Error: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>Add Faculty</title>
  <script src="https://cdn.tailwindcss.com"></script>

  <style>
    body {
        position: relative;
        background-color: #f3f4f6;
        min-height: 100vh;
        display: flex;
        justify-content: center;
        align-items: center;
    }

    /* Background watermark */
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

    /* White form container that covers logo */
    .form-container {
        position: relative;
        z-index: 1;
        background: white;
        padding: 2rem;
        border-radius: 0.75rem;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        width: 100%;
        max-width: 550px;
    }
  </style>
</head>
<body>
  <div class="form-container">
    <h2 class="text-2xl font-bold mb-4 text-center">Add Faculty</h2>

    <?php if ($success): ?>
      <div class="bg-green-100 text-green-700 p-3 rounded mb-4"><?= $success ?></div>
    <?php endif; ?>

    <?php foreach ($errors as $error): ?>
      <div class="bg-red-100 text-red-700 p-3 rounded mb-2"><?= $error ?></div>
    <?php endforeach; ?>

    <form method="POST" class="space-y-3">
      <input name="username" placeholder="Faculty ID" class="w-full border rounded p-2" required>
      <input name="email" type="email" placeholder="Email" class="w-full border rounded p-2" required>
      <input name="password" type="password" placeholder="Password" class="w-full border rounded p-2" required>

      <div class="grid grid-cols-3 gap-2">
        <input name="last_name" placeholder="Last Name" class="border rounded p-2" required>
        <input name="first_name" placeholder="First Name" class="border rounded p-2" required>
        <input name="middle_name" placeholder="Middle Name" class="border rounded p-2">
      </div>

      <input name="department" placeholder="Department" class="w-full border rounded p-2" required>
      <input name="position" placeholder="Position" class="w-full border rounded p-2" required>

      <div class="flex justify-between">
        <button class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Add Faculty</button>
        <a href="faculties.php" class="text-gray-600 self-center hover:underline">Cancel</a>
      </div>
    </form>
  </div>
</body>
</html>
