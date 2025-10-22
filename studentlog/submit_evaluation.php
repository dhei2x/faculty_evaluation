<?php
session_start();
require_once '../php/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student_id = $_SESSION['student_id'] ?? null;
    $faculty_id = $_POST['faculty_id'] ?? null;
    $academic_year_id = $_POST['academic_year_id'] ?? null;
    $ratings = $_POST['ratings'] ?? [];
    $criteria_ids = $_POST['criteria_id'] ?? [];
    // form has comment[] in your form; accept both array and direct string
    $rawComment = $_POST['comment'] ?? '';
    if (is_array($rawComment)) {
        $overallComment = trim($rawComment[0] ?? '');
    } else {
        $overallComment = trim($rawComment);
    }

    if (!$student_id || !$faculty_id || !$academic_year_id) {
        die("Missing student, faculty or academic year ID.");
    }

    // Insert rating + optional per-question comment (we attach overall comment to the first inserted question row)
    $stmt = $pdo->prepare("
        INSERT INTO evaluation_report 
        (student_id, faculty_id, academic_year_id, criteria_id, question_id, rating, comment)
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");

    $first = true;
    foreach ($ratings as $question_id => $rating) {
        $criteria_id = $criteria_ids[$question_id] ?? null;

        // ensure criteria_id exists; skip if it doesn't (shouldn't happen if form hidden inputs are correct)
        if (empty($criteria_id)) {
            continue;
        }

        // Put the overall comment on the first question row (avoids inserting NULL into criteria_id)
        $commentText = '';
        if ($first && $overallComment !== '') {
            $commentText = $overallComment;
        }

        $stmt->execute([
            $student_id,
            $faculty_id,
            $academic_year_id,
            $criteria_id,
            $question_id,
            $rating,
            $commentText // store empty string if no comment or if not the first question
        ]);

        $first = false;
    }

    echo "<script>alert('Evaluation submitted successfully!'); window.location.href='student_dashboard.php';</script>";
    exit;
}
?>
