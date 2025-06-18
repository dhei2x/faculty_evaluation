<?php
session_start();
include 'php/auth.php';
require_role('faculty');
// Restrict access if not logged in or not a faculty member
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'faculties') {
    header('Location: php/login.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Faculty Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-green-50 min-h-screen flex items-center justify-center">
    <div class="bg-white shadow-lg rounded-lg p-8 max-w-md w-full">
        <h2 class="text-2xl font-bold mb-4 text-green-600">Welcome, <?php echo htmlspecialchars($_SESSION['user']); ?>!</h2>
        <p class="text-gray-700">This is your faculty dashboard.</p>
        <p class="text-sm mt-2 text-gray-500">Email: <?php echo htmlspecialchars($_SESSION['email']); ?></p>

        <a href="logout.php" class="mt-6 inline-block text-sm text-red-500 hover:underline">Logout</a>
    </div>
</body>
</html>

