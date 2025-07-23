<?php
session_start();
require_once '../php/db.php';

if (!isset($_SESSION['faculty'])) {
    session_destroy();
    header("Location: ../php/login.php");
    exit();
}

$faculty_id = $_SESSION['faculty']['id'];

// Fetch summary
$summaryStmt = $pdo->prepare("
    SELECT ay.year, ay.semester, ec.name AS criteria_name, ROUND(AVG(er.rating), 2) AS avg_rating, COUNT(er.id) AS total
    FROM evaluation_report er
    JOIN questions q ON er.question_id = q.id
    JOIN evaluation_criteria ec ON q.criteria_id = ec.id
    JOIN academic_years ay ON er.academic_year_id = ay.id
    WHERE er.faculty_id = ?
    GROUP BY ay.id, ec.id
    ORDER BY ay.year DESC, ay.semester
");
$summaryStmt->execute([$faculty_id]);
$summaryData = $summaryStmt->fetchAll();

// Fetch comments
$commentsStmt = $pdo->prepare("
    SELECT ay.year, ay.semester, er.comment
    FROM evaluation_report er
    JOIN academic_years ay ON er.academic_year_id = ay.id
    WHERE er.faculty_id = ? AND er.comment IS NOT NULL AND er.comment != ''
    ORDER BY ay.year DESC, ay.semester
");
$commentsStmt->execute([$faculty_id]);
$commentsData = $commentsStmt->fetchAll(PDO::FETCH_ASSOC);

// Group comments
$commentsGrouped = [];
foreach ($commentsData as $row) {
    $key = $row['year'] . ' - ' . $row['semester'];
    $commentsGrouped[$key][] = $row['comment'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Faculty Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 text-gray-800 flex">
    <!-- Sidebar -->
    <?php include 'faculty_sidebar.php'; ?>

    <!-- Main content -->
    <main class="flex-1 p-8 space-y-12">
        <section>
            <h1 class="text-3xl font-bold mb-6 text-green-700">Evaluation Summary</h1>

            <?php if (empty($summaryData)): ?>
                <p class="text-gray-500">No evaluation data available yet.</p>
            <?php else: ?>
                <?php
                $grouped = [];
                foreach ($summaryData as $row) {
                    $grouped[$row['year'] . ' - ' . $row['semester']][] = $row;
                }
                ?>
                <div class="space-y-6">
                    <?php foreach ($grouped as $period => $items): ?>
                        <div class="bg-white rounded-xl shadow-md p-6">
                            <h2 class="text-xl font-semibold text-green-600 mb-4"><?= htmlspecialchars($period) ?></h2>
                            <table class="w-full table-auto border text-sm">
                                <thead class="bg-gray-100 text-left">
                                    <tr>
                                        <th class="border px-3 py-2">Criteria</th>
                                        <th class="border px-3 py-2">Average Rating</th>
                                        <th class="border px-3 py-2"># Responses</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($items as $r): ?>
                                        <tr class="hover:bg-gray-50">
                                            <td class="border px-3 py-2"><?= htmlspecialchars($r['criteria_name']) ?></td>
                                            <td class="border px-3 py-2"><?= $r['avg_rating'] ?></td>
                                            <td class="border px-3 py-2"><?= $r['total'] ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>

        <!-- Comments Section -->
        <section>
            <h2 class="text-2xl font-bold text-green-700 mb-4">Student Comments</h2>
            <?php if (empty($commentsGrouped)): ?>
                <p class="text-gray-500">No comments yet.</p>
            <?php else: ?>
                <?php foreach ($commentsGrouped as $period => $comments): ?>
                    <div class="mb-6 bg-white rounded-xl shadow p-5">
                        <h3 class="font-semibold text-lg text-blue-700 mb-3"><?= htmlspecialchars($period) ?></h3>
                        <ul class="list-disc pl-6 space-y-1 text-gray-700">
                            <?php foreach ($comments as $comment): ?>
                                <li><?= htmlspecialchars($comment) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </section>
    </main>
</body>
</html>
