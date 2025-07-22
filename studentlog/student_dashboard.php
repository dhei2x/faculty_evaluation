<?php
session_start();
require_once '../php/db.php';
if ($_SESSION['role'] !== 'students') {
    header("Location: ../php/login.php");
    exit();
}

$successMsg = '';
$errorMsg = '';

if (isset($_GET['success']) && $_GET['success'] == 1) {
    $successMsg = "Evaluation submitted successfully!";
} elseif (isset($_GET['error']) && $_GET['error'] == 'already_evaluated') {
    $errorMsg = "You have already submitted an evaluation for this faculty.";
}

$userID = $_SESSION['user_id'];

// Get student ID from student table
$stmt = $pdo->prepare("SELECT id FROM students WHERE user_id = ?");
$stmt->execute([$userID]);
$student = $stmt->fetch(PDO::FETCH_ASSOC);
$student_id = $student['id'] ?? null;

// Fetch submitted evaluations with breakdown
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
$stmt->execute([$student_id]);
$data = $stmt->fetchAll();

// Group data by faculty and submission time
$evaluations = [];
foreach ($data as $row) {
    $key = $row['faculty_name'] . '|' . $row['created_at'];
    $evaluations[$key]['faculty_name'] = $row['faculty_name'];
    $evaluations[$key]['created_at'] = $row['created_at'];
    $evaluations[$key]['comment'] = $row['comment'];
    $evaluations[$key]['items'][] = [
        'criteria' => $row['criteria_name'],
        'question' => $row['question_text'],
        'rating' => $row['rating']
    ];
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Student Dashboard - Evaluations</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 flex">

<?php include 'student_sidebar.php'; ?>

<!-- Main Content -->
<div class="ml-64 p-6 w-full">
    <h1 class="text-2xl font-bold mb-6">Your Submitted Evaluations</h1>

    <?php if (!empty($successMsg)): ?>
        <div class="bg-green-100 text-green-800 px-4 py-2 rounded mb-4">
            <?= htmlspecialchars($successMsg) ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($errorMsg)): ?>
        <div class="bg-red-100 text-red-800 px-4 py-2 rounded mb-4">
            <?= htmlspecialchars($errorMsg) ?>
        </div>
    <?php endif; ?>

    <?php if (empty($evaluations)): ?>
        <div class="text-gray-600">You haven't submitted any evaluations yet.</div>
    <?php else: ?>
        <?php foreach ($evaluations as $eval): ?>
            <div class="bg-white shadow p-5 mb-6 rounded-lg">
                <h2 class="text-lg font-semibold text-blue-800"><?= htmlspecialchars($eval['faculty_name']) ?></h2>
                <p class="text-sm text-gray-500 mb-2">Submitted on <?= date('F j, Y, g:i a', strtotime($eval['created_at'])) ?></p>

                <div class="mb-3">
                    <?php foreach ($eval['items'] as $item): ?>
                        <div class="mb-2">
                            <p class="text-gray-700"><strong><?= htmlspecialchars($item['criteria']) ?>:</strong> <?= htmlspecialchars($item['question']) ?></p>
                            <p class="ml-4 text-yellow-600 font-semibold">Rating: <?= $item['rating'] ?>/5</p>
                        </div>
                    <?php endforeach; ?>
                </div>

                <?php if (!empty($eval['comment'])): ?>
                    <div class="bg-gray-50 p-3 border rounded text-sm text-gray-800">
                        <strong>Comment:</strong> <?= htmlspecialchars($eval['comment']) ?>
                    </div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

</body>
</html>
