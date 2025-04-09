<?php
include 'db.php';

// Function to calculate government deductions
function calculateDeductions($monthlySalary) {
    $deductions = [];

    // SSS Contribution
    if ($monthlySalary <= 3250) {
        $deductions['sss'] = 135.00;
    } elseif ($monthlySalary <= 24750) {
        $sssRate = floor(($monthlySalary - 3250) / 500);
        $deductions['sss'] = 135.00 + ($sssRate * 22.50);
    } else {
        $deductions['sss'] = 1125.00;
    }

    // PhilHealth Contribution
    $philHealthRate = 0.02;
    $philHealthContribution = $monthlySalary * $philHealthRate;
    if ($philHealthContribution < 300) {
        $deductions['philhealth'] = 300;
    } elseif ($philHealthContribution > 1800) {
        $deductions['philhealth'] = 1800;
    } else {
        $deductions['philhealth'] = $philHealthContribution;
    }

    // Pag-IBIG Contribution
    if ($monthlySalary <= 1500) {
        $deductions['pagibig'] = $monthlySalary * 0.01;
    } else {
        $deductions['pagibig'] = min($monthlySalary * 0.02, 100);
    }

    // Tax computation
    $taxableIncome = $monthlySalary - $deductions['sss'] - $deductions['philhealth'] - $deductions['pagibig'];
    if ($taxableIncome <= 20833) {
        $deductions['tax'] = 0;
    } elseif ($taxableIncome <= 33333) {
        $deductions['tax'] = ($taxableIncome - 20833) * 0.20;
    } elseif ($taxableIncome <= 66667) {
        $deductions['tax'] = 2500 + ($taxableIncome - 33333) * 0.25;
    } elseif ($taxableIncome <= 166667) {
        $deductions['tax'] = 10833 + ($taxableIncome - 66667) * 0.30;
    } elseif ($taxableIncome <= 666667) {
        $deductions['tax'] = 40833.33 + ($taxableIncome - 166667) * 0.32;
    } else {
        $deductions['tax'] = 200833.33 + ($taxableIncome - 666667) * 0.35;
    }

    return $deductions;
}

// Function to get attendance
function getEmployeeAttendance($conn, $employeeId, $startDate, $endDate) {
    $query = "SELECT * FROM attendance WHERE employee_id = ? AND attendance_date BETWEEN ? AND ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("iss", $employeeId, $startDate, $endDate);
    $stmt->execute();
    $result = $stmt->get_result();

    $attendance = ['present' => 0, 'absent' => 0, 'leave' => 0, 'total_salary' => 0];
    while ($row = $result->fetch_assoc()) {
        $attendance[$row['status']]++;
        if ($row['status'] == 'present' || $row['status'] == 'leave') {
            $attendance['total_salary'] += $row['daily_salary'];
        }
    }
    return $attendance;
}

// Payslip reference
function generatePayslipRef() {
    return 'PS-' . date('Ymd') . '-' . substr(str_shuffle("0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 6);
}

// Display Form
if (!isset($_POST['employee_id'])) {
    $employeeResult = $conn->query("SELECT id, first_name, last_name FROM employees");
    echo '<h2>Generate Payslip</h2>
    <form method="post" action="manage_payroll.php">
        <label>Employee:</label>
        <select name="employee_id" required>';
    while ($row = $employeeResult->fetch_assoc()) {
        echo "<option value='{$row['id']}'>{$row['first_name']} {$row['last_name']}</option>";
    }
    echo '</select><br>
        <label>From Date:</label>
        <input type="date" name="from_date" required><br>
        <label>To Date:</label>
        <input type="date" name="to_date" required><br>
        <button type="submit">Generate Payslip</button>
    </form>';
}

// Display Payslip
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['employee_id'])) {
    $employeeId = $_POST['employee_id'];
    $from = $_POST['from_date'];
    $to = $_POST['to_date'];

    $empQuery = $conn->prepare("SELECT * FROM employees WHERE id = ?");
    $empQuery->bind_param("i", $employeeId);
    $empQuery->execute();
    $empResult = $empQuery->get_result();
    $employee = $empResult->fetch_assoc();

    $basic_salary = $employee['monthly_salary'];
    $attendance_data = getEmployeeAttendance($conn, $employeeId, $from, $to);
    $deductions = calculateDeductions($basic_salary);

    $gross = $attendance_data['total_salary'];
    $total_deductions = array_sum($deductions);
    $net = $gross - $total_deductions;
    $payslip_ref = generatePayslipRef();

    echo '<hr><div id="payslip">';
    echo "<h3>Payslip: {$payslip_ref}</h3>";
    echo "<p><strong>Employee:</strong> {$employee['first_name']} {$employee['last_name']}</p>";
    echo "<p><strong>Period:</strong> " . date("M d, Y", strtotime($from)) . " to " . date("M d, Y", strtotime($to)) . "</p>";

    echo "<h4>Attendance</h4>";
    echo "<ul>
        <li>Present: {$attendance_data['present']}</li>
        <li>Leave: {$attendance_data['leave']}</li>
        <li>Absent: {$attendance_data['absent']}</li>
    </ul>";

    echo "<h4>Salary & Deductions</h4>";
    echo "<ul>
        <li>Gross Salary: ₱" . number_format($gross, 2) . "</li>
        <li>SSS: ₱" . number_format($deductions['sss'], 2) . "</li>
        <li>PhilHealth: ₱" . number_format($deductions['philhealth'], 2) . "</li>
        <li>Pag-IBIG: ₱" . number_format($deductions['pagibig'], 2) . "</li>
        <li>Tax: ₱" . number_format($deductions['tax'], 2) . "</li>
        <li><strong>Total Deductions: ₱" . number_format($total_deductions, 2) . "</strong></li>
        <li><strong>Net Salary: ₱" . number_format($net, 2) . "</strong></li>
    </ul>";

    echo '<button onclick="window.print()">Print Payslip</button>';
    echo "</div><script>window.location.hash = '#payslip';</script>";
}
?>
