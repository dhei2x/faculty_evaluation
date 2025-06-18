<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
require_once '../php/db.php';
require_once '../php/auth.php';
require_role('admin');

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($_POST['action'] === 'add') {
        if (isset($_POST['is_active'])) {
            $pdo->query("UPDATE academic_years SET is_active = 0");
        }

        $stmt = $pdo->prepare("INSERT INTO academic_years (year, semester, is_active, created_at) VALUES (:year, :semester, :is_active, NOW())");
        $stmt->execute([
            'year' => $_POST['year'],
            'semester' => $_POST['semester'],
            'is_active' => isset($_POST['is_active']) ? 1 : 0
        ]);
    } elseif ($_POST['action'] === 'edit') {
        if (isset($_POST['is_active'])) {
            $pdo->query("UPDATE academic_years SET is_active = 0");
        }

        $stmt = $pdo->prepare("UPDATE academic_years SET year = :year, semester = :semester, is_active = :is_active WHERE id = :id");
        $stmt->execute([
            'year' => $_POST['year'],
            'semester' => $_POST['semester'],
            'is_active' => isset($_POST['is_active']) ? 1 : 0,
            'id' => $_POST['id']
        ]);
    } elseif ($_POST['action'] === 'delete') {
        $stmt = $pdo->prepare("DELETE FROM academic_years WHERE id = :id");
        $stmt->execute(['id' => $_POST['id']]);
    }
    header("Location: academic_year.php");
    exit();
}

// Fetch all academic years
$years = $pdo->query("SELECT * FROM academic_years ORDER BY year DESC, FIELD(semester, '2nd', '1st', 'Summer')")->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Academic Year Management</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 font-sans">

<div class="flex">
    <!-- Sidebar -->
    <?php include '../php/admin_sidebar.php';  ?>

    <!-- Main Content -->
    <div class="flex-1 p-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">Academic Year Settings</h1>
        </div>

        <!-- Add Form -->
        <form method="POST" class="space-x-2 mb-6 bg-white p-4 rounded shadow">
            <input type="hidden" name="action" value="add">
            <input type="text" name="year" required placeholder="e.g. 2024-2025" class="border p-2 rounded w-48">
            <select name="semester" class="border p-2 rounded w-32">
                <option value="1st">1st</option>
                <option value="2nd">2nd</option>
                <option value="Summer">Summer</option>
            </select>
            <label class="inline-flex items-center">
                <input type="checkbox" name="is_active" class="form-checkbox">
                <span class="ml-2">Set as Active</span>
            </label>
            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Add</button>
        </form>

        <!-- Academic Year List -->
        <ul class="space-y-3">
        <?php foreach ($years as $y): ?>
            <li class="bg-white shadow p-4 rounded flex justify-between items-center <?= $y['is_active'] ? 'border-l-4 border-green-500' : '' ?>">
                <div class="flex items-center space-x-4 w-full">
                    <?php if ($y['is_active']): ?>
                        <span class="bg-green-500 text-white text-xs px-2 py-1 rounded">Active</span>
                    <?php endif; ?>

                    <?php if (!$y['is_active']): ?>
                        <form method="POST" class="flex space-x-2 items-center w-full">
                            <input type="hidden" name="action" value="edit">
                            <input type="hidden" name="id" value="<?= $y['id'] ?>">
                            <input type="text" name="year" value="<?= htmlspecialchars($y['year']) ?>" class="border p-1 rounded w-40">
                            <select name="semester" class="border p-1 rounded w-28">
                                <option value="1st" <?= $y['semester'] === '1st' ? 'selected' : '' ?>>1st</option>
                                <option value="2nd" <?= $y['semester'] === '2nd' ? 'selected' : '' ?>>2nd</option>
                                <option value="Summer" <?= $y['semester'] === 'Summer' ? 'selected' : '' ?>>Summer</option>
                            </select>
                            <label class="flex items-center space-x-1">
                                <input type="checkbox" name="is_active" <?= $y['is_active'] ? 'checked' : '' ?>>
                                <span>Active</span>
                            </label>
                            <button class="bg-green-500 text-white px-3 py-1 rounded">Update</button>
                        </form>
                    <?php else: ?>
                        <div class="text-gray-700"><?= htmlspecialchars($y['year']) ?> - <?= $y['semester'] ?></div>
                    <?php endif; ?>
                </div>

                <!-- Delete Button -->
                <form method="POST" onsubmit="return confirm('Delete this academic year?')">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="id" value="<?= $y['id'] ?>">
                    <button class="bg-red-600 text-white px-3 py-1 rounded ml-4">Delete</button>
                </form>
            </li>
        <?php endforeach; ?>
        </ul>
    </div>
</div>

</body>
</html>
