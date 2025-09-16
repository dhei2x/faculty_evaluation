<?php
session_start();
require_once '../php/db.php';

// 🔒 Session check
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'students' || !isset($_SESSION['student_id'])) {
    header("Location: ../php/login.php");
    exit;
}

$studentId   = $_SESSION['student_id'];
$studentName = $_SESSION['student_name'] ?? '';

// ✅ Handle popup close
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['close_welcome'])) {
    unset($_SESSION['welcome']); // remove popup
    header("Location: student_dashboard.php"); // reload to hide popup
    exit;
}

// ✅ Flash messages
$successMsg = $_GET['success'] ?? '';
$errorMsg   = $_GET['error'] ?? '';

// ✅ Fetch evaluations
$sql = "
SELECT 
    f.full_name AS faculty_name,
    ec.name AS criteria_name,
    q.text AS question_text,
    er.rating,
    er.comment,
    er.created_at
FROM evaluation_report er
JOIN faculties f ON er.faculty_id = f.id
JOIN questions q ON er.question_id = q.id
JOIN evaluation_criteria ec ON q.criteria_id = ec.id
WHERE er.student_id = ?
ORDER BY er.created_at DESC, f.full_name, ec.id, q.id
";
$stmt = $pdo->prepare($sql);
$stmt->execute([$studentId]);
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Group evaluations
$evaluations = [];
foreach ($data as $row) {
    $key = $row['faculty_name'] . '|' . $row['created_at'];
    $evaluations[$key]['faculty_name'] = $row['faculty_name'];
    $evaluations[$key]['created_at']   = $row['created_at'];
    $evaluations[$key]['comment']      = $row['comment'];
    $evaluations[$key]['items'][] = [
        'criteria' => $row['criteria_name'],
        'question' => $row['question_text'],
        'rating'   => $row['rating']
    ];
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Student Dashboard</title>
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
            background-size: 900px 900px;
            opacity: 0.09;
            pointer-events: none;
            z-index: 0;
        }
        .content {
            position: relative;
            z-index: 1;
        }
    </style>
</head>
<body class="bg-gray-100 flex">

<?php include 'student_sidebar.php'; ?>
<?php if (!empty($_SESSION['welcome'])): ?>
    <div id="welcomeToast" class="fixed top-4 right-4 bg-blue-200 text-white px-4 py-2 rounded shadow-lg z-50">
        <?= htmlspecialchars($_SESSION['welcome']) ?>
    </div>
    <script>
        setTimeout(() => {
            const toast = document.getElementById('welcomeToast');
            if (toast) toast.style.display = 'none';
        }, 2000); // hide after 2 seconds
    </script>
    <?php unset($_SESSION['welcome']); ?>
<?php endif; ?>


<div class="ml-64 p-6 w-full">
  
    <h2 class="text-xl font-semibold mb-4">Your Submitted Evaluations</h2>

    <?php if ($successMsg): ?>
        <div class="bg-green-100 text-green-800 px-4 py-2 rounded mb-4">
            <?= htmlspecialchars($successMsg) ?>
        </div>
    <?php endif; ?>

    <?php if ($errorMsg): ?>
        <div class="bg-red-100 text-red-800 px-4 py-2 rounded mb-4">
            <?= htmlspecialchars($errorMsg) ?>
        </div>
    <?php endif; ?>

    <?php if (empty($evaluations)): ?>
        <p class="text-gray-600">You haven't submitted any evaluations yet.</p>
    <?php else: ?>
        <?php foreach ($evaluations as $eval): ?>
            <div class="bg-white shadow p-5 mb-6 rounded-lg">
                <h3 class="text-lg font-semibold text-blue-800"><?= htmlspecialchars($eval['faculty_name']) ?></h3>
                <p class="text-sm text-gray-500 mb-2">
                    Submitted on <?= date('F j, Y, g:i a', strtotime($eval['created_at'])) ?>
                </p>

                <div class="mb-3">
                    <?php foreach ($eval['items'] as $item): ?>
                        <div class="mb-2">
                            <p><strong><?= htmlspecialchars($item['criteria']) ?>:</strong> <?= htmlspecialchars($item['question']) ?></p>
                            <p class="ml-4 text-yellow-600 font-semibold">Rating: <?= $item['rating'] ?>/5</p>
                        </div>
                    <?php endforeach; ?>
                </div>

                <?php if (!empty($eval['comment'])): ?>
                    <div class="bg-gray-50 p-3 border rounded text-sm">
                        <strong>Comment:</strong> <?= htmlspecialchars($eval['comment']) ?>
                    </div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<!-- ✅ Welcome Popup Modal -->
<?php if (!empty($_SESSION['welcome'])): ?>
<div id="welcomeModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center">
  <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-sm text-center">
    <h2 class="text-xl font-bold mb-4">🎉 Welcome</h2>
    <p class="mb-4"><?= htmlspecialchars($_SESSION['welcome']) ?></p>
    <form method="post">
      <button type="submit" name="close_welcome" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
        Continue
      </button>
    </form>
  </div>
</div>
<?php endif; ?>

</body>
</html>
