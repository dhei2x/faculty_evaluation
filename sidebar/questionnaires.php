<?php
session_start();
require_once '../php/db.php';
require_once '../php/auth.php';
require_role('admin');

$criteria = $pdo->query("SELECT * FROM evaluation_criteria ORDER BY name")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($_POST['action'] === 'add') {
        $stmt = $pdo->prepare("INSERT INTO questions (criteria_id, text) VALUES (:cid, :text)");
        $stmt->execute(['cid' => $_POST['criteria_id'], 'text' => $_POST['question_text']]);
    } elseif ($_POST['action'] === 'edit') {
        $stmt = $pdo->prepare("UPDATE questions SET criteria_id = :cid, text = :text WHERE id = :id");
        $stmt->execute(['cid' => $_POST['criteria_id'], 'text' => $_POST['question_text'], 'id' => $_POST['question_id']]);
    }
    header("Location: questions.php"); exit();
} elseif (isset($_GET['delete'])) {
    $stmt = $pdo->prepare("DELETE FROM questions WHERE id = :id");
    $stmt->execute(['id' => $_GET['delete']]);
    header("Location: questions.php"); exit();
}

$questions = $pdo->query("SELECT q.*, c.name AS criteria_name FROM questions q JOIN evaluation_criteria c ON q.criteria_id = c.id ORDER BY q.id")->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Evaluation Questions</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 p-6">
<div class="max-w-4xl mx-auto bg-white p-6 rounded shadow">
<div class="max-w-4xl mx-auto bg-white p-6 rounded shadow">
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-bold">Evaluation Questions</h1>
        <a href="../php/admin_dashboard.php" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold px-4 py-2 rounded">
            ← Back to Dashboard
        </a>
    </div>

    <form method="POST" class="mb-6">
        <input type="hidden" name="action" value="add">
        <select name="criteria_id" required class="border rounded p-2 mb-2 w-full">
            <option value="">Select Criteria</option>
            <?php foreach ($criteria as $c): ?>
                <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['name']) ?></option>
            <?php endforeach; ?>
        </select>
        <textarea name="question_text" class="border rounded p-2 w-full" placeholder="Enter question..." required></textarea>
        <button class="bg-blue-600 text-white mt-2 px-4 py-2 rounded">Add Question</button>
    </form>

    <?php foreach ($questions as $q): ?>
        <form method="POST" class="border-b py-3 flex gap-2 items-start">
            <input type="hidden" name="action" value="edit">
            <input type="hidden" name="question_id" value="<?= $q['id'] ?>">
            <select name="criteria_id" class="border rounded p-2">
                <?php foreach ($criteria as $c): ?>
                    <option value="<?= $c['id'] ?>" <?= $q['criteria_id'] == $c['id'] ? 'selected' : '' ?>><?= htmlspecialchars($c['name']) ?></option>
                <?php endforeach; ?>
            </select>
            <textarea name="question_text" class="flex-grow border rounded p-2"><?= htmlspecialchars($q['text']) ?></textarea>
            <button class="bg-green-600 text-white px-3 py-1 rounded">Save</button>
            <a href="?delete=<?= $q['id'] ?>" class="text-red-500 ml-2" onclick="return confirm('Delete this?')">Delete</a>
        </form>
    <?php endforeach; ?>
</div>
</body>
</html>
