<?php
session_start();
require_once '../php/db.php';
require_once '../php/auth.php';
require_role('admin');

// Fetch evaluations grouped by student + faculty
$sql = "
    SELECT 
        er.student_id,
        er.faculty_id,
        f.full_name,
        AVG(er.rating) AS avg_rating,
        GROUP_CONCAT(DISTINCT er.comment SEPARATOR ' | ') AS comment
    FROM evaluation_report er
    JOIN faculties f ON er.faculty_id = f.id
    GROUP BY er.student_id, er.faculty_id
";
$data = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);

// Z-score detection
$ratings = array_column($data, 'avg_rating');
$mean = array_sum($ratings) / count($ratings);
$stddev = sqrt(array_sum(array_map(fn($r) => pow($r - $mean, 2), $ratings)) / count($ratings));

$keywords = ['lazy', 'rude', 'unfair', 'incompetent', 'bad', 'terrible', 'unhelpful', 'strict'];
$flagged = [];

foreach ($data as $row) {
    $z = ($stddev > 0) ? ($row['avg_rating'] - $mean) / $stddev : 0;
    $row['z_score'] = round($z, 2);

    foreach ($keywords as $kw) {
        if (stripos($row['comment'], $kw) !== false && $z > 1) {
            $flagged[] = $row;
            break;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Flagged Evaluations</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">
    <div class="max-w-6xl mx-auto py-10 px-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-800">🚩 Flagged Evaluations</h1>
            <a href="../php/admin_dashboard.php" class="text-sm text-blue-600 hover:underline">← Back to Dashboard</a>
        </div>

        <div class="bg-white p-6 rounded shadow">
            <?php if (empty($flagged)): ?>
                <p class="text-green-600 font-semibold">✅ No suspicious evaluations found.</p>
            <?php else: ?>
                <p class="mb-4 text-red-600 font-medium">
                    ⚠️ <strong><?= count($flagged) ?></strong> suspicious evaluations detected based on high ratings with negative comments.
                </p>

                <table class="table-auto w-full text-sm border border-gray-200">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-4 py-2 border">Faculty</th>
                            <th class="px-4 py-2 border">Avg Rating</th>
                            <th class="px-4 py-2 border">Z-Score</th>
                            <th class="px-4 py-2 border">Comment(s)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($flagged as $entry): ?>
                            <tr class="hover:bg-red-50 border-b">
                                <td class="px-4 py-2 font-medium"><?= htmlspecialchars($entry['full_name']) ?></td>
                                <td class="px-4 py-2 text-center"><?= round($entry['avg_rating'], 2) ?></td>
                                <td class="px-4 py-2 text-center text-blue-600"><?= $entry['z_score'] ?></td>
                                <td class="px-4 py-2 text-red-700"><?= htmlspecialchars($entry['comment']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
