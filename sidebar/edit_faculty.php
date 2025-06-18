<?php
session_start();
require_once '../php/db.php';
require_once '../php/auth.php';
require_role('admin');

$id = $_GET['id'] ?? null;

if (!$id) {
    header('Location: faculties.php');
    exit();
}

// Fetch user and faculty info
$stmt = $pdo->prepare("SELECT f.*, u.username, u.email FROM faculties f JOIN users u ON f.user_id = u.id WHERE f.user_id = ?");
$stmt->execute([$id]);
$faculty = $stmt->fetch();

if (!$faculty) {
    echo "Faculty not found.";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $full_name = $_POST['full_name'];
    $department = $_POST['department'];
    $position = $_POST['position'];

    $pdo->prepare("UPDATE users SET username=?, email=? WHERE id=?")->execute([$username, $email, $id]);
    $pdo->prepare("UPDATE faculties SET full_name=?, department=?, position=? WHERE user_id=?")->execute([$full_name, $department, $position, $id]);

    header('Location: faculties.php');
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Faculty</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="p-8 bg-gray-50">
    <div class="max-w-lg mx-auto bg-white p-6 rounded shadow">
        <h2 class="text-2xl font-bold mb-4">Edit Faculty</h2>
        <form method="POST">
            <input name="username" value="<?= htmlspecialchars($faculty['username']) ?>" class="block w-full p-2 border rounded mb-3" required>
            <input name="email" type="email" value="<?= htmlspecialchars($faculty['email']) ?>" class="block w-full p-2 border rounded mb-3" required>
            <input name="full_name" value="<?= htmlspecialchars($faculty['full_name']) ?>" class="block w-full p-2 border rounded mb-3" required>
            <input name="department" value="<?= htmlspecialchars($faculty['department']) ?>" class="block w-full p-2 border rounded mb-3" required>
            <input name="position" value="<?= htmlspecialchars($faculty['position']) ?>" class="block w-full p-2 border rounded mb-3" required>
            <button class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">Update Faculty</button>
            <a href="students.php" class="ml-2 text-gray-600">Cancel</a>
        </form>
    </div>
</body>
</html>
