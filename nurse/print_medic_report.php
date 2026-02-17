<?php
session_start();
require_once __DIR__ . '/../libs/dompdf/autoload.inc.php'; 
include "../connect.php";

use Dompdf\Dompdf;


if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'nurse') {
    header("Location: ../index.php");
    exit();
}


if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("No student selected.");
}

$student_id = intval($_GET['id']);

$query = "
SELECT s.student_id, s.full_name, s.reg_no, s.gender, s.course, s.DoB, m.blood_group
FROM students s
LEFT JOIN medical_records m ON s.student_id = m.student_id
WHERE s.student_id = ?
ORDER BY s.course DESC
";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $student_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) == 0) {
    die("Student not found.");
}

$student = mysqli_fetch_assoc($result);

$visitStmt = mysqli_prepare($conn,
    "SELECT * FROM visits WHERE student_id = ? ORDER BY visit_date DESC"
);
mysqli_stmt_bind_param($visitStmt, "i", $student_id);
mysqli_stmt_execute($visitStmt);
$visitResult = mysqli_stmt_get_result($visitStmt);

$html = '
<style>
body { font-family: Arial, sans-serif; }
h2 { text-align:center; }
table { width:100%; border-collapse: collapse; margin-top:10px; }
table, th, td { border:1px solid black; }
th, td { padding:6px; font-size:12px; }
.info td { border:none; }
</style>

<h2>STUDENT HEALTH RECORD SYSTEM (SHRS)</h2>
<hr>

<h3>Student Information</h3>
<table class="info">
<tr><td><strong>Full Name:</strong></td><td>'.$student['full_name'].'</td></tr>
<tr><td><strong>Reg No:</strong></td><td>'.$student['reg_no'].'</td></tr>
<tr><td><strong>Gender:</strong></td><td>'.$student['gender'].'</td></tr>
<tr><td><strong>Course:</strong></td><td>'.$student['course'].'</td></tr>
<tr><td><strong>Date of Birth:</strong></td><td>'.$student['DoB'].'</td></tr>
<tr><td><strong>Blood Group:</strong></td><td>'.$student['blood_group'].'</td></tr>
</table>

<h3>Medical History</h3>
<table>
<tr>
<th>Date</th>
<th>Complaint</th>
<th>Diagnosis</th>
<th>Treatment</th>
</tr>';

while ($row = mysqli_fetch_assoc($visitResult)) {
    $html .= '
    <tr>
        <td>'.$row['visit_date'].'</td>
        <td>'.$row['complaint'].'</td>
        <td>'.$row['diagnosis'].'</td>
        <td>'.$row['treatment'].'</td>
    </tr>';
}

$html .= '
</table>

<br><br>
<hr>
<p>Generated on: '.date("Y-m-d H:i:s").'</p>
<p>Nurse Signature: __________________________</p>
';

$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper("A4", "portrait");
$dompdf->render();
$dompdf->stream("Medical_Report_".$student['reg_no'].".pdf", ["Attachment" => false]);