<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once 'db.php';

// Create new PDF document
$pdf = new TCPDF();
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Payroll System');
$pdf->SetTitle('Payroll Summary Report');
$pdf->SetSubject('Payroll');
$pdf->SetKeywords('TCPDF, payroll, PDF, report');

// Set document protection
$pdf->SetProtection(['print', 'copy'], '123456'); // Replace '123456' with desired password

// Set default header and footer off
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);

// Set default font
$pdf->SetFont('dejavusans', '', 10);

// Add a page
$pdf->AddPage();

// Fetch payroll data
$year = date("Y");
$month = date("m");

$sql = "SELECT * FROM employee ORDER BY first_name";
$result = $conn->query($sql);

$html = '<h2 style="text-align:center;">Payroll Summary Report</h2>';
$html .= '<p style="text-align:center;">Pay Period: ' . date("F Y", strtotime($year . "-" . $month . "-01")) . '</p>';
$html .= '<table border="1" cellspacing="0" cellpadding="4">
<tr style="background-color:#f2f2f2;">
<th><b>Employee Name</b></th>
<th><b>Department</b></th>
<th><b>Present</b></th>
<th><b>Leave</b></th>
<th><b>Absent</b></th>
<th><b>Gross Pay</b></th>
<th><b>Deductions</b></th>
<th><b>Net Pay</b></th>
</tr>';

if ($result && $result->num_rows > 0) {
    while ($employee = $result->fetch_assoc()) {
        $employee_id = $employee['id'];

        // Get attendance
        $attendanceQuery = "SELECT * FROM attendance 
                            WHERE employee_id = $employee_id 
                            AND YEAR(attendance_date) = '$year' 
                            AND MONTH(attendance_date) = '$month'";
        $attendanceResult = $conn->query($attendanceQuery);

        $present = $leave = $absent = $gross = 0;

        while ($row = $attendanceResult->fetch_assoc()) {
            if ($row['status'] === 'present') {
                $present++;
                $gross += $row['daily_salary'];
            } elseif ($row['status'] === 'leave') {
                $leave++;
                $gross += $row['daily_salary'];
            } elseif ($row['status'] === 'absent') {
                $absent++;
            }
        }

        $sss = $gross * 0.04;
        $philhealth = $gross * 0.02;
        $pagibig = $gross * 0.01;
        $deductions = $sss + $philhealth + $pagibig;
        $net = $gross - $deductions;

        $html .= '<tr>
            <td>' . htmlspecialchars($employee['first_name'] . ' ' . $employee['last_name']) . '</td>
            <td>' . htmlspecialchars($employee['department']) . '</td>
            <td>' . $present . '</td>
            <td>' . $leave . '</td>
            <td>' . $absent . '</td>
            <td>₱' . number_format($gross, 2) . '</td>
            <td>₱' . number_format($deductions, 2) . '</td>
            <td>₱' . number_format($net, 2) . '</td>
        </tr>';
    }
} else {
    $html .= '<tr><td colspan="8" align="center">No records found.</td></tr>';
}

$html .= '</table>';

// Output HTML content
$pdf->writeHTML($html, true, false, true, false, '');

// Close and output PDF document
$pdf->Output('payroll_summary_' . $month . '_' . $year . '.pdf', 'I'); // 'I' = inline view, use 'D' to force download

$conn->close();
?>
