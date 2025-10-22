<?php
session_start();
require_once '../php/db.php';
require_once '../php/auth.php';
require_role(['admin', 'superadmin']);


$faculty_id = $_GET['id'] ?? null;
if (!$faculty_id) {
    header('Location: faculties.php');
    exit();
}

// Fetch faculty and user data
$stmt = $pdo->prepare("
    SELECT f.*, u.username, u.email 
    FROM faculties f 
    JOIN users u ON f.faculty_id = u.username 
    WHERE f.faculty_id = ?
");
$stmt->execute([$faculty_id]);
$faculty = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$faculty) {
    echo "<p style='color:red;text-align:center;'>Faculty not found.</p>";
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $last_name = trim($_POST['last_name']);
    $first_name = trim($_POST['first_name']);
    $middle_name = trim($_POST['middle_name']);
    $department = trim($_POST['department']);
    $position = trim($_POST['position']);

    try {
        $pdo->beginTransaction();

        $pdo->prepare("UPDATE users SET username = ?, email = ? WHERE username = ?")
            ->execute([$username, $email, $faculty_id]);

        $pdo->prepare("
            UPDATE faculties 
            SET faculty_id = ?, last_name = ?, first_name = ?, middle_name = ?, department = ?, position = ? 
            WHERE faculty_id = ?
        ")->execute([$username, $last_name, $first_name, $middle_name, $department, $position, $faculty_id]);

        $pdo->commit();
        header("Location: faculties.php");
        exit;
    } catch (PDOException $e) {
        $pdo->rollBack();
        echo "<div class='text-red-600 text-center'>Error: " . $e->getMessage() . "</div>";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>Edit Faculty</title>
  <script src="https://cdn.tailwindcss.com"></script>

  <style>
    body {
        position: relative;
        background-color: #f3f4f6;
        min-height: 100vh;
        display: flex;
        justify-content: center;
        align-items: center;
    }

    /* Watermark background */
    body::before {
        content: "";
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: url('../php/logo.png') no-repeat center center;
        background-size: 900px 900px;
        opacity: 0.09;
        pointer-events: none;
        z-index: 0;
    }

    /* White container that covers the logo area */
    .form-container {
        position: relative;
        z-index: 1;
        background: white;
        padding: 2rem;
        border-radius: 0.75rem;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        width: 100%;
        max-width: 550px;
    }
  </style>
</head>
<body>
  <div class="form-container">
    <h2 class="text-2xl font-bold mb-4 text-center">Edit Faculty</h2>
    <form method="POST" class="space-y-3">
      <input name="username" value="<?= htmlspecialchars($faculty['username']) ?>" class="w-full border rounded p-2" required>
      <input name="email" type="email" value="<?= htmlspecialchars($faculty['email']) ?>" class="w-full border rounded p-2" required>

      <div class="grid grid-cols-3 gap-2">
        <input name="last_name" value="<?= htmlspecialchars($faculty['last_name']) ?>" placeholder="Last Name" class="border rounded p-2" required>
        <input name="first_name" value="<?= htmlspecialchars($faculty['first_name']) ?>" placeholder="First Name" class="border rounded p-2" required>
        <input name="middle_name" value="<?= htmlspecialchars($faculty['middle_name']) ?>" placeholder="Middle Name" class="border rounded p-2">
      </div>

      <input name="department" value="<?= htmlspecialchars($faculty['department']) ?>" placeholder="Department" class="w-full border rounded p-2" required>
      <input name="position" value="<?= htmlspecialchars($faculty['position']) ?>" placeholder="Position" class="w-full border rounded p-2" required>

      <div class="flex justify-between">
        <button class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">Update Faculty</button>
        <a href="faculties.php" class="text-gray-600 self-center hover:underline">Cancel</a>
      </div>
    </form>
  </div>
</body>
</html>
