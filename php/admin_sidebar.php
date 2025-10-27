<?php
$current = basename($_SERVER['PHP_SELF']);
?>

<aside class="w-64 bg-blue-200 text-gray-900 h-screen shadow-md p-4">
  <h2 class="text-2xl font-bold mb-6">Admin Panel</h2>
  <ul class="space-y-2 text-lg font-medium">
    <li>
      <a href="../php/admin_dashboard.php"
         class="block py-3 px-4 rounded <?= $current == 'admin_dashboard.php' ? 'bg-blue-400 text-white font-semibold' : 'hover:bg-blue-300'; ?>">
         Dashboard
      </a>
    </li>
    <li>
      <a href="../sidebar/students.php"
         class="block py-3 px-4 rounded <?= $current == 'students.php' ? 'bg-blue-400 text-white font-semibold' : 'hover:bg-blue-300'; ?>">
         Manage Students
      </a>
    </li>
    <li>
      <a href="../sidebar/faculties.php"
         class="block py-3 px-4 rounded <?= $current == 'faculties.php' ? 'bg-blue-400 text-white font-semibold' : 'hover:bg-blue-300'; ?>">
         Manage Faculties
      </a>
    </li>
    <li>
      <a href="../sidebar/classes.php"
         class="block py-3 px-4 rounded <?= $current == 'classes.php' ? 'bg-blue-400 text-white font-semibold' : 'hover:bg-blue-300'; ?>">
         Manage Classes
      </a>
    </li>
    <li>
      <a href="../sidebar/subjects.php"
         class="block py-3 px-4 rounded <?= $current == 'subjects.php' ? 'bg-blue-400 text-white font-semibold' : 'hover:bg-blue-300'; ?>">
         Manage Subjects
      </a>
    </li>
    <li>
      <a href="../sidebar/academic_year.php"
         class="block py-3 px-4 rounded <?= $current == 'academic_year.php' ? 'bg-blue-400 text-white font-semibold' : 'hover:bg-blue-300'; ?>">
         Academic Year
      </a>
    </li>
    <li>
      <a href="../sidebar/questionnaires.php"
         class="block py-3 px-4 rounded <?= $current == 'questionnaires.php' ? 'bg-blue-400 text-white font-semibold' : 'hover:bg-blue-300'; ?>">
         Questionnaires
      </a>
    </li>
    <li>
      <a href="../sidebar/evaluation_criteria.php"
         class="block py-3 px-4 rounded <?= $current == 'evaluation_criteria.php' ? 'bg-blue-400 text-white font-semibold' : 'hover:bg-blue-300'; ?>">
         Evaluation Criteria
      </a>
    </li>
    <li>
      <a href="../sidebar/evaluation_report.php"
         class="block py-3 px-4 rounded <?= $current == 'evaluation_report.php' ? 'bg-blue-400 text-white font-semibold' : 'hover:bg-blue-300'; ?>">
         Evaluation Reports
      </a>
    </li>
    <li>
      <a href="../sidebar/flagged_evaluations.php"
         class="block py-3 px-4 rounded <?= $current == 'flagged_evaluations.php' ? 'bg-blue-400 text-white font-semibold' : 'hover:bg-blue-300'; ?>">
         Flagged
      </a>
    </li>
    <li>
      <a href="../sidebar/users.php"
         class="block py-3 px-4 rounded <?= $current == 'users.php' ? 'bg-blue-400 text-white font-semibold' : 'hover:bg-blue-300'; ?>">
         Users
      </a>
    </li>
    <li>
      <a href="../php/logout.php"
         class="block py-3 px-4 rounded hover:bg-red-100 text-red-600 mt-8 font-semibold">
         Logout
      </a>
    </li>
  </ul>
</aside>