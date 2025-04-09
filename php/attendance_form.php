<?php
include 'db.php';

// Kunin ang employee at pre-selected na date kung meron, o default values
$employee_id = isset($_GET['employee_id']) ? intval($_GET['employee_id']) : 0;
$attendance_date = isset($_GET['attendance_date']) ? htmlspecialchars($_GET['attendance_date']) : date("Y-m-d");
$status = "present"; // default na status
$daily_salary = "";
$time_in = "";   // bagong field
$time_out = "";  // bagong field

// Process attendance record submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_attendance'])) {
    $employee_id = intval($_POST['employee_id']);
    $attendance_date = htmlspecialchars($_POST['attendance_date']);
    $status = htmlspecialchars($_POST['status']); // present, absent, leave, holiday
    $daily_salary = floatval($_POST['daily_salary']);
    $time_in = !empty($_POST['time_in']) ? $_POST['time_in'] : null;
    $time_out = !empty($_POST['time_out']) ? $_POST['time_out'] : null;
    
    // Compute overtime hours kung parehong available ang time_in at time_out
    $overtime_hours = 0.00;
    if ($time_in && $time_out) {
        $timeInDT = new DateTime($time_in);
        $timeOutDT = new DateTime($time_out);
        $interval = $timeInDT->diff($timeOutDT);
        // Kumuha ng kabuuang oras bilang decimal. (hal. 8.50 means 8 oras at 30 minuto)
        $workedHours = $interval->h + ($interval->i / 60);
        // Halimbawa: Kung lumagpas ng 8 oras, ang labis ay itinuturing na overtime
        $overtime_hours = ($workedHours > 8) ? $workedHours - 8 : 0;
        $overtime_hours = round($overtime_hours, 2);
    }
    
    // Check kung may existing record para sa employee sa partikular na date
    $sql_check = "SELECT id FROM attendance WHERE employee_id = ? AND attendance_date = ?";
    $stmt_check = $conn->prepare($sql_check);
    $stmt_check->bind_param("is", $employee_id, $attendance_date);
    $stmt_check->execute();
    $stmt_check->store_result();
    
    if ($stmt_check->num_rows > 0) {
        // May record na, i-update ito kasama na ang time fields at overtime
        $sql_update = "UPDATE attendance 
                       SET status = ?, daily_salary = ?, time_in = ?, time_out = ?, overtime_hours = ? 
                       WHERE employee_id = ? AND attendance_date = ?";
        $stmt_update = $conn->prepare($sql_update);
        $stmt_update->bind_param("sdsdsis", $status, $daily_salary, $time_in, $time_out, $overtime_hours, $employee_id, $attendance_date);
        $stmt_update->execute();
        $stmt_update->close();
    } else {
        // Walang record, insert bagong record
        $sql_insert = "INSERT INTO attendance (employee_id, attendance_date, status, daily_salary, time_in, time_out, overtime_hours) 
                       VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt_insert = $conn->prepare($sql_insert);
        $stmt_insert->bind_param("issdssd", $employee_id, $attendance_date, $status, $daily_salary, $time_in, $time_out, $overtime_hours);
        $stmt_insert->execute();
        $stmt_insert->close();
    }
    $stmt_check->close();
    // Pagkatapos ng pag-save, ire-redirect sa calendar view
    header("Location: attendance_calendar.php?employee_id=$employee_id&filter_date=$attendance_date&success=1");
    exit;
}

// Kunin ang listahan ng mga employee para sa dropdown
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
  <title>Attendance Entry</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    /* Optional: dagdagan ng kaunting CSS kung kinakailangan */
  </style>
</head>
<body class="bg-light">
  <?php include 'admin_sidebar.php'; ?>
  <div class="container my-5">
    <h2>Attendance Entry</h2>
    
    <?php if (isset($_GET['success'])): ?>
      <div class="alert alert-success">Attendance record saved successfully.</div>
    <?php endif; ?>
    
    <form method="POST" action="attendance_form.php">
      <div class="mb-3">
        <label for="employee_id" class="form-label">Select Employee</label>
        <select class="form-select" id="employee_id" name="employee_id" required>
          <option value="">-- Select Employee --</option>
          <?php foreach ($employees as $emp): ?>
            <option value="<?= $emp['id'] ?>" <?= ($emp['id'] == $employee_id) ? 'selected' : '' ?>>
              <?= $emp['first_name'] . " " . ($emp['middle_name'] ? $emp['middle_name'] . " " : "") . $emp['last_name'] ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>
      
      <div class="mb-3">
        <label for="attendance_date" class="form-label">Attendance Date</label>
        <input type="date" class="form-control" id="attendance_date" name="attendance_date" value="<?= $attendance_date ?>" required>
      </div>
      
      <!-- Bagong fields para sa time_in at time_out -->
      <div class="mb-3">
        <label for="time_in" class="form-label">Time In</label>
        <input type="time" class="form-control" id="time_in" name="time_in" value="<?= $time_in ?>">
      </div>
      
      <div class="mb-3">
        <label for="time_out" class="form-label">Time Out</label>
        <input type="time" class="form-control" id="time_out" name="time_out" value="<?= $time_out ?>">
      </div>
      
      <div class="mb-3">
        <label class="form-label">Status</label>
        <div>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="status" id="present" value="present" <?= ($status == "present") ? "checked" : "" ?>>
            <label class="form-check-label" for="present">Present</label>
          </div>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="status" id="absent" value="absent">
            <label class="form-check-label" for="absent">Absent</label>
          </div>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="status" id="leave" value="leave">
            <label class="form-check-label" for="leave">Leave</label>
          </div>
          <!-- Idagdag ang Holiday option kung nais mo, siguraduhing updated din ang enum sa database -->
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="status" id="holiday" value="holiday">
            <label class="form-check-label" for="holiday">Holiday</label>
          </div>
        </div>
      </div>
      
      <div class="mb-3">
        <label for="daily_salary" class="form-label">Daily Salary (PHP)</label>
        <input type="number" step="0.01" class="form-control" id="daily_salary" name="daily_salary" value="<?= $daily_salary ?>" required>
      </div>
      
      <button type="submit" name="submit_attendance" class="btn btn-success">Save Attendance</button>
    </form>
    
    <hr>
    <!-- Link papuntang calendar view -->
    <a href="attendance.php?employee_id=<?= $employee_id ?>&filter_date=<?= $attendance_date ?>" class="btn btn-primary mt-3">View Calendar</a>
  </div>
  
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
