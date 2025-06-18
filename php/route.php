<?php
// router.php or index.php

// Get the requested path, e.g. "/" or "/login"
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Remove trailing slash for consistency (optional)
$path = rtrim($path, '/');

switch ($path) {
    case '':
    case '/':
        include 'pages/Index.php';
        break;
    case '/login':
        include 'pages/Login.php';
        break;
    case '/dashboard':
        include 'pages/Dashboard.php';
        break;
    default:
        // Catch-all route
        http_response_code(404);
        include 'pages/NotFound.php';
        break;
}
?>