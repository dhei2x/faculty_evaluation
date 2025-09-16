<?php
session_start();
require_once '../php/db.php';
require_once '../php/auth.php';
require_role('admin');

$faculty_id = $_GET['id'] ?? null;
if ($faculty_id) {
    $pdo->prepare("DELETE FROM faculties WHERE faculty_id = ?")->execute([$faculty_id]);
    $pdo->prepare("DELETE FROM users WHERE username = ?")->execute([$faculty_id]);
}
header('Location: faculties.php');
exit();
