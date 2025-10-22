<?php
session_start();
require_once '../php/db.php';

if ($_SESSION['role'] !== 'faculty' || empty($_SESSION['faculty_id'])) {
    header("Location: ../php/login.php");
    exit;
}

// Fetch faculty numeric ID from database using faculty_id
$stmt = $pdo->prepare("SELECT id FROM faculties WHERE faculty_id = ?");
$stmt->execute([$_SESSION['faculty_id']]);
$facultyRecord = $stmt->fetch(PDO::FETCH_ASSOC);
$faculty_id = $facultyRecord['id'] ?? 0;
$faculty_name = $_SESSION['faculty_name'] ?? '';

// ✅ Fetch summary
$stmt = $pdo->prepare("
    SELECT 
        ec.name AS criteria, 
        ROUND((AVG(er.rating) / 5) * 100, 2) AS average, 
        COUNT(DISTINCT er.comment) AS comments
    FROM evaluation_report er
    JOIN questions q ON er.question_id = q.id
    JOIN evaluation_criteria ec ON q.criteria_id = ec.id
    WHERE er.faculty_id = ?
    GROUP BY ec.id
");
$stmt->execute([$faculty_id]);
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Evaluation Summary</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
<style>
        body {
            position: relative;
            background-color: #f3f4f6;
        }
        /* ✅ Background logo behind content */
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
        .content {
            position: relative;
            z-index: 1; /* ensures it's above the background logo */
            background-color: #ffffff;
            border-radius: 0.75rem;
            padding: 2rem;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            width: 100%;
        }
        th {
            background-color: #bfdbfe;
        }
        th, td {
            border: 1px solid #d1d5db;
            padding: 12px 16px;
            text-align: left;
        }
        tr:hover {
            background-color: #f9fafb;
        }
    </style>
</head>

<body class="bg-gray-100 flex min-h-screen">
    <?php include 'faculty_sidebar.php'; ?>

    <!-- ✅ Main content area beside sidebar -->
    <main class="flex-1 p-8 content">
        <h1 class="text-3xl font-bold mb-6 text-blue-800">Evaluation Summary</h1>

        <?php if (empty($data)): ?>
            <p class="text-gray-600">No summary data available.</p>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white rounded shadow border">
                    <thead>
                        <tr>
                            <th class="w-1/2">Criteria</th>
                            <th class="w-1/4">Average (%)</th>
                            <th class="w-1/4">Number of Comments</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($data as $row): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['criteria']) ?></td>
                                <td class="text-blue-700 font-semibold text-center"><?= $row['average'] ?>%</td>
                                <td class="text-center"><?= $row['comments'] ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </main>
</body>
</html>
