<?php
session_start();
require_once '../php/db.php';


if ($_SESSION['role'] !== 'faculty' || empty($_SESSION['faculty_id'])) {
    header("Location: ../php/login.php");
    exit;
}
$faculty_id   = $_SESSION['faculty_id'];
$faculty_name = $_SESSION['faculty_name'];

$stmt = $pdo->prepare("SELECT * FROM faculties WHERE id = ?");
$stmt->execute([$faculty_id]);
$faculty = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Faculty Profile</title>
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
