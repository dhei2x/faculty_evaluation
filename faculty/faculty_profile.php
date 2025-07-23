<?php
session_start();
require_once '../php/db.php';

if (!isset($_SESSION['faculty'])) {
    header("Location: ../php/login.php");
    exit;
}

$faculty_id = $_SESSION['faculty']['id'];

$stmt = $pdo->prepare("SELECT * FROM faculties WHERE id = ?");
$stmt->execute([$faculty_id]);
$faculty = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Faculty Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 flex">
    <?php include 'faculty_sidebar.php'; ?>
    <div class="p-6 flex-1">
        <h1 class="text-2xl font-bold mb-4">Profile Information</h1>
        <div class="bg-white p-6 rounded shadow-md w-full max-w-xl">
            <p><strong>Name:</strong> <?= htmlspecialchars($faculty['full_name']) ?></p>
            <p><strong>Faculty ID:</strong> <?= htmlspecialchars($faculty['faculty_id']) ?></p>
            <p><strong>Department:</strong> <?= htmlspecialchars($faculty['department']) ?></p>
            <p><strong>Position:</strong> <?= htmlspecialchars($faculty['position']) ?></p>
        </div>
    </div>
</body>
</html>
