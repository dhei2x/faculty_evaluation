<?php
session_start();
require_once '../php/db.php';

// ✅ Session check
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'faculty' || empty($_SESSION['faculty_id'])) {
    header("Location: ../php/login.php");
    exit;
}

$facultyId   = $_SESSION['faculty_id'];
$facultyName = $_SESSION['faculty_name'] ?? 'faculty';

// ✅ Summary query (fix overcounting with DISTINCT)
$summaryStmt = $pdo->prepare("
    SELECT ay.year, ay.semester, ec.name AS criteria_name,
           ROUND((AVG(er.rating) / 5) * 100, 2) AS avg_percentage,
           COUNT(DISTINCT er.student_id) AS total
    FROM evaluation_report er
    JOIN questions q ON er.question_id = q.id
    JOIN evaluation_criteria ec ON q.criteria_id = ec.id
    JOIN academic_years ay ON er.academic_year_id = ay.id
    WHERE er.faculty_id = ?
    GROUP BY ay.id, ec.id
    ORDER BY ay.year DESC, ay.semester
");
$summaryStmt->execute([$facultyId]);
$summaryData = $summaryStmt->fetchAll(PDO::FETCH_ASSOC);

// ✅ Comments query (fix duplicates)
$commentsStmt = $pdo->prepare("
    SELECT DISTINCT ay.year, ay.semester, TRIM(er.comment) AS comment
    FROM evaluation_report er
    JOIN academic_years ay ON er.academic_year_id = ay.id
    WHERE er.faculty_id = ? 
      AND er.comment IS NOT NULL 
      AND TRIM(er.comment) != ''
    ORDER BY ay.year DESC, ay.semester, er.id DESC
");
$commentsStmt->execute([$facultyId]);
$commentsData = $commentsStmt->fetchAll(PDO::FETCH_ASSOC);

// Group comments by semester/year
$commentsGrouped = [];
foreach ($commentsData as $row) {
    $key = $row['year'] . ' - ' . $row['semester'];
    $commentsGrouped[$key][] = $row['comment'];
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Faculty Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        body {
            position: relative;
            background-color: #f3f4f6;
        }
        body::before {
            content: "";
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            background: url('../php/logo.png') no-repeat center center;
            background-size: 900px 900px;
            opacity: 0.09;
            pointer-events: none;
            z-index: 0;
        }
        .content { position: relative; z-index: 1; }
    </style>
</head>
<body class="bg-gray-100 flex">

<?php include 'faculty_sidebar.php'; ?>

<main class="flex-1 p-8 space-y-12 content">

    <?php if (!empty($_SESSION['welcome'])): ?>
        <div id="welcomeToast" class="fixed top-4 right-4 bg-blue-200 text-white px-4 py-2 rounded shadow-lg z-50">
            <?= htmlspecialchars($_SESSION['welcome']) ?>
        </div>
        <script>
            setTimeout(() => {
                const toast = document.getElementById('welcomeToast');
                if (toast) toast.style.display = 'none';
            }, 2000);
        </script>
        <?php unset($_SESSION['welcome']); ?>
    <?php endif; ?>

    <!-- Evaluation Summary -->
    <h2 class="text-2xl font-semibold mb-4">Evaluation Summary</h2>

    <?php if (empty($summaryData)): ?>
        <p>No evaluation data available yet.</p>
    <?php else: ?>
        <?php
        $grouped = [];
        foreach ($summaryData as $row) {
            $grouped[$row['year'] . ' - ' . $row['semester']][] = $row;
        }
        ?>
        <?php foreach ($grouped as $period => $items): ?>
            <div class="bg-white rounded-xl shadow-md p-6 mb-6">
                <h3 class="text-blue-700 font-semibold mb-4"><?= htmlspecialchars($period) ?></h3>
                <table class="w-full table-auto border text-sm">
                    <thead class="bg-gray-100 text-left">
                        <tr>
                            <th class="border px-3 py-2">Criteria</th>
                            <th class="border px-3 py-2">Average (%)</th>
                            <th class="border px-3 py-2">Responses</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($items as $r): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="border px-3 py-2"><?= htmlspecialchars($r['criteria_name']) ?></td>
                                <td class="border px-3 py-2"><?= $r['avg_percentage'] ?>%</td>
                                <td class="border px-3 py-2"><?= $r['total'] ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

    <!-- Comments Section -->
    <h2 class="text-2xl font-bold mb-4">Student Comments</h2>
    <?php if (empty($commentsGrouped)): ?>
        <p>No comments yet.</p>
    <?php else: ?>
        <?php foreach ($commentsGrouped as $period => $comments): ?>
            <div class="bg-white rounded-xl shadow p-5 mb-6">
                <h3 class="font-semibold text-blue-700 mb-3"><?= htmlspecialchars($period) ?></h3>
                <ul class="list-disc pl-6 space-y-1">
                    <?php foreach (array_unique($comments) as $comment): ?>
                        <li><?= htmlspecialchars($comment) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

</main>
</body>
</html>
