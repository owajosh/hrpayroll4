<?php
include 'admin_sidebar.php';  // Siguraduhing mayroon kayong sidebar HTML/CSS dito.

$apiKey = getenv('GEMINI_API_KEY') ?: 'AIzaSyBY09U4wNLsiqa4AvscuRtcYUz0PrC46Hs';
$url = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key=' . $apiKey;

function cleanInput($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}

session_start();
if (!isset($_SESSION['employees'])) {
    $_SESSION['employees'] = [
        [
            'name' => 'Juan Dela Cruz',
            'attendance' => '98%',
            'productivity' => '85%',
            'overtime' => '5',
            'salary' => '35000',
            'training' => 'Completed',
            'competency' => 'Advanced'
        ],
        [
            'name' => 'Maria Santos',
            'attendance' => '95%',
            'productivity' => '88%',
            'overtime' => '8',
            'salary' => '40000',
            'training' => 'Ongoing',
            'competency' => 'Intermediate'
        ],
        [
            'name' => 'Pedro Reyes',
            'attendance' => '92%',
            'productivity' => '80%',
            'overtime' => '2',
            'salary' => '30000',
            'training' => 'Completed',
            'competency' => 'Beginner'
        ]
    ];
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name         = isset($_POST['name']) ? cleanInput($_POST['name']) : '';
    $attendance   = isset($_POST['attendance']) ? cleanInput($_POST['attendance']) : '';
    $productivity = isset($_POST['productivity']) ? cleanInput($_POST['productivity']) : '';
    $overtime     = isset($_POST['overtime']) ? cleanInput($_POST['overtime']) : '';
    $salary       = isset($_POST['salary']) ? cleanInput($_POST['salary']) : '';
    $training     = isset($_POST['training']) ? cleanInput($_POST['training']) : '';
    $competency   = isset($_POST['competency']) ? cleanInput($_POST['competency']) : '';

    // Auto-append '%' if not present
    if ($attendance && !str_contains($attendance, '%')) {
        $attendance .= '%';
    }
    if ($productivity && !str_contains($productivity, '%')) {
        $productivity .= '%';
    }

    // Only add employee if all required fields are present
    if ($name && $attendance && $productivity && $overtime && $salary && $training && $competency) {
        $newEmployee = [
            'name'         => $name,
            'attendance'   => $attendance,
            'productivity' => $productivity,
            'overtime'     => $overtime,
            'salary'       => $salary,
            'training'     => $training,
            'competency'   => $competency
        ];
        array_push($_SESSION['employees'], $newEmployee);
    }
}

$employees = $_SESSION['employees'];

// Prepare employee data string for AI analysis
$employeeData = '';
foreach ($employees as $employee) {
    $employeeData .= "{$employee['name']} - Attendance: {$employee['attendance']}, Productivity: {$employee['productivity']}, "
        . "Overtime: {$employee['overtime']} hours, Salary: PHP {$employee['salary']}, "
        . "Training: {$employee['training']}, Competency: {$employee['competency']}\n";
}

$data = [
    'contents' => [
        [
            'parts' => [
                [
                    'text' => "Employee Performance Data:\n" . $employeeData .
                              "Analyze this data and provide insights on employee performance, salary adjustments, training effectiveness, and competency improvements."
                ]
            ]
        ]
    ]
];

// API error handling improved
$insights = 'No insights available.';
$error = null;

