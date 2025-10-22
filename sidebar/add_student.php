<?php
session_start();
require_once '../php/db.php';
require_once '../php/auth.php';
require_role(['admin', 'super_admin']);


$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student_id = trim($_POST['student_id']);
    $full_name  = trim($_POST['full_name']);
    $course     = trim($_POST['course']);
    $year_level = max(1, min(4, (int)($_POST['year_level'] ?? 1)));
    $section    = trim($_POST['section']);
    $email      = trim($_POST['email']);
    $password   = $_POST['password'];

    // Check if student_id or email already exists
    $check = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
    $check->execute([$student_id, $email]);
    if ($check->fetch()) {
        $errors[] = "Student ID or Email already exists.";
    }

    if (empty($errors)) {
        try {
            $pdo->beginTransaction();

            // Insert into users
            $stmtUser = $pdo->prepare("
                INSERT INTO users (username, email, password, role, created_at) 
                VALUES (?, ?, ?, 'students', NOW())
            ");
            $stmtUser->execute([$student_id, $email, password_hash($password, PASSWORD_DEFAULT)]);

            // Insert into students
            $stmtStudent = $pdo->prepare("
                INSERT INTO students (student_id, full_name, course, year_level, section)
                VALUES (?, ?, ?, ?, ?)
            ");
            $stmtStudent->execute([$student_id, $full_name, $course, $year_level, $section]);

            $pdo->commit();

            // redirect with success flag
            header("Location: students.php?success=1");
            exit;
        } catch (Exception $e) {
            $pdo->rollBack();
            $errors[] = "Error: " . $e->getMessage();
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Add Student</title>
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
<body class="p-6 bg-gray-100">
  <div class="max-w-xl mx-auto bg-white/80 p-6 rounded shadow content">
    <h1 class="text-2xl font-bold mb-4">Add Student</h1>

    <?php if (!empty($errors)): ?>
      <div class="bg-red-100 text-red-700 p-3 rounded mb-4">
        <?= implode('<br>', $errors) ?>
      </div>
    <?php endif; ?>

    <form method="post">
      <div class="mb-4">
        <label class="block">Student ID</label>
        <input name="student_id" required class="w-full border px-3 py-2 rounded" />
      </div>
      <div class="mb-4">
        <label class="block">Full Name</label>
        <input name="full_name" required class="w-full border px-3 py-2 rounded" />
      </div>
      <div class="mb-4">
        <label class="block">Email</label>
        <input type="email" name="email" required class="w-full border px-3 py-2 rounded" />
      </div>
      <div class="mb-4">
        <label class="block">Password</label>
        <input type="password" name="password" required class="w-full border px-3 py-2 rounded" />
      </div>
      <div class="mb-4">
        <label class="block">Course</label>
        <input name="course" required class="w-full border px-3 py-2 rounded" />
      </div>
      <div class="mb-4">
        <label class="block">Year Level</label>
        <input name="year_level" type="number" min="1" max="4" required class="w-full border px-3 py-2 rounded" />
      </div>
      <div class="mb-4">
        <label class="block">Section</label>
        <input name="section" required class="w-full border px-3 py-2 rounded" />
      </div>
      <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Add Student</button>
      <a href="students.php" class="ml-2 text-gray-600">Cancel</a>
    </form>
  </div>
</body>
</html>
