<?php
session_start();
require_once '../php/db.php';
require_once '../php/auth.php';
require_role('admin');

$id = $_GET['id'] ?? null;
if ($id) {
    $stmt = $pdo->prepare("DELETE FROM students WHERE id = ?");
    $stmt->execute([$id]);
}

header("Location: students.php");
exit;