try {
    $ch = curl_init($url);
    if ($ch === false) {
        throw new Exception("Failed to initialize cURL");
    }

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
    ]);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10); // Add timeout

    $response = curl_exec($ch);

    if ($response === false) {
        throw new Exception(curl_error($ch));
    }

    $responseData = json_decode($response, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception("JSON decoding error: " . json_last_error_msg());
    }

    if (isset($responseData['candidates'][0]['content']['parts'][0]['text'])) {
        $insights = $responseData['candidates'][0]['content']['parts'][0]['text'];
    } else {
        $insights = 'API response format was unexpected. No insights available.';
    }

} catch (Exception $e) {
    $error = "Error: " . $e->getMessage();
    $insights = "Unable to retrieve insights at this time. Please try again later.";
} finally {
    if (isset($ch) && $ch !== false) {
        curl_close($ch);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Employee Performance Tracker</title>

  <style>
    /* RESET/BASE */
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }
    body, html {
      font-family: Arial, sans-serif;
      height: 100%;
    }

    /* SIDEBAR - Kung may sidebar kayo sa admin_sidebar.php, pwede ninyong i-adjust. */
    .sidebar {
      position: fixed;
      left: 0;
      top: 0;
      width: 220px;  /* Lapad ng sidebar */
      height: 100%;
      background-color: #0d6efd; /* Pwede nyo palitan ang kulay */
      color: #fff;
      padding: 20px;
      box-sizing: border-box;
      overflow-y: auto;
    }

    /* MAIN CONTENT */
    .main-content {
      margin-left: 220px; /* Kapantay ng lapad ng sidebar */
      padding: 20px;
      background-color: #f8f9fa;
      min-height: 100vh;
    }

    .main-content h2, 
    .main-content h3 {
      text-align: center;
      margin-bottom: 20px;
    }

    /* FORM CONTAINER */
    .form-container {
      background: #fff;
      border-radius: 8px;
      box-shadow: 0 2px 4px rgba(0,0,0,0.1);
      max-width: 100%;
      margin: 20px auto;
      padding: 15px;
    }

    .form-row {
      display: flex;
      flex-wrap: wrap;
      gap: 10px;
      margin-bottom: 10px;
    }

    .form-group {
      flex: 1;
      min-width: 120px;
    }

    .form-group label {
      display: block;
      font-size: 12px;
      margin-bottom: 3px;
      font-weight: bold;
    }

    .form-group input {
      width: 100%;
      padding: 6px;
      font-size: 13px;
      border: 1px solid #ddd;
      border-radius: 4px;
    }

    .submit-btn {
      background-color: #4caf50;
      color: white;
      border: none;
      border-radius: 4px;
      cursor: pointer;
      padding: 8px 15px;
      font-size: 14px;
      margin-top: 10px;
    }

    /* COLLAPSIBLE FORM */
    .collapsible {
      background-color: #f1f1f1;
      color: #333;
      cursor: pointer;
      padding: 10px;
      width: 100%;
      border: none;
      text-align: left;
      outline: none;
      font-size: 15px;
      border-radius: 4px;
      margin-bottom: 10px;
    }
    .active, .collapsible:hover {
      background-color: #e0e0e0;
    }
    .content {
      padding: 0;
      max-height: 0;
      overflow: hidden;
      transition: max-height 0.2s ease-out;
    }

    /* TABLE STYLING */
    table {
      width: 100%;
      border-collapse: collapse;
      margin: 20px auto;
    }
    table, th, td {
      border: 1px solid #ddd;
    }
    th, td {
      padding: 8px;
      text-align: left;
      font-size: 14px;
    }
    th {
      background-color: #f2f2f2;
    }
    tr:nth-child(even) {
      background-color: #f9f9f9;
    }

    /* CHART */
    canvas {
      max-width: 100%;
      height: 400px;
      display: block;
      margin: 20px auto;
    }

    /* INSIGHTS CONTAINER */
    .insights-container {
      background: #f9f9f9;
      padding: 20px;
      margin: 20px auto;
      border-radius: 8px;
      box-shadow: 0 2px 4px rgba(0,0,0,0.1);
      max-width: 100%;
    }

    /* ERROR */
    .error {
      color: #f44336;
      background-color: #ffebee;
      padding: 10px;
      border-radius: 4px;
      margin: 10px 0;
    }

  </style>
</head>
<body>

  <!-- Kung meron kayong .sidebar na dinadala ni admin_sidebar.php, okay lang. 
       Kung wala, pwede ninyong i-remove ang .sidebar rule sa CSS at i-adjust ang .main-content. -->

  <div class="main-content">

    <h2>Employee Performance Management</h2>

    <button class="collapsible">+ Add New Employee</button>
    <div class="content">
      <div class="form-container">
        <form method="POST">
          <div class="form-row">
            <div class="form-group">
              <label for="name">Name:</label>
              <input type="text" id="name" name="name" required>
            </div>
            <div class="form-group">
              <label for="attendance">Attendance (%):</label>
              <input type="text" id="attendance" name="attendance" 
                     pattern="[0-9]{1,3}%?" required placeholder="e.g. 95">
            </div>
            <div class="form-group">
              <label for="productivity">Productivity (%):</label>
              <input type="text" id="productivity" name="productivity" 
                     pattern="[0-9]{1,3}%?" required placeholder="e.g. 85">
            </div>
            <div class="form-group">
              <label for="overtime">Overtime (hrs):</label>
              <input type="number" id="overtime" name="overtime" required min="0">
            </div>
          </div>
          <div class="form-row">
            <div class="form-group">
              <label for="salary">Salary (PHP):</label>
              <input type="number" id="salary" name="salary" required min="0">
            </div>
            <div class="form-group">
              <label for="training">Training:</label>
              <input type="text" id="training" name="training" 
                     required placeholder="Completed, Ongoing">
            </div>
            <div class="form-group">
              <label for="competency">Competency:</label>
              <input type="text" id="competency" name="competency" 
                     required placeholder="Beginner, Advanced">
            </div>
            <div class="form-group">
              <label style="visibility: hidden;">Submit:</label>
              <button type="submit" class="submit-btn">Add Employee</button>
            </div>
          </div>
        </form>
      </div>
    </div>

    <h3>Current Employee Data</h3>
    <table>
      <thead>
        <tr>
          <th>Name</th>
          <th>Attendance</th>
          <th>Productivity</th>
          <th>Overtime</th>
          <th>Salary (PHP)</th>
          <th>Training</th>
          <th>Competency</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($employees as $employee): ?>
        <tr>
          <td><?= htmlspecialchars($employee['name']) ?></td>
          <td><?= htmlspecialchars($employee['attendance']) ?></td>
          <td><?= htmlspecialchars($employee['productivity']) ?></td>
          <td><?= htmlspecialchars($employee['overtime']) ?></td>
          <td><?= htmlspecialchars($employee['salary']) ?></td>
          <td><?= htmlspecialchars($employee['training']) ?></td>
          <td><?= htmlspecialchars($employee['competency']) ?></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>

    <h3>Employee Performance Graph</h3>
    <canvas id="performanceChart"></canvas>

    <?php if ($error): ?>
      <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <div class="insights-container">
      <h3>AI Insights and Recommendations</h3>
      <div><?= nl2br(htmlspecialchars($insights)) ?></div>
    </div>
  </div> <!-- end of main-content -->

  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script>
    // Collapsible form functionality
    var coll = document.getElementsByClassName("collapsible");
    for (var i = 0; i < coll.length; i++) {
      coll[i].addEventListener("click", function() {
        this.classList.toggle("active");
        var content = this.nextElementSibling;
        if (content.style.maxHeight) {
          content.style.maxHeight = null;
        } else {
          content.style.maxHeight = content.scrollHeight + "px";
        }
      });
    }

    document.addEventListener('DOMContentLoaded', function() {
      const ctx = document.getElementById("performanceChart").getContext("2d");
      const employeeData = <?= json_encode($employees) ?>;

      const names = employeeData.map(emp => emp.name);
      const productivity = employeeData.map(emp => parseInt(emp.productivity.replace('%', '')));
      const attendance = employeeData.map(emp => parseInt(emp.attendance.replace('%', '')));

      new Chart(ctx, {
        type: "bar",
        data: {
          labels: names,
          datasets: [
            {
              label: "Productivity (%)",
              data: productivity,
              backgroundColor: "rgba(76, 175, 80, 0.7)",
              borderColor: "#388e3c",
              borderWidth: 1
            },
            {
              label: "Attendance (%)",
              data: attendance,
              backgroundColor: "rgba(33, 150, 243, 0.7)",
              borderColor: "#1976d2",
              borderWidth: 1
            }
          ]
        },
        options: {
          responsive: true,
          scales: {
            y: {
              beginAtZero: true,
              max: 100
            }
          },
          plugins: {
            title: {
              display: true,
              text: 'Employee Performance Metrics'
            }
          }
        }
      });
    });
  </script>
</body>
</html>
