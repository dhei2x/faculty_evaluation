<?php
session_start();
require_once '../php/db.php';
require_once '../php/auth.php';
require_role('admin');

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $stmt = $pdo->prepare("DELETE FROM students WHERE id = ?");
    $stmt->execute([$id]);

    // also delete from users if you want
    // $pdo->prepare("DELETE FROM users WHERE id = ?")->execute([$id]);

    header("Location: students.php?deleted=1");
    exit;
}
?>
