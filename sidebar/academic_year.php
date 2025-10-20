<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once '../php/db.php';
require_once '../php/auth.php';
require_role('admin');

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($_POST['action'] === 'add') {
        if (isset($_POST['is_active'])) {
            $pdo->query("UPDATE academic_years SET is_active = 0");
        }

        $stmt = $pdo->prepare("INSERT INTO academic_years (year, semester, is_active, created_at) 
                               VALUES (:year, :semester, :is_active, NOW())");
        $stmt->execute([
            'year' => $_POST['year'],
            'semester' => $_POST['semester'],
            'is_active' => isset($_POST['is_active']) ? 1 : 0
        ]);
    } elseif ($_POST['action'] === 'edit') {
        if (isset($_POST['is_active'])) {
            $pdo->query("UPDATE academic_years SET is_active = 0");
        }

        $check = $pdo->prepare("SELECT COUNT(*) FROM academic_years 
                                WHERE year = :year AND semester = :semester AND id != :id");
        $check->execute([
            'year' => $_POST['year'],
            'semester' => $_POST['semester'],
            'id' => $_POST['id']
        ]);

        if ($check->fetchColumn() > 0) {
            $_SESSION['error'] = "The academic year and semester already exist.";
        } else {
            $stmt = $pdo->prepare("UPDATE academic_years 
                                   SET year = :year, semester = :semester, is_active = :is_active 
                                   WHERE id = :id");
            $stmt->execute([
                'year' => $_POST['year'],
                'semester' => $_POST['semester'],
                'is_active' => isset($_POST['is_active']) ? 1 : 0,
                'id' => $_POST['id']
            ]);
        }
    } elseif ($_POST['action'] === 'delete') {
        $stmt = $pdo->prepare("DELETE FROM academic_years WHERE id = :id");
        $stmt->execute(['id' => $_POST['id']]);
    }

    header("Location: academic_year.php");
    exit();
}

// Fetch all academic years
$years = $pdo->query("SELECT * FROM academic_years 
                      ORDER BY year DESC, FIELD(semester, '2nd', '1st', 'Summer')")
             ->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Manage Academic Years</title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
  <style>
    body {
      position: relative;
      background-color: #f3f4f6;
      min-height: 100vh;
      display: flex;
    }

    /* ✅ Background watermark like other pages */
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

    /* ✅ Matches faculty panel style */
    .content-wrapper {
      position: relative;
      z-index: 1;
      background: rgba(255, 255, 255, 0.85);
      margin: 2rem;
      padding: 2rem;
      border-radius: 0.75rem;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
      width: 100%;
      max-width: 950px;
    }

    button:hover {
      transform: scale(1.02);
      transition: transform 0.15s ease-in-out;
    }
  </style>
</head>

<body class="bg-gray-100 min-h-screen">
  <?php include '../php/admin_sidebar.php'; ?>

  <div class="flex-1 flex justify-center items-start">
    <div class="content-wrapper">
      <h1 class="text-3xl font-bold text-gray-800 mb-6">Manage Academic Years</h1>

      <?php if (isset($_SESSION['error'])): ?>
        <div class="bg-red-100 text-red-800 border border-red-300 p-3 mb-4 rounded">
          <?= $_SESSION['error']; unset($_SESSION['error']); ?>
        </div>
      <?php endif; ?>

      <!-- Add Form -->
      <form method="POST" class="flex flex-wrap items-center gap-3 mb-6 bg-white p-4 rounded shadow">
        <input type="hidden" name="action" value="add">
        <input type="text" name="year" required placeholder="e.g. 2024-2025" class="border p-2 rounded w-48">
        <select name="semester" class="border p-2 rounded w-32">
          <option value="1st">1st</option>
          <option value="2nd">2nd</option>
          <option value="Summer">Summer</option>
        </select>
        <label class="inline-flex items-center">
          <input type="checkbox" name="is_active" class="form-checkbox">
          <span class="ml-2">Set Active</span>
        </label>
        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
          + Add Year
        </button>
      </form>

      <!-- Academic Year List -->
      <div class="space-y-3">
        <?php foreach ($years as $y): ?>
          <div class="bg-white p-4 rounded shadow flex justify-between items-center <?= $y['is_active'] ? 'border-l-4 border-green-500' : '' ?>">
            <div class="flex items-center space-x-4 w-full">
              <?php if ($y['is_active']): ?>
                <span class="bg-green-500 text-white text-xs px-2 py-1 rounded">Active</span>
                <div class="text-gray-700 font-semibold">
                  <?= htmlspecialchars($y['year']) ?> - <?= htmlspecialchars($y['semester']) ?>
                </div>
              <?php else: ?>
                <form method="POST" class="flex flex-wrap items-center gap-2 w-full">
                  <input type="hidden" name="action" value="edit">
                  <input type="hidden" name="id" value="<?= $y['id'] ?>">
                  <input type="text" name="year" value="<?= htmlspecialchars($y['year']) ?>" class="border p-1 rounded w-40">
                  <select name="semester" class="border p-1 rounded w-28">
                    <option value="1st" <?= $y['semester'] === '1st' ? 'selected' : '' ?>>1st</option>
                    <option value="2nd" <?= $y['semester'] === '2nd' ? 'selected' : '' ?>>2nd</option>
                    <option value="Summer" <?= $y['semester'] === 'Summer' ? 'selected' : '' ?>>Summer</option>
                  </select>
                  <label class="flex items-center space-x-1">
                    <input type="checkbox" name="is_active">
                    <span>Set Active</span>
                  </label>
                  <button class="bg-green-500 text-white px-3 py-1 rounded hover:bg-green-600">Update</button>
                </form>
              <?php endif; ?>
            </div>

            <form method="POST" onsubmit="return confirm('Delete this academic year?')">
              <input type="hidden" name="action" value="delete">
              <input type="hidden" name="id" value="<?= $y['id'] ?>">
              <button class="bg-red-600 text-white px-3 py-1 rounded hover:bg-red-700">Delete</button>
            </form>
          </div>
        <?php endforeach; ?>

        <?php if (empty($years)): ?>
          <div class="text-center text-gray-500 py-4">No academic years added yet.</div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</body>
</html>
