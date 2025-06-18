<?php
// subjects.php
session_start();
require_once '../php/db.php';
require_once '../php/auth.php';
require_role('admin');

// Add Subject
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'add') {
    $stmt = $pdo->prepare("INSERT INTO subjects (name) VALUES (:name)");
    $stmt->execute(['name' => $_POST['subject_name']]);
    header("Location: subjects.php");
    exit();
}

// Edit Subject
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'edit') {
    $stmt = $pdo->prepare("UPDATE subjects SET name = :name WHERE id = :id");
    $stmt->execute(['name' => $_POST['subject_name'], 'id' => $_POST['subject_id']]);
    header("Location: subjects.php");
    exit();
}

// Delete Subject
if (isset($_GET['delete'])) {
    $stmt = $pdo->prepare("DELETE FROM subjects WHERE id = :id");
    $stmt->execute(['id' => $_GET['delete']]);
    header("Location: subjects.php");
    exit();
}

$subjects = $pdo->query("SELECT * FROM subjects ORDER BY name ASC")->fetchAll();
?>

<!-- HTML -->
<!DOCTYPE html>
<html>
<head>
    <title>Subjects</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet" />
</head>
<body class="p-6 bg-gray-100">

    <!-- Back to Dashboard Button -->
    <a href="../php/admin_dashboard.php" class="inline-block mb-4 bg-gray-700 text-white px-4 py-2 rounded hover:bg-gray-800">
        ← Back to Dashboard
    </a>

    <h1 class="text-2xl font-bold mb-4">Manage Subjects</h1>

    <form method="POST" class="mb-6">
        <input type="hidden" name="action" value="add">
        <input type="text" name="subject_name" required placeholder="New Subject Name" class="border p-2 rounded w-64">
        <button type="submit" class="ml-2 bg-blue-500 text-white px-4 py-2 rounded">Add</button>
    </form>

    <ul class="space-y-2">
        <?php foreach ($subjects as $sub): ?>
            <li class="bg-white p-4 shadow rounded flex justify-between items-center">
                <form method="POST" class="flex items-center space-x-2">
                    <input type="hidden" name="action" value="edit">
                    <input type="hidden" name="subject_id" value="<?= $sub['id'] ?>">
                    <input type="text" name="subject_name" value="<?= htmlspecialchars($sub['name']) ?>" class="border rounded p-1 w-64">
                    <button class="bg-green-500 text-white px-3 py-1 rounded">Update</button>
                </form>
                <a href="?delete=<?= $sub['id'] ?>" onclick="return confirm('Delete this subject?')" class="text-red-500 hover:underline">Delete</a>
            </li>
        <?php endforeach; ?>
    </ul>

</body>
</html>
