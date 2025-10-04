<?php
session_start();
require_once '../php/db.php';

// Faculty session check
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'faculty' || empty($_SESSION['faculty_id'])) {
    header("Location: ../php/login.php");
    exit;
}

$faculty_id   = $_SESSION['faculty_id'];
$faculty_name = $_SESSION['faculty_name'];

// Fetch evaluation summary
$stmt = $pdo->prepare("
    SELECT 
        ay.year, ay.semester, ec.name AS criteria, q.text AS question,
        ROUND(AVG(er.rating),2) AS avg_rating,
        COUNT(er.id) AS total_responses,
        GROUP_CONCAT(DISTINCT TRIM(er.comment) ORDER BY er.id DESC SEPARATOR '||') AS all_comments
    FROM evaluation_report er
    JOIN questions q ON er.question_id = q.id
    JOIN evaluation_criteria ec ON q.criteria_id = ec.id
    JOIN academic_years ay ON er.academic_year_id = ay.id
    WHERE er.faculty_id = ?
    GROUP BY ay.id, q.id, ec.id
    ORDER BY ay.year DESC, ay.semester, ec.name, q.text
");
$stmt->execute([$faculty_id]);
$evaluations = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Function to limit comments
function getSampleComments($commentString, $max = 3) {
    if (!$commentString) return [];
    $comments = explode('||', $commentString);
    return array_slice($comments, 0, $max);
}

// Group evaluations by year-semester
$grouped = [];
foreach ($evaluations as $e) {
    $period = $e['year'] . ' - ' . $e['semester'];
    $grouped[$period][] = $e;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Evaluations</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        body { background-color: #f3f4f6; }
        body::before {
            content: "";
            position: fixed; top: 0; left: 0;
            width: 100%; height: 100%;
            background: url('../php/logo.png') no-repeat center center;
            background-size: 900px 900px; opacity: 0.09;
            pointer-events: none; z-index: 0;
        }
        .content { position: relative; z-index: 1; }
    </style>
</head>
<body class="bg-gray-100">
<div class="flex content">
    <?php include 'faculty_sidebar.php'; ?>

    <div class="flex-1 p-6">
        <h1 class="text-2xl font-bold mb-6">My Evaluation Summary</h1>

        <?php if (empty($grouped)): ?>
            <p class="text-gray-500">No evaluations found.</p>
        <?php else: ?>
            <?php foreach ($grouped as $period => $items): ?>
                <div class="mb-8">
                    <h2 class="text-xl font-semibold text-blue-700 mb-4"><?= htmlspecialchars($period) ?></h2>
                    <div class="overflow-auto">
                        <table class="min-w-full table-auto bg-white shadow rounded">
                            <thead class="bg-blue-200 text-black">
                                <tr>
                                    <th class="px-4 py-2">Criteria</th>
                                    <th class="px-4 py-2">Question</th>
                                    <th class="px-4 py-2">Average Rating</th>
                                    <th class="px-4 py-2">Total Responses</th>
                                    <th class="px-4 py-2">Comments</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($items as $e): ?>
                                    <tr class="border-b hover:bg-gray-100 align-top">
                                        <td class="px-4 py-2"><?= htmlspecialchars($e['criteria']) ?></td>
                                        <td class="px-4 py-2"><?= htmlspecialchars($e['question']) ?></td>
                                        <td class="px-4 py-2 text-center"><?= $e['avg_rating'] ?></td>
                                        <td class="px-4 py-2 text-center"><?= $e['total_responses'] ?></td>
                                        <td class="px-4 py-2">
                                            <ul class="list-disc pl-5">
                                                <?php foreach (getSampleComments($e['all_comments'], 3) as $comment): ?>
                                                    <li><?= htmlspecialchars($comment) ?></li>
                                                <?php endforeach; ?>
                                            </ul>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>
</body>
</html>
