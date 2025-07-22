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
    $classCount = $pdo->query("SELECT COUNT(*) FROM classes")->fetchColumn();
    $subjectCount = $pdo->query("SELECT COUNT(*) FROM subjects")->fetchColumn();
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}

// Detect suspicious evaluations
$flaggedCount = 0;
try {
    $sql = "
        SELECT 
            AVG(er.rating) AS avg_rating,
            GROUP_CONCAT(DISTINCT er.comment SEPARATOR ' | ') AS comment
        FROM evaluation_report er
        GROUP BY er.student_id, er.faculty_id
    ";

    $data = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);

    $ratings = array_column($data, 'avg_rating');
    $mean = array_sum($ratings) / count($ratings);
    $stddev = sqrt(array_sum(array_map(fn($r) => pow($r - $mean, 2), $ratings)) / count($ratings));

    $keywords = ['lazy', 'rude', 'unfair', 'incompetent', 'bad', 'terrible', 'unhelpful', 'strict'];

    foreach ($data as $row) {
        $z = ($stddev > 0) ? ($row['avg_rating'] - $mean) / $stddev : 0;

        foreach ($keywords as $kw) {
            if ($z > 1 && stripos($row['comment'], $kw) !== false) {
                $flaggedCount++;
                break;
            }
        }
    }

    // ✅ Reset review flag if new suspicious evaluations are found
    if ($flaggedCount > 0) {
        $_SESSION['reviewed_flagged'] = false;
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
</head>
<body class="bg-gray-100 font-sans">
<div class="flex">
    <!-- Sidebar -->
    <?php include 'admin_sidebar.php'; ?>

    <!-- Main Content -->
    <div class="flex-1 p-6">
        <h1 class="text-3xl font-bold mb-6">Welcome, Admin</h1>

        <!-- Suspicious Evaluation Notification -->
      <?php if ($flaggedCount > 0 && empty($_SESSION['reviewed_flagged'])): ?>
    <div class="mb-6 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
        ⚠️ <strong><?= $flaggedCount ?></strong> suspicious evaluations detected.
        <a href="../sidebar/flagged_evaluations.php?reviewed=1" class="underline text-blue-700 hover:text-blue-900 ml-2">Review Now</a>
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
