<?php
session_start();
require_once '../php/db.php';
require_once '../php/auth.php';
require_role('admin');

$id = $_GET['id'] ?? null;

if ($id) {
    // Delete from faculties first to avoid FK constraints
    $pdo->prepare("DELETE FROM faculties WHERE user_id = ?")->execute([$id]);
    $pdo->prepare("DELETE FROM users WHERE id = ?")->execute([$id]);
}

header('Location: faculties.php');
exit();
