<?php
session_start();
require_once '../php/db.php';

if (!isset($_SESSION['faculty'])) {
    session_destroy();
    header("Location: ../php/login.php");
    exit();
}

$faculty_id = $_SESSION['faculty']['id'];

$commentsStmt = $pdo->prepare("
    SELECT ay.year, ay.semester, er.comment
    FROM evaluation_report er
    JOIN academic_years ay ON er.academic_year_id = ay.id
    WHERE er.faculty_id = ? AND er.comment IS NOT NULL AND er.comment != ''
    ORDER BY ay.year DESC, ay.semester, er.id DESC
");
$commentsStmt->execute([$faculty_id]);
$commentsData = $commentsStmt->fetchAll(PDO::FETCH_ASSOC);

// Group comments
$groupedComments = [];
foreach ($commentsData as $row) {
    $key = $row['year'] . ' - ' . $row['semester'];
    $groupedComments[$key][] = $row['comment'];
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>View Comments</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
<div class="flex">
    <?php include 'faculty_sidebar.php'; ?>

    <div class="flex-1 p-6">
        <h1 class="text-2xl font-bold mb-6">All Student Comments</h1>

        <?php if (empty($groupedComments)): ?>
            <p class="text-gray-500">No comments available yet.</p>
        <?php else: ?>
            <?php foreach ($groupedComments as $period => $comments): ?>
                <div class="mb-6 bg-white rounded shadow p-4">
                    <h2 class="text-lg font-semibold text-blue-700 mb-2"><?= htmlspecialchars($period) ?></h2>
                    <ul class="list-disc pl-5 space-y-1 text-gray-800">
                        <?php foreach ($comments as $comment): ?>
                            <li><?= htmlspecialchars($comment) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>
</body>
</html>
