<?php
session_start();
require_once '../php/db.php';

// Ensure user is logged in and is a student
if (!isset($_SESSION['user']) || $_SESSION['role'] !== 'student') {
    header("Location: ../php/login.php");
    exit();
}

$faculty_id = $_POST['faculty_id'] ?? null;
$criteria_data = $_POST['criteria'] ?? [];
$student_id = $_SESSION['student_id'] ?? null;

// Validate inputs
if (!$faculty_id || !$student_id || empty($criteria_data)) {
    echo "<script>alert('Invalid submission. Please complete the form.'); window.history.back();</script>";
    exit();
}

// Check if this faculty was already evaluated
$checkStmt = $pdo->prepare("SELECT COUNT(*) FROM evaluation_report WHERE student_id = ? AND faculty_id = ?");
$checkStmt->execute([$student_id, $faculty_id]);

if ($checkStmt->fetchColumn() > 0) {
    echo "<script>alert('You have already evaluated this faculty.'); window.location.href='student_dashboard.php';</script>";
    exit();
}

// Prepare insert statement
$insertStmt = $pdo->prepare("
    INSERT INTO evaluation_report (student_id, faculty_id, criteria_id, question_id, rating)
    VALUES (:student_id, :faculty_id, :criteria_id, :question_id, :rating)
");

// Insert each rating
foreach ($criteria_data as $criteria_id => $questions) {
    foreach ($questions as $question_id => $rating) {
        if ($rating >= 1 && $rating <= 5) { // Optional: validate rating range
            $insertStmt->execute([
                'student_id'   => $student_id,
                'faculty_id'   => $faculty_id,
                'criteria_id'  => $criteria_id,
                'question_id'  => $question_id,
                'rating'       => $rating
            ]);
        }
    }
}

// Success redirect
header("Location: thank_you.php");
exit();
?>
