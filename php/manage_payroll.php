<?php
session_start();

// Kasama na dito ang connection file. Siguraduhing nasa tamang lokasyon.
include 'db.php';
include 'admin_sidebar.php';

// I-initialize ang payslip variable
$payslip = false;

// Kunin ang listahan ng mga empleyado para sa dropdown
$resultEmployeeList = $conn->query("SELECT id, first_name, last_name, department, position FROM employee");

// Kapag nag-submit ng form (POST method)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Kunin ang napiling employee ID mula sa form
    $employee_id = isset($_POST['employee_id']) ? intval($_POST['employee_id']) : 0;
    
    // Kunin ang detalye ng employee
    $stmt = $conn->prepare("SELECT * FROM employee WHERE id = ?");
    $stmt->bind_param("i", $employee_id);
    $stmt->execute();
    $employee = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    // Itakda ang current month at year (mula sa system date)
    $month = date('m');
    $year = date('Y');

    // Kunin ang mga attendance records ng employee para sa kasalukuyang buwan at taon,
    // kasama na ang overtime_hours field
    $stmt = $conn->prepare("SELECT * FROM attendance WHERE employee_id = ? AND MONTH(attendance_date) = ? AND YEAR(attendance_date) = ?");
    $stmt->bind_param("iii", $employee_id, $month, $year);
    $stmt->execute();
    $resultAttendance = $stmt->get_result();
    
    // I-initialize ang mga counter at daily rate
    $present_days = 0;
    $leave_days   = 0;
    $absent_days  = 0;
    $total_overtime_hours = 0.00; // bagong variable para sa overtime hours
    $daily_rate   = 0;
    
    // Bilangin ang mga attendance record ayon sa status at i-sum ang overtime hours kung "present"
    while ($row = $resultAttendance->fetch_assoc()) {
        // Gamitin ang daily rate mula sa unang record (inaasumeng pareho para sa buong buwan)
        if ($daily_rate == 0) {
            $daily_rate = $row['daily_salary'];
        }
        
        if ($row['status'] == 'present') {
            $present_days++;
            // Kung may overtime record, idagdag ito. Siguraduhing naka-save bilang decimal o float.
            $total_overtime_hours += floatval($row['overtime_hours']);
        } elseif ($row['status'] == 'leave') {
            $leave_days++;
        } elseif ($row['status'] == 'absent') {
            $absent_days++;
        }
    }
    $stmt->close();
    
    // Para sa payroll computation:
    // Basic salary batay sa present days lamang.
    $basic_salary = $present_days * $daily_rate;
    
    // Kalkulahin ang overtime pay
    // Hourly Rate = daily_rate / 8
    // Gamitin ang overtime multiplier; dito itinakda sa 1.25 (maaari mong baguhin kung kinakailangan)
    $overtime_multiplier = 1.25;
    $hourly_rate = $daily_rate / 8;
    $overtime_pay = $total_overtime_hours * $hourly_rate * $overtime_multiplier;
    
    // Gross pay ay basic salary plus overtime pay
    $actual_gross_pay = $basic_salary + $overtime_pay;
    
    // Optional: Bilangin ang kabuuang araw para sa pag-display (maaaring hindi na kailangan para sa payroll computation)
    $total_days = $present_days + $leave_days + $absent_days;
    
    // Kalkulasyon ng mga deduction para sa absent at leave (kung hindi bayad)
    // Kung hindi binabayaran ang leave days, ibawas ito.
    $absent_deduction = $absent_days * $daily_rate;
    $leave_deduction  = $leave_days * $daily_rate;
    
    // Deductions para sa statutory contributions ay base sa gross pay
    $sss        = round($actual_gross_pay * 0.04, 2);
    $philhealth = round($actual_gross_pay * 0.02, 2);
    $pagibig    = round($actual_gross_pay * 0.01, 2);
    $total_deductions = $sss + $philhealth + $pagibig + $absent_deduction + $leave_deduction;
    
    // Kalkulahin ang net pay
    $net_pay = $actual_gross_pay - $total_deductions;
    
    // Ibuo ang array na gagamitin sa payslip view
    $payslip = [
        'employee' => [
            'first_name' => $employee['first_name'],
            'last_name'  => $employee['last_name'],
            'department' => $employee['department'],
            'position'   => $employee['position']
        ],
        'year' => $year,
        'month' => $month,
        'present_days' => $present_days,
        'leave_days' => $leave_days,
        'absent_days' => $absent_days,
        'daily_rate' => $daily_rate,
        'total_overtime_hours' => round($total_overtime_hours, 2),
        'overtime_pay' => round($overtime_pay, 2),
        'basic_salary' => $basic_salary,
        'gross_pay' => $actual_gross_pay,
        'absent_deduction' => $absent_deduction,
        'leave_deduction' => $leave_deduction,
        'sss' => $sss,
        'philhealth' => $philhealth,
        'pagibig' => $pagibig,
        'total_deductions' => $total_deductions,
        'net_pay' => $net_pay
    ];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Payslip Generator</title>
  <!-- Bootstrap CSS -->
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
  <!-- Google Fonts -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&family=Poppins:wght@300;400;500;600;700&display=swap">
  <style>
    /* Modern at Elegant CSS styles (hindi binago) */
    :root {
      --primary: #4361ee;
      --primary-light: #4895ef;
      --secondary: #3f37c9;
      --success: #0bb37e;
      --success-light: #4cc9a0;
      --info: #4cc9f0;
      --warning: #f8961e;
      --danger: #ef476f;
      --light: #f8f9fa;
      --dark: #212529;
      --gray-200: #e9ecef;
      --gray-700: #495057;
      --border-radius: 12px;
      --box-shadow: 0 10px 30px rgba(0,0,0,0.08);
      --transition: all 0.3s ease;
    }
    
    body {
      background-color: #f0f2f5;
      font-family: 'Poppins', sans-serif;
      color: var(--gray-700);
      line-height: 1.6;
    }
    
    h1, h2, h3, h4, h5, h6 {
      font-family: 'Montserrat', sans-serif;
    }
    
    .main-container {
      max-width: 880px;
      margin: 40px auto;
      padding: 0 15px;
    }
    
    .page-header {
      margin-bottom: 30px;
      text-align: center;
    }
    
    .page-title {
      color: var(--primary);
      font-weight: 700;
      font-size: 2.4rem;
      margin-bottom: 10px;
      position: relative;
      display: inline-block;
    }
    
    .page-title:after {
      content: '';
      position: absolute;
      width: 60%;
      height: 4px;
      background: linear-gradient(90deg, var(--primary), var(--info));
      bottom: -10px;
      left: 50%;
      transform: translateX(-50%);
      border-radius: 2px;
    }
    
    .form-container {
      background-color: #fff;
      border-radius: var(--border-radius);
      padding: 35px;
      box-shadow: var(--box-shadow);
      margin-bottom: 40px;
      transition: var(--transition);
      border: 1px solid var(--gray-200);
    }
    
    .form-container:hover {
      box-shadow: 0 15px 35px rgba(0,0,0,0.1);
      transform: translateY(-5px);
    }
    
    .payslip {
      background-color: #fff;
      border: none;
      border-radius: var(--border-radius);
      padding: 40px;
      box-shadow: var(--box-shadow);
      position: relative;
      overflow: hidden;
      transition: var(--transition);
    }
    
    .payslip:hover {
      box-shadow: 0 15px 35px rgba(0,0,0,0.1);
    }
    
    .payslip::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 8px;
      background: linear-gradient(90deg, var(--primary), var(--info));
    }
    
    .payslip-corner {
      position: absolute;
      top: 0;
      right: 0;
      width: 100px;
      height: 100px;
      overflow: hidden;
    }
    
    .payslip-corner::before {
      content: '';
      position: absolute;
      top: 0;
      right: 0;
      width: 0;
      height: 0;
      border-style: solid;
      border-width: 0 100px 100px 0;
      border-color: transparent rgba(67, 97, 238, 0.1) transparent transparent;
    }
    
    h2 {
      color: var(--primary);
      font-weight: 700;
      text-align: center;
      margin-bottom: 25px;
      font-size: 2.2rem;
    }
    
    h3 {
      color: var(--primary);
      text-align: center;
      margin-bottom: 30px;
      font-weight: 700;
      letter-spacing: 1px;
      position: relative;
      padding-bottom: 15px;
    }
    
    h3:after {
      content: '';
      position: absolute;
      bottom: 0;
      left: 50%;
      transform: translateX(-50%);
      width: 60px;
      height: 3px;
      background: linear-gradient(90deg, var(--primary), var(--info));
      border-radius: 3px;
    }
    
    .section-title {
      background-color: rgba(67, 97, 238, 0.08);
      padding: 12px 20px;
      border-radius: 8px;
      margin: 30px 0 20px 0;
      color: var(--primary);
      font-weight: 600;
      font-size: 16px;
      letter-spacing: 0.5px;
      display: flex;
      align-items: center;
      position: relative;
    }
    
    .section-title i {
      margin-right: 10px;
      font-size: 18px;
    }
    
    .section-title::after {
      content: '';
      position: absolute;
      left: 10px;
      bottom: -2px;
      width: 40px;
      height: 3px;
      background: linear-gradient(90deg, var(--primary), var(--info));
      border-radius: 3px;
    }
    
    .payment-card {
      position: relative;
      padding: 20px;
      background-color: rgba(248, 249, 250, 0.6);
      border-radius: 10px;
      margin-bottom: 5px;
    }
    
    .details-row {
      display: flex;
      justify-content: space-between;
      margin-bottom: 15px;
      padding: 5px 0;
      border-bottom: 1px dotted var(--gray-200);
    }
    
    .details-row:last-child {
      border-bottom: none;
    }
    
    .details-row strong {
      min-width: 150px;
      color: var(--gray-700);
      font-weight: 600;
      display: flex;
      align-items: center;
    }
    
    .details-row strong i {
      margin-right: 8px;
      color: var(--primary);
      font-size: 16px;
      width: 20px;
      text-align: center;
    }
    
    .details-row span {
      font-weight: 500;
      color: var(--gray-800);
    }
    
    hr {
      margin: 25px 0;
      border: none;
      height: 1px;
      background: linear-gradient(90deg, transparent, var(--gray-200), transparent);
    }
    
    .total-row {
      font-weight: 700;
      font-size: 22px;
      color: var(--primary);
      text-align: right;
      padding: 18px 0;
      margin-top: 15px;
      background-color: rgba(67, 97, 238, 0.05);
      border-radius: 8px;
      padding-right: 20px;
      display: flex;
      justify-content: flex-end;
      align-items: center;
    }
    
    .total-row i {
      font-size: 24px;
      margin-right: 10px;
      color: var(--warning);
    }
    
    .print-button {
      text-align: center;
      margin-top: 35px;
    }
    
    .btn-print {
      background: linear-gradient(45deg, var(--success), var(--success-light));
      border: none;
      color: white;
      padding: 12px 35px;
      border-radius: 8px;
      font-weight: 600;
      transition: var(--transition);
      box-shadow: 0 4px 10px rgba(11, 179, 126, 0.2);
      font-size: 16px;
    }
    
    .btn-print:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 20px rgba(11, 179, 126, 0.3);
      background: linear-gradient(45deg, #09a06f, var(--success));
    }
    
    .currency-value {
      font-family: 'Montserrat', sans-serif;
      font-weight: 600;
      background-color: rgba(248, 249, 250, 0.8);
      padding: 5px 10px;
      border-radius: 6px;
      min-width: 100px;
      text-align: right;
      position: relative;
    }
    
    .deduction .currency-value {
      color: var(--danger);
    }
    
    .earning .currency-value {
      color: var(--success);
    }
    
    .payslip-footer {
      margin-top: 40px;
      padding-top: 20px;
      border-top: 1px solid var(--gray-200);
      text-align: center;
      font-size: 12px;
      color: var(--gray-600);
    }
    
    @media print {
      body {
        background-color: #fff;
      }
      .main-container {
        margin: 0;
        padding: 0;
        width: 100%;
        max-width: 100%;
      }
      .form-container, .print-button, .btn, .page-header {
        display: none;
      }
      .payslip {
        box-shadow: none;
        border: none;
        padding: 15px 0;
      }
      .payslip::before,
      .payslip-corner::before {
        display: none;
      }
      .no-print {
        display: none;
      }
      .payslip-footer {
        margin-top: 30px;
      }
    }
  </style>
