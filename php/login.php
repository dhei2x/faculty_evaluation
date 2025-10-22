<?php
session_start();
require_once 'db.php';

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
        $error = "Username and password are required.";
    } else {
        // ✅ Fetch user by username
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['role'] = $user['role'];
            $_SESSION['username'] = $user['username'];

            // ✅ STUDENT LOGIN
            if ($user['role'] === 'students') {
                $stmt = $pdo->prepare("SELECT * FROM students WHERE student_id = ?");
                $stmt->execute([$user['username']]);
                $student = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($student) {
                    $_SESSION['student_id'] = $student['id'];
                    $_SESSION['student_name'] = $student['full_name'];
                    $_SESSION['welcome'] = "Welcome, " . $student['full_name'] . "!";
                    header("Location: ../studentlog/student_dashboard.php");
                    exit;
                } else {
                    $error = "Student record not found for this account.";
                }

            // ✅ FACULTY LOGIN
            } elseif ($user['role'] === 'faculty') {
                $fac = $pdo->prepare("SELECT faculty_id, first_name, middle_name, last_name FROM faculties WHERE faculty_id = ?");
                $fac->execute([$user['username']]);
                $faculty = $fac->fetch(PDO::FETCH_ASSOC);

                if ($faculty) {
                    $_SESSION['faculty_id'] = $faculty['faculty_id'];
                    $_SESSION['faculty_name'] = trim($faculty['first_name'] . ' ' . $faculty['middle_name'] . ' ' . $faculty['last_name']);
                    $_SESSION['welcome'] = "Welcome, " . $_SESSION['faculty_name'] . "!";
                    header("Location: ../faculty/faculty_dashboard.php");
                    exit;
                } else {
                    $error = "Faculty record not found for this account.";
                }

            // ✅ ADMIN LOGIN
            } elseif ($user['role'] === 'admin') {
                $_SESSION['welcome'] = "Welcome, Admin " . $user['username'] . "!";
                header("Location: ../php/admin_dashboard.php");
                exit;

            // ✅ SUPER ADMIN LOGIN
            } elseif ($user['role'] === 'superadmin' || $user['role'] === 'super_admin') {
                $_SESSION['welcome'] = "Welcome, Super Admin " . $user['username'] . "!";
                header("Location: ../php/admin_dashboard.php");
                exit;

            } else {
                $error = "User has an unknown role.";
            }
        } else {
            $error = "Invalid username or password.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Login</title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
  <style>
    body {
      background: url('gccbg.jpg') no-repeat center center fixed;
      background-size: cover;
    }
    .overlay {
      position: fixed;
      inset: 0;
      background-color: rgba(0, 0, 0, 0.4);
      backdrop-filter: blur(5px);
      z-index: -1;
    }
    .login-card {
      background-color: rgba(255, 255, 255, 0.96);
      border-radius: 1rem;
    }
    .logo {
      width: 80px;
      margin: 0 auto;
    }
  </style>
</head>
<body class="min-h-screen flex flex-col items-center justify-center p-4">
  <div class="overlay"></div>

  <div class="w-full max-w-md">
    <div class="login-card shadow-lg p-8">
      <div class="text-center mb-6">
        <img src="logo.png" alt="Logo" class="logo mb-2">
        <h2 class="text-xl font-bold text-gray-700">Log in to your account</h2>
      </div>

      <?php if (!empty($error)): ?>
        <p class="text-red-600 text-center mb-4"><?= htmlspecialchars($error) ?></p>
      <?php endif; ?>

      <form method="POST" class="space-y-4">
        <div>
          <input type="text" name="username" placeholder="Username" required
            class="px-4 h-12 w-full border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-300"/>
        </div>

        <div>
          <input type="password" name="password" placeholder="Password" required
            class="px-4 h-12 w-full border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-300"/>
        </div>

        <div class="flex items-center justify-between text-sm">
          <label class="flex items-center space-x-2">
            <input type="checkbox" name="remember" class="h-4 w-4">
            <span>Remember me</span>
          </label>
          <button type="submit" name="login" class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-2 rounded">
            Log in
          </button>
        </div>
      </form>

      <div class="text-center mt-4 text-sm">
        <a href="forgot_password.php" class="text-blue-500 hover:underline">Forgot password?</a>
      </div>

      <div class="mt-6 text-center text-sm">
        <p class="text-gray-600">Don't have an account?</p>
        <a href="register.php" class="inline-block mt-1 bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">
          Sign up here
        </a>
      </div>
    </div>
  </div>
</body>
</html>
