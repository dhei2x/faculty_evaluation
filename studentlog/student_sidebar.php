<?php 
$current = basename($_SERVER['PHP_SELF']); 
?>

<div class="w-64 bg-blue-100 h-screen shadow-md p-6 fixed hidden md:block">
    <h2 class="text-2xl font-bold mb-8 text-gray-800">Student Panel</h2>
    <nav class="flex flex-col space-y-4 text-lg font-medium">
        <a href="student_dashboard.php" 
           class="px-3 py-2 rounded transition 
           <?= $current == 'student_dashboard.php' ? 'bg-blue-300 text-white font-semibold' : 'hover:bg-blue-200'; ?>">
           Dashboard
        </a>
        <a href="evaluate_faculty.php" 
           class="px-3 py-2 rounded transition 
           <?= $current == 'evaluate_faculty.php' ? 'bg-yellow-400 text-white font-semibold' : 'hover:bg-yellow-200'; ?>">
           Evaluate Faculty
        </a>
        <a href="../php/logout.php" 
           class="px-3 py-2 rounded mt-10 transition text-red-600 
           hover:bg-red-200 hover:text-red-800">
           Logout
        </a>
    </nav>
</div>
