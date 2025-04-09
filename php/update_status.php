<?php
session_start();
include 'db.php';

// Sample function to predict net salary using AI (e.g., TensorFlow.js or some trained AI model)
function predictPayroll($basic_salary, $overtime_pay, $holiday_pay, $deductions) {
    // Simple formula, can be replaced with actual AI prediction logic
    $predicted_salary = $basic_salary + $overtime_pay + $holiday_pay - $deductions;
    return $predicted_salary;
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_status'])) {
    $payroll_id = $_POST['payroll_id'];
    $status = $_POST['status'];

    // Fetch the current payroll record to calculate the prediction
    $query = "SELECT * FROM payroll WHERE payroll_id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $payroll_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $payroll = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);

    // Predict the new net salary based on the data
    $predicted_net_salary = predictPayroll($payroll['basic_salary'], $payroll['overtime_pay'], $payroll['holiday_pay'], $payroll['deductions']);

    // Update payroll status and the predicted salary
    $update_query = "UPDATE payroll SET status = ?, net_salary = ? WHERE payroll_id = ?";
    $update_stmt = mysqli_prepare($conn, $update_query);
    mysqli_stmt_bind_param($update_stmt, "sdi", $status, $predicted_net_salary, $payroll_id);

    if (mysqli_stmt_execute($update_stmt)) {
        $_SESSION['message'] = "<p style='color: green;'>Payroll status and predicted salary updated successfully!</p>";
    } else {
        $_SESSION['message'] = "<p style='color: red;'>Failed to update payroll status and predicted salary.</p>";
    }

    mysqli_stmt_close($update_stmt);
    header("Location: manage_payroll.php");
    exit();
}
?>
