<?php
session_start();
require_once '../php/db.php';

// Ensure student is logged in
if (!isset($_SESSION['student_id'])) {
    die("Session expired. Please log in again.");
}

$student_id = $_SESSION['student_id'];

// Get active academic year
$ayStmt = $pdo->query("SELECT id, year, semester FROM academic_years WHERE is_active = 1 LIMIT 1");
$activeAY = $ayStmt->fetch(PDO::FETCH_ASSOC);

if (!$activeAY) {
    die("No active academic year found.");
}

// Faculties not yet evaluated
$facultyStmt = $pdo->prepare("
    SELECT id, full_name FROM faculties
    WHERE id NOT IN (
        SELECT faculty_id FROM evaluation_report
        WHERE student_id = ? AND academic_year_id = ?
    )
");
$facultyStmt->execute([$student_id, $activeAY['id']]);
$faculties = $facultyStmt->fetchAll(PDO::FETCH_ASSOC);

// Criteria and questions
$criteriaStmt = $pdo->query("
    SELECT c.id AS criteria_id, c.name AS criteria_name, q.id AS question_id, q.text AS question_text
    FROM evaluation_criteria c
    JOIN questions q ON q.criteria_id = c.id
    ORDER BY c.id, q.id
");

$criteriaMap = [];
while ($row = $criteriaStmt->fetch(PDO::FETCH_ASSOC)) {
    $cid = $row['criteria_id'];
    if (!isset($criteriaMap[$cid])) {
        $criteriaMap[$cid] = ['name' => $row['criteria_name'], 'questions' => []];
    }
    $criteriaMap[$cid]['questions'][] = ['id' => $row['question_id'], 'text' => $row['question_text']];
}
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
        <div class="flex justify-between items-center mb-4">
            <h1 class="text-2xl font-bold">Faculty Evaluation</h1>
            <a href="student_dashboard.php" class="text-blue-600 hover:underline text-sm">← Back to Dashboard</a>
        </div>
        <p class="text-sm text-gray-600 mb-4">Academic Year: <?= htmlspecialchars($activeAY['year'] . ' - ' . $activeAY['semester']) ?></p>

        <?php if (empty($faculties)): ?>
            <p class="text-red-600 font-semibold">You have evaluated all faculty for this academic year.</p>
        <?php else: ?>
            <form action="submit_evaluation.php" method="POST" class="space-y-6">
                <input type="hidden" name="academic_year_id" value="<?= $activeAY['id'] ?>">

                <!-- Faculty Select -->
                <select name="faculty_id" required class="border p-2 rounded w-full">
                    <option value="">-- Select Faculty --</option>
                    <?php foreach ($faculties as $faculty): ?>
                        <option value="<?= $faculty['id'] ?>"><?= htmlspecialchars($faculty['full_name']) ?></option>
                    <?php endforeach; ?>
                </select>

                <!-- Criteria & Questions -->
                <?php foreach ($criteriaMap as $criteria_id => $criteria): ?>
                    <div class="border p-4 rounded bg-gray-50">
                        <h3 class="font-bold mb-2"><?= htmlspecialchars($criteria['name']) ?></h3>
                        <?php foreach ($criteria['questions'] as $question): ?>
                            <div class="mb-3">
                                <label class="block mb-1"><?= htmlspecialchars($question['text']) ?></label>
                                <div class="flex space-x-2">
                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                        <label class="inline-flex items-center">
                                            <input type="radio" name="criteria[<?= $criteria_id ?>][<?= $question['id'] ?>]" value="<?= $i ?>" required>
                                            <span class="ml-1"><?= $i ?></span>
                                        </label>
                                    <?php endfor; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endforeach; ?>

                <!-- Comments -->
                <textarea name="comment" rows="4" class="w-full border rounded p-2" placeholder="Additional comments..."></textarea>

                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Submit Evaluation</button>
            </form>
        <?php endif; ?>
    </div>
</div>
</body>
</html>
