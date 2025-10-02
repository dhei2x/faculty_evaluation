<?php
session_start();
require_once 'db.php';

$error = "";

// If no pending user, redirect to login
if (!isset($_SESSION['pending_user'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $enteredOtp = trim($_POST['otp'] ?? '');

    if ($enteredOtp === '' || !isset($_SESSION['otp'])) {
        $error = "Please enter the OTP.";
    } elseif (time() > $_SESSION['otp_expiry']) {
        $error = "OTP has expired. Please log in again.";
        session_unset();
        session_destroy();
    } elseif ($enteredOtp == $_SESSION['otp']) {
        // OTP correct
        $user = $_SESSION['pending_user'];

        $_SESSION['role'] = $user['role'];
        $_SESSION['username'] = $user['username'];

        if ($user['role'] === 'students') {
            $stmt = $pdo->prepare("SELECT * FROM students WHERE student_id = ?");
            $stmt->execute([$user['username']]);
            $student = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($student) {
                $_SESSION['student_id'] = $student['id']; // PK
                $_SESSION['student_name'] = $student['full_name'];
                $_SESSION['welcome'] = "Welcome, " . $student['full_name'] . "!";
                unset($_SESSION['otp'], $_SESSION['otp_expiry'], $_SESSION['pending_user']);
                header("Location: ../studentlog/student_dashboard.php");
                exit;
            }
        } elseif ($user['role'] === 'faculty') {
            $stmt = $pdo->prepare("SELECT * FROM faculties WHERE faculty_id = ?");
            $stmt->execute([$user['username']]);
            $faculty = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($faculty) {
                $_SESSION['faculty_id'] = $faculty['id']; // PK
                $_SESSION['faculty_name'] = $faculty['full_name'];
                $_SESSION['welcome'] = "Welcome, Prof. " . $faculty['full_name'] . "!";
                unset($_SESSION['otp'], $_SESSION['otp_expiry'], $_SESSION['pending_user']);
                header("Location: ../faculty/faculty_dashboard.php");
                exit;
            }
        }
    } else {
        $error = "Invalid OTP.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Verify OTP</title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">
  <div class="bg-white p-8 rounded shadow w-full max-w-md">
    <h2 class="text-xl font-bold mb-4 text-center">Enter Verification Code</h2>

    <?php if (!empty($error)): ?>
      <div class="bg-red-100 text-red-700 p-3 rounded mb-4"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" class="space-y-4">
      <input type="text" name="otp" placeholder="Enter 6-digit code"
        class="w-full border p-2 rounded" required />

      <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded">
        Verify
      </button>
    </form>

    <p class="mt-4 text-center text-sm text-gray-600">
      Didn’t receive the code? <a href="login.php" class="text-blue-500 hover:underline">Login again</a>
    </p>
  </div>
</body>
</html>
