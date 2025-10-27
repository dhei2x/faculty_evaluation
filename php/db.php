<?php

// local credentials 
 $host = '127.0.0.1';
 $db   = 'faculty_evaluation';
 $user = 'root';
 $pass = '';
$charset = 'utf8mb4';


// infinity.free.credentials

// $db = 'if0_40258485_faculty_evaluation_dbmain';
// $user = 'if0_40258485';
// $pass = 'DU2ndRHeDF7BCJ';
// $host = 'sql204.infinityfree.com';
// $charset = 'utf8';


$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    
    die("DB Connection failed: " . $e->getMessage());
}
?>





