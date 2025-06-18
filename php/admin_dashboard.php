<?php
session_start();
require_once 'db.php';
require_once 'auth.php';

// Restrict access to admins only
require_role('admin');

// Fetch counts for dashboard metrics
try {
    $studentCount = $pdo->query("SELECT COUNT(*) FROM students")->fetchColumn();
    $facultyCount = $pdo->query("SELECT COUNT(*) FROM faculties")->fetchColumn();
    $classCount = $pdo->query("SELECT COUNT(*) FROM classes")->fetchColumn();
    $subjectCount = $pdo->query("SELECT COUNT(*) FROM subjects")->fetchColumn();
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard - Faculty Evaluation System</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 font-sans">

<div class="flex">
    <!-- Sidebar -->
    <?php include 'admin_sidebar.php'; ?>

    <!-- Main Content -->
    <div class="flex-1 p-6">
        <h1 class="text-3xl font-bold mb-6">Welcome, Admin</h1>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="bg-white p-4 rounded-lg shadow">
                <h2 class="text-xl font-semibold">Students</h2>
                <p class="text-3xl font-bold text-blue-500"><?= $studentCount ?></p>
            </div>
            <div class="bg-white p-4 rounded-lg shadow">
                <h2 class="text-xl font-semibold">Faculties</h2>
                <p class="text-3xl font-bold text-green-500"><?= $facultyCount ?></p>
            </div>
            <div class="bg-white p-4 rounded-lg shadow">
                <h2 class="text-xl font-semibold">Classes</h2>
                <p class="text-3xl font-bold text-yellow-500"><?= $classCount ?></p>
            </div>
            <div class="bg-white p-4 rounded-lg shadow">
                <h2 class="text-xl font-semibold">Subjects</h2>
                <p class="text-3xl font-bold text-purple-500"><?= $subjectCount ?></p>
            </div>
        </div>
    </div>
</div>

</body>
</html>
