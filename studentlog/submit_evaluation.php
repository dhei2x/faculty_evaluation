<?php
session_start();
require_once '../php/db.php';

if (!isset($_SESSION['student_id'])) {
    die("Unauthorized access.");
}

$student_id = $_SESSION['student_id'];
$faculty_id = $_POST['faculty_id'] ?? null;
$criteria = $_POST['criteria'] ?? [];
$academic_year_id = $_POST['academic_year_id'] ?? null;

if (!$student_id || !$faculty_id || !$academic_year_id || empty($criteria)) {
    die("Missing data.");
}

// Prevent duplicate
$checkStmt = $pdo->prepare("
    SELECT COUNT(*) FROM evaluation_report
    WHERE student_id = ? AND faculty_id = ? AND academic_year_id = ?
");
$checkStmt->execute([$student_id, $faculty_id, $academic_year_id]);
if ($checkStmt->fetchColumn() > 0) {
    header("Location: student_dashboard.php?error=already_evaluated");
    exit();
}

$comment = trim($_POST['comment'] ?? '');

// Prepare insert
$insertStmt = $pdo->prepare("
    INSERT INTO evaluation_report 
    (student_id, faculty_id, academic_year_id, criteria_id, question_id, rating, comment, created_at)
    VALUES (?, ?, ?, ?, ?, ?, ?, NOW())
");

foreach ($criteria as $criteria_id => $questions) {
    foreach ($questions as $question_id => $rating) {
        if ($rating >= 1 && $rating <= 5) {
            $insertStmt->execute([
                $student_id,
                $faculty_id,
                $academic_year_id,
                $criteria_id,
                $question_id,
                $rating,
                $comment
            ]);
        }
    }
}

header("Location: student_dashboard.php?success=1");
exit();
