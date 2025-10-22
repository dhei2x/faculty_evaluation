<?php
session_start();
require_once '../php/db.php';

// ✅ Ensure only logged-in faculty can access
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'faculty' || empty($_SESSION['faculty_id'])) {
    header("Location: ../php/login.php");
    exit;
}

$faculty_id   = $_SESSION['faculty_id'];
$faculty_name = $_SESSION['faculty_name'] ?? '';

// ✅ Fetch faculty info using correct column name
$stmt = $pdo->prepare("SELECT * FROM faculties WHERE faculty_id = ?");
$stmt->execute([$faculty_id]);
$faculty = $stmt->fetch(PDO::FETCH_ASSOC);

// ✅ Prevent warnings if not found
if (!$faculty) {
    $faculty = [
        'first_name' => '',
        'middle_name' => '',
        'last_name' => '',
        'faculty_id' => '',
        'department' => '',
        'position' => '',
        'created_at' => ''
    ];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Faculty Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        body {
            position: relative;
            background-color: #f3f4f6;
        }

        /* ✅ Watermark logo */
        body::before {
            content: "";
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: url('../php/logo.png') no-repeat center center;
            background-size: 900px 900px;
            opacity: 0.08;
            pointer-events: none;
            z-index: 0;
        }

        .content {
            position: relative;
            z-index: 1;
            background-color: #ffffff;
            border-radius: 0.5rem;
            padding: 1.5rem;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
            width: 100%;
        }
    </style>
</head>
<body class="flex bg-gray-100 min-h-screen">

    <?php include 'faculty_sidebar.php'; ?>

    <div class="ml-64 p-6 flex-1">
        <div class="content">
            <h1 class="text-2xl font-bold mb-4 text-blue-800">Faculty Profile Information</h1>
            <div class="bg-gray-50 p-5 rounded-lg">
                <p><strong>Name:</strong> <?= htmlspecialchars(trim($faculty['first_name'] . ' ' . $faculty['middle_name'] . ' ' . $faculty['last_name'])) ?></p>
                <p><strong>Faculty ID:</strong> <?= htmlspecialchars($faculty['faculty_id']) ?></p>
                <p><strong>Department:</strong> <?= htmlspecialchars($faculty['department']) ?></p>
                <p><strong>Position:</strong> <?= htmlspecialchars($faculty['position']) ?></p>
                <p><strong>Created At:</strong> <?= htmlspecialchars($faculty['created_at']) ?></p>
            </div>
        </div>
    </div>

</body>
</html>
