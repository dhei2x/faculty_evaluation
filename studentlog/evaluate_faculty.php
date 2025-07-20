<?php
session_start();
require_once '../php/db.php'; 

// Dummy values (replace with actual from DB/session)
$stmt = $pdo->query("SELECT id, full_name FROM faculties");
$faculties = $stmt->fetchAll(PDO::FETCH_ASSOC);

$academic_year = "2024–2025";
$semester = "1st Semester";

// Evaluation Criteria and Questions
$criteriaMap = [
    1 => [
        'name' => 'Teaching Effectiveness',
        'questions' => [
            ['id' => 1, 'text' => 'The teacher explains lessons clearly.'],
            ['id' => 2, 'text' => 'The teacher encourages student participation and engagement.'],
            ['id' => 3, 'text' => 'The teacher provides relevant examples and real-life applications.']
        ]
    ],
    2 => [
        'name' => 'Classroom Management',
        'questions' => [
            ['id' => 4, 'text' => 'The teacher starts and ends classes on time.'],
            ['id' => 5, 'text' => 'The teacher maintains discipline and respects all students.']
        ]
    ],
    3 => [
        'name' => 'Professionalism',
        'questions' => [
            ['id' => 6, 'text' => 'The teacher is respectful and approachable.'],
            ['id' => 7, 'text' => 'The teacher is well-prepared for every class.']
        ]
    ]
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Faculty Evaluation</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">

<div class="min-h-screen p-8">
    <div class="max-w-4xl mx-auto bg-white p-6 rounded shadow">
        <h1 class="text-2xl font-bold mb-4">Faculty Evaluation</h1>

        <form action="submit_evaluation.php" method="POST">

        <!-- Faculty Dropdown -->
<div class="mb-6">
    <label for="faculty_id" class="block font-semibold mb-2">Select Faculty:</label>
    <select name="faculty_id" id="faculty_id" required class="w-full border rounded p-2">
        <option value="">-- Select Faculty --</option>
        <?php foreach ($faculties as $f): ?>
            <option value="<?= $f['id'] ?>"><?= htmlspecialchars($f['full_name']) ?></option>
        <?php endforeach; ?>
    </select>
</div>


            <div class="mb-4">
                <p><strong>Academic Year:</strong> <?= htmlspecialchars($academic_year) ?></p>
                <p><strong>Semester:</strong> <?= htmlspecialchars($semester) ?></p>
            </div>

            <?php foreach ($criteriaMap as $criteria_id => $criteria): ?>
                <div class="mb-6">
                    <h3 class="text-lg font-semibold mb-2"><?= htmlspecialchars($criteria['name']) ?></h3>

                    <?php foreach ($criteria['questions'] as $q): ?>
                        <div class="mb-4">
                            <label class="block mb-1"><?= htmlspecialchars($q['text']) ?></label>
                            <div class="flex space-x-2">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <label class="flex items-center space-x-1">
                                        <input type="radio" name="criteria[<?= $criteria_id ?>][<?= $q['id'] ?>]" value="<?= $i ?>" required>
                                        <span><?= $i ?></span>
                                    </label>
                                <?php endfor; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endforeach; ?>

            <!-- Comments -->
            <div class="mb-6">
                <label for="comments" class="block font-semibold mb-2">Comments (Optional):</label>
                <textarea name="comments" id="comments" rows="4" class="w-full border rounded p-2" placeholder="Write any feedback..."></textarea>
            </div>

            <!-- Submit -->
            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">Submit Evaluation</button>
        </form>
    </div>
</div>

</body>
</html>
