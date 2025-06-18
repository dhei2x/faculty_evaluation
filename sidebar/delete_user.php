<?php
session_start();
require_once '../php/db.php';
require_once '../php/auth.php';
require_role('admin');

// Ensure user ID is passed
if (!isset($_GET['id'])) {
    header("Location: users.php");
    exit();
}

$id = $_GET['id'];

// Prevent deletion of the currently logged-in user
if ($_SESSION['user']['id'] == $id) {
    echo "You cannot delete your own account.";
    exit();
}

// Fetch user role
$stmt = $pdo->prepare("SELECT role FROM users WHERE id = :id");
$stmt->execute(['id' => $id]);
$user = $stmt->fetch();

if (!$user) {
    echo "User not found.";
    exit();
}

// Prevent deleting another admin
if ($user['role'] === 'admin') {
    echo "Cannot delete another admin.";
    exit();
}

// Delete user
$stmt = $pdo->prepare("DELETE FROM users WHERE id = :id");
$stmt->execute(['id' => $id]);

header("Location: users.php");
exit();
