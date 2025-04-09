<?php
include 'db.php'; // Include your database connection

// Get data from AJAX POST request
$employeeName = $_POST['employeeName'];
$department = $_POST['department'];
$designation = $_POST['designation'];
$basicRate = $_POST['basicRate'];
$overtime = $_POST['overtime'];
$allowance = $_POST['allowance'];
$pagibig = $_POST['pagibig'];
$sss = $_POST['sss'];
$philhealth = $_POST['philhealth'];
$tax = $_POST['tax'];
$totalEarning = $_POST['totalEarning'];
$totalDeduction = $_POST['totalDeduction'];
$netPay = $_POST['netPay'];

// Insert into database
$query = "INSERT INTO payslips (employee_name, department, designation, basic_rate, overtime, allowance, pagibig, sss, philhealth, tax, total_earning, total_deduction, net_pay)
VALUES ('$employeeName', '$department', '$designation', '$basicRate', '$overtime', '$allowance', '$pagibig', '$sss', '$philhealth', '$tax', '$totalEarning', '$totalDeduction', '$netPay')";

if (mysqli_query($conn, $query)) {
    echo "Payslip saved successfully!";
} else {
    echo "Error: " . mysqli_error($conn);
}

mysqli_close($conn);
?>
