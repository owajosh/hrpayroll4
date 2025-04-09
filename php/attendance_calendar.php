<?php
include 'db.php';

// Get filter parameters
$selected_employee = isset($_GET['employee_id']) ? intval($_GET['employee_id']) : 0;
$filter_date = isset($_GET['filter_date']) ? htmlspecialchars($_GET['filter_date']) : date("Y-m-d");
$filter_month = date("m", strtotime($filter_date));
$filter_year = date("Y", strtotime($filter_date));

// Get attendance records for selected employee and month, kasama na ang time fields at overtime
$records = [];
if ($selected_employee > 0) {
    $start_date = "$filter_year-$filter_month-01";
    $end_date = date("Y-m-t", strtotime($start_date));
    $sql = "SELECT attendance_date, status, daily_salary, time_in, time_out, overtime_hours 
            FROM attendance 
            WHERE employee_id = ? AND attendance_date BETWEEN ? AND ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iss", $selected_employee, $start_date, $end_date);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $records[$row['attendance_date']] = $row;
    }
    $stmt->close();
}

// Get employee list for dropdown
$employees = [];
$result = $conn->query("SELECT id, first_name, middle_name, last_name FROM employee ORDER BY first_name ASC");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $employees[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Attendance Calendar</title>
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Font Awesome & Boxicons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link href="https://cdn.jsdelivr.net/npm/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
  <!-- Poppins Font from Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  
  <style>
    :root {
      --primary: #4361ee;
      --secondary: #3f37c9;
      --success: #4cc9f0;
      --info: #4895ef;
      --warning: #f9c74f;
      --danger: #f94144;
      --light: #f8f9fa;
      --dark: #212529;
    }
    
    body {
      font-family: 'Poppins', Roboto, 'Helvetica Neue', sans-serif;
      background-color: #f5f7fa;
    }
    
    /* Main content style from base template */
    #mainContent {
      margin-left: 250px; 
      padding: 20px;
      transition: margin-left 0.3s ease;
      min-height: 100vh;
      background-color: #f4f6f9;
    }
    
    .container {
      background-color: white;
      border-radius: 12px;
      box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
      padding: 25px 30px;
      margin-top: 30px;
      margin-bottom: 30px;
    }
    
    h2 {
      color: var(--dark);
      font-weight: 600;
      border-bottom: 2px solid var(--primary);
      padding-bottom: 10px;
      margin-bottom: 25px;
    }
    
    .filter-card {
      background: white;
      border-radius: 10px;
      box-shadow: 0 3px 10px rgba(0, 0, 0, 0.05);
      padding: 20px;
      margin-bottom: 30px;
    }
    
    .btn-primary {
      background-color: var(--primary);
      border-color: var(--primary);
      padding: 10px 20px;
      font-weight: 500;
      transition: all 0.3s ease;
    }
    
    .btn-primary:hover {
      background-color: var(--secondary);
      border-color: var(--secondary);
      transform: translateY(-2px);
      box-shadow: 0 4px 8px rgba(67, 97, 238, 0.3);
    }
    
    .form-select, .form-control {
      border-radius: 8px;
      border: 1px solid #e1e5eb;
      padding: 12px 15px;
      transition: all 0.3s;
    }
    
    .form-select:focus, .form-control:focus {
      border-color: var(--primary);
      box-shadow: 0 0 0 0.25rem rgba(67, 97, 238, 0.25);
    }
    
    .form-label {
      font-weight: 500;
      color: #495057;
    }
    
    .calendar-container {
      background: white;
      border-radius: 10px;
      box-shadow: 0 3px 10px rgba(0, 0, 0, 0.05);
      padding: 20px;
      overflow: hidden;
    }
    
    .calendar-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 15px;
    }
    
    .month-title {
      font-size: 1.4rem;
      font-weight: 600;
      color: var(--dark);
      margin: 0;
    }
    
    .calendar-table {
      border-collapse: separate;
      border-spacing: 5px;
      width: 100%;
    }
    
    .calendar-table th {
      background-color: #f8f9fa;
      color: #6c757d;
      text-align: center;
      padding: 10px;
      font-weight: 500;
      border-radius: 6px;
    }
    
    .calendar-day {
      border-radius: 10px;
      height: 110px;
      vertical-align: top;
      padding: 8px;
      cursor: pointer;
      transition: all 0.2s ease;
      position: relative;
      background-color: #f9f9f9;
    }
    
    .calendar-day:hover {
      transform: translateY(-3px);
      box-shadow: 0 5px 10px rgba(0, 0, 0, 0.1);
    }
    
    .day-number {
      font-weight: 600;
      font-size: 1.1rem;
      margin-bottom: 5px;
      display: inline-block;
    }
    
    .status-badge {
      display: inline-block;
      padding: 4px 8px;
      border-radius: 30px;
      font-size: 0.75rem;
      font-weight: 500;
      text-transform: uppercase;
    }
    
    .present {
      background-color: rgba(76, 201, 240, 0.2);
      border-left: 3px solid var(--success);
    }
    
    .present .status-badge {
      background-color: var(--success);
      color: white;
    }
    
    .absent {
      background-color: rgba(249, 65, 68, 0.1);
      border-left: 3px solid var(--danger);
    }
    
    .absent .status-badge {
      background-color: var(--danger);
      color: white;
    }
    
    .leave {
      background-color: rgba(249, 199, 79, 0.1);
      border-left: 3px solid var(--warning);
    }
    
    .leave .status-badge {
      background-color: var(--warning);
      color: #212529;
    }
    
    .empty-day {
      background-color: #f1f1f1;
      opacity: 0.5;
    }
    
    .alert {
      border-radius: 8px;
    }
    
    .alert-success {
      background-color: rgba(76, 201, 240, 0.2);
      border-color: var(--success);
      color: #155724;
    }
    
    /* Responsive adjustments for calendar and sidebar */
    @media (max-width: 768px) {
      .calendar-day {
        height: 80px;
        padding: 5px;
      }
      
      .day-number {
        font-size: 0.9rem;
      }
      
      .status-badge {
        padding: 2px 5px;
        font-size: 0.65rem;
      }
      
      /* Adjust sidebar responsiveness */
      .admin-sidebar {
        position: relative !important;
        width: 100% !important;
        margin-bottom: 20px;
      }
      
      /* Also adjust mainContent margin */
      #mainContent {
        margin-left: 0;
      }
    }
  </style>
