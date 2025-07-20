<?php
session_start();
var_dump($_SESSION);
exit;

require_once '../php/db.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'students') {
    header("Location: ../php/login.php");
    exit();
}

$student_id = $_SESSION['student_id'] ?? null;
$faculty_id = $_POST['faculty_id'] ?? null;
$criteria = $_POST['criteria'] ?? [];

if (!$student_id || !$faculty_id || empty($criteria)) {
    die("Missing data.");
}

// Prevent duplicate submission
$checkStmt = $pdo->prepare("SELECT COUNT(*) FROM evaluation_report WHERE student_id = ? AND faculty_id = ?");
$checkStmt->execute([$student_id, $faculty_id]);
if ($checkStmt->fetchColumn() > 0) {
    die("You have already evaluated this faculty.");
}
$comment = trim($_POST['comment'] ?? null);

// Insert one row per question
$insertStmt = $pdo->prepare("INSERT INTO evaluation_report 
    (student_id, faculty_id, criteria_id, question_id, rating, comment, created_at)
    VALUES (?, ?, ?, ?, ?, ?, NOW())");

foreach ($criteria as $criteria_id => $questions) {
    foreach ($questions as $question_id => $rating) {
        if ($rating >= 1 && $rating <= 5) {
            $insertStmt->execute([
                $student_id,
                $faculty_id,
                $criteria_id,
                $question_id,
                $rating,
                $comment // comment saved with each row
            ]);
        }
    }
}


header("Location: student_dashboard.php?success=1");
exit();
