<?php
session_start();
require_once '../php/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student_id = $_SESSION['student_id'] ?? null;
    $faculty_id = $_POST['faculty_id'] ?? null;
    $academic_year_id = $_POST['academic_year_id'] ?? null;
    $class_id = $_POST['class_id'] ?? null;
    $subject_id = $_POST['subject_id'] ?? null;
    $ratings = $_POST['ratings'] ?? [];
    $comments = $_POST['comments'] ?? [];

    if (!$student_id || !$faculty_id) {
        die("Missing student or faculty ID.");
    }

    $stmt = $pdo->prepare("
        INSERT INTO evaluation_report 
        (student_id, faculty_id, academic_year_id, class_id, subject_id, criteria_id, question_id, rating, comment) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");

    foreach ($ratings as $question_id => $rating) {
        $criteria_id = $_POST['criteria_id'][$question_id] ?? null;
        $comment = $comments[$question_id] ?? null;

        $stmt->execute([
            $student_id,
            $faculty_id,
            $academic_year_id,
            $class_id,
            $subject_id,
            $criteria_id,
            $question_id,
            $rating,
            $comment
        ]);
    }

    echo "<script>alert('Evaluation submitted successfully!'); window.location.href='student_dashboard.php';</script>";
    exit;
}
?>
