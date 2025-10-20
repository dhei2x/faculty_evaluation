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
        $firstName  = trim($_POST['faculty_first_name'] ?? '');
        $middleName = trim($_POST['faculty_middle_name'] ?? '');
        $lastName   = trim($_POST['faculty_last_name'] ?? '');
        $department = trim($_POST['department'] ?? '');
        $position   = trim($_POST['position'] ?? '');

        if ($firstName === '') $errors[] = "First Name is required.";
        if ($lastName === '') $errors[] = "Last Name is required.";
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
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT); // 🔒 hashed password

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
                    INSERT INTO faculties (id, faculty_id, first_name, middle_name, last_name, department, position)
                    VALUES (?, ?, ?, ?, ?, ?, ?)
                ");
                $stmt->execute([$userId, $username, $firstName, $middleName, $lastName, $department, $position]);
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
            background-color: #f3f4f6;
        }
        body::before {
            content: "";
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            background: url('../php/logo.png') no-repeat center center;
            background-size: 900px 900px;
            opacity: 0.09;
            pointer-events: none;
            z-index: 0;
        }
        .content { position: relative; z-index: 1; }
        .card { background-color: #ffffff; padding: 2rem; border-radius: 0.5rem; box-shadow: 0 2px 6px rgba(0,0,0,0.1);}
  </style>
</head>
<body class="flex items-center justify-center min-h-screen p-4">
  <div class="w-full max-w-md card content">
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

    <form method="POST" novalidate class="space-y-3">
      <label>Username</label>
      <input name="username" value="<?= htmlspecialchars($old['username'] ?? '') ?>" class="w-full border p-2 rounded" required>

      <label>Email</label>
      <input name="email" type="email" value="<?= htmlspecialchars($old['email'] ?? '') ?>" class="w-full border p-2 rounded" required>

      <label>Password</label>
      <input name="password" type="password" class="w-full border p-2 rounded" required>

      <label>Confirm Password</label>
      <input name="confirm_password" type="password" class="w-full border p-2 rounded" required>

      <label>Role</label>
      <select name="role" id="roleSelect" class="w-full border p-2 rounded" required>
        <option value="" disabled <?= empty($role) ? 'selected' : '' ?>>Select Role</option>
        <option value="faculty" <?= $role === 'faculty' ? 'selected' : '' ?>>Faculty</option>
        <option value="students" <?= $role === 'students' ? 'selected' : '' ?>>Student</option>
      </select>

      <!-- Student fields -->
      <div id="studentFields" style="display:none;">
        <label>Full Name</label>
        <input name="student_full_name" value="<?= htmlspecialchars($old['student_full_name'] ?? '') ?>" class="w-full border p-2 rounded mb-2">
        <label>Course</label>
        <input name="course" value="<?= htmlspecialchars($old['course'] ?? '') ?>" class="w-full border p-2 rounded mb-2">
        <label>Year Level</label>
        <select name="year_level" class="w-full border p-2 rounded mb-2">
          <option value="" disabled <?= empty($old['year_level']) ? 'selected' : '' ?>>-- Select Year --</option>
          <option value="1" <?= ($old['year_level'] ?? '') === '1' ? 'selected' : '' ?>>1</option>
          <option value="2" <?= ($old['year_level'] ?? '') === '2' ? 'selected' : '' ?>>2</option>
          <option value="3" <?= ($old['year_level'] ?? '') === '3' ? 'selected' : '' ?>>3</option>
          <option value="4" <?= ($old['year_level'] ?? '') === '4' ? 'selected' : '' ?>>4</option>
        </select>
        <label>Section</label>
        <input name="section" value="<?= htmlspecialchars($old['section'] ?? '') ?>" class="w-full border p-2 rounded mb-2">
      </div>

      <!-- Faculty fields -->
      <div id="facultyFields" style="display:none;">
        <label>First Name</label>
        <input name="faculty_first_name" value="<?= htmlspecialchars($old['faculty_first_name'] ?? '') ?>" class="w-full border p-2 rounded mb-2">

        <label>Middle Name</label>
        <input name="faculty_middle_name" value="<?= htmlspecialchars($old['faculty_middle_name'] ?? '') ?>" class="w-full border p-2 rounded mb-2">

        <label>Last Name</label>
        <input name="faculty_last_name" value="<?= htmlspecialchars($old['faculty_last_name'] ?? '') ?>" class="w-full border p-2 rounded mb-2">

        <label>Department</label>
        <input name="department" value="<?= htmlspecialchars($old['department'] ?? '') ?>" class="w-full border p-2 rounded mb-2">

        <label>Position</label>
        <input name="position" value="<?= htmlspecialchars($old['position'] ?? '') ?>" class="w-full border p-2 rounded mb-2">
      </div>

      <div class="flex justify-between space-x-2 mt-4">
        <button type="submit" name="submit" value="1" class="flex-1 bg-blue-600 text-white p-2 rounded hover:bg-blue-700">Register</button>
        <a href="login.php" class="flex-1 text-center bg-gray-400 hover:bg-gray-500 text-white p-2 rounded">Cancel</a>
      </div>
    </form>
  </div>

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
  toggleFields();
});
</script>
</body>
</html>
