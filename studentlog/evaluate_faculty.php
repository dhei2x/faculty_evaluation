<?php
// Replace with session and DB logic as needed
$faculty_name = "Prof. Jane Smith";
// $subject = "Introduction to Programming";
$academic_year = "2024–2025";
$semester = "1st Semester";

// Evaluation questions (You can load these from DB)
$evaluation = [
    "Teaching Effectiveness" => [
        "The teacher explains lessons clearly.",
        "The teacher encourages student participation and engagement.",
        "The teacher provides relevant examples and real-life applications."
    ],
    "Classroom Management" => [
        "The teacher starts and ends classes on time.",
        "The teacher maintains discipline and respects all students."
    ],
    "Professionalism" => [
        "The teacher is respectful and approachable.",
        "The teacher is well-prepared for every class."
    ]
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">

  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
  <div class="max-w-4xl mx-auto p-6 bg-white mt-8 shadow-md rounded-lg">

    <div class="mb-4">
      <p><strong>Faculty Name:</strong> <?= $faculty_name ?></p>
      <!-- <p><strong>Subject:</strong> <?= $subject ?></p> -->
      <p><strong>Academic Year:</strong> <?= $academic_year ?></p>
      <p><strong>Semester:</strong> <?= $semester ?></p>
    </div>

    <form action="submit_evaluation.php" method="POST">
      <?php
      $q_index = 1;
      foreach ($evaluation as $criteria => $questions) {
          echo "<h3 class='text-xl font-semibold mt-6 mb-2'>$criteria</h3>";
          foreach ($questions as $question) {
              echo "
              <div class='mb-4'>
                <label class='block font-medium mb-1'>$q_index. $question</label>
                <div class='flex space-x-4'>
                  ";
              for ($i = 1; $i <= 5; $i++) {
                  echo "
                    <label class='flex items-center space-x-1'>
                      <input type='radio' name='q$q_index' value='$i' required class='accent-blue-500'>
                      <span>$i</span>
                    </label>
                  ";
              }
              echo "</div></div>";
              $q_index++;
          }
      }
      ?>

      <!-- Open-ended Feedback -->
      <div class="mb-4">
        <label class="block font-medium mb-1">What are the teacher’s strengths?</l
