<?php
session_start();
require_once '../php/db.php';

if (!isset($_SESSION['faculty'])) {
    header("Location: ../php/login.php");
    exit;
}

$faculty_id = $_SESSION['faculty']['id'];

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
</head>
<body class="bg-gray-100 flex">
    <?php include 'faculty_sidebar.php'; ?>
    <div class="p-6 flex-1">
        <h1 class="text-2xl font-bold mb-4">Evaluation Summary</h1>

        <?php if (empty($data)): ?>
            <p>No summary data available.</p>
        <?php else: ?>
            <table class="w-full table-auto bg-white shadow rounded border">
                <thead class="bg-gray-200">
                    <tr>
                        <th class="px-4 py-2 border">Criteria</th>
                        <th class="px-4 py-2 border">Average Rating</th>
                        <th class="px-4 py-2 border"># of Comments</th>
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
