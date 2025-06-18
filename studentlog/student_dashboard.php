<?php
session_start();
require_once '../php/db.php';
var_dump($_SESSION['role']);
var_dump(isset($_SESSION['user']));

if ($_SESSION['role'] !== 'students') {
    header("Location: ../php/login.php");
    exit();
}

$userID = $_SESSION['user_id'];


$stmt = $pdo->prepare("SELECT student_id FROM students WHERE id = ?");
$stmt->execute([$userID]);
$student_id = $stmt->fetch(PDO::FETCH_ASSOC);






// Fetch faculties not yet evaluated
// $facultyStmt = $pdo->prepare("
//     SELECT f.id, f.full_name, f.department
//     FROM faculties f
//     WHERE f.id NOT IN (
//         SELECT faculty_id FROM evaluation_report WHERE student_id = ?
//     )
// ");
$facultyStmt = $pdo->prepare("
    SELECT f.id, f.full_name, f.department
    FROM faculties f
");
$facultyStmt->execute();
$faculties = $facultyStmt->fetchAll();


//  ?????
// Fetch criteria and questions
// $criteriaStmt = $pdo->query("
//     SELECT c.id AS criteria_id, c.name AS criteria_name, q.id AS question_id, q.text AS question_text
//     FROM criteria c
//     JOIN questions q ON q.criteria_id = c.id
//     ORDER BY c.id, q.id
// ");
/// ????

$criteriaMap = [];
foreach ($criteriaMap as $row) {
    $criteriaMap[$row['criteria_id']]['name'] = $row['criteria_name'];
    $criteriaMap[$row['criteria_id']]['questions'][] = [
        'id' => $row['question_id'],
        'text' => $row['question_text']
    ];
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Student Dashboard - Evaluation</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <style>
        .star-rating .fa-star {
            color: #ccc;
            cursor: pointer;
        }
        .star-rating .fa-star.checked {
            color: #f59e0b;
        }
    </style>
</head>
<body class="bg-gray-100 flex">

<?php include 'student_sidebar.php'; ?>

<!-- Main Content -->
<div class="ml-64 p-6 w-full">
    <div class="max-w-4xl mx-auto bg-white p-6 rounded shadow">
        <h1 class="text-2xl font-bold mb-4">Faculty Evaluation</h1>

        <?php if (empty($faculties)): ?>
            <p class="text-gray-600">You have already evaluated all faculties. Thank you!</p>
            <?php else: ?>
    <form action="submit_evaluation.php" method="POST" id="evaluationForm">
        <label class="block mb-2 font-semibold">Select Faculty to Evaluate:</label>
        <select name="faculty_id" id="facultySelect" required class="mb-6 border rounded p-2 w-full">
            <option value="">-- Select Faculty --</option>
            <?php foreach ($faculties as $f): ?>
                <option value="<?= $f['id'] ?>"><?= htmlspecialchars($f['full_name']) ?></option>
            <?php endforeach; ?>
        </select>

        <!-- Evaluation Section: Hidden until a faculty is selected -->
        <div id="evaluationSection" class="hidden">
            <?php include 'evaluate_faculty.php'; ?>
        </div>

        <div id="submitButtonContainer" class="hidden mt-4">
            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">Submit Evaluation</button>
        </div>
    </form>

    <script>
        const facultySelect = document.getElementById('facultySelect');
        const evaluationSection = document.getElementById('evaluationSection');
        const submitButtonContainer = document.getElementById('submitButtonContainer');

        facultySelect.addEventListener('change', () => {
            const selected = facultySelect.value;
            if (selected) {
                evaluationSection.classList.remove('hidden');
                submitButtonContainer.classList.remove('hidden');
            } else {
                evaluationSection.classList.add('hidden');
                submitButtonContainer.classList.add('hidden');
            }
        });
    </script>
<?php endif; ?>

    </div>
</div>

<script>
    document.querySelectorAll('.star-rating').forEach(group => {
        const stars = group.querySelectorAll('.fa-star');
        const inputId = stars[0].getAttribute('data-input');
        const input = document.getElementById(inputId);

        stars.forEach(star => {
            star.addEventListener('click', () => {
                const rating = star.getAttribute('data-rating');
                input.value = rating;

                stars.forEach(s => {
                    s.classList.remove('checked');
                    if (s.getAttribute('data-rating') <= rating) {
                        s.classList.add('checked');
                    }
                });
            });
        });
    });
</script>

</body>
</html>
