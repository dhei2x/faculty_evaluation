<?php 
$current = basename($_SERVER['PHP_SELF']); 
?>

<aside class="w-64 bg-blue-200 text-gray-900 flex flex-col py-8 px-6 shadow-lg min-h-screen">
    <h2 class="text-2xl font-bold mb-8">Faculty Panel</h2>
    <nav class="flex flex-col space-y-2 text-base font-medium">
        <a href="faculty_dashboard.php"
           class="block px-4 py-2 rounded <?= $current == 'faculty_dashboard.php' ? 'bg-blue-400 text-white font-semibold' : 'hover:bg-blue-300'; ?>">
            Dashboard
        </a>
        <a href="faculty_evaluations.php"
           class="block px-4 py-2 rounded <?= $current == 'faculty_evaluations.php' ? 'bg-blue-400 text-white font-semibold' : 'hover:bg-blue-300'; ?>">
            My Evaluations
        </a>
        <a href="faculty_summary.php"
           class="block px-4 py-2 rounded <?= $current == 'faculty_summary.php' ? 'bg-blue-400 text-white font-semibold' : 'hover:bg-blue-300'; ?>">
            Summary
        </a>
        <a href="view_comments.php"
           class="block px-4 py-2 rounded <?= $current == 'view_comments.php' ? 'bg-blue-400 text-white font-semibold' : 'hover:bg-blue-300'; ?>">
            View Comments
        </a>
        <a href="faculty_profile.php"
           class="block px-4 py-2 rounded <?= $current == 'faculty_profile.php' ? 'bg-blue-400 text-white font-semibold' : 'hover:bg-blue-300'; ?>">
            Profile
        </a>
        <a href="../php/logout.php"
           class="block px-4 py-2 rounded mt-10 text-red-600 hover:bg-red-100">
            Logout
        </a>
    </nav>
</aside>
