<?php 
session_start();
require_once '../php/db.php';
require_once '../php/auth.php';
require_role('admin');

$search = trim($_GET['search'] ?? '');
$query = "SELECT * FROM students";
$params = [];

if ($search !== '') {
  $query .= " WHERE student_id LIKE ? OR full_name LIKE ? OR course LIKE ?";
  $params = ["%$search%", "%$search%", "%$search%"];
}

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$students = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
      background-color: #f3f4f6;
      min-height: 100vh;
      display: flex;
    }

    /* ✅ Logo watermark (behind everything) */
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

    /* ✅ Content panel beside sidebar */
    .content-wrapper {
      position: relative;
      z-index: 1;
      background: white;
      margin: 2rem;
      padding: 2rem;
      border-radius: 0.75rem;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
      width: 100%;
    }
  </style>

  <script>
    function toggleDropdown(id) {
      document.querySelectorAll('.dropdown-menu').forEach(menu => {
        if (menu.id !== id) menu.classList.add('hidden');
      });
      document.getElementById(id).classList.toggle('hidden');
    }

    window.addEventListener('click', e => {
      if (!e.target.closest('.dropdown')) {
        document.querySelectorAll('.dropdown-menu').forEach(m => m.classList.add('hidden'));
      }
    });
  </script>
</head>

<body class="bg-gray-100 min-h-screen">
  <?php include '../php/admin_sidebar.php'; ?>

  <div class="flex-1 flex justify-center items-start">
    <div class="content-wrapper">
      <div class="flex items-center justify-between mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Manage Students</h1>

        <!-- 🔍 Search Bar -->
        <form method="get" class="flex items-center space-x-2">
          <input type="text" name="search" placeholder="Search..." 
                 value="<?= htmlspecialchars($search) ?>"
                 class="border border-gray-300 rounded px-3 py-2 focus:ring-2 focus:ring-blue-400 focus:outline-none">
          <button class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Search</button>
        </form>
      </div>

      <a href="add_student.php" 
         class="mb-4 inline-block bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
         + Add Student
      </a>

      <div class="bg-white p-4 rounded shadow mt-4 overflow-x-auto border border-gray-200">
        <table class="min-w-full table-auto border border-gray-200">
          <thead class="bg-gray-100">
            <tr>
              <th class="px-4 py-2 border">Student ID</th>
              <th class="px-4 py-2 border">Full Name</th>
              <th class="px-4 py-2 border">Course</th>
              <th class="px-4 py-2 border">Year Level</th>
              <th class="px-4 py-2 border">Section</th>
              <th class="px-4 py-2 border">Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($students as $i => $student): ?>
              <tr class="text-center hover:bg-gray-50">
                <td class="border px-4 py-2"><?= htmlspecialchars($student['student_id']) ?></td>
                <td class="border px-4 py-2"><?= htmlspecialchars($student['full_name']) ?></td>
                <td class="border px-4 py-2"><?= htmlspecialchars($student['course']) ?></td>
                <td class="border px-4 py-2"><?= htmlspecialchars($student['year_level']) ?></td>
                <td class="border px-4 py-2"><?= htmlspecialchars($student['section']) ?></td>
                <td class="border px-4 py-2">
                  <div class="relative dropdown inline-block text-left">
                    <button onclick="toggleDropdown('menu<?= $i ?>')" 
                            class="px-3 py-1 bg-gray-200 rounded hover:bg-gray-300">⋮</button>
                    <div id="menu<?= $i ?>" 
                         class="dropdown-menu hidden absolute right-0 mt-2 w-32 bg-white border border-gray-200 rounded shadow-md z-10">
                      <a href="edit_student.php?id=<?= urlencode($student['id']) ?>" 
                         class="block px-4 py-2 text-sm text-blue-600 hover:bg-gray-100">Edit</a>
                      <a href="delete_student.php?id=<?= urlencode($student['id']) ?>" 
                         class="block px-4 py-2 text-sm text-red-600 hover:bg-gray-100"
                         onclick="return confirm('Are you sure you want to delete this student?')">Delete</a>
                    </div>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>

            <?php if (empty($students)): ?>
              <tr>
                <td colspan="6" class="text-center py-6 text-gray-500">No students found.</td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</body>
</html>
