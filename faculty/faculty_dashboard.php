<?php
session_start();
require_once '../php/db.php';
require_once '../php/auth.php';
require_role('faculty');

$faculty_id = $_SESSION['faculty']['id'];

$sql = "
SELECT ec.name AS criteria_name, ROUND(AVG(er.rating), 2) AS avg_rating, COUNT(er.id) AS total
FROM evaluation_report er
JOIN questions q ON er.question_id = q.id
JOIN evaluation_criteria ec ON q.criteria_id = ec.id
WHERE er.faculty_id = ?
GROUP BY ec.id
";
$stmt = $pdo->prepare($sql);
$stmt->execute([$faculty_id]);
$ratings = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Evaluation Summary</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 p-6">
<div class="max-w-4xl mx-auto bg-white p-6 rounded shadow">
    <h1 class="text-2xl font-bold mb-4">My Evaluation Summary</h1>

    <?php if (empty($ratings)): ?>
        <p>No evaluation data available yet.</p>
    <?php else: ?>
        <table class="w-full table-auto border">
            <thead>
                <tr class="bg-gray-200">
                    <th class="border px-4 py-2 text-left">Criteria</th>
                    <th class="border px-4 py-2 text-left">Average Rating</th>
                    <th class="border px-4 py-2 text-left"># Responses</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($ratings as $r): ?>
                    <tr>
                        <td class="border px-4 py-2"><?= htmlspecialchars($r['criteria_name']) ?></td>
                        <td class="border px-4 py-2"><?= $r['avg_rating'] ?></td>
                        <td class="border px-4 py-2"><?= $r['total'] ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
</body>
</html>
