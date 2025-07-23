<?php
session_start();
require_once '../php/db.php';

if (!isset($_SESSION['faculty'])) {
    session_destroy();
    header("Location: ../php/login.php");
    exit();
}

$faculty_id = $_SESSION['faculty']['id'];

$stmt = $pdo->prepare("
    SELECT ay.year, ay.semester, ec.name AS criteria, q.text AS question, er.rating, er.comment
    FROM evaluation_report er
    JOIN questions q ON er.question_id = q.id
    JOIN evaluation_criteria ec ON q.criteria_id = ec.id
    JOIN academic_years ay ON er.academic_year_id = ay.id
    WHERE er.faculty_id = ?
    ORDER BY ay.year DESC, ay.semester, er.id DESC
");
$stmt->execute([$faculty_id]);
$evaluations = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Evaluations</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
<div class="flex">
    <?php include 'faculty_sidebar.php'; ?>

    <div class="flex-1 p-6">
        <h1 class="text-2xl font-bold mb-6">My Evaluation Records</h1>

        <?php if (empty($evaluations)): ?>
            <p class="text-gray-500">No evaluations found.</p>
        <?php else: ?>
            <div class="overflow-auto">
                <table class="min-w-full table-auto bg-white shadow rounded">
                    <thead class="bg-green-600 text-white">
                        <tr>
                            <th class="px-4 py-2">Year</th>
                            <th class="px-4 py-2">Semester</th>
                            <th class="px-4 py-2">Criteria</th>
                            <th class="px-4 py-2">Question</th>
                            <th class="px-4 py-2">Rating</th>
                            <th class="px-4 py-2">Comment</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($evaluations as $e): ?>
                        <tr class="border-b hover:bg-gray-100">
                            <td class="px-4 py-2"><?= htmlspecialchars($e['year']) ?></td>
                            <td class="px-4 py-2"><?= htmlspecialchars($e['semester']) ?></td>
                            <td class="px-4 py-2"><?= htmlspecialchars($e['criteria']) ?></td>
                            <td class="px-4 py-2"><?= htmlspecialchars($e['question']) ?></td>
                            <td class="px-4 py-2 text-center"><?= $e['rating'] ?></td>
                            <td class="px-4 py-2"><?= htmlspecialchars($e['comment']) ?></td>
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
