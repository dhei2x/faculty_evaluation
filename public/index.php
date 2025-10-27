<?php
// index.php - main router entry point

session_start();

$page = $_GET['page'] ?? 'index';

switch ($page) {
    case 'index':
        // If you want this page to just redirect, you can remove this case
        // Or just show a homepage
        include 'pages/Index.php';
        break;
    case 'login':
        include 'pages/Login.php';
        break;
    case 'dashboard':
        // You can add auth check here before including
        include 'pages/Dashboard.php';
        break;
    default:
        http_response_code(404);
        include 'pages/NotFound.php';
        break;
}
?>
<?php
// pages/Index.php

// Redirect immediately to login page
header("Location: login.php");
exit;
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Redirecting...</title>
  <style>
    body {
      min-height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
      background: linear-gradient(to bottom right, #f1f5f9, #e2e8f0);
      font-family: system-ui, sans-serif;
      color: #334155;
      text-align: center;
    }
    .spinner {
      animation: spin 1s linear infinite;
      height: 64px;
      width: 64px;
      margin-right: 12px;
      vertical-align: middle;
      color: #64748b;
    }
    @keyframes spin {
      from { transform: rotate(0deg); }
      to { transform: rotate(360deg); }
    }
    .spinner circle.opacity-30 {
      opacity: 0.3;
      stroke-width: 5;
      fill: none;
      stroke: currentColor;
    }
    .spinner circle.text-slate-600 {
      stroke: currentColor;
      stroke-width: 5;
      fill: none;
      stroke-dasharray: 100;
      stroke-dashoffset: 75;
      color: #475569;
    }
    h1 {
      display: flex;
      justify-content: center;
      align-items: center;
      gap: 12px;
      font-weight: 600;
      font-size: 1.5rem;
      max-width: 400px;
      margin: 0 auto;
    }
  </style>
</head>
<body>
  <h1>
    <svg class="spinner" viewBox="0 0 50 50" aria-hidden="true" role="img" focusable="false">
      <circle class="opacity-30" cx="25" cy="25" r="20"></circle>
      <circle class="text-slate-600" cx="25" cy="25" r="20"></circle>
    </svg>
    Redirecting to Faculty Evaluation System...
  </h1>
</body>
</html>
