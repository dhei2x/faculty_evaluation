<?php
session_start();
require_once '../php/db.php';
require_once '../php/auth.php';
require_role('admin');

$message = "";
$message_type = "error"; // default type

// Handle Add
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'add') {
    $class_name = trim($_POST['class_name']);
    $academic_year = trim($_POST['academic_year']);

    if (empty($class_name) || empty($academic_year)) {
        $message = "All fields are required.";
    } else {
        $check = $pdo->prepare("SELECT id FROM classes WHERE class_name = :name AND academic_year = :year");
        $check->execute(['name' => $class_name, 'year' => $academic_year]);
        if ($check->fetch()) {
            $message = "Class already exists for that year.";
        } else {
            $stmt = $pdo->prepare("INSERT INTO classes (class_name, academic_year) VALUES (:name, :year)");
            $stmt->execute(['name' => $class_name, 'year' => $academic_year]);
            $message = "Class added successfully.";
            $message_type = "success";
        }
    }
}

// Handle Edit
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'edit') {
    $stmt = $pdo->prepare("UPDATE classes SET class_name = :name, academic_year = :year WHERE id = :id");
    $stmt->execute([
        'name' => $_POST['class_name'],
        'year' => $_POST['academic_year'],
        'id' => $_POST['class_id']
    ]);
    header("Location: classes.php");
    exit();
}

// Handle Delete
if (isset($_GET['delete'])) {
    $stmt = $pdo->prepare("DELETE FROM classes WHERE id = :id");
    $stmt->execute(['id' => $_GET['delete']]);
    header("Location: classes.php");
    exit();
}

// Sorting
$sort_column = isset($_GET['sort']) && in_array($_GET['sort'], ['class_name', 'academic_year']) ? $_GET['sort'] : 'academic_year';
$sort_order = isset($_GET['order']) && $_GET['order'] === 'asc' ? 'ASC' : 'DESC';
$next_order = $sort_order === 'ASC' ? 'desc' : 'asc';

// Fetch classes
$stmt = $pdo->prepare("SELECT * FROM classes ORDER BY $sort_column $sort_order");
$stmt->execute();
$classes = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Manage Classes</title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="flex bg-gray-100 min-h-screen">

  <!-- Sidebar -->
  <?php include '../php/admin_sidebar.php'; ?>

  <!-- Main -->
  <div class="flex-1 p-6">
    <div class="max-w-4xl mx-auto bg-white p-6 rounded shadow">
      <h1 class="text-2xl font-bold mb-4">Manage Classes</h1>

      <?php if ($message): ?>
        <div class="mb-4 text-<?= $message_type === 'success' ? 'green' : 'red' ?>-600 font-semibold">
          <?= htmlspecialchars($message) ?>
        </div>
      <?php endif; ?>

      <!-- Add Form -->
      <form method="POST" class="mb-6">
        <input type="hidden" name="action" value="add">
        <label class="block text-sm font-medium">Class Name</label>
        <input type="text" name="class_name" required class="mt-1 w-full border rounded p-2">

        <label class="block text-sm font-medium mt-3">Academic Year</label>
        <input type="text" name="academic_year" required placeholder="e.g. 2024-2025" class="mt-1 w-full border rounded p-2">

        <button type="submit" class="mt-4 bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Add Class</button>
      </form>

      <!-- Table -->
      <h2 class="text-xl font-semibold mb-2">Existing Classes</h2>
      <table class="w-full text-left border border-gray-300">
        <thead>
          <tr class="bg-gray-100">
            <th class="p-2 border-b">
              <a href="?sort=class_name&order=<?= $sort_column === 'class_name' ? $next_order : 'asc' ?>" class="hover:underline">
                Class Name <?= $sort_column === 'class_name' ? ($sort_order === 'ASC' ? '↑' : '↓') : '' ?>
              </a>
            </th>
            <th class="p-2 border-b">
              <a href="?sort=academic_year&order=<?= $sort_column === 'academic_year' ? $next_order : 'asc' ?>" class="hover:underline">
                Academic Year <?= $sort_column === 'academic_year' ? ($sort_order === 'ASC' ? '↑' : '↓') : '' ?>
              </a>
            </th>
            <th class="p-2 border-b">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($classes as $class): ?>
            <tr class="border-t">
              <td class="p-2">
                <form method="POST" class="flex space-x-2 items-center">
                  <input type="hidden" name="action" value="edit">
                  <input type="hidden" name="class_id" value="<?= $class['id'] ?>">
                  <input type="text" name="class_name" value="<?= htmlspecialchars($class['class_name']) ?>" class="border rounded p-1 w-full">
              </td>
              <td class="p-2">
                  <input type="text" name="academic_year" value="<?= htmlspecialchars($class['academic_year']) ?>" class="border rounded p-1 w-full">
              </td>
              <td class="p-2 flex space-x-2">
                  <button type="submit" class="bg-green-500 text-white px-2 py-1 rounded hover:bg-green-600">Update</button>
                </form>
                <a href="?delete=<?= $class['id'] ?>" class="text-red-500 hover:underline" onclick="return confirm('Are you sure you want to delete this class?')">Delete</a>
              </td>
            </tr>
          <?php endforeach; ?>
          <?php if (empty($classes)): ?>
            <tr><td colspan="3" class="p-2 text-center text-gray-500">No classes available.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</body>
</html>
