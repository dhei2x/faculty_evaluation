<?php 
ob_start();
session_start();

require_once '../php/db.php';
require_once '../php/auth.php';

// ✅ Allow only super admins (handles both spellings)
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['superadmin', 'super_admin'])) {
    header("Location: ../php/admin_dashboard.php");
    exit;
}

// ✅ Load CSP (no newline issues)
$cspFile = __DIR__ . '/../php/policy.csp';
if (file_exists($cspFile)) {
    $cspRules = trim(file_get_contents($cspFile));
    header("Content-Security-Policy: $cspRules");
}

// ✅ Faculty-level evaluation summary (with name parts)
$sql = "
    SELECT 
        er.faculty_id, 
        CONCAT(f.last_name, ', ', f.first_name, ' ', IFNULL(f.middle_name, '')) AS full_name,
        AVG(er.rating) AS avg_rating,
        GROUP_CONCAT(DISTINCT er.comment SEPARATOR ' | ') AS comments
    FROM evaluation_report er
    JOIN faculties f ON er.faculty_id = f.id
    WHERE er.comment IS NOT NULL AND er.comment != ''
    GROUP BY er.faculty_id
";
$stmt = $pdo->query($sql);
$evaluations = $stmt->fetchAll(PDO::FETCH_ASSOC);

// ✅ Fetch bad/good words dynamically from database
$badwords  = $pdo->query("SELECT word FROM flagged_words WHERE type = 'bad'")
                 ->fetchAll(PDO::FETCH_COLUMN);
$goodwords = $pdo->query("SELECT word FROM flagged_words WHERE type = 'good'")
                 ->fetchAll(PDO::FETCH_COLUMN);

// --------------------------------------
// 🔹 Step 1: Collect ratings for Z-score & Tukey
// --------------------------------------
$ratings = array_column($evaluations, 'avg_rating');
$mean    = count($ratings) ? array_sum($ratings) / count($ratings) : 0;
$stdDev  = count($ratings) > 1 ? sqrt(array_sum(array_map(fn($r) => pow($r - $mean, 2), $ratings)) / (count($ratings) - 1)) : 0;

// Quartiles for Tukey
sort($ratings);
$n = count($ratings);
$q1 = $q3 = 0;
if ($n > 0) {
    $q1 = $ratings[(int) floor(($n + 1) / 4) - 1] ?? $ratings[0];
    $q3 = $ratings[(int) floor(3 * ($n + 1)) / 4 - 1] ?? $ratings[$n - 1];
}
$IQR = $q3 - $q1;
$lowerFence = $q1 - 1.5 * $IQR;
$upperFence = $q3 + 1.5 * $IQR;

// --------------------------------------
// 🔹 Step 2: Detect anomalies
// --------------------------------------
$flagged = [];

foreach ($evaluations as $eval) {
    $faculty  = $eval['full_name'];
    $rating   = (float)$eval['avg_rating'];
    $comments = strtolower($eval['comments']);
    $zScore   = ($stdDev > 0) ? ($rating - $mean) / $stdDev : 0;

    $reason = [];

    if (abs($zScore) > 2) $reason[] = "Z-score outlier (z=" . round($zScore, 2) . ")";
    if ($rating < $lowerFence || $rating > $upperFence) $reason[] = "Tukey outlier (outside IQR)";

    foreach ($badwords as $bw) {
        if (stripos($comments, $bw) !== false) {
            if ($rating >= 4) $reason[] = "Suspicious: High rating but contains bad word '{$bw}'";
            else $reason[] = "Negative sentiment detected: {$bw}";
            break;
        }
    }

    foreach ($goodwords as $gw) {
        if (stripos($comments, $gw) !== false && $rating <= 2) {
            $reason[] = "Suspicious: Low rating but contains good word '{$gw}'";
            break;
        }
    }

    if (!empty($reason)) {
        $flagged[] = [
            'faculty' => $faculty,
            'avg_rating' => $rating,
            'comments' => $eval['comments'],
            'reason' => implode(", ", $reason)
        ];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>🚩 Flagged Evaluations (in %)</title>
<link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
<style>
body {
    background-color: #f3f4f6;
    position: relative;
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
<body class="content p-8">
<div class="max-w-7xl mx-auto bg-white shadow-lg rounded-lg p-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">🚩 Flagged Faculty Evaluations</h2>
        <form action="../php/admin_dashboard.php" method="post">
            <button type="submit"
                class="bg-blue-500 hover:bg-blue-600 text-white font-semibold px-4 py-2 rounded">
                🔙 Back to Dashboard
            </button>
        </form>
    </div>

    <?php if (empty($flagged)): ?>
        <p class="text-green-600 text-lg font-semibold">✅ No anomalies or mismatched comments detected.</p>
    <?php else: ?>
        <div class="overflow-x-auto">
            <table class="min-w-full border border-gray-300 rounded-lg">
                <thead class="bg-blue-200">
                    <tr>
                        <th class="border px-4 py-2 text-left">Faculty</th>
                        <th class="border px-4 py-2 text-center">Average Score (%)</th>
                        <th class="border px-4 py-2 text-left">Comments</th>
                        <th class="border px-4 py-2 text-left">Reason</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($flagged as $f): 
                        $percent = round(($f['avg_rating'] / 5) * 100, 2);
                    ?>
                    <tr class="bg-red-50 hover:bg-red-100">
                        <td class="border px-4 py-2 font-semibold"><?= htmlspecialchars($f['faculty']) ?></td>
                        <td class="border px-4 py-2 text-center"><?= $percent ?>%</td>
                        <td class="border px-4 py-2"><?= htmlspecialchars($f['comments']) ?></td>
                        <td class="border px-4 py-2 text-red-700"><?= htmlspecialchars($f['reason']) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
</body>
</html>
