<?php
$current = basename($_SERVER['PHP_SELF']);
?>

<aside class="w-64 bg-blue-200 text-gray-900 h-screen shadow-md p-4">
  <h2 class="text-xl font-semibold mb-4">Admin Panel</h2>
  <ul class="space-y-1 text-sm">
    <li>
      <a href="/faculty_eval/php/admin_dashboard.php"
         class="block py-2 px-4 rounded <?= $current == 'admin_dashboard.php' ? 'bg-blue-400 text-white font-semibold' : 'hover:bg-blue-300'; ?>">
         Dashboard
      </a>
    </li>
    <li>
      <a href="/faculty_eval/sidebar/students.php"
         class="block py-2 px-4 rounded <?= $current == 'students.php' ? 'bg-blue-400 text-white font-semibold' : 'hover:bg-blue-300'; ?>">
         Manage Students
      </a>
    </li>
    <li>
      <a href="/faculty_eval/sidebar/faculties.php"
         class="block py-2 px-4 rounded <?= $current == 'faculties.php' ? 'bg-blue-400 text-white font-semibold' : 'hover:bg-blue-300'; ?>">
         Manage Faculties
      </a>
    </li>
    <li>
      <a href="/faculty_eval/sidebar/classes.php"
         class="block py-2 px-4 rounded <?= $current == 'classes.php' ? 'bg-blue-400 text-white font-semibold' : 'hover:bg-blue-300'; ?>">
         Manage Classes
      </a>
    </li>
    <li>
      <a href="/faculty_eval/sidebar/subjects.php"
         class="block py-2 px-4 rounded <?= $current == 'subjects.php' ? 'bg-blue-400 text-white font-semibold' : 'hover:bg-blue-300'; ?>">
         Manage Subjects
      </a>
    </li>
    <li>
      <a href="/faculty_eval/sidebar/academic_year.php"
         class="block py-2 px-4 rounded <?= $current == 'academic_year.php' ? 'bg-blue-400 text-white font-semibold' : 'hover:bg-blue-300'; ?>">
         Academic Year
      </a>
    </li>
    <li>
      <a href="/faculty_eval/sidebar/questionnaires.php"
         class="block py-2 px-4 rounded <?= $current == 'questionnaires.php' ? 'bg-blue-400 text-white font-semibold' : 'hover:bg-blue-300'; ?>">
         Questionnaires
      </a>
    </li>
    <li>
      <a href="/faculty_eval/sidebar/evaluation_criteria.php"
         class="block py-2 px-4 rounded <?= $current == 'evaluation_criteria.php' ? 'bg-blue-400 text-white font-semibold' : 'hover:bg-blue-300'; ?>">
         Evaluation Criteria
      </a>
    </li>
    <li>
      <a href="/faculty_eval/sidebar/evaluation_report.php"
         class="block py-2 px-4 rounded <?= $current == 'evaluation_report.php' ? 'bg-blue-400 text-white font-semibold' : 'hover:bg-blue-300'; ?>">
         Evaluation Reports
      </a>
    </li>
    <li>
      <a href="/faculty_eval/sidebar/flagged_evaluations.php"
         class="block py-2 px-4 rounded <?= $current == 'flagged_evaluations.php' ? 'bg-blue-400 text-white font-semibold' : 'hover:bg-blue-300'; ?>">
         Flagged
      </a>
    </li>
    <li>
      <a href="/faculty_eval/sidebar/users.php"
         class="block py-2 px-4 rounded <?= $current == 'users.php' ? 'bg-blue-400 text-white font-semibold' : 'hover:bg-blue-300'; ?>">
         Users
      </a>
    </li>
    <li>
      <a href="/faculty_eval/php/logout.php"
         class="block py-2 px-4 rounded hover:bg-red-100 text-red-600 mt-8">
         Logout
      </a>
    </li>
  </ul>
</aside>
