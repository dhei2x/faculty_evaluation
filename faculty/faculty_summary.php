<?php
session_start();
require_once '../php/db.php';

if ($_SESSION['role'] !== 'faculty' || empty($_SESSION['faculty_id'])) {
    header("Location: ../php/login.php");
    exit;
}

$faculty_id   = $_SESSION['faculty_id'];
$faculty_name = $_SESSION['faculty_name'] ?? '';

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

<body class="flex bg-gray-100 min-h-screen">
    <?php include 'faculty_sidebar.php'; ?>

    <div class="ml-64 p-6 w-full">
        <div class="content">
            <h1 class="text-2xl font-bold mb-4 text-blue-800">Evaluation Summary</h1>

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
                                    <td><?= $row['average'] ?>%</td>
                                    <td><?= $row['comments'] ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
