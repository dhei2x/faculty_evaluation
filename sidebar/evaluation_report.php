<?php  
session_start();
require_once '../php/db.php';
require_once '../php/auth.php';
require_role('admin');

// 📌 Academic years for filter dropdown
$years = $pdo->query("SELECT id, year, semester FROM academic_years ORDER BY year DESC")->fetchAll();

$where = '';
$params = [];
if (!empty($_GET['academic_year_id'])) {
    $where = 'WHERE er.academic_year_id = ?';
    $params[] = $_GET['academic_year_id'];
}

// 📊 Main ratings query
$sql = "
SELECT
  f.id AS faculty_id,
  f.full_name AS faculty_name,
  ec.name AS criteria_name,
  ROUND(AVG(er.rating), 2) AS avg_rating,
  COUNT(DISTINCT er.student_id) AS total_students,
  ay.year,
  ay.semester
FROM evaluation_report er
JOIN faculties f ON er.faculty_id = f.id
JOIN questions q ON er.question_id = q.id
JOIN evaluation_criteria ec ON q.criteria_id = ec.id
JOIN academic_years ay ON er.academic_year_id = ay.id
$where
GROUP BY f.id, ec.id, ay.id
ORDER BY f.full_name, ec.name, ay.year DESC
";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$rows = $stmt->fetchAll();

// 💬 Comments query
$commentSql = "
SELECT
  f.id AS faculty_id,
  ay.year,
  ay.semester,
  GROUP_CONCAT(DISTINCT er.comment SEPARATOR ' | ') AS comments
FROM evaluation_report er
JOIN faculties f ON er.faculty_id = f.id
JOIN academic_years ay ON er.academic_year_id = ay.id
WHERE er.comment IS NOT NULL AND er.comment != ''
" . ($where ? " AND er.academic_year_id = ?" : "") . "
GROUP BY f.id, ay.id
ORDER BY ay.year DESC, ay.semester DESC
";
$commentStmt = $pdo->prepare($commentSql);
$commentStmt->execute($params);
$comments = $commentStmt->fetchAll();

// 🗂️ Group ratings
$grouped = [];
foreach ($rows as $row) {
    $fid    = $row['faculty_id'];
    $period = $row['year'] . ' - ' . $row['semester'];

    if (!isset($grouped[$fid])) {
        $grouped[$fid] = [
            'name'    => $row['faculty_name'],
            'periods' => []
        ];
    }
    if (!isset($grouped[$fid]['periods'][$period])) {
        $grouped[$fid]['periods'][$period] = ['ratings' => [], 'comments' => []];
    }
    $grouped[$fid]['periods'][$period]['ratings'][] = $row;
}

// 📌 Add comments
foreach ($comments as $c) {
    $fid    = $c['faculty_id'];
    $period = $c['year'] . ' - ' . $c['semester'];
    if (isset($grouped[$fid]['periods'][$period])) {
        $grouped[$fid]['periods'][$period]['comments'] = explode(' | ', $c['comments']);
    }
}

// 🛑 Suspicious Evaluations Detection (Z-score + Tukey IQR)
$allRatings = array_column($rows, 'avg_rating');
$mean   = (count($allRatings) > 0) ? array_sum($allRatings) / count($allRatings) : 0;
$stddev = (count($allRatings) > 0) ? sqrt(array_sum(array_map(fn($r) => pow($r - $mean, 2), $allRatings)) / count($allRatings)) : 0;

// Tukey
sort($allRatings);
$q1 = $allRatings[floor((count($allRatings) - 1) * 0.25)] ?? 0;
$q3 = $allRatings[floor((count($allRatings) - 1) * 0.75)] ?? 0;
$iqr = $q3 - $q1;
$lowerFence = $q1 - 1.5 * $iqr;
$upperFence = $q3 + 1.5 * $iqr;

$keywords = ['lazy','rude','unfair','incompetent','bad','terrible','strict']; 
$flagged = [];

