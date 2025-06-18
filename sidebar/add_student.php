<?php
session_start();
require_once '../php/db.php';
require_once '../php/auth.php';
require_role('admin');

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student_id = $_POST['student_id'] ?? '';
    $full_name = $_POST['full_name'] ?? '';
    $course = $_POST['course'] ?? '';
    $year_level = max(1, (int)($_POST['year_level'] ?? 1));
    $section = $_POST['section'] ?? '';
    $username = $_POST['username'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = isset($_POST['password']) ? password_hash($_POST['password'], PASSWORD_DEFAULT) : '';

    // Create user
    $stmtUser = $pdo->prepare("INSERT INTO users (username, email, password, role, created_at) VALUES (?, ?, ?, 'student', NOW())");
    $stmtUser->execute([$username, $email, $password]);
    $user_id = $pdo->lastInsertId();

    // Create student
    $stmtStudent = $pdo->prepare("INSERT INTO students (user_id, student_id, full_name, course, year_level, section) VALUES (?, ?, ?, ?, ?, ?)");
    $stmtStudent->execute([$user_id, $student_id, $full_name, $course, $year_level, $section]);

    header("Location: students.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Add Student</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="p-6 bg-gray-100">
  <div class="max-w-xl mx-auto bg-white p-6 rounded shadow">
    <h1 class="text-2xl font-bold mb-4">Add Student</h1>
    <form method="post">
      <div class="mb-4">
        <label class="block">Username</label>
        <input name="username" required class="w-full border px-3 py-2 rounded" />
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
        <label class="block">Student ID</label>
        <input name="student_id" required class="w-full border px-3 py-2 rounded" />
      </div>
      <div class="mb-4">
        <label class="block">Full Name</label>
        <input name="full_name" required class="w-full border px-3 py-2 rounded" />
      </div>
      <div class="mb-4">
        <label class="block">Course</label>
        <input name="course" required class="w-full border px-3 py-2 rounded" />
      </div>
      <div class="mb-4">
        <label class="block">Year Level</label>
        <input name="year_level" type="number" min="1" required class="w-full border px-3 py-2 rounded" />
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
