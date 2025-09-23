<?php
session_start();
require_once '../php/db.php';
require_once '../php/auth.php';
require_role('admin');

// Handle add/edit/delete
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    if ($_POST['action'] === 'add') {
        $stmt = $pdo->prepare("INSERT INTO evaluation_criteria (name) VALUES (:name)");
        $stmt->execute(['name' => $name]);
    } elseif ($_POST['action'] === 'edit') {
        $stmt = $pdo->prepare("UPDATE evaluation_criteria SET name = :name WHERE id = :id");
        $stmt->execute(['name' => $name, 'id' => $_POST['id']]);
    } elseif ($_POST['action'] === 'delete') {
        $stmt = $pdo->prepare("DELETE FROM evaluation_criteria WHERE id = :id");
        $stmt->execute(['id' => $_POST['id']]);
    }

    // ✅ FIXED: Redirect to the correct filename
    header("Location: evaluation_criteria.php");
    exit();
}

$criteria = $pdo->query("SELECT * FROM evaluation_criteria ORDER BY id")->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Evaluation Criteria</title>
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
<body class="bg-gray-100 p-6">
<div class="max-w-3xl mx-auto bg-white p-6 rounded shadow">

    <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-bold">Evaluation Criteria</h1>
        <a href="../php/admin_dashboard.php" class="bg-blue-300 hover:bg-gray-400 text-gray-800 font-semibold px-4 py-2 rounded">
            ← Back to Dashboard
        </a>
    </div>

    <!-- Add Form -->
    <form method="POST" class="mb-6">
        <input type="hidden" name="action" value="add">
        <input type="text" name="name" required class="border p-2 rounded w-full" placeholder="Enter new criteria name">
        <button class="bg-blue-600 text-white mt-2 px-4 py-2 rounded">Add</button>
    </form>

    <!-- List -->
    <ul>
        <?php foreach ($criteria as $c): ?>
            <li class="border-b py-2 flex justify-between items-center">
                <form method="POST" class="flex gap-2 w-full">
                    <input type="hidden" name="action" value="edit">
                    <input type="hidden" name="id" value="<?= $c['id'] ?>">
                    <input type="text" name="name" value="<?= htmlspecialchars($c['name']) ?>" class="border rounded p-2 flex-grow">
                    <button class="bg-green-600 text-white px-3 py-1 rounded">Save</button>
                </form>
                <form method="POST" onsubmit="return confirm('Delete this criteria?')">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="id" value="<?= $c['id'] ?>">
                    <button class="bg-red-600 text-white px-3 py-1 rounded ml-2">Delete</button>
                </form>
            </li>
        <?php endforeach; ?>
    </ul>
</div>
</body>
</html>
