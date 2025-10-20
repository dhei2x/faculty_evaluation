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

// Fetch student with linked email
$stmt = $pdo->prepare("
    SELECT s.id, s.student_id, s.full_name, s.course, s.year_level, s.section, u.email
    FROM students s
    LEFT JOIN users u ON u.username = s.student_id
    WHERE s.id = ?
");
$stmt->execute([$student_id]);
$student = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$student) {
    echo "Student not found.";
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student_id_val = $_POST['student_id'];
    $full_name      = $_POST['full_name'];
    $course         = $_POST['course'];
    $year_level     = max(1, min(4, (int)$_POST['year_level'])); // restrict to 1–4
    $section        = $_POST['section'];
    $email          = $_POST['email'];

    try {
        $pdo->beginTransaction();

        // Update students table
        $update = $pdo->prepare("
            UPDATE students 
            SET student_id = ?, full_name = ?, course = ?, year_level = ?, section = ?
            WHERE id = ?
        ");
        $update->execute([$student_id_val, $full_name, $course, $year_level, $section, $_GET['id']]);

        // Update email in users table (linked by username=student_id)
        $updateEmail = $pdo->prepare("
            UPDATE users SET email = ?, username = ?
            WHERE username = ?
        ");
        $updateEmail->execute([$email, $student_id_val, $student['student_id']]);

        $pdo->commit();

        header("Location: students.php?success=1");
        exit;
    } catch (Exception $e) {
        $pdo->rollBack();
        echo "Error updating student: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Edit Student</title>
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
<body class="bg-gray-100 p-6">
  <div class="max-w-xl mx-auto bg-white/80 p-6 rounded shadow content">
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
        <label class="block">Email</label>
        <input type="email" name="email" value="<?= htmlspecialchars($student['email'] ?? '') ?>" required class="w-full border px-3 py-2 rounded" />
      </div>
      <div class="mb-4">
        <label class="block">Course</label>
        <input name="course" value="<?= htmlspecialchars($student['course']) ?>" required class="w-full border px-3 py-2 rounded" />
      </div>
      <div class="mb-4">
        <label class="block">Year Level</label>
        <input type="number" name="year_level" min="1" max="4" value="<?= htmlspecialchars($student['year_level']) ?>" required class="w-full border px-3 py-2 rounded" />
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
