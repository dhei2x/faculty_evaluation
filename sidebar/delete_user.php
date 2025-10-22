<?php
session_start();
require_once '../php/db.php';
require_once '../php/auth.php';
require_role(['admin', 'superadmin']);


// ✅ Ensure user ID is passed
if (!isset($_GET['id'])) {
    header("Location: users.php");
    exit();
}

$id = (int) $_GET['id'];

// ✅ Prevent deleting Super Admin
if ($id === 1) {
    echo "<div style='padding:20px; color:red; font-weight:bold;'>⚠️ You cannot delete the Super Admin account.</div>";
    echo "<a href='users.php' style='color:blue; text-decoration:underline;'>Back to Users</a>";
    exit();
}

// ✅ Prevent deletion of currently logged-in user
if (isset($_SESSION['user']['id']) && $_SESSION['user']['id'] == $id) {
    echo "⚠️ You cannot delete your own account.";
    exit();
}

// ✅ Fetch user role
$stmt = $pdo->prepare("SELECT role, username FROM users WHERE id = :id");
$stmt->execute(['id' => $id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    echo "⚠️ User not found.";
    exit();
}

// ✅ Prevent deleting another admin
if ($user['role'] === 'admin') {
    echo "⚠️ You cannot delete another admin.";
    exit();
}

// ✅ Delete related records
if ($user['role'] === 'students') {
    $stmt = $pdo->prepare("DELETE FROM students WHERE student_id = :student_id");
    $stmt->execute(['student_id' => $user['username']]);
}

if ($user['role'] === 'faculty') { // 👈 fixed: your role is "faculty" not "faculties"
    $stmt = $pdo->prepare("DELETE FROM faculties WHERE faculty_id = :faculty_id");
    $stmt->execute(['faculty_id' => $user['username']]);
}

// ✅ Finally delete user
$stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
$stmt->execute([$id]);

header("Location: users.php");
exit();
?>
