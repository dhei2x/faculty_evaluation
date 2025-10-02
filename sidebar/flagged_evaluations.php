<?php 
session_start();
require_once '../php/db.php';
require_once '../php/auth.php';
require_role('admin');

// ✅ Mark reviewed
$showToast = false;
if (isset($_GET['reviewed']) && $_GET['reviewed'] == 1) {
    $_SESSION['reviewed_flagged'] = true;
    $showToast = true;
}

// ✅ Fetch evaluations (avg per student per faculty)
$sql = "
    SELECT er.student_id, er.faculty_id, f.full_name,
           AVG(er.rating) AS avg_rating,
           GROUP_CONCAT(DISTINCT er.comment SEPARATOR ' | ') AS comment
    FROM evaluation_report er
    JOIN faculties f ON er.faculty_id = f.id
    GROUP BY er.student_id, er.faculty_id
";
$data = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);

// ✅ Collect all ratings
$ratings = array_column($data, 'avg_rating');
sort($ratings);

// ✅ Mean & Stddev
$mean = (count($ratings) > 0) ? array_sum($ratings) / count($ratings) : 0;
$stddev = (count($ratings) > 0) 
    ? sqrt(array_sum(array_map(fn($r)=>pow($r-$mean,2),$ratings))/count($ratings)) 
    : 0;

// ✅ Quartiles & IQR for Tukey
function getQuantile($arr, $q) {
    if (empty($arr)) return 0;
    $pos = ($q * (count($arr) + 1)) - 1;
    $pos = max(0, min(count($arr) - 1, $pos));
    return $arr[(int)$pos];
}
$q1 = getQuantile($ratings, 0.25);
$q3 = getQuantile($ratings, 0.75);
$iqr = $q3 - $q1;
$lowerBound = $q1 - 1.5 * $iqr;
$upperBound = $q3 + 1.5 * $iqr;
$extremeLower = $q1 - 3 * $iqr;
$extremeUpper = $q3 + 3 * $iqr;

// ✅ Fetch flagged words from DB
$badwords = $pdo->query("SELECT word FROM flagged_words WHERE type = 'bad'")->fetchAll(PDO::FETCH_COLUMN);
$goodwords = $pdo->query("SELECT word FROM flagged_words WHERE type = 'good'")->fetchAll(PDO::FETCH_COLUMN);

// ✅ Analyze evaluations
$flagged = [];

foreach ($data as $row) {
    $reasons = [];
    $extreme = false;

    // Z-score anomaly
    $z = ($stddev > 0) ? ($row['avg_rating'] - $mean) / $stddev : 0;
    if (abs($z) > 2) $reasons[] = "⚠️ Z-score anomaly (z=" . round($z,2) . ")";

    // Tukey outlier
    if ($row['avg_rating'] < $lowerBound || $row['avg_rating'] > $upperBound) {
        $reasons[] = "⚠️ Tukey IQR outlier";
    }

    // Extreme Tukey
    if ($row['avg_rating'] < $extremeLower || $row['avg_rating'] > $extremeUpper) {
        $reasons[] = "🚨 Extreme outlier";
        $extreme = true;
    }

    // Bad word detection
    foreach ($badwords as $bw) {
        if (!empty($row['comment']) && stripos($row['comment'], $bw) !== false) {
            $reasons[] = "❌ Contains bad word ($bw)";
            break;
        }
    }

    // Good word detection
    foreach ($goodwords as $gw) {
        if (!empty($row['comment']) && stripos($row['comment'], $gw) !== false) {
            $reasons[] = "✅ Contains good word ($gw)";
            break;
        }
    }

    // High rating but has bad words
    if ($row['avg_rating'] >= 4 && !empty($row['comment'])) {
        foreach ($badwords as $bw) {
            if (stripos($row['comment'], $bw) !== false) {
                $reasons[] = "⚠️ High rating but negative comment";
                break;
            }
        }
    }

    // Low rating but has good words
    if ($row['avg_rating'] <= 2 && !empty($row['comment'])) {
        foreach ($goodwords as $gw) {
            if (stripos($row['comment'], $gw) !== false) {
                $reasons[] = "⚠️ Low rating but positive comment";
                break;
            }
        }
    }

    if (!empty($reasons)) {
        $row['z_score'] = round($z, 2);
        $row['extreme'] = $extreme;
        $row['reasons'] = implode(" | ", $reasons);
        $flagged[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Flagged Evaluations</title>
    <script src="https://cdn.tailwindcss.com"></script>
 <style>
        body {
            position: relative;
            background-color: #f3f4f6;
        }
        body::before {
            content: "";
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: url('../php/logo.png') no-repeat center center;
            background-size: 900px 900px;
            opacity: 0.09;
            pointer-events: none;
            z-index: 0;
        }
        .content { position: relative; z-index: 1; }
        .extreme { background-color: #fee2e2 !important; }
    </style>
</head>
<body class="bg-gray-100 min-h-screen">
    <div class="max-w-6xl mx-auto py-10 px-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-800">🚩 Flagged Evaluations</h1>
            <a href="../php/admin_dashboard.php" class="bg-blue-300 hover:bg-gray-400 text-gray-800 font-semibold px-4 py-2 rounded">
                ← Back to Dashboard
            </a>
        </div>

        <!-- ✅ Toast Notification -->
        <?php if ($showToast): ?>
        <div id="toast" class="fixed top-4 right-4 bg-green-500 text-white px-4 py-2 rounded shadow-lg z-50">
            ✅ Flagged evaluations reviewed
        </div>
        <script>
            setTimeout(() => {
                const toast = document.getElementById('toast');
                if (toast) toast.style.display = 'none';
            }, 2500);
        </script>
        <?php endif; ?>

        <div class="bg-white p-6 rounded shadow">
            <?php if (empty($flagged)): ?>
                <p class="text-green-600 font-semibold">✅ No suspicious evaluations found.</p>
            <?php else: ?>
                <p class="mb-4 text-red-600 font-medium">
                    ⚠️ <strong><?= count($flagged) ?></strong> suspicious evaluations detected.
                </p>

                <table class="table-auto w-full text-sm border border-gray-200">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-4 py-2 border">Faculty</th>
                            <th class="px-4 py-2 border">Avg Rating</th>
                            <th class="px-4 py-2 border">Z-Score</th>
                            <th class="px-4 py-2 border">Comment(s)</th>
                            <th class="px-4 py-2 border">Reason(s)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($flagged as $entry): ?>
                            <tr class="border-b <?= $entry['extreme'] ? 'extreme font-bold text-red-700' : 'hover:bg-red-50' ?>">
                                <td class="px-4 py-2 font-medium"><?= htmlspecialchars($entry['full_name']) ?></td>
                                <td class="px-4 py-2 text-center"><?= round($entry['avg_rating'], 2) ?></td>
                                <td class="px-4 py-2 text-center text-blue-600"><?= $entry['z_score'] ?></td>
                                <td class="px-4 py-2 text-gray-700"><?= htmlspecialchars($entry['comment']) ?></td>
                                <td class="px-4 py-2 text-red-700"><?= htmlspecialchars($entry['reasons']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
