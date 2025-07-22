<?php 
session_start();
require_once '../php/db.php';
require_once '../php/auth.php';
require_role('admin');

// Fetch academic years for filter dropdown
$years = $pdo->query("SELECT id, year, semester FROM academic_years ORDER BY year DESC")->fetchAll();

$where = '';
$params = [];
if (!empty($_GET['academic_year_id'])) {
    $where = 'WHERE er.academic_year_id = ?';
    $params[] = $_GET['academic_year_id'];
}

// Fetch evaluation report with comments
$sql = "
SELECT
  f.id AS faculty_id,
  f.full_name AS faculty_name,
  ec.name AS criteria_name,
  ROUND(AVG(er.rating), 2) AS avg_rating,
  COUNT(er.id) AS total_ratings,
  ay.year AS academic_year,
  ay.semester AS semester,
  er.comment
FROM evaluation_report er
JOIN faculties f ON er.faculty_id = f.id
JOIN questions q ON er.question_id = q.id
JOIN evaluation_criteria ec ON q.criteria_id = ec.id
JOIN academic_years ay ON er.academic_year_id = ay.id
$where
GROUP BY f.id, ec.id, ay.year, ay.semester, er.comment
ORDER BY f.full_name, ec.name, ay.year DESC
";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$rows = $stmt->fetchAll();

// Group results by faculty
$grouped = [];
foreach ($rows as $row) {
    $fid = $row['faculty_id'];
    if (!isset($grouped[$fid])) {
        $grouped[$fid] = [
            'name' => $row['faculty_name'],
            'entries' => [],
            'comments' => []
        ];
    }
    $grouped[$fid]['entries'][] = $row;
    if (!empty($row['comment'])) {
        $grouped[$fid]['comments'][] = $row['comment'];
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Evaluation Reports</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-gray-100 p-6">
<div class="max-w-6xl mx-auto bg-white p-6 rounded shadow">
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-bold">Evaluation Reports</h1>
        <a href="../php/admin_dashboard.php" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold px-4 py-2 rounded">
            ← Back to Dashboard
        </a>
    </div>

    <!-- Academic Year Filter -->
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
        <button class="px-4 py-2 bg-green-600 text-white rounded">Export to PDF</button>
        <button class="px-4 py-2 bg-yellow-500 text-white rounded">Export to Excel</button>
    </div>

    <?php if (empty($grouped)): ?>
        <p class="text-gray-500">No evaluation data found.</p>
    <?php else: ?>
        <?php foreach ($grouped as $fid => $faculty): ?>
            <div class="mb-10">
                <h2 class="text-xl font-semibold mb-2"><?= htmlspecialchars($faculty['name']) ?></h2>

                <!-- Ratings Table -->
                <table class="w-full table-auto border mb-4">
                    <thead>
                        <tr class="bg-gray-200">
                            <th class="border px-4 py-2 text-left">Criteria</th>
                            <th class="border px-4 py-2 text-left">Average Rating</th>
                            <th class="border px-4 py-2 text-left"># Ratings</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($faculty['entries'] as $entry): ?>
                            <tr>
                                <td class="border px-4 py-2"><?= htmlspecialchars($entry['criteria_name']) ?></td>
                                <td class="border px-4 py-2"><?= $entry['avg_rating'] ?></td>
                                <td class="border px-4 py-2"><?= $entry['total_ratings'] ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <!-- Chart -->
                <canvas id="chart-<?= $fid ?>" height="120"></canvas>
                <script>
                const ctx<?= $fid ?> = document.getElementById('chart-<?= $fid ?>');
                new Chart(ctx<?= $fid ?>, {
                    type: 'bar',
                    data: {
                        labels: <?= json_encode(array_column($faculty['entries'], 'criteria_name')) ?>,
                        datasets: [{
                            label: 'Average Rating',
                            data: <?= json_encode(array_column($faculty['entries'], 'avg_rating')) ?>,
                            backgroundColor: 'rgba(59,130,246,0.6)',
                            borderColor: 'rgba(59,130,246,1)',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        scales: { y: { beginAtZero: true, max: 5 } }
                    }
                });
                </script>

                <!-- Comments Section -->
                <?php if (!empty($faculty['comments'])): ?>
                    <div class="mt-4">
                        <h3 class="text-md font-semibold mb-2">Student Comments:</h3>
                        <ul class="list-disc pl-5 space-y-1 text-gray-700">
                            <?php foreach ($faculty['comments'] as $c): ?>
                                <li><?= htmlspecialchars($c) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
</body>
</html>
