<?php

session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'teacher') {
    header("Location: login.php");
    exit();
}

$pdo = new PDO('mysql:host=localhost;faculty_evaluation', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$user_id = $_SESSION['user_id'];

// Insert evaluation record
$stmt = $pdo->prepare("INSERT INTO evaluations (user_id) VALUES (?)");
$stmt->execute([$user_id]);

echo "Thank you for evaluating!";
?>
