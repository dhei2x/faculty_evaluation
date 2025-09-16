<?php
session_start();
require_once '../php/db.php';
require_once '../php/auth.php';
require_role('admin');

if (isset($_GET['reviewed'])) {
    $_SESSION['reviewed_flagged'] = true;
}

// Dashboard metrics
try {
    $studentCount = $pdo->query("SELECT COUNT(*) FROM students")->fetchColumn();
    $facultyCount = $pdo->query("SELECT COUNT(*) FROM faculties")->fetchColumn();
    $classCount   = $pdo->query("SELECT COUNT(*) FROM classes")->fetchColumn();
    $subjectCount = $pdo->query("SELECT COUNT(*) FROM subjects")->fetchColumn();
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}

// Detect suspicious evaluations (Z-score + Tukey)
$flaggedCount = 0;
try {
    $sql = "
        SELECT AVG(er.rating) AS avg_rating, GROUP_CONCAT(DISTINCT er.comment SEPARATOR ' | ') AS comment
        FROM evaluation_report er
        GROUP BY er.student_id, er.faculty_id
    ";
    $data = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);

    $ratings = array_column($data, 'avg_rating');
    sort($ratings);

    if (count($ratings) > 0) {
        $mean   = array_sum($ratings) / count($ratings);
        $stddev = sqrt(array_sum(array_map(fn($r) => pow($r - $mean, 2), $ratings)) / count($ratings));

        // Quartiles for Tukey
        $count = count($ratings);
        $q1Index = floor(($count + 1) / 4);
        $q3Index = floor((3 * ($count + 1)) / 4);
        $q1 = $ratings[$q1Index] ?? $ratings[0];
        $q3 = $ratings[$q3Index] ?? $ratings[$count - 1];
        $iqr = $q3 - $q1;
        $lowerBound = $q1 - 1.5 * $iqr;
        $upperBound = $q3 + 1.5 * $iqr;

        $keywords = ['lazy', 'rude', 'unfair', 'retard', 'incompetent', 'bad', 'terrible', 'unhelpful', 'strict'];

        foreach ($data as $row) {
            $z = ($stddev > 0) ? ($row['avg_rating'] - $mean) / $stddev : 0;

            foreach ($keywords as $kw) {
                if (!empty($row['comment']) &&
                    (stripos($row['comment'], $kw) !== false) &&
                    ($z > 1 || $row['avg_rating'] < $lowerBound || $row['avg_rating'] > $upperBound)) {
                    $flaggedCount++;
                    break;
                }
            }
        }
    }
} catch (Exception $e) {
    $flaggedCount = 0;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard - Faculty Evaluation System</title>
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
<body class="font-sans bg-gray-100">
<div class="flex content">
    <?php include 'admin_sidebar.php'; ?>

    <div class="flex-1 p-6">
        <!-- Suspicious Evaluation Notification -->
        <?php if ($flaggedCount > 0 && empty($_SESSION['reviewed_flagged'])): ?>
        <div class="mb-6 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
            ⚠️ <strong><?= $flaggedCount ?></strong> suspicious evaluations detected.
            <div class="mt-2">
                <a href="../sidebar/flagged_evaluations.php?reviewed=1" 
                   class="bg-purple-600 hover:bg-purple-700 text-white px-3 py-1 rounded">
                   Review Now
                </a>
            </div>
        </div>
        <?php endif; ?>

        <!-- Dashboard Metrics -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
            <div class="bg-white p-4 rounded-lg shadow">
                <h2 class="text-xl font-semibold">Students</h2>
                <p class="text-3xl font-bold text-blue-500"><?= $studentCount ?></p>
            </div>
            <div class="bg-white p-4 rounded-lg shadow">
                <h2 class="text-xl font-semibold">Faculties</h2>
                <p class="text-3xl font-bold text-green-500"><?= $facultyCount ?></p>
            </div>
            <div class="bg-white p-4 rounded-lg shadow">
                <h2 class="text-xl font-semibold">Classes</h2>
                <p class="text-3xl font-bold text-yellow-500"><?= $classCount ?></p>
            </div>
            <div class="bg-white p-4 rounded-lg shadow">
                <h2 class="text-xl font-semibold">Subjects</h2>
                <p class="text-3xl font-bold text-purple-500"><?= $subjectCount ?></p>
            </div>
        </div>
    </div>
</div>
</body>
</html>
