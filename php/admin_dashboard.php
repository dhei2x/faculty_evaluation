<?php
session_start();
require_once '../php/db.php';
require_once '../php/auth.php';
require_role('admin');

// ✅ Dashboard metrics
try {
    $studentCount = $pdo->query("SELECT COUNT(*) FROM students")->fetchColumn();
    $facultyCount = $pdo->query("SELECT COUNT(*) FROM faculties")->fetchColumn();
    $classCount   = $pdo->query("SELECT COUNT(*) FROM classes")->fetchColumn();
    $subjectCount = $pdo->query("SELECT COUNT(*) FROM subjects")->fetchColumn();
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}

// ✅ Detect suspicious evaluations
$flaggedCount = 0;
try {
    $sql = "
        SELECT er.faculty_id, AVG(er.rating) AS avg_rating, 
               GROUP_CONCAT(DISTINCT er.comment SEPARATOR ' | ') AS comment
        FROM evaluation_report er
        GROUP BY er.student_id, er.faculty_id
    ";
    $data = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);

    $ratings = array_column($data, 'avg_rating');
    sort($ratings);

    if (count($ratings) > 0) {
        $mean   = array_sum($ratings) / count($ratings);
        $stddev = sqrt(array_sum(array_map(fn($r) => pow($r - $mean, 2), $ratings)) / count($ratings));

        $count = count($ratings);
        $q1Index = floor(($count + 1) / 4);
        $q3Index = floor((3 * ($count + 1)) / 4);
        $q1 = $ratings[$q1Index] ?? $ratings[0];
        $q3 = $ratings[$q3Index] ?? $ratings[$count - 1];
        $iqr = $q3 - $q1;
        $lowerBound = $q1 - 1.5 * $iqr;
        $upperBound = $q3 + 1.5 * $iqr;

        $badWords  = $pdo->query("SELECT word FROM flagged_words WHERE type = 'bad'")->fetchAll(PDO::FETCH_COLUMN);
        $goodWords = $pdo->query("SELECT word FROM flagged_words WHERE type = 'good'")->fetchAll(PDO::FETCH_COLUMN);

        foreach ($data as $row) {
            $rating = $row['avg_rating'];
            $comment = strtolower($row['comment'] ?? '');
            $z = ($stddev > 0) ? ($rating - $mean) / $stddev : 0;

            $flagged = false;

            foreach ($badWords as $bw) {
                if (stripos($comment, $bw) !== false && $rating >= 4) {
                    $flagged = true;
                    break;
                }
            }

            foreach ($goodWords as $gw) {
                if (stripos($comment, $gw) !== false && $rating <= 2) {
                    $flagged = true;
                    break;
                }
            }

            if (abs($z) > 2 || $rating < $lowerBound || $rating > $upperBound) {
                $flagged = true;
            }

            if ($flagged) {
                $flaggedCount++;
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
#flagged-box {
    transition: all 0.4s ease;
}
</style>
</head>
<body class="font-sans bg-gray-100">
<div class="flex content">
    <?php include 'admin_sidebar.php'; ?>

    <div class="flex-1 p-6">
        <!-- 🚩 Suspicious Evaluation Notification -->
        <?php if ($flaggedCount > 0): ?>
            <div id="flagged-box" class="mb-6 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                ⚠️ <strong><?= $flaggedCount ?></strong> suspicious evaluations detected.
                <div class="mt-2">
                    <button id="review-btn"
                        class="bg-purple-600 hover:bg-purple-700 text-white px-3 py-1 rounded transition">
                        Review Now
                    </button>
                </div>
            </div>
        <?php endif; ?>

        <!-- 📊 Dashboard Metrics -->
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

<!-- ✅ JS for Vanish + Redirect -->
<script>
document.addEventListener("DOMContentLoaded", () => {
    const reviewBtn = document.getElementById("review-btn");
    const flaggedBox = document.getElementById("flagged-box");

    if (reviewBtn && flaggedBox) {
        reviewBtn.addEventListener("click", (e) => {
            e.preventDefault();
            // fade + shrink effect
            flaggedBox.style.opacity = "0";
            flaggedBox.style.transform = "scale(0.9)";
            flaggedBox.style.pointerEvents = "none";
            // wait before redirect
            setTimeout(() => {
                window.location.href = "../sidebar/flagged_evaluations.php";
            }, 400);
        });
    }
});
</script>
</body>
</html>
