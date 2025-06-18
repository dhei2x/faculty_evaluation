<?php
session_start();
require_once '../php/db.php';
require_once '../php/auth.php';
require_role('admin');

// Fetch average scores per faculty per criteria
$sql = "
SELECT
  f.full_name AS faculty_name,
  ec.name AS criteria_name,
  ROUND(AVG(er.rating), 2) AS avg_rating,
  COUNT(er.id) AS total_ratings
FROM evaluation_report er
JOIN faculties f ON er.faculty_id = f.id
JOIN questions q ON er.question_id = q.id
JOIN evaluation_criteria ec ON q.criteria_id = ec.id
GROUP BY f.id, ec.id
ORDER BY f.full_name, ec.name
";
$rows = $pdo->query($sql)->fetchAll();

// Group by faculty
$grouped = [];
foreach ($rows as $row) {
    $grouped[$row['faculty_name']][] = $row;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Evaluation Reports</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 p-6">
<div class="max-w-5xl mx-auto bg-white p-6 rounded shadow">

    <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-bold">Evaluation Reports</h1>
        <a href="../php/admin_dashboard.php" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold px-4 py-2 rounded">
            ← Back to Dashboard
        </a>
    </div>

    <?php if (empty($grouped)): ?>
        <p>No evaluation data found.</p>
    <?php else: ?>
        <?php foreach ($grouped as $faculty => $entries): ?>
            <div class="mb-6">
                <h2 class="text-xl font-semibold mb-2"><?= htmlspecialchars($faculty) ?></h2>
                <table class="w-full table-auto border">
                    <thead>
                        <tr class="bg-gray-200">
                            <th class="border px-4 py-2 text-left">Criteria</th>
                            <th class="border px-4 py-2 text-left">Average Rating</th>
                            <th class="border px-4 py-2 text-left"># Ratings</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($entries as $entry): ?>
                            <tr>
                                <td class="border px-4 py-2"><?= htmlspecialchars($entry['criteria_name']) ?></td>
                                <td class="border px-4 py-2"><?= $entry['avg_rating'] ?></td>
                                <td class="border px-4 py-2"><?= $entry['total_ratings'] ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
</body>
</html>