</head>
<body>
  <div class="main-container">
    <div class="page-header">
      <h2 class="page-title"><i class="fas fa-file-invoice-dollar mr-2"></i>Payslip Generator</h2>
    </div>
    
    <!-- Form para sa pagpili ng employee -->
    <div class="form-container">
      <form method="POST">
        <div class="form-group">
          <label for="employee_id"><i class="fas fa-user-tie"></i> Select Employee:</label>
          <select name="employee_id" id="employee_id" class="form-control" required>
            <option value="">--Select Employee--</option>
            <?php 
            if ($resultEmployeeList && $resultEmployeeList->num_rows > 0) {
                while ($row = $resultEmployeeList->fetch_assoc()):
            ?>
                  <option value="<?php echo $row['id']; ?>">
                    <?php echo htmlspecialchars($row['first_name'] . " " . $row['last_name']); ?>
                  </option>
            <?php 
                endwhile;
            } else {
                echo "<option value=''>Walang available na empleyado</option>";
            }
            ?>
          </select>
        </div>
        <button type="submit" class="btn btn-primary">
          <i class="fas fa-sync-alt mr-2"></i>Generate Payslip
        </button>
      </form>
    </div>

    <!-- Ipakita ang payslip kung may datos na -->
    <?php if ($payslip): ?>
      <div class="payslip" id="payslip-content">
        <div class="payslip-corner"></div>
        <div class="company-header">
          <div class="company-logo">NFD</div>
          <div class="company-name">NEXTFLEETDYNAMICS</div>
          <div class="company-slogan">Enhancing Lives Through Excellence</div>
        </div>
        
        <h3><i class="fas fa-file-invoice mr-2"></i>PAYSLIP</h3>
        
        <div class="section-title">
          <i class="fas fa-user-circle"></i> Employee Information
        </div>
        <div class="payment-card">
          <div class="details-row">
            <strong><i class="fas fa-id-card"></i> Employee Name:</strong>
            <span><?php echo htmlspecialchars($payslip['employee']['first_name'] . " " . $payslip['employee']['last_name']); ?></span>
          </div>
          <div class="details-row">
            <strong><i class="fas fa-building"></i> Department:</strong> 
            <span><?php echo htmlspecialchars($payslip['employee']['department']); ?></span>
          </div>
          <div class="details-row">
            <strong><i class="fas fa-briefcase"></i> Position:</strong>
            <span><?php echo htmlspecialchars($payslip['employee']['position']); ?></span>
          </div>
          <div class="details-row">
            <strong><i class="fas fa-calendar-alt"></i> Pay Period:</strong>
            <span><?php echo date("F Y", strtotime($payslip['year'] . "-" . $payslip['month'] . "-01")); ?></span>
          </div>
        </div>
        
        <div class="section-title">
          <i class="fas fa-calendar-check"></i> Attendance Summary
        </div>
        <div class="payment-card">
          <div class="details-row">
            <strong><i class="fas fa-check-circle"></i> Present Days:</strong>
            <span><span class="badge badge-success"><?php echo $payslip['present_days']; ?> days</span></span>
          </div>
          <div class="details-row">
            <strong><i class="fas fa-umbrella-beach"></i> Leave Days:</strong>
            <span><span class="badge badge-info"><?php echo $payslip['leave_days']; ?> days</span></span>
          </div>
          <div class="details-row">
            <strong><i class="fas fa-times-circle"></i> Absent Days:</strong>
            <span><span class="badge badge-danger"><?php echo $payslip['absent_days']; ?> days</span></span>
          </div>
          <div class="details-row">
            <strong><i class="fas fa-clock"></i> Total Overtime Hours:</strong>
            <span><?php echo $payslip['total_overtime_hours']; ?> hrs</span>
          </div>
        </div>
        
        <div class="section-title">
          <i class="fas fa-money-bill-wave"></i> Salary Details
        </div>
        <div class="payment-card earning">
          <div class="details-row">
            <strong><i class="fas fa-hand-holding-usd"></i> Basic Salary:</strong>
            <span class="currency-value">₱<?php echo number_format($payslip['basic_salary'], 2); ?></span>
          </div>
          <div class="details-row">
            <strong><i class="fas fa-stopwatch"></i> Overtime Pay:</strong>
            <span class="currency-value">₱<?php echo number_format($payslip['overtime_pay'], 2); ?></span>
          </div>
          <div class="details-row">
            <strong><i class="fas fa-hand-holding-usd"></i> Gross Pay:</strong>
            <span class="currency-value">₱<?php echo number_format($payslip['gross_pay'], 2); ?></span>
          </div>
        </div>
        
        <div class="section-title">
          <i class="fas fa-minus-circle"></i> Deductions
        </div>
        <div class="payment-card deduction">
          <div class="details-row">
            <strong><i class="fas fa-building"></i> SSS (4%):</strong>
            <span class="currency-value">₱<?php echo number_format($payslip['sss'], 2); ?></span>
          </div>
          <div class="details-row">
            <strong><i class="fas fa-heartbeat"></i> PhilHealth (2%):</strong>
            <span class="currency-value">₱<?php echo number_format($payslip['philhealth'], 2); ?></span>
          </div>
          <div class="details-row">
            <strong><i class="fas fa-home"></i> Pag-IBIG (1%):</strong>
            <span class="currency-value">₱<?php echo number_format($payslip['pagibig'], 2); ?></span>
          </div>
          <div class="details-row">
            <strong><i class="fas fa-user-times"></i> Absent Deduction:</strong>
            <span class="currency-value">₱<?php echo number_format($payslip['absent_deduction'], 2); ?></span>
          </div>
          <div class="details-row">
            <strong><i class="fas fa-user-times"></i> Leave Deduction:</strong>
            <span class="currency-value">₱<?php echo number_format($payslip['leave_deduction'], 2); ?></span>
          </div>
          <div class="details-row">
            <strong><i class="fas fa-calculator"></i> Total Deductions:</strong>
            <span class="currency-value">₱<?php echo number_format($payslip['total_deductions'], 2); ?></span>
          </div>
        </div>
        
        <hr>
        <div class="total-row">
          <i class="fas fa-coins"></i> Net Pay: ₱<?php echo number_format($payslip['net_pay'], 2); ?>
        </div>
        
        <div class="print-button no-print">
          <button type="button" class="btn btn-print" onclick="printPayslip()">
            <i class="fas fa-print mr-2"></i> Print Payslip
          </button>
        </div>
        
        <div class="payslip-footer no-print">
          This is a computer-generated document. No signature is required.
        </div>
      </div>
    <?php endif; ?>
  </div>

  <!-- Bootstrap JS at dependencies -->
  <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
  
  <script>
    function printPayslip() {
      window.print();
    }
  </script>
</body>
</html>
