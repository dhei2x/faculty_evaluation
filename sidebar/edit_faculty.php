<?php
session_start();
require_once '../php/db.php';
require_once '../php/auth.php';
require_role('admin');

$faculty_id = $_GET['id'] ?? null;

if (!$faculty_id) {
    header('Location: faculties.php');
    exit();
}

// Fetch faculty data
$stmt = $pdo->prepare("
    SELECT f.*, u.username, u.email 
    FROM faculties f 
    JOIN users u ON f.faculty_id = u.username 
    WHERE f.faculty_id = ?
");
$stmt->execute([$faculty_id]);
$faculty = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$faculty) {
    echo "<p style='color:red; text-align:center;'>Faculty with ID '$faculty_id' not found.</p>";
    exit();
}

// Update on form submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username   = $_POST['username'];
    $email      = $_POST['email'];
    $full_name  = $_POST['full_name'];
    $department = $_POST['department'];
    $position   = $_POST['position'];

    try {
        // Update users table
        $pdo->prepare("UPDATE users SET username = ?, email = ? WHERE username = ?")
            ->execute([$username, $email, $faculty_id]);

        // Update faculties table
        $pdo->prepare("UPDATE faculties SET faculty_id = ?, full_name = ?, department = ?, position = ? WHERE faculty_id = ?")
            ->execute([$username, $full_name, $department, $position, $faculty_id]);

        header("Location: faculties.php");
        exit();
    } catch (PDOException $e) {
        echo "<div style='color:red;'>Error: " . $e->getMessage() . "</div>";
    }
}
?>


<!DOCTYPE html>
<html>
<head>
    <title>Edit Faculty</title>
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
            <a href="faculties.php" class="ml-2 text-gray-600">Cancel</a>
        </form>
    </div>
</body>
</html>
