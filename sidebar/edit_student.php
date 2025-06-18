<?php
session_start();
require_once '../php/db.php';
require_once '../php/auth.php';
require_role('admin');

// Get student ID from URL
if (!isset($_GET['id'])) {
    header("Location: students.php");
    exit;
}
$student_id = $_GET['id'];

// Fetch student record
$stmt = $pdo->prepare("SELECT * FROM students WHERE id = ?");
$stmt->execute([$student_id]);
$student = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$student) {
    echo "Student not found.";
    exit;
}

// Fetch all classes for dropdown
$classes = $pdo->query("SELECT id, class_name FROM classes")->fetchAll(PDO::FETCH_ASSOC);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $student_id = $_POST['student_id'];
  $full_name = $_POST['full_name'];
  $course = $_POST['course'];
  $year_level = $_POST['year_level'];
  $section = $_POST['section'];

  $update = $pdo->prepare("
      UPDATE students SET 
          student_id = ?, 
          full_name = ?,  
          course = ?, 
          year_level = ?, 
          section = ?
      WHERE id = ?
  ");
  $update->execute([$student_id, $full_name, $course, $year_level, $section, $_GET['id']]);

  header("Location: students.php");
  exit;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Edit Student</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-6">
  <div class="max-w-xl mx-auto bg-white p-6 rounded shadow">
    <h2 class="text-2xl font-bold mb-4">Edit Student</h2>
    <form method="POST">
      <div class="mb-4">
        <label class="block">Student ID</label>
        <input name="student_id" value="<?= htmlspecialchars($student['student_id']) ?>" required class="w-full border px-3 py-2 rounded" />
      </div>
      <div class="mb-4">
        <label class="block">Full Name</label>
        <input name="full_name" value="<?= htmlspecialchars($student['full_name']) ?>" required class="w-full border px-3 py-2 rounded" />
      </div>
      <div class="mb-4">
        <label class="block">Course</label>
        <input name="course" value="<?= htmlspecialchars($student['course']) ?>" required class="w-full border px-3 py-2 rounded" />
      </div>
      <div class="mb-4">
        <label class="block">Year Level</label>
        <input type="number" name="year_level" value="<?= htmlspecialchars($student['year_level']) ?>" required class="w-full border px-3 py-2 rounded" />
      </div>
      <div class="mb-4">
        <label class="block">Section</label>
        <input name="section" value="<?= htmlspecialchars($student['section']) ?>" required class="w-full border px-3 py-2 rounded" />
      </div>
      <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Update</button>
      <a href="students.php" class="ml-2 text-gray-600">Cancel</a>
    </form>
  </div>
</body>
</html>