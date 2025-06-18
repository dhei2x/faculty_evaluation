<?php
include 'db.php';

// Set your desired admin username and password
$username = 'apple';
$email = 'apple@gcc.edu.ph';
$password = 'prettyapple'; // You can change this
$role = 'admin';

// Hash the password
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

// Check if user already exists
$stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
$stmt->execute([$username]);

if ($stmt->fetch()) {
    echo "❌ Admin user already exists.";
} else {
    // Insert the admin user
    $stmt = $pdo->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)");
    if ($stmt->execute([$username, $email, $hashedPassword, $role])) {
        echo "✅ Admin user created successfully.<br>";
        echo "Username: <strong>$username</strong><br>";
        echo "Password: <strong>$password</strong>";
    } else {
        echo "❌ Failed to create admin user.";
    }
}
?>
