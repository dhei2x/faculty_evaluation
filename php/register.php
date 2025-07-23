<?php
session_start();
require_once 'db.php';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    $role = $_POST['role'] ?? '';

    // Validate inputs
    if (empty($username) || empty($email) || empty($password) || empty($role)) {
        $errors[] = 'All fields are required.';
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Invalid email address.';
    }
    if ($password !== $confirmPassword) {
        $errors[] = 'Passwords do not match.';
    }
    if (!in_array($role, ['faculties', 'students'])) {
        $errors[] = 'Invalid role selected.';
    }

    // Check if email already exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        $errors[] = 'Email already registered.';
    }

    // If no errors, insert into users table
    if (empty($errors)) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)");
        $stmt->execute([$username, $email, $hashedPassword, $role]);
        $userId = $pdo->lastInsertId();

        if ($role === 'students') {
    $studentId = trim($_POST['student_id'] ?? '');
    $fullName = trim($_POST['full_name'] ?? '');
    $course = trim($_POST['course'] ?? '');
    $yearLevel = trim($_POST['year_level'] ?? '');
    $section = trim($_POST['section'] ?? '');

    if (!$studentId || !$fullName || !$course || !$yearLevel || !$section) {
        $errors[] = 'Please fill in all student details.';
    } else {
        $stmt = $pdo->prepare("INSERT INTO students (user_id, student_id, full_name, course, year_level, section)
                               VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$userId, $studentId, $fullName, $course, $yearLevel, $section]);
    }
}

       if ($role === 'faculties') {

    $fullName = trim($_POST['full_name'] ?? '');
    $department = trim($_POST['department'] ?? '');
    $position = trim($_POST['position'] ?? '');

    if (!$fullName || !$department || !$position) {
        $errors[] = 'Please fill in all faculty details.';
    } else {
        $stmt = $pdo->prepare("INSERT INTO faculties (user_id, full_name, department, position)
                               VALUES (?, ?, ?, ?)");
        $stmt->execute([$userId, $fullName, $department, $position]);
    }
}

        $_SESSION['success'] = 'Registration successful! Please log in.';
        header('Location: login.php');
        exit();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Register</title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet" />
  <script>
    function toggleFields() {
      var role = document.querySelector('select[name="role"]').value;
      document.getElementById('student-fields').style.display = (role === 'students') ? 'block' : 'none';
      document.getElementById('faculty-fields').style.display = (role === 'faculties') ? 'block' : 'none';
    }

    window.addEventListener('DOMContentLoaded', toggleFields);
  </script>
</head>
<body class="bg-gray-100 flex justify-center items-center min-h-screen">
  <div class="bg-white p-8 rounded shadow-md w-full max-w-md">
    <h2 class="text-2xl font-bold mb-6 text-center">Register</h2>

    <?php if (!empty($errors)): ?>
      <div class="bg-red-100 text-red-700 p-4 mb-4 rounded">
        <ul>
          <?php foreach ($errors as $error): ?>
            <li><?php echo htmlspecialchars($error); ?></li>
          <?php endforeach; ?>
        </ul>
      </div>
    <?php endif; ?>

    <form method="POST">
      <div class="mb-4">
        <label class="block text-gray-700">Username</label>
        <input type="text" name="username" class="w-full p-2 border rounded" required>
      </div>
      <div class="mb-4">
        <label class="block text-gray-700">Email</label>
        <input type="email" name="email" class="w-full p-2 border rounded" required>
      </div>
      <div class="mb-4">
        <label class="block text-gray-700">Password</label>
        <input type="password" name="password" class="w-full p-2 border rounded" required>
      </div>
      <div class="mb-4">
        <label class="block text-gray-700">Confirm Password</label>
        <input type="password" name="confirm_password" class="w-full p-2 border rounded" required>
      </div>
      <div class="mb-4">
        <label class="block text-gray-700">Role</label>
        <select name="role" onchange="toggleFields()" required class="w-full border px-3 py-2 rounded">
          <option value="">Select Role</option>
          <option value="faculties">Faculty</option>
          <option value="students">Student</option>
        </select>
      </div>

      <!-- Student Fields -->
      <div id="student-fields" style="display: none;">
        <div class="mb-4">
          <label class="block text-gray-700">Student ID</label>
          <input type="text" name="student_id" class="w-full p-2 border rounded">
        </div>
        <div class="mb-4">
          <label class="block text-gray-700">Full Name</label>
          <input type="text" name="full_name" class="w-full p-2 border rounded">
        </div>
        <div class="mb-4">
          <label class="block text-gray-700">Course</label>
          <input type="text" name="course" class="w-full p-2 border rounded">
        </div>
        <div class="mb-4">
          <label class="block text-gray-700">Year Level</label>
          <input type="number" name="year_level" class="w-full p-2 border rounded" min="1">
        </div>
        <div class="mb-4">
          <label class="block text-gray-700">Section</label>
          <input type="text" name="section" class="w-full p-2 border rounded">
        </div>
      </div>

      <!-- Faculty Fields -->
      <div id="faculty-fields" style="display: none;">
        <div class="mb-4">
          <label class="block text-gray-700">Full Name</label>
          <input type="text" name="full_name" class="w-full p-2 border rounded">
        </div>
        <div class="mb-4">
          <label class="block text-gray-700">Department</label>
          <input type="text" name="department" class="w-full p-2 border rounded">
        </div>
        <div class="mb-4">
          <label class="block text-gray-700">Position</label>
          <input type="text" name="position" class="w-full p-2 border rounded">
        </div>
      </div>

      <button type="submit" class="w-full bg-blue-500 text-white p-2 rounded hover:bg-blue-600">Register</button>
      <p class="text-sm text-center text-gray-600 mt-4">Already have an account? <a href="login.php" class="text-blue-500 hover:underline">Login</a></p>
    </form>
  </div>
</body>
</html>
