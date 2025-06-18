<!-- student_sidebar.php -->


<div class="w-64 bg-white h-screen shadow-md p-6 fixed hidden md:block">
    <h2 class="text-xl font-bold mb-6">Student Panel</h2>
    <nav class="space-y-4">
        <!-- Dashboard Link -->
        <a href="dashboard.php" class="flex items-center text-gray-700 hover:text-blue-600">
            <i class="fas fa-home mr-2"></i> Dashboard
        </a>

        <!-- Faculty Evaluation Link (mark active if this is the current page) -->
        <a href="submit_evaluation.php" class="flex items-center text-blue-600 font-semibold">
            <i class="fas fa-star mr-2 text-yellow-500"></i> Faculty Evaluation
        </a>
        
        <!-- <a href="../php/evaluate_faculty.php" class="flex items-center text-gray-700 hover:text-red-600">
             📋 Evaluate Now
        </a> -->

        <!-- Logout Link -->
        <a href="../php/logout.php" class="flex items-center text-gray-700 hover:text-red-600">
            <i class="fas fa-sign-out-alt mr-2"></i> Logout
        </a>

    </nav>
</div>
