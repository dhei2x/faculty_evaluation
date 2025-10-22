<?php
// subjects.php
session_start();
require_once '../php/db.php';
require_once '../php/auth.php';
require_role(['admin', 'superadmin']);


// Add Subject
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'add') {
    $lec  = max(0, (int)$_POST['lec_hours']);
    $lab  = max(0, (int)$_POST['lab_hours']);
    $unit = max(0, (int)$_POST['units']);

    // Check for duplicates within the same course
    $check = $pdo->prepare("SELECT COUNT(*) FROM subjects WHERE code = :code AND course = :course");
    $check->execute([
        'code'   => $_POST['code'],
        'course' => $_POST['course']
    ]);

    if ($check->fetchColumn() > 0) {
        $_SESSION['error'] = "Subject code already exists in this course!";
        header("Location: subjects.php");
        exit();
    }

    $stmt = $pdo->prepare("INSERT INTO subjects 
        (code, description, lec_hours, lab_hours, units, prerequisite, course, year_level, semester) 
        VALUES (:code, :description, :lec, :lab, :units, :prereq, :course, :year, :sem)");
    $stmt->execute([
        'code'        => $_POST['code'],
        'description' => $_POST['description'],
        'lec'         => $lec,
        'lab'         => $lab,
        'units'       => $unit,
        'prereq'      => $_POST['prerequisite'],
        'course'      => $_POST['course'],
        'year'        => $_POST['year_level'],
        'sem'         => $_POST['semester']
    ]);
    header("Location: subjects.php");
    exit();
}

// Edit Subject
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'edit') {
    $lec  = max(0, (int)$_POST['lec_hours']);
    $lab  = max(0, (int)$_POST['lab_hours']);
    $unit = max(0, (int)$_POST['units']);

    $stmt = $pdo->prepare("UPDATE subjects 
        SET code=:code, description=:description, 
            lec_hours=:lec, lab_hours=:lab, units=:units, 
            prerequisite=:prereq, course=:course, year_level=:year, semester=:sem 
        WHERE id=:id");
    $stmt->execute([
        'id'          => $_POST['subject_id'],
        'code'        => $_POST['code'],
        'description' => $_POST['description'],
        'lec'         => $lec,
        'lab'         => $lab,
        'units'       => $unit,
        'prereq'      => $_POST['prerequisite'],
        'course'      => $_POST['course'],
        'year'        => $_POST['year_level'],
        'sem'         => $_POST['semester']
    ]);
    header("Location: subjects.php");
    exit();
}

// Delete Subject
if (isset($_GET['delete'])) {
    $stmt = $pdo->prepare("DELETE FROM subjects WHERE id = :id");
    $stmt->execute(['id' => $_GET['delete']]);
    header("Location: subjects.php");
    exit();
}

// Fetch subjects grouped
$subjects = $pdo->query("SELECT * FROM subjects ORDER BY course, year_level, semester, code ASC")->fetchAll();

/**
 * Helper: 1 -> 1st, 2 -> 2nd, 3 -> 3rd, 4 -> 4th ...
 */
function ordinal(int $n): string {
    $mod100 = $n % 100;
    if ($mod100 >= 11 && $mod100 <= 13) {
        return $n . 'th';
    }
    $suffixes = ['th','st','nd','rd','th','th','th','th','th','th'];
    return $n . $suffixes[$n % 10];
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Subjects</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet" />
    <style>
    body {
        position: relative;
        background-color: #f3f4f6;
    }
    body::before {
        content: "";
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: url('../php/logo.png') no-repeat center center;
        background-size: 900px 900px;
        opacity: 0.13;
        pointer-events: none;
        z-index: 0;
    }
    .content {
        position: relative;
        z-index: 1;
        background-color: rgba(255, 255, 255, 0.75);
        border-radius: 0.75rem;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        padding: 2rem;
    }
    table tr { background-color: rgba(255, 255, 255, 0.95) !important; }
    thead tr { background-color: rgba(229, 231, 235, 0.95) !important; }
    tr.bg-gray-100, tr.bg-yellow-100, tr.bg-green-200 { background-color: rgba(243, 244, 246, 0.9) !important; }
    </style>
</head>
<body class="p-6 bg-gray-100">
<div class="content">

    <a href="../php/admin_dashboard.php" class="inline-block mb-4 bg-blue-300 text-white px-4 py-2 rounded hover:bg-gray-800">
        ← Back to Dashboard
    </a>

    <h1 class="text-2xl font-bold mb-4">Manage Subjects</h1>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="bg-red-100 text-red-800 border border-red-300 p-3 mb-4 rounded">
            <?= $_SESSION['error']; unset($_SESSION['error']); ?>
        </div>
    <?php endif; ?>

    <!-- Add Subject Form -->
    <form method="POST" class="mb-6 grid grid-cols-10 gap-2 bg-white p-4 shadow rounded">
        <input type="hidden" name="action" value="add">
        <input type="text" name="code" required placeholder="Code" class="border p-2 rounded">
        <input type="text" name="description" required placeholder="Subject Description" class="border p-2 rounded col-span-3">
        <input type="number" name="lec_hours" placeholder="Lec" class="border p-2 rounded" min="0">
        <input type="number" name="lab_hours" placeholder="Lab" class="border p-2 rounded" min="0">
        <input type="number" name="units" placeholder="Units" class="border p-2 rounded" min="0">
        <input type="text" name="prerequisite" placeholder="Prerequisite" class="border p-2 rounded col-span-2">
        <input type="text" name="course" placeholder="Course" class="border p-2 rounded">

        <select name="year_level" class="border p-2 rounded">
            <option value="1">1st Year</option>
            <option value="2">2nd Year</option>
            <option value="3">3rd Year</option>
            <option value="4">4th Year</option>
        </select>

        <select name="semester" class="border p-2 rounded">
            <option value="1">1st Semester</option>
            <option value="2">2nd Semester</option>
        </select>

        <button type="submit" class="col-span-10 mt-2 bg-blue-500 text-white px-4 py-2 rounded">Add Subject</button>
    </form>

    <!-- Grouped Subjects -->
    <?php
    $grouped = [];
    foreach ($subjects as $sub) {
        $course = $sub['course'];
        $year   = $sub['year_level'];
        $sem    = $sub['semester'];
        $grouped[$course][$year][$sem][] = $sub;
    }

    $globalLecTotal = $globalLabTotal = $globalUnitTotal = 0;
    $courses = ['BPA', 'BSA']; // Add all courses
    ?>

    <?php foreach ($grouped as $course => $years): ?>
        <h1 class="text-2xl font-bold text-purple-700 mt-8 mb-4"><?= htmlspecialchars($course) ?></h1>
        <?php 
        $courseLecTotal = $courseLabTotal = $courseUnitTotal = 0;
        ?>

        <?php foreach ($years as $year => $semesters): ?>
            <?php foreach ($semesters as $sem => $list): ?>
                <h2 class="text-xl font-semibold mt-6 mb-2">
                    <?= ordinal((int)$year) ?> Year - <?= ($sem == 1 ? "1st Semester" : "2nd Semester") ?>
                </h2>

                <table class="min-w-full bg-white shadow rounded mb-6">
                    <thead class="bg-gray-200">
                        <tr>
                            <th class="p-2">Code</th>
                            <th class="p-2">Description</th>
                            <th class="p-2">Lec</th>
                            <th class="p-2">Lab</th>
                            <th class="p-2">Units</th>
                            <th class="p-2">Prerequisite</th>
                            <th class="p-2">Course</th>
                            <th class="p-2">Year</th>
                            <th class="p-2">Sem</th>
                            <th class="p-2">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $lecTotal = $labTotal = $unitTotal = 0;
                        foreach ($list as $sub): 
                            $lecTotal += (int)$sub['lec_hours'];
                            $labTotal += (int)$sub['lab_hours'];
                            $unitTotal += (int)$sub['units'];
                        ?>
                            <tr class="border-t">
                                <form method="POST" class="contents">
                                    <input type="hidden" name="action" value="edit">
                                    <input type="hidden" name="subject_id" value="<?= $sub['id'] ?>">

                                    <td class="p-2"><input type="text" name="code" value="<?= htmlspecialchars($sub['code']) ?>" class="border p-1 rounded w-20"></td>
                                    <td class="p-2"><input type="text" name="description" value="<?= htmlspecialchars($sub['description']) ?>" class="border p-1 rounded w-48"></td>
                                    <td class="p-2"><input type="number" name="lec_hours" value="<?= (int)$sub['lec_hours'] ?>" class="border p-1 rounded w-16" min="0"></td>
                                    <td class="p-2"><input type="number" name="lab_hours" value="<?= (int)$sub['lab_hours'] ?>" class="border p-1 rounded w-16" min="0"></td>
                                    <td class="p-2"><input type="number" name="units" value="<?= (int)$sub['units'] ?>" class="border p-1 rounded w-16" min="0"></td>
                                    <td class="p-2"><input type="text" name="prerequisite" value="<?= htmlspecialchars($sub['prerequisite']) ?>" class="border p-1 rounded w-32"></td>

                                    <!-- Course Dropdown -->
                                    <td class="p-2">
                                        <select name="course" class="border p-1 rounded w-24">
                                            <?php foreach ($courses as $courseOption): ?>
                                                <option value="<?= $courseOption ?>" <?= $sub['course'] == $courseOption ? 'selected' : '' ?>>
                                                    <?= $courseOption ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </td>

                                    <!-- Year dropdown -->
                                    <td class="p-2">
                                        <select name="year_level" class="border p-1 rounded">
                                            <option value="1" <?= $sub['year_level']==1 ? 'selected' : '' ?>>1st</option>
                                            <option value="2" <?= $sub['year_level']==2 ? 'selected' : '' ?>>2nd</option>
                                            <option value="3" <?= $sub['year_level']==3 ? 'selected' : '' ?>>3rd</option>
                                            <option value="4" <?= $sub['year_level']==4 ? 'selected' : '' ?>>4th</option>
                                        </select>
                                    </td>

                                    <!-- Semester dropdown -->
                                    <td class="p-2">
                                        <select name="semester" class="border p-1 rounded">
                                            <option value="1" <?= $sub['semester']==1 ? 'selected' : '' ?>>1st</option>
                                            <option value="2" <?= $sub['semester']==2 ? 'selected' : '' ?>>2nd</option>
                                        </select>
                                    </td>

                                    <td class="p-2 flex space-x-2">
                                        <button class="bg-green-500 text-white px-3 py-1 rounded">Update</button>
                                        <a href="?delete=<?= $sub['id'] ?>" onclick="return confirm('Delete this subject?')" class="bg-red-500 text-white px-3 py-1 rounded">Delete</a>
                                    </td>
                                </form>
                            </tr>
                        <?php endforeach; ?>

                        <tr class="bg-gray-100 font-semibold">
                            <td class="p-2" colspan="2">Semester Total</td>
                            <td class="p-2"><?= $lecTotal ?></td>
                            <td class="p-2"><?= $labTotal ?></td>
                            <td class="p-2"><?= $unitTotal ?></td>
                            <td colspan="4"></td>
                        </tr>

                        <?php 
                        $courseLecTotal += $lecTotal;
                        $courseLabTotal += $labTotal;
                        $courseUnitTotal += $unitTotal;

                        $globalLecTotal += $lecTotal;
                        $globalLabTotal += $labTotal;
                        $globalUnitTotal += $unitTotal;
                        ?>
                    </tbody>
                </table>
            <?php endforeach; ?>
        <?php endforeach; ?>

        <table class="min-w-full bg-white shadow rounded mb-12">
            <tr class="bg-yellow-100 font-bold">
                <td class="p-2" colspan="2"><?= htmlspecialchars($course) ?> Grand Total</td>
                <td class="p-2"><?= $courseLecTotal ?></td>
                <td class="p-2"><?= $courseLabTotal ?></td>
                <td class="p-2"><?= $courseUnitTotal ?></td>
                <td colspan="4"></td>
            </tr>
        </table>
    <?php endforeach; ?>

    <table class="min-w-full bg-white shadow rounded mt-12">
        <tr class="bg-green-200 font-bold">
            <td class="p-2" colspan="2">Global Grand Total (All Courses)</td>
            <td class="p-2"><?= $globalLecTotal ?></td>
            <td class="p-2"><?= $globalLabTotal ?></td>
            <td class="p-2"><?= $globalUnitTotal ?></td>
            <td colspan="4"></td>
        </tr>
    </table>

</div>
</body>
</html>
