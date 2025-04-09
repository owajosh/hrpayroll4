<?php
include 'db.php'; // Include database connection

// Get the payroll ID to edit
$id = $_GET['id'];

// Fetch the existing payroll record
$sql = "SELECT * FROM payroll WHERE id = $id";
$result = $conn->query($sql);
$payroll = $result->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $employee_id = $_POST['employee_id'];
    $employee_name = $_POST['employee_name'];
    $basic_salary = $_POST['basic_salary'];
    $overtime_pay = $_POST['overtime_pay'];
    $holiday_pay = $_POST['holiday_pay'];
    $deductions = $_POST['deductions'];
    
    // Calculate Gross Pay and Net Pay
    $gross_pay = $basic_salary + $overtime_pay + $holiday_pay;
    $net_pay = $gross_pay - $deductions;

    $pay_date = $_POST['pay_date'];

    // Update the payroll record in the database
    $sql = "UPDATE payroll SET employee_id = '$employee_id', employee_name = '$employee_name', basic_salary = '$basic_salary', 
            overtime_pay = '$overtime_pay', holiday_pay = '$holiday_pay', deductions = '$deductions', gross_pay = '$gross_pay', 
            net_pay = '$net_pay', pay_date = '$pay_date' WHERE id = $id";

    if ($conn->query($sql) === TRUE) {
        echo "Payroll record updated successfully.";
        header("Location: manage_payroll.php"); // Redirect to the payroll management page after successful update
    } else {
        echo "Error: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Payroll</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h2>Edit Payroll</h2>
        <form method="POST">
            <div class="form-group">
                <label for="employee_id">Employee ID</label>
                <input type="number" class="form-control" id="employee_id" name="employee_id" value="<?php echo $payroll['employee_id']; ?>" required>
            </div>
            <div class="form-group">
                <label for="employee_name">Employee Name</label>
                <input type="text" class="form-control" id="employee_name" name="employee_name" value="<?php echo $payroll['employee_name']; ?>" required>
            </div>
            <div class="form-group">
                <label for="basic_salary">Basic Salary</label>
                <input type="number" class="form-control" id="basic_salary" name="basic_salary" value="<?php echo $payroll['basic_salary']; ?>" required oninput="calculatePayroll()">
            </div>
            <div class="form-group">
                <label for="overtime_pay">Overtime Pay</label>
                <input type="number" class="form-control" id="overtime_pay" name="overtime_pay" value="<?php echo $payroll['overtime_pay']; ?>" oninput="calculatePayroll()">
            </div>
            <div class="form-group">
                <label for="holiday_pay">Holiday Pay</label>
                <input type="number" class="form-control" id="holiday_pay" name="holiday_pay" value="<?php echo $payroll['holiday_pay']; ?>" oninput="calculatePayroll()">
            </div>
            <div class="form-group">
                <label for="deductions">Deductions</label>
                <input type="number" class="form-control" id="deductions" name="deductions" value="<?php echo $payroll['deductions']; ?>" oninput="calculatePayroll()">
            </div>
            <div class="form-group">
                <label for="gross_pay">Gross Pay</label>
                <input type="number" class="form-control" id="gross_pay" name="gross_pay" value="<?php echo $payroll['gross_pay']; ?>" readonly>
            </div>
            <div class="form-group">
                <label for="net_pay">Net Pay</label>
                <input type="number" class="form-control" id="net_pay" name="net_pay" value="<?php echo $payroll['net_pay']; ?>" readonly>
            </div>
            <div class="form-group">
                <label for="pay_date">Pay Date</label>
                <input type="date" class="form-control" id="pay_date" name="pay_date" value="<?php echo $payroll['pay_date']; ?>" required>
            </div>
            <button type="submit" class="btn btn-primary mt-3">Update</button>
        </form>
    </div>

    <script>
        // Function to calculate the gross and net pay
        function calculatePayroll() {
            var basicSalary = parseFloat(document.getElementById('basic_salary').value) || 0;
            var overtimePay = parseFloat(document.getElementById('overtime_pay').value) || 0;
            var holidayPay = parseFloat(document.getElementById('holiday_pay').value) || 0;
            var deductions = parseFloat(document.getElementById('deductions').value) || 0;

            // Calculate Gross Pay
            var grossPay = basicSalary + overtimePay + holidayPay;

            // Calculate Net Pay
            var netPay = grossPay - deductions;

            // Set the values of Gross Pay and Net Pay
            document.getElementById('gross_pay').value = grossPay.toFixed(2);
            document.getElementById('net_pay').value = netPay.toFixed(2);
        }
    </script>
</body>
</html>
