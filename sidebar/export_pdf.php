<?php
session_start();
require_once '../php/db.php';
require_once '../php/auth.php';
require_role('admin');

// Include FPDF
require('../fpdf/fpdf.php');

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

// Fetch comments (flat)
$commentSql = "
SELECT
    er.faculty_id,
    er.question_id,
    GROUP_CONCAT(DISTINCT er.comment SEPARATOR ' | ') AS comments,
    er.academic_year_id
FROM evaluation_report er
WHERE er.comment IS NOT NULL AND er.comment != ''
" . ($where ? " AND er.academic_year_id = ?" : "") . "
GROUP BY er.faculty_id, er.question_id, er.academic_year_id
";
$commentStmt = $pdo->prepare($commentSql);
$commentStmt->execute($params);
$commentsRaw = $commentStmt->fetchAll(PDO::FETCH_GROUP|PDO::FETCH_ASSOC);

// Map comments
$commentsMap = [];
foreach ($commentsRaw as $facultyId => $arr) {
    foreach ($arr as $c) {
        $key = $c['faculty_id'].'-'.$c['academic_year_id'].'-'.$c['question_id'];
        $commentsMap[$key] = $c['comments'];
    }
}

// Initialize PDF
$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial','B',14);

// Watermark
$pdf->SetAlpha(0.1); // Transparent
$pdf->Image('../php/logo.png',30,50,150);
$pdf->SetAlpha(1);

// Title
$pdf->Cell(0,10,'Evaluation Report',0,1,'C');
$pdf->Ln(5);

// Table headers
$pdf->SetFont('Arial','B',10);
$pdf->SetFillColor(200,200,200);
$pdf->Cell(40,8,'Faculty',1,0,'C',true);
$pdf->Cell(20,8,'Year',1,0,'C',true);
$pdf->Cell(20,8,'Semester',1,0,'C',true);
$pdf->Cell(50,8,'Criteria',1,0,'C',true);
$pdf->Cell(20,8,'Avg Rating',1,0,'C',true);
$pdf->Cell(20,8,'Students',1,0,'C',true);
$pdf->Cell(50,8,'Comments',1,1,'C',true);

$pdf->SetFont('Arial','',10);

// Output data
foreach ($rows as $r) {
    $key = $r['faculty_id'].'-'.$r['year'].'-'.$r['criteria_name'];
    
    // Try to find comments for this faculty+criteria+year
    $commentList = [];
    foreach ($commentsMap as $cKey => $cVal) {
        if (strpos($cKey,$r['faculty_id'].'-'.$r['year']) === 0) {
            $commentList[] = $cVal;
        }
    }
    $comments = implode(" | ", $commentList);

    $pdf->Cell(40,8,$r['faculty_name'],1);
    $pdf->Cell(20,8,$r['year'],1);
    $pdf->Cell(20,8,$r['semester'],1);
    $pdf->Cell(50,8,$r['criteria_name'],1);
    $pdf->Cell(20,8,$r['avg_rating'],1,0,'C');
    $pdf->Cell(20,8,$r['total_students'],1,0,'C');
    $pdf->Cell(50,8,$comments,1,1);
}

$pdf->Output('D','evaluation_report.pdf');
exit();
