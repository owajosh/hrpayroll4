<?php
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "userman";

$conn = mysqli_connect($host, $user, $pass, $dbname);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Function to calculate government deductions
function calculateDeductions($monthlySalary) {
    $deductions = [];

    if ($monthlySalary <= 3250) {
        $deductions['sss'] = 135.00;
    } elseif ($monthlySalary <= 24750) {
        $sssRate = floor(($monthlySalary - 3250) / 500);
        $deductions['sss'] = 135.00 + ($sssRate * 22.50);
    } else {
        $deductions['sss'] = 1125.00;
    }

    $philHealthRate = 0.02;
    $philHealthContribution = $monthlySalary * $philHealthRate;
    $deductions['philhealth'] = max(300, min($philHealthContribution, 1800));

    if ($monthlySalary <= 1500) {
        $deductions['pagibig'] = $monthlySalary * 0.01;
    } else {
        $deductions['pagibig'] = min($monthlySalary * 0.02, 100);
    }

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

function getEmployeeAttendance($conn, $employeeId, $startDate, $endDate) {
    $query = "SELECT * FROM attendance WHERE employee_id = ? AND attendance_date BETWEEN ? AND ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("iss", $employeeId, $startDate, $endDate);
    $stmt->execute();
    $result = $stmt->get_result();

    $attendance = [
        'present' => 0,
        'absent' => 0,
        'leave' => 0,
        'total_salary' => 0
    ];

    while ($row = $result->fetch_assoc()) {
        $attendance[$row['status']]++;
        if ($row['status'] == 'present' || $row['status'] == 'leave') {
            $attendance['total_salary'] += $row['daily_salary'];
        }
    }

    return $attendance;
}

function generatePayslipRef() {
    return 'PS-' . date('Ymd') . '-' . substr(str_shuffle("0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 6);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Payslip Generator</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .payslip-container { max-width: 800px; margin: 0 auto; }
        .payslip { background-color: white; border: 1px solid #ddd; padding: 20px; margin-top: 20px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        .company-header { text-align: center; margin-bottom: 20px; padding-bottom: 20px; border-bottom: 2px solid #ddd; }
        .payslip-title { text-align: center; font-weight: bold; font-size: 1.5em; margin: 15px 0; background-color: #f8f9fa; padding: 10px; border-radius: 5px; }
        .table th { background-color: #f8f9fa; }
        @media print {
            .no-print { display: none; }
            body { background-color: white; }
        }
    </style>
</head>
<body>
<div class="container mt-5">
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h3 class="mb-0">Payslip Generator</h3>
        </div>
        <div class="card-body">
            <form id="payslipForm">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="employee" class="form-label">Select Employee</label>
                        <select class="form-select" id="employee" name="employee" required>
                            <option value="">-- Select Employee --</option>
                            <?php
                            $query = "SELECT id, first_name, middle_name, last_name FROM employee WHERE status = 'active'";
                            $result = $conn->query($query);
                            while ($row = $result->fetch_assoc()) {
                                $middleInitial = !empty($row['middle_name']) ? ' ' . substr($row['middle_name'], 0, 1) . '.' : '';
                                echo "<option value='{$row['id']}'>{$row['last_name']}, {$row['first_name']}{$middleInitial}</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="start_date" class="form-label">Start Date</label>
                        <input type="date" class="form-control" id="start_date" name="start_date" required>
                    </div>
                    <div class="col-md-3">
                        <label for="end_date" class="form-label">End Date</label>
                        <input type="date" class="form-control" id="end_date" name="end_date" required>
                    </div>
                </div>
                <div class="text-center">
                    <button type="button" id="generateBtn" class="btn btn-primary">Generate Payslip</button>
                </div>
            </form>
        </div>
    </div>

    <div id="payslipOutput" class="payslip-container" style="display: none;"></div>
</div>

<script>
    document.getElementById('generateBtn').addEventListener('click', function () {
        const employeeId = document.getElementById('employee').value;
        const startDate = document.getElementById('start_date').value;
        const endDate = document.getElementById('end_date').value;

        if (!employeeId || !startDate || !endDate) {
            alert('Please fill all required fields');
            return;
        }

        fetch('generate_payslip.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `employee_id=${employeeId}&start_date=${startDate}&end_date=${endDate}`
        })
        .then(response => response.text())
        .then(data => {
            document.getElementById('payslipOutput').innerHTML = data;
            document.getElementById('payslipOutput').style.display = 'block';
        })
        .catch(error => {
            console.error('Error:', error);
        });
    });
</script>
</body>
</html>
