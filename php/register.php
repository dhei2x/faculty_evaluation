<?php
session_start();
require_once 'db.php';

$errors = [];
$old = $_POST ?? [];
$role = $_POST['role'] ?? '';
$isFinalSubmit = isset($_POST['submit']);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $isFinalSubmit) {
    $username        = trim($_POST['username'] ?? '');
    $email           = trim($_POST['email'] ?? '');
    $password        = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    // --- Basic validation ---
    if ($username === '') $errors[] = "Username is required.";
    if ($email === '') {
        $errors[] = "Email is required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email address.";
    }
    if ($password === '') $errors[] = "Password is required.";
    if ($confirmPassword === '') $errors[] = "Confirm password is required.";
    if ($password && $confirmPassword && $password !== $confirmPassword) {
        $errors[] = "Passwords do not match.";
    }
    if ($password && strlen($password) < 6) {
        $errors[] = "Password must be at least 6 characters.";
    }
    if ($role === '') {
        $errors[] = "Role is required.";
    } elseif (!in_array($role, ['faculty', 'students'])) {
        $errors[] = "Invalid role selected.";
    }

    // --- Role-specific validation ---
    if ($role === 'students') {
        $fullName  = trim($_POST['student_full_name'] ?? '');
        $course    = trim($_POST['course'] ?? '');
        $yearLevel = trim($_POST['year_level'] ?? '');
        $section   = trim($_POST['section'] ?? '');

        if ($fullName === '') $errors[] = "Student full name is required.";
        if ($course === '') $errors[] = "Course is required.";
        if ($yearLevel === '') $errors[] = "Year level is required.";
        if ($section === '') $errors[] = "Section is required.";
    } elseif ($role === 'faculty') {
        $fullName   = trim($_POST['faculty_full_name'] ?? '');
        $department = trim($_POST['department'] ?? '');
        $position   = trim($_POST['position'] ?? '');

        if ($fullName === '') $errors[] = "Faculty full name is required.";
        if ($department === '') $errors[] = "Department is required.";
        if ($position === '') $errors[] = "Position is required.";
    }

    // --- Check duplicates ---
    if (empty($errors)) {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$username, $email]);
        if ($stmt->fetch()) {
            $errors[] = "Username or Email already registered.";
        }
    }

    // --- Insert ---
    if (empty($errors)) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        try {
            $pdo->beginTransaction();
            $stmt = $pdo->prepare("
                INSERT INTO users (username, email, password, role, created_at) 
                VALUES (?, ?, ?, ?, NOW())
            ");
            $stmt->execute([$username, $email, $hashedPassword, $role]);
            $userId = $pdo->lastInsertId();

            if ($role === 'students') {
                $stmt = $pdo->prepare("
                    INSERT INTO students (id, student_id, full_name, course, year_level, section)
                    VALUES (?, ?, ?, ?, ?, ?)
                ");
                $stmt->execute([$userId, $username, $fullName, $course, $yearLevel, $section]);
            } elseif ($role === 'faculty') {
                $stmt = $pdo->prepare("
                    INSERT INTO faculties (id, faculty_id, full_name, department, position)
                    VALUES (?, ?, ?, ?, ?)
                ");
                $stmt->execute([$userId, $username, $fullName, $department, $position]);
            }

            $pdo->commit();
            $_SESSION['success'] = "Registration successful! Please log in.";
            header("Location: login.php");
            exit;
        } catch (Exception $e) {
            $pdo->rollBack();
            $errors[] = "Registration failed: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8" />
  <title>Register</title>
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
<body class="bg-gray-100 flex items-center justify-center min-h-screen">
  <div class="bg-white p-8 rounded shadow w-full max-w-md">
    <h2 class="text-2xl font-bold mb-6 text-center">Register</h2>

    <?php if (!empty($errors)): ?>
      <div class="bg-red-100 text-red-700 p-3 rounded mb-4">
        <ul>
          <?php foreach ($errors as $err): ?>
            <li><?= htmlspecialchars($err) ?></li>
          <?php endforeach; ?>
        </ul>
      </div>
    <?php endif; ?>
<form method="POST" novalidate>
  <label class="block mb-2">Username</label>
  <input name="username" value="<?= htmlspecialchars($old['username'] ?? '') ?>" class="w-full border p-2 mb-3" required>

  <label class="block mb-2">Email</label>
  <input name="email" type="email" value="<?= htmlspecialchars($old['email'] ?? '') ?>" class="w-full border p-2 mb-3" required>

  <label class="block mb-2">Password</label>
  <input name="password" type="password" class="w-full border p-2 mb-3" required>

  <label class="block mb-2">Confirm Password</label>
  <input name="confirm_password" type="password" class="w-full border p-2 mb-3" required>

  <label class="block mb-2">Role</label>
  <select name="role" id="roleSelect" class="w-full border p-2 mb-4" required>
    <option value="" disabled <?= empty($role) ? 'selected' : '' ?>>Select Role</option>
    <option value="faculty" <?= $role === 'faculty' ? 'selected' : '' ?>>Faculty</option>
    <option value="students" <?= $role === 'students' ? 'selected' : '' ?>>Student</option>
  </select>

  <!-- Student fields -->
  <div id="studentFields" style="display:none;">
    <label class="block mb-2">Full name</label>
    <input name="student_full_name" value="<?= htmlspecialchars($old['student_full_name'] ?? '') ?>" class="w-full border p-2 mb-3">

    <label class="block mb-2">Course</label>
    <input name="course" value="<?= htmlspecialchars($old['course'] ?? '') ?>" class="w-full border p-2 mb-3">

    <label class="block mb-2">Year level</label>
    <select name="year_level" class="w-full border p-2 mb-3">
      <option value="" disabled <?= empty($old['year_level']) ? 'selected' : '' ?>>-- Select Year --</option>
      <option value="1" <?= ($old['year_level'] ?? '') === '1' ? 'selected' : '' ?>>1</option>
      <option value="2" <?= ($old['year_level'] ?? '') === '2' ? 'selected' : '' ?>>2</option>
      <option value="3" <?= ($old['year_level'] ?? '') === '3' ? 'selected' : '' ?>>3</option>
      <option value="4" <?= ($old['year_level'] ?? '') === '4' ? 'selected' : '' ?>>4</option>
    </select>

    <label class="block mb-2">Section</label>
    <input name="section" value="<?= htmlspecialchars($old['section'] ?? '') ?>" class="w-full border p-2 mb-3">
  </div>

  <!-- Faculty fields -->
  <div id="facultyFields" style="display:none;">
    <label class="block mb-2">Full name</label>
    <input name="faculty_full_name" value="<?= htmlspecialchars($old['faculty_full_name'] ?? '') ?>" class="w-full border p-2 mb-3">

    <label class="block mb-2">Department</label>
    <input name="department" value="<?= htmlspecialchars($old['department'] ?? '') ?>" class="w-full border p-2 mb-3">

    <label class="block mb-2">Position</label>
    <input name="position" value="<?= htmlspecialchars($old['position'] ?? '') ?>" class="w-full border p-2 mb-3">
  </div>

  <div class="flex justify-between space-x-2 mt-4">
    <button type="submit" name="submit" value="1" class="flex-1 bg-blue-600 text-white p-2 rounded">Register</button>
    <a href="login.php" class="flex-1 text-center bg-gray-400 hover:bg-gray-500 text-white p-2 rounded">Cancel</a>
  </div>
</form>

<script>
document.addEventListener("DOMContentLoaded", function() {
  const roleSelect = document.getElementById("roleSelect");
  const studentFields = document.getElementById("studentFields");
  const facultyFields = document.getElementById("facultyFields");

  function toggleFields() {
    if (roleSelect.value === "students") {
      studentFields.style.display = "block";
      facultyFields.style.display = "none";
    } else if (roleSelect.value === "faculty") {
      facultyFields.style.display = "block";
      studentFields.style.display = "none";
    } else {
      studentFields.style.display = "none";
      facultyFields.style.display = "none";
    }
  }

  roleSelect.addEventListener("change", toggleFields);
  toggleFields(); // run on page load
});
</script>
