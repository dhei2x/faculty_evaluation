<?php
ob_start();
session_start();
include 'db.php';

$error = "";

// Already logged-in user
if (isset($_SESSION['user_id']) && isset($_SESSION['role'])) {
    $role = strtolower($_SESSION['role']);
    if ($role === 'admin') {
        header("Location: admin_dashboard.php"); exit();
    } elseif ($role === 'faculty') {
        header("Location: ../faculty/faculty_dashboard.php"); exit();
    } elseif ($role === 'students') {
        header("Location: ../studentlog/student_dashboard.php"); exit();
    }
}

// Process login
// if ($_SERVER["REQUEST_METHOD"] === "POST" && ) {
if ($_SERVER["REQUEST_METHOD"] === "POST" ) {

    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username && $password) {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        


        // if ($user && password_verify($password, $user['password'])) {
          if ($user && password_verify($password, $user['password'])) {
          var_dump($user);
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = strtolower($user['role']);

            if (!empty($_POST['remember'])) {
                setcookie("remember_user", $username, time() + (86400 * 30), "/");
            }
            switch ($_SESSION['role']) {
                case 'admin': header("Location: admin_dashboard.php"); exit();
                case 'faculty': header("Location: faculty_dashboard.php"); exit();
                case 'students': header("Location: ../studentlog/student_dashboard.php"); exit();
                // default: $error = "Invalid user role."; break;
                default: header("Location: ../studentlog/student_dashboard.php"); exit();
            }
        } else {
            $error = "Invalid username or password.";
        }
    } else {
        $error = "Please fill in both fields.";
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
<body class="min-h-screen flex items-center justify-center p-4">
  <div class="overlay"></div>

  <div class="w-full max-w-md">
    <div class="login-card shadow-lg p-8">
      <div class="text-center mb-6">
        <img src="logo.png" alt="Logo" class="logo mb-2">
        <h2 class="text-xl font-bold text-gray-700">Log in to your account</h2>
      </div>

      <?php if (!empty($error)): ?>
        <p class="text-red-600 text-center mb-4"><?php echo htmlspecialchars($error); ?></p>
      <?php endif; ?>

      <form method="POST" class="space-y-4">
        <div class="relative">
          <input type="text" name="username" placeholder="Username" required
            class="pl-10 pr-4 h-12 w-full border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-300"
            value="<?php echo isset($_COOKIE['remember_user']) ? htmlspecialchars($_COOKIE['remember_user']) : ''; ?>" />
          <span class="absolute left-3 top-3 text-gray-400">👤</span>
        </div>

        <div class="relative">
          <input type="password" name="password" placeholder="Password" required
            class="pl-10 pr-4 h-12 w-full border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-300"/>
          <span class="absolute left-3 top-3 text-gray-400">🔒</span>
        </div>

        <div class="flex items-center justify-between text-sm">
          <label class="flex items-center space-x-2">
            <input type="checkbox" name="remember" class="h-4 w-4"
              <?php echo isset($_COOKIE['remember_user']) ? 'checked' : ''; ?>>
            <span>Remember me</span>
          </label>
          <button type="submit" name="login" class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-2 rounded">
            Log in
          </button>
        </div>
      </form>

      <!-- ✅ Added Reset Password Section -->
      <div class="text-center mt-4 space-y-1 text-sm">
        <p class="text-gray-600">Forgot your password?</p>
        <a href="reset_password.php" class="text-blue-500 hover:underline">Click here to reset it</a>
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
