<?php
ob_start(); // Para maiwasan ang TCPDF error: "Some data has already been output"

// Isama ang mga kinakailangang file
require_once 'db.php';
require_once __DIR__ . '/../vendor/autoload.php'; // I-adjust ang path kung kinakailangan
include 'admin_sidebar.php';

/**
 * Kumuha ng payroll data para sa kasalukuyang buwan at taon.
 *
 * @param mysqli $conn Koneksyon sa database.
 * @return array Listahan ng payroll records ng bawat empleyado.
 */
function getPayrollData($conn) {
    $data  = [];
    $year  = date("Y");
    $month = date("m");

    $sql = "SELECT * FROM employee ORDER BY first_name";
    if ($result = $conn->query($sql)) {
        while ($emp = $result->fetch_assoc()) {
            $id      = $emp['id'];
            $present = $leave = $absent = $gross = 0;

            // Kunin ang mga attendance record ng empleyado para sa kasalukuyang buwan at taon
            $attendanceSql = "SELECT daily_salary, status FROM attendance 
                              WHERE employee_id = $id 
                              AND YEAR(attendance_date) = '$year' 
                              AND MONTH(attendance_date) = '$month'";
            if ($attResult = $conn->query($attendanceSql)) {
                while ($row = $attResult->fetch_assoc()) {
                    // Batay sa status, dagdagan ang bilang at gross salary
                    if ($row['status'] == 'present') {
                        $present++;
                        $gross += $row['daily_salary'];
                    } elseif ($row['status'] == 'leave') {
                        $leave++;
                        $gross += $row['daily_salary']; // May bayad rin ang leave depende sa polisiya
                    } elseif ($row['status'] == 'absent') {
                        $absent++;
                    }
                }
            }

            // Kalkulahin ang deductions: SSS (4%), PhilHealth (2%), Pag-IBIG (1%)
            $sss        = $gross * 0.04;
            $philhealth = $gross * 0.02;
            $pagibig    = $gross * 0.01;
            $deductions = $sss + $philhealth + $pagibig;
            $net        = $gross - $deductions;

            $data[] = [
                'name'       => $emp['first_name'] . ' ' . $emp['last_name'],
                'department' => $emp['department'],
                'present'    => $present,
                'leave'      => $leave,
                'absent'     => $absent,
                'gross'      => $gross,
                'deductions' => $deductions,
                'net'        => $net,
            ];
        }
    }
    return $data;
}

/**
 * Gumawa at mag-render ng PDF report gamit ang TCPDF.
 *
 * @param array $payroll Ang payroll data.
 */
function generatePDF($payroll) {
    // Inisyalisa ang TCPDF object
    $pdf = new TCPDF();
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor('Payroll System');
    $pdf->SetTitle('Payroll Summary');
    $pdf->SetSubject('Payroll Report');
    $pdf->SetKeywords('Payroll, TCPDF, PDF');
    $pdf->SetProtection(['print', 'copy'], 'secret123'); // PDF password

    $pdf->setPrintHeader(false);
    $pdf->setPrintFooter(false);
    $pdf->SetFont('dejavusans', '', 10);
    $pdf->AddPage();

    $monthYear = date("F Y");
    $html  = "<h2 style='text-align:center;'>Payroll Summary Report</h2>";
    $html .= "<p style='text-align:center;'>Pay Period: $monthYear</p>";
    $html .= "<table border='1' cellpadding='4'>
                <tr style='background-color:#f2f2f2;'>
                    <th>Name</th>
                    <th>Department</th>
                    <th>Present</th>
                    <th>Leave</th>
                    <th>Absent</th>
                    <th>Gross Pay</th>
                    <th>Deductions</th>
                    <th>Net Pay</th>
                </tr>";

    if (count($payroll) > 0) {
        foreach ($payroll as $row) {
            $html .= "<tr>
                        <td>" . htmlspecialchars($row['name']) . "</td>
                        <td>" . htmlspecialchars($row['department']) . "</td>
                        <td>{$row['present']}</td>
                        <td>{$row['leave']}</td>
                        <td>{$row['absent']}</td>
                        <td>₱" . number_format($row['gross'], 2) . "</td>
                        <td>₱" . number_format($row['deductions'], 2) . "</td>
                        <td>₱" . number_format($row['net'], 2) . "</td>
                      </tr>";
        }
    } else {
        $html .= "<tr><td colspan='8' align='center'>No records found.</td></tr>";
    }
    $html .= "</table>";

    $pdf->writeHTML($html, true, false, true, false, '');
    ob_end_clean(); // Linisin ang buffer bago i-output ang PDF
    $pdf->Output('payroll_summary_' . date("Ym") . '.pdf', 'I'); // 'I' para ipakita sa browser; 'D' para i-force download
    exit();
}


// Main Process

// Kunin ang payroll data mula sa database
$payroll = getPayrollData($conn);
$conn->close();

// Kung naka-set ang ?pdf=1 sa URL, i-generate ang PDF report
if (isset($_GET['pdf']) && $_GET['pdf'] == 1) {
    generatePDF($payroll);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Payroll Summary</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">
</head>
<body class="bg-light">
<div class="container mt-5">
    <h2 class="text-center">Payroll Summary Report</h2>
    <p class="text-center">Pay Period: <?php echo date("F Y"); ?></p>
    <div class="text-right mb-3">
        <a href="?pdf=1" class="btn btn-success">Download PDF</a>
    </div>
    <table id="payrollTable" class="table table-bordered table-striped">
        <thead class="thead-dark">
            <tr>
                <th>Name</th>
                <th>Department</th>
                <th>Present</th>
                <th>Leave</th>
                <th>Absent</th>
                <th>Gross Pay</th>
                <th>Deductions</th>
                <th>Net Pay</th>
            </tr>
        </thead>
        <tbody>
        <?php if (count($payroll) > 0): ?>
            <?php foreach ($payroll as $row): ?>
                <tr>
                    <td><?= htmlspecialchars($row['name']) ?></td>
                    <td><?= htmlspecialchars($row['department']) ?></td>
                    <td><?= $row['present'] ?></td>
                    <td><?= $row['leave'] ?></td>
                    <td><?= $row['absent'] ?></td>
                    <td>₱<?= number_format($row['gross'], 2) ?></td>
                    <td>₱<?= number_format($row['deductions'], 2) ?></td>
                    <td>₱<?= number_format($row['net'], 2) ?></td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="8" class="text-center">No records found.</td>
            </tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- jQuery, Bootstrap JS, DataTables JS -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>
<script>
$(document).ready(function() {
    $('#payrollTable').DataTable({
        "pageLength": 10,
        "lengthMenu": [5, 10, 25, 50, 100],
        "ordering": true,
        "searching": true
    });
});
</script>
</body>
</html>
