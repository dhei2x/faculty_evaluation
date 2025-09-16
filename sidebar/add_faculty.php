<?php
require_once '../php/db.php';
$success = "";
$errors = [];
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $full_name = $_POST['full_name'];
    $department = $_POST['department'];
    $position = $_POST['position'];

    try {
        $pdo->beginTransaction();
        $stmt = $pdo->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, 'faculty')");
        $stmt->execute([$username, $email, $password]);
        $user_id = $pdo->lastInsertId();
        $stmt = $pdo->prepare("INSERT INTO faculties (id, faculty_id, full_name, department, position) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$user_id, $username, $full_name, $department, $position]);
        $pdo->commit();
        $success = "Faculty added successfully!";
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
    </style>
</head>
<body class="p-8 bg-gray-50">
  <div class="max-w-lg mx-auto bg-white p-6 rounded shadow">
    <h2 class="text-2xl font-bold mb-4">Add Faculty</h2>
    <?php if (!empty($success)): ?>
        <div class="text-green-600 mb-4"><?= $success ?></div>
    <?php endif; ?>
    <?php foreach ($errors as $error): ?>
        <div class="text-red-600 mb-2"><?= $error ?></div>
    <?php endforeach; ?>
    <form method="POST">
        <input name="username" placeholder="Username" class="block w-full p-2 border rounded mb-3" required>
        <input name="email" type="email" placeholder="Email" class="block w-full p-2 border rounded mb-3" required>
        <input name="password" type="password" placeholder="Password" class="block w-full p-2 border rounded mb-3" required>
        <input name="full_name" placeholder="Full Name" class="block w-full p-2 border rounded mb-3" required>
        <input name="department" placeholder="Department" class="block w-full p-2 border rounded mb-3" required>
        <input name="position" placeholder="Position" class="block w-full p-2 border rounded mb-3" required>
        <button class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Add Faculty</button>
        <a href="faculties.php" class="ml-2 text-gray-600">Cancel</a>
    </form>
  </div>
</body>
</html>