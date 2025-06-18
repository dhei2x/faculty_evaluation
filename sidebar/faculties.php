<?php
session_start();
require_once '../php/db.php';
require_once '../php/auth.php';
require_role('admin');

// Fetch faculties
$faculties = $pdo->query("SELECT * FROM faculties")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Manage Faculties</title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="flex bg-gray-100 min-h-screen">

  <!-- Sidebar -->
  <?php include '../php/admin_sidebar.php'; ?>

  <!-- Main Content -->
  <div class="flex-1 p-6">
    <h1 class="text-3xl font-bold mb-6 text-gray-800">Manage Faculties</h1>

    <a href="add_faculty.php" class="mb-4 inline-block bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
      + Add Faculty
    </a>

    <div class="bg-white p-4 rounded shadow mt-4 overflow-x-auto">
      <table class="min-w-full table-auto border border-gray-200">
        <thead class="bg-gray-100">
          <tr>
            <th class="px-4 py-2 border">User ID</th>
            <th class="px-4 py-2 border">Full Name</th>
            <th class="px-4 py-2 border">Department</th>
            <th class="px-4 py-2 border">Position</th>
            <th class="px-4 py-2 border">Created At</th>
            <th class="px-4 py-2 border">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($faculties as $faculty): ?>
            <tr class="text-center hover:bg-gray-50">
              <td class="border px-4 py-2"><?= htmlspecialchars($faculty['user_id']) ?></td>
              <td class="border px-4 py-2"><?= htmlspecialchars($faculty['full_name']) ?></td>
              <td class="border px-4 py-2"><?= htmlspecialchars($faculty['department']) ?></td>
              <td class="border px-4 py-2"><?= htmlspecialchars($faculty['position']) ?></td>
              <td class="border px-4 py-2"><?= htmlspecialchars($faculty['created_at']) ?></td>
              <td class="border px-4 py-2 space-x-2">
                <a href="edit_faculty.php?user_id=<?= $faculty['user_id'] ?>" class="text-blue-600 hover:underline">Edit</a>
                <a href="delete_faculty.php?user_id=<?= $faculty['user_id'] ?>" class="text-red-600 hover:underline" onclick="return confirm('Delete this faculty?');">Delete</a>
              </td>
            </tr>
          <?php endforeach; ?>
          <?php if (empty($faculties)): ?>
            <tr>
              <td colspan="6" class="text-center py-6 text-gray-500">No faculty members found.</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</body>
</html>
