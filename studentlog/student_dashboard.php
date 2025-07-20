<?php
session_start();
$successMsg = '';
$errorMsg = '';
require_once '../php/db.php';

if (isset($_GET['success']) && $_GET['success'] == 1) {
    $successMsg = "Evaluation submitted successfully!";
} elseif (isset($_GET['error']) && $_GET['error'] == 'already_evaluated') {
    $errorMsg = "You have already submitted an evaluation for this faculty.";
}

var_dump($_SESSION['role']);
var_dump(isset($_SESSION['user']));

if ($_SESSION['role'] !== 'students') {
    header("Location: ../php/login.php");
    exit();
}

$userID = $_SESSION['user_id'];


$stmt = $pdo->prepare("SELECT student_id FROM students WHERE id = ?");
$stmt->execute([$userID]);
$student_id = $stmt->fetch(PDO::FETCH_ASSOC);






// Fetch faculties not yet evaluated
// $facultyStmt = $pdo->prepare("
//     SELECT f.id, f.full_name, f.department
//     FROM faculties f
//     WHERE f.id NOT IN (
//         SELECT faculty_id FROM evaluation_report WHERE student_id = ?
//     )
// ");
$facultyStmt = $pdo->prepare("
    SELECT f.id, f.full_name, f.department
    FROM faculties f
");
$facultyStmt->execute();
$faculties = $facultyStmt->fetchAll();


//  ?????
// Fetch criteria and questions
// $criteriaStmt = $pdo->query("
//     SELECT c.id AS criteria_id, c.name AS criteria_name, q.id AS question_id, q.text AS question_text
//     FROM criteria c
//     JOIN questions q ON q.criteria_id = c.id
//     ORDER BY c.id, q.id
// ");
/// ????

$criteriaMap = [];
foreach ($criteriaMap as $row) {
    $criteriaMap[$row['criteria_id']]['name'] = $row['criteria_name'];
    $criteriaMap[$row['criteria_id']]['questions'][] = [
        'id' => $row['question_id'],
        'text' => $row['question_text']
    ];
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Student Dashboard - Evaluation</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <style>
        .star-rating .fa-star {
            color: #ccc;
            cursor: pointer;
        }
        .star-rating .fa-star.checked {
            color: #f59e0b;
        }
    </style>
    
</head>
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

<body class="bg-gray-100 flex">

<?php include 'student_sidebar.php'; ?>

<!-- Main Content -->
     
<div class="ml-64 p-6">
    <h1 class="text-2xl font-bold mb-4">Your Evaluations</h1>
    
    <?php if (empty($evaluations)): ?>
        <p class="text-gray-600">You haven't submitted any evaluations yet.</p>
    <?php else: ?>
        <?php foreach ($evaluations as $eval): ?>
            <div class="bg-white shadow p-4 mb-4 rounded">
                <h2 class="font-semibold"><?= htmlspecialchars($eval['faculty_name']) ?></h2>
                <p class="text-sm text-gray-500 mb-2">Submitted: <?= date('F j, Y, g:i a', strtotime($eval['created_at'])) ?></p>
                <?php if (!empty($eval['comment'])): ?>
                    <p><strong>Your Comment:</strong> <?= htmlspecialchars($eval['comment']) ?></p>
                <?php else: ?>
                    <p class="text-gray-500 italic">No comment provided.</p>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<!-- End of Main Content -->

</body>
</html>