</head>
<body>
  <?php include 'admin_sidebar.php'; ?>
  <div class="container-fluid">
    <div id="mainContent">
      <div class="container">
        <h2><i class="fas fa-calendar-alt me-2"></i>Attendance Calendar</h2>
        
        <?php if (isset($_GET['success'])): ?>
          <div class="alert alert-success">
            <i class="fas fa-check-circle me-2"></i>Attendance record saved successfully.
          </div>
        <?php endif; ?>

        <!-- Filter Form -->
        <div class="filter-card">
          <form method="GET" action="attendance_calendar.php" class="row g-3">
            <div class="col-md-5">
              <label for="employee_id" class="form-label"><i class="fas fa-user me-1"></i> Select Employee</label>
              <select class="form-select" id="employee_id" name="employee_id" required>
                <option value="">-- Select Employee --</option>
                <?php foreach ($employees as $emp): ?>
                  <option value="<?= $emp['id'] ?>" <?= ($emp['id'] == $selected_employee) ? 'selected' : '' ?>>
                    <?= $emp['first_name'] . " " . ($emp['middle_name'] ? $emp['middle_name'] . " " : "") . $emp['last_name'] ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-4">
              <label for="filter_date" class="form-label"><i class="fas fa-calendar me-1"></i> Select Month</label>
              <input type="date" class="form-control" id="filter_date" name="filter_date" value="<?= $filter_date ?>" required>
            </div>
            <div class="col-md-3 align-self-end">
              <button type="submit" class="btn btn-primary w-100">
                <i class="fas fa-search me-1"></i> Load Calendar
              </button>
            </div>
          </form>
        </div>
        
        <?php if ($selected_employee > 0): ?>
          <div class="calendar-container">
            <div class="calendar-header">
              <h4 class="month-title"><i class="fas fa-calendar-week me-2"></i><?= date("F Y", strtotime("$filter_year-$filter_month-01")) ?></h4>
              <div class="legend d-flex gap-3">
                <span><span class="status-badge present">Present</span></span>
                <span><span class="status-badge absent">Absent</span></span>
                <span><span class="status-badge leave">Leave</span></span>
              </div>
            </div>
            <table class="calendar-table">
              <thead>
                <tr>
                  <th>Sunday</th>
                  <th>Monday</th>
                  <th>Tuesday</th>
                  <th>Wednesday</th>
                  <th>Thursday</th>
                  <th>Friday</th>
                  <th>Saturday</th>
                </tr>
              </thead>
              <tbody>
                <?php
                $firstDayOfMonth = date("w", strtotime("$filter_year-$filter_month-01"));
                $totalDays = date("t", strtotime("$filter_year-$filter_month-01"));
                $day = 1;
                for ($i = 0; $i < ceil(($firstDayOfMonth + $totalDays) / 7); $i++) {
                    echo "<tr>";
                    for ($j = 0; $j < 7; $j++) {
                        if ($i == 0 && $j < $firstDayOfMonth) {
                            echo "<td class='calendar-day empty-day'></td>";
                        } elseif ($day > $totalDays) {
                            echo "<td class='calendar-day empty-day'></td>";
                        } else {
                            $currentDate = date("Y-m-d", strtotime("$filter_year-$filter_month-$day"));
                            $statusClass = "";
                            $statusText = "";
                            $salaryText = "";
                            if (isset($records[$currentDate])) {
                                $status = $records[$currentDate]['status'];
                                $statusText = ucfirst($status);
                                $statusClass = $status;
                                if (isset($records[$currentDate]['daily_salary'])) {
                                    $salaryText = "" . number_format($records[$currentDate]['daily_salary'], 2);
                                }
                            }
                            
                            // Gumamit ng relative URL para manatili sa kasalukuyang host/port
                            echo "<td class='calendar-day $statusClass' onclick=\"window.location.href='attendance_form.php?employee_id=$selected_employee&attendance_date=$currentDate'\">";
                            echo "<span class='day-number'>$day</span>";
                            if ($statusText) {
                                echo "<div><span class='status-badge $statusClass'>$statusText</span></div>";
                                if ($salaryText) {
                                    echo "<div class='mt-2'><small>$salaryText</small></div>";
                                }
                            }
                            echo "</td>";
                            $day++;
                        }
                    }
                    echo "</tr>";
                }
                ?>
              </tbody>
            </table>
          </div>
        <?php else: ?>
          <div class="text-center py-5">
            <i class="fas fa-calendar-alt fa-4x text-muted mb-3"></i>
            <p class="lead">Please select an employee to view attendance calendar</p>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
  
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
