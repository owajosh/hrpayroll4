<?php
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_payroll'])) {
    $payroll_id = $_POST['payroll_id'];
    $basic_salary = $_POST['basic_salary'];
    $overtime_pay = $_POST['overtime_pay'];
    $holiday_pay = $_POST['holiday_pay'];
    $deductions = $_POST['deductions'];

    // Compute Net Salary
    $net_salary = ($basic_salary + $overtime_pay + $holiday_pay) - $deductions;

    // Update Payroll Record
    $query = "UPDATE payroll SET 
                basic_salary = '$basic_salary', 
                overtime_pay = '$overtime_pay', 
                holiday_pay = '$holiday_pay', 
                deductions = '$deductions',
                net_salary = '$net_salary'
              WHERE payroll_id = '$payroll_id'";

    if (mysqli_query($conn, $query)) {
        echo "<script>alert('Payroll Updated Successfully!'); window.location.href='manage_payroll.php';</script>";
    } else {
        echo "<script>alert('Error updating payroll!'); window.location.href='manage_payroll.php';</script>";
    }
}
?>
