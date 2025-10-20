<?php
session_start();
require_once '../php/db.php';

// Ensure student is logged in
if (!isset($_SESSION['student_id'])) {
    die("Session expired. Please log in again.");
}

$student_id = $_SESSION['student_id'];

// ✅ Get active academic year
$ayStmt = $pdo->query("SELECT id, year, semester FROM academic_years WHERE is_active = 1 LIMIT 1");
$activeAY = $ayStmt->fetch(PDO::FETCH_ASSOC);

if (!$activeAY) {
    die("No active academic year found.");
}

// ✅ Faculties not yet evaluated
$facultyStmt = $pdo->prepare("
    SELECT 
        id, 
        CONCAT(first_name, ' ', COALESCE(middle_name, ''), ' ', last_name) AS full_name
    FROM faculties
    WHERE id NOT IN (
        SELECT faculty_id FROM evaluation_report
        WHERE student_id = ? AND academic_year_id = ?
    )
");
$facultyStmt->execute([$student_id, $activeAY['id']]);
$faculties = $facultyStmt->fetchAll(PDO::FETCH_ASSOC);

// ✅ Fetch classes
$classStmt = $pdo->query("SELECT id, class_name FROM classes ORDER BY class_name ASC");
$classes = $classStmt->fetchAll(PDO::FETCH_ASSOC);

// ✅ Fetch subjects
$subjectStmt = $pdo->query("SELECT id, code, description FROM subjects ORDER BY code ASC");
$subjects = $subjectStmt->fetchAll(PDO::FETCH_ASSOC);

// ✅ Criteria and questions
$criteriaStmt = $pdo->query("
    SELECT c.id AS criteria_id, c.name AS criteria_name, q.id AS question_id, q.text AS question_text
    FROM evaluation_criteria c
    JOIN questions q ON q.criteria_id = c.id
    ORDER BY c.id, q.id
");

$criteriaMap = [];
while ($row = $criteriaStmt->fetch(PDO::FETCH_ASSOC)) {
    $cid = $row['criteria_id'];
    if (!isset($criteriaMap[$cid])) {
        $criteriaMap[$cid] = ['name' => $row['criteria_name'], 'questions' => []];
    }
    $criteriaMap[$cid]['questions'][] = ['id' => $row['question_id'], 'text' => $row['question_text']];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Faculty Evaluation</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        body {
            position: relative;
            background-color: #f3f4f6; /* gray background */
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
        .content {
            position: relative;
            z-index: 1;
        }
        .card {
            background-color: #ffffff;
            padding: 1rem;
            border-radius: 0.5rem;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
        }
        .card + .card {
            margin-top: 1rem;
        }
    </style>
</head>

<body class="min-h-screen p-6">
<div class="max-w-4xl mx-auto content">

    <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-bold">Faculty Evaluation</h1>
        <a href="../studentlog/student_dashboard.php" 
           class="inline-block bg-blue-200 hover:bg-gray-300 text-gray-800 px-4 py-2 rounded">
            ← Back to Dashboard
        </a>
    </div>

    <p class="text-sm text-gray-600 mb-4">
        Academic Year: <?= htmlspecialchars($activeAY['year'] . ' - ' . $activeAY['semester']) ?>
    </p>

    <?php if (empty($faculties)): ?>
        <div class="card text-red-600 font-semibold">
            You have evaluated all faculty for this academic year.
        </div>
    <?php else: ?>
        <form action="submit_evaluation.php" method="POST" class="space-y-4">
            <input type="hidden" name="academic_year_id" value="<?= $activeAY['id'] ?>">

            <!-- ✅ Faculty Select -->
            <div class="card">
                <label class="block font-semibold mb-2">Select Faculty</label>
                <select name="faculty_id" required class="border p-2 rounded w-full">
                    <option value="">-- Choose Faculty --</option>
                    <?php foreach ($faculties as $faculty): ?>
                        <option value="<?= $faculty['id'] ?>"><?= htmlspecialchars($faculty['full_name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- ✅ Class Select -->
            <div class="card">
                <label class="block font-semibold mb-2">Select Class</label>
                <select name="class_id" required class="border p-2 rounded w-full">
                    <option value="">-- Choose Class --</option>
                    <?php foreach ($classes as $class): ?>
                        <option value="<?= $class['id'] ?>"><?= htmlspecialchars($class['class_name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- ✅ Subject Select -->
            <div class="card">
                <label class="block font-semibold mb-2">Select Subject</label>
                <select name="subject_id" required class="border p-2 rounded w-full">
                    <option value="">-- Choose Subject --</option>
                    <?php foreach ($subjects as $subject): ?>
                        <option value="<?= $subject['id'] ?>">
                            <?= htmlspecialchars($subject['code'] . ' - ' . substr($subject['description'], 0, 50)) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- ✅ Rating Legend -->
            <div class="card flex justify-between text-sm font-semibold text-gray-700">
                <span>1 - Poor</span>
                <span>2 - Fair</span>
                <span>3 - Good</span>
                <span>4 - Very Good</span>
                <span>5 - Excellent</span>
            </div>

            <!-- ✅ Criteria & Questions -->
            <?php foreach ($criteriaMap as $criteria_id => $criteria): ?>
                <div class="card">
                    <h3 class="font-bold mb-2"><?= htmlspecialchars($criteria['name']) ?></h3>
                    <?php foreach ($criteria['questions'] as $question): ?>
                        <div class="mb-3">
                            <label class="block mb-1"><?= htmlspecialchars($question['text']) ?></label>
                            <div class="flex space-x-4">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <label class="inline-flex items-center">
                                        <input type="radio" name="criteria[<?= $criteria_id ?>][<?= $question['id'] ?>]" value="<?= $i ?>" required>
                                        <span class="ml-1"><?= $i ?></span>
                                    </label>
                                <?php endfor; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endforeach; ?>

            <!-- ✅ Comment -->
            <div class="card">
                <div class="flex items-center justify-between mb-1">
                    <label class="block font-semibold">Additional Comments</label>
                    <span class="text-sm text-gray-500 italic">(optional)</span>
                </div>
                <textarea name="comment" rows="4" class="w-full border rounded p-2" placeholder="Write your feedback here..."></textarea>
            </div>

            <!--