foreach ($comments as $c) {
    if (!empty($c['comments'])) {
        $periodComments = explode(' | ', $c['comments']);
        foreach ($periodComments as $cm) {
            foreach ($keywords as $kw) {
                if (stripos($cm, $kw) !== false) {
                    foreach ($rows as $r) {
                        if ($r['faculty_id'] == $c['faculty_id'] && $r['year'] == $c['year'] && $r['semester'] == $c['semester']) {
                            $z = ($stddev > 0) ? ($r['avg_rating'] - $mean) / $stddev : 0;
                            if ($z > 1 || $r['avg_rating'] < $lowerFence || $r['avg_rating'] > $upperFence) {
                                $flagged[] = [
                                    'faculty' => $r['faculty_name'],
                                    'comment' => $cm,
                                    'z_score' => round($z, 2),
                                    'rating'  => $r['avg_rating'],
                                    'period'  => $r['year'] . ' - ' . $r['semester']
                                ];
                            }
                        }
                    }
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Evaluation Reports</title>
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
            background-size: 900px 900px; /* adjust size */
            opacity: 0.09; /* 👈 controls transparency (lower = more transparent) */
            pointer-events: none; /* so it won’t block clicks */
            z-index: 0;
        }

        /* Keep content above background */
        .content {
            position: relative;
            z-index: 1;
        }
    </style></head>
<body class="bg-gray-100 p-6">
<div class="max-w-6xl mx-auto bg-white p-6 rounded shadow">
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-bold">Evaluation Reports</h1>
        <a href="../php/admin_dashboard.php" class="bg-blue-300 hover:bg-gray-400 text-gray-800 font-semibold px-4 py-2 rounded">
            ← Back to Dashboard
        </a>
    </div>

    <!-- Filter -->
    <form method="GET" class="mb-4">
        <label for="academic_year_id" class="font-semibold">Filter by Academic Year:</label>
        <select name="academic_year_id" id="academic_year_id" class="border p-1 rounded ml-2">
            <option value="">All</option>
            <?php foreach ($years as $y): ?>
                <option value="<?= $y['id'] ?>" <?= isset($_GET['academic_year_id']) && $_GET['academic_year_id'] == $y['id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($y['year'] . ' - ' . $y['semester']) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <button type="submit" class="ml-2 px-3 py-1 bg-blue-600 text-white rounded">Filter</button>
    </form>

    <!-- Export Buttons -->
    <div class="mb-4 flex space-x-2">
        <form method="POST" action="export_pdf.php">
            <input type="hidden" name="academic_year_id" value="<?= $_GET['academic_year_id'] ?? '' ?>">
            <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded">Export to PDF</button>
        </form>
        <form method="POST" action="export_excel.php">
            <input type="hidden" name="academic_year_id" value="<?= $_GET['academic_year_id'] ?? '' ?>">
            <button type="submit" class="px-4 py-2 bg-yellow-500 text-white rounded">Export to Excel</button>
        </form>
    </div>

    <!-- Suspicious Evaluations -->
    <?php if (!empty($flagged)): ?>
    <div class="mb-6 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
        🚩 <strong><?= count($flagged) ?></strong> suspicious evaluations detected.
    </div>
    <table class="w-full table-auto border mb-6">
        <thead class="bg-gray-200">
            <tr>
                <th class="border px-4 py-2">Faculty</th>
                <th class="border px-4 py-2">Period</th>
                <th class="border px-4 py-2">Avg Rating</th>
                <th class="border px-4 py-2">Z-Score</th>
                <th class="border px-4 py-2">Comment</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($flagged as $f): ?>
            <tr class="bg-red-50">
                <td class="border px-4 py-2"><?= htmlspecialchars($f['faculty']) ?></td>
                <td class="border px-4 py-2"><?= htmlspecialchars($f['period']) ?></td>
                <td class="border px-4 py-2 text-center"><?= $f['rating'] ?></td>
                <td class="border px-4 py-2 text-center text-blue-600"><?= $f['z_score'] ?></td>
                <td class="border px-4 py-2 text-red-700"><?= htmlspecialchars($f['comment']) ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>

    <!-- Main Reports -->
    <?php if (empty($grouped)): ?>
        <p class="text-gray-500">No evaluation data found.</p>
    <?php else: ?>
        <?php foreach ($grouped as $fid => $faculty): ?>
            <div class="mb-10">
                <h2 class="text-2xl font-bold mb-4"><?= htmlspecialchars($faculty['name']) ?></h2>

                <!-- Overall Summary -->
                <div class="mb-6">
                    <h3 class="text-lg font-semibold text-green-700 mb-2">Overall Summary</h3>
                    <table class="w-full table-auto border mb-4">
                        <thead>
                            <tr class="bg-gray-200">
                                <th class="border px-4 py-2">Academic Year</th>
                                <th class="border px-4 py-2">Average Rating</th>
                                <th class="border px-4 py-2">Total Students</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($faculty['periods'] as $period => $data): ?>
                                <?php 
                                $allRatings = array_column($data['ratings'], 'avg_rating');
                                $periodAvg  = !empty($allRatings) ? round(array_sum($allRatings) / count($allRatings), 2) : '-';
                                $studentCounts = array_column($data['ratings'], 'total_students');
                                $uniqueStudents = max($studentCounts);
                                ?>
                                <tr>
                                    <td class="border px-4 py-2"><?= htmlspecialchars($period) ?></td>
                                    <td class="border px-4 py-2"><?= $periodAvg ?></td>
                                    <td class="border px-4 py-2"><?= $uniqueStudents ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Criteria Breakdown -->
                <?php foreach ($faculty['periods'] as $period => $data): ?>
                    <h3 class="text-lg font-semibold text-blue-700 mb-2"><?= htmlspecialchars($period) ?> - Criteria Breakdown</h3>
                    <table class="w-full table-auto border mb-4">
                        <thead>
                            <tr class="bg-gray-100">
                                <th class="border px-4 py-2">Criteria</th>
                                <th class="border px-4 py-2">Average Rating</th>
                                <th class="border px-4 py-2">Students</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($data['ratings'] as $entry): ?>
                                <tr>
                                    <td class="border px-4 py-2"><?= htmlspecialchars($entry['criteria_name']) ?></td>
                                    <td class="border px-4 py-2"><?= $entry['avg_rating'] ?></td>
                                    <td class="border px-4 py-2"><?= $entry['total_students'] ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>

                    <!-- Student Comments -->
                    <?php if (!empty($data['comments'])): ?>
                        <div class="mt-4 mb-6">
                            <h4 class="text-md font-semibold text-gray-800">💬 Student Comments:</h4>
                            <ul class="list-disc pl-5 space-y-1 text-gray-700">
                                <?php foreach ($data['comments'] as $c): ?>
                                    <li><?= htmlspecialchars($c) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
</body>
</html>
