<?php
session_start();
require_once '../php/db.php';

if ($_SESSION['role'] !== 'faculty' || empty($_SESSION['faculty_id'])) {
    header("Location: ../php/login.php");
    exit;
}
$faculty_id   = $_SESSION['faculty_id'];
$faculty_name = $_SESSION['faculty_name'];

$stmt = $pdo->prepare("
    SELECT ec.name AS criteria, ROUND(AVG(er.rating), 2) AS average, COUNT(DISTINCT er.comment) AS comments
    FROM evaluation_report er
    JOIN questions q ON er.question_id = q.id
    JOIN evaluation_criteria ec ON q.criteria_id = ec.id
    WHERE er.faculty_id = ?
    GROUP BY ec.id
");
$stmt->execute([$faculty_id]);
$data = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Evaluation Summary</title>
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
        <h1 class="text-2xl font-bold mb-4">Evaluation Summary</h1>

        <?php if (empty($data)): ?>
            <p>No summary data available.</p>
        <?php else: ?>
            <table class="w-full table-auto bg-white shadow rounded border">
                <thead class="bg-blue-200">
                    <tr>
                        <th class="px-4 py-2 border">Criteria</th>
                        <th class="px-4 py-2 border">Average Rating</th>
                        <th class="px-4 py-2 border">Number of Comments</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($data as $row): ?>
                        <tr>
                            <td class="border px-4 py-2"><?= htmlspecialchars($row['criteria']) ?></td>
                            <td class="border px-4 py-2"><?= $row['average'] ?></td>
                            <td class="border px-4 py-2"><?= $row['comments'] ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</body>
</html>
