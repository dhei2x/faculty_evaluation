<?php
// Content Security Policy
header("Content-Security-Policy: default-src 'self'; img-src 'self' data:; style-src 'self' 'unsafe-inline'; script-src 'none';");

// Your existing code starts here
session_start();
require_once '../php/db.php';
require_once '../php/auth.php';
require_role(['admin', 'superadmin']);


// Get selected academic year
$academic_year_id = $_POST['academic_year_id'] ?? '';

// Filter
$where = '';
$params = [];
if (!empty($academic_year_id)) {
    $where = 'WHERE er.academic_year_id = ?';
    $params[] = $academic_year_id;
}

// Fetch ratings (flat)
$sql = "
SELECT
  f.id AS faculty_id,
  CONCAT(f.last_name, ', ', f.first_name, ' ', IFNULL(f.middle_name, '')) AS faculty_name,
  ec.name AS criteria_name,
  ROUND(AVG(er.rating), 2) AS avg_rating,
  COUNT(DISTINCT er.student_id) AS total_students,
  ay.year,
  ay.semester
FROM evaluation_report er
JOIN faculties f ON er.faculty_id = f.id
JOIN questions q ON er.question_id = q.id
JOIN evaluation_criteria ec ON q.criteria_id = ec.id
JOIN academic_years ay ON er.academic_year_id = ay.id
$where
GROUP BY f.id, ec.id, ay.id
ORDER BY f.last_name, f.first_name, ay.year DESC
";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$rows = $stmt->fetchAll();

// Set headers to force download as Excel
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=\"evaluation_report.xls\"");
header("Pragma: no-cache");
header("Expires: 0");

// Output column headers
echo "Faculty\tYear\tSemester\tCriteria\tAvg Rating\tStudents\n";

// Output data rows
foreach ($rows as $r) {
    echo "{$r['faculty_name']}\t{$r['year']}\t{$r['semester']}\t{$r['criteria_name']}\t{$r['avg_rating']}\t{$r['total_students']}\n";
}

exit();
