<?php
session_start();
require '../libs/dompdf/autoload.inc.php';
use Dompdf\Dompdf;

include '../connect.php';

if (!isset($_SESSION['role'])) exit();

$period = $_GET['period'] ?? 'monthly';
$year = $_GET['year'] ?? date('Y');

$query = ($period === 'yearly')
? "SELECT YEAR(visit_date) period, COUNT(*) total FROM visit GROUP BY YEAR(visit_date)"
: "SELECT MONTH(visit_date) period, COUNT(*) total FROM visit WHERE YEAR(visit_date)='$year' GROUP BY MONTH(visit_date)";

$result = mysqli_query($conn, $query);

$html = "<h2>EduHealth Report</h2>
<table border='1' width='100%' cellpadding='6'>
<tr><th>Period</th><th>Total Visits</th></tr>";

while ($row = mysqli_fetch_assoc($result)) {
    $html .= "<tr>
        <td>{$row['period']}</td>
        <td>{$row['total']}</td>
    </tr>";
}

$html .= "</table>";

$pdf = new Dompdf();
$pdf->loadHtml($html);
$pdf->setPaper('A4');
$pdf->render();
$pdf->stream("shrs_report.pdf", ["Attachment" => true]);
