<?php
session_start();
include '../php/db.php';
include '../php/auth.php';
require_role('admin');

// Fetch all students
$students = $pdo->query("SELECT * FROM students")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Manage Students</title>
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
<body class="flex bg-gray-50 min-h-screen">
 <?php if (isset($_GET['success'])): ?>
<div id="popup" 
     class="fixed top-5 left-1/2 transform -translate-x-1/2 bg-green-500 text-white px-6 py-3 rounded shadow-lg">
  ✅ Student added successfully!
</div>
<script>
  setTimeout(() => document.getElementById('popup').remove(), 3000);
</script>
<?php endif; ?>

<?php if (isset($_GET['deleted'])): ?>
<div id="popup" 
     class="fixed top-5 left-1/2 transform -translate-x-1/2 bg-red-500 text-white px-6 py-3 rounded shadow-lg">
  🗑️ Student deleted successfully!
</div>
<script>
  setTimeout(() => document.getElementById('popup').remove(), 3000);
</script>
<?php endif; ?>



  <!-- Sidebar -->
  <?php include '../php/admin_sidebar.php'; ?>

  <!-- Main Content -->
  <div class="flex-1">
    <div class="p-6">
      <h2 class="text-2xl font-bold mb-4 text-gray-800">Manage Students</h2>
      <a href="add_student.php" class="mb-4 inline-block bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Add Student</a>

      <table class="w-full bg-white border rounded shadow mt-4">
        <thead class="bg-gray-100 text-left">
          <tr>
            <th class="p-2 border">ID</th>
            <th class="p-2 border">Student ID</th>
            <th class="p-2 border">Full Name</th>
            <th class="p-2 border">Course</th>
            <th class="p-2 border">Year Level</th>
            <th class="p-2 border">Section</th>
            <th class="p-2 border">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($students as $s): ?>
          <tr class="hover:bg-gray-50">
            <td class="p-2 border"><?= $s['id'] ?></td>
            <td class="p-2 border"><?= htmlspecialchars($s['student_id']) ?></td>
            <td class="p-2 border"><?= htmlspecialchars($s['full_name']) ?></td>
            <td class="p-2 border"><?= htmlspecialchars($s['course']) ?></td>
            <td class="p-2 border"><?= htmlspecialchars($s['year_level']) ?></td>
            <td class="p-2 border"><?= htmlspecialchars($s['section']) ?></td>
            <td class="p-2 border">
              <a href="edit_student.php?id=<?= $s['id'] ?>" class="text-blue-600 hover:underline">Edit</a>
              <a href="delete_student.php?id=<?= $s['id'] ?>" class="text-red-600 hover:underline ml-2" onclick="return confirm('Delete this student?');">Delete</a>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</body>
</html>
