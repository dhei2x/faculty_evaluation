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

// 📊 Main ratings query (only faculties with evaluations)
$sql = "
SELECT
  f.id AS faculty_id,
  CONCAT(f.last_name, ', ', f.first_name, ' ', IFNULL(f.middle_name, '')) AS faculty_name,
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
ORDER BY f.last_name, f.first_name, ay.year DESC
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
?>
<!DOCTYPE html>
<html>
<head>
    <title>Evaluation Reports</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">

    <style>
        body {
            position: relative;
            background-color: #f3f4f6;
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
            background-size: 1100px 1100px;
            opacity: 0.09;
            pointer-events: none;
            z-index: 0;
        }

        .content {
            position: relative;
            z-index: 1;
        }

        /* Percentage colors */
        .percent-green { color: #16a34a; font-weight: 600; }
        .percent-yellow { color: #ca8a04; font-weight: 600; }
        .percent-red { color: #dc2626; font-weight: 600; }
    </style>
</head>
<body class="bg-gray-100 p-6">
<div class="content max-w-6xl mx-auto bg-white p-6 rounded shadow">
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
                                <th class="border px-4 py-2">Percentage</th>
                                <th class="border px-4 py-2">Total Students Responses</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($faculty['periods'] as $period => $data): 
                                $allRatings = array_column($data['ratings'], 'avg_rating');
                                $periodAvg  = !empty($allRatings) ? round(array_sum($allRatings) / count($allRatings), 2) : '-';
                                $studentCounts = array_column($data['ratings'], 'total_students');
                                $uniqueStudents = max($studentCounts);
                                $percentage = $periodAvg !== '-' ? round(($periodAvg / 5) * 100, 2) : '-';
                                $colorClass = ($percentage >= 80) ? 'percent-green' : (($percentage >= 60) ? 'percent-yellow' : 'percent-red');
                            ?>
                                <tr>
                                    <td class="border px-4 py-2"><?= htmlspecialchars($period) ?></td>
                                    <td class="border px-4 py-2 <?= $colorClass ?>"><?= $percentage ?>%</td>
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
                                <th class="border px-4 py-2">Percentage</th>
                                <th class="border px-4 py-2">Students Responses</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($data['ratings'] as $entry): 
                                $percentage = round(($entry['avg_rating'] / 5) * 100, 2);
                                $colorClass = ($percentage >= 80) ? 'percent-green' : (($percentage >= 60) ? 'percent-yellow' : 'percent-red');
                            ?>
                                <tr>
                                    <td class="border px-4 py-2"><?= htmlspecialchars($entry['criteria_name']) ?></td>
                                    <td class="border px-4 py-2 <?= $colorClass ?>"><?= $percentage ?>%</td>
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
