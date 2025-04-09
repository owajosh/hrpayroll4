<?php

include 'admin_sidebar.php';
// Replace this dummy data with your actual data from the database
$employee_count = 50;
$present_count = 42;
$payroll_count = 120;

// Simulated attendance data (replace with actual data from attendance table)
$days = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
$present_data = [38, 40, 42, 39, 44, 41, 42];
$absent_data = [12, 10, 8, 11, 6, 9, 8];
$leave_data = [0, 0, 0, 0, 0, 0, 0];

// Simulated payroll data (replace with actual data)
$months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'];
$salary_data = [125000, 127500, 130000, 132000, 135000, 138000];
$bonus_data = [15000, 0, 0, 20000, 0, 25000];
$overtime_data = [8500, 9200, 7800, 10500, 9800, 11200];
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>HR Analytics Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"/>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>
  <style>
    body {
      font-family: 'Inter', sans-serif;
      background-color: #f8f9fa;
      padding: 2rem;
    }

    .dashboard-container {
      max-width: 1400px;
      margin: 0 auto;
    }

    .dashboard-header {
      margin-bottom: 2rem;
    }

    .dashboard-title {
      font-weight: 600;
      font-size: 2rem;
      margin-bottom: 0.5rem;
    }

    .dashboard-subtitle {
      font-size: 1rem;
      color: #6c757d;
    }

    .stat-card {
      border-radius: 1rem;
      box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.05);
      padding: 2rem;
      background-color: #ffffff;
      height: 100%;
      font-size: 1.1rem;
    }

    .stat-card-content {
      display: flex;
      flex-direction: column;
      gap: 0.75rem;
    }

    .stat-card-icon {
      font-size: 2.5rem;
      padding: 1rem;
      border-radius: 50%;
      width: 4rem;
      height: 4rem;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .icon-primary {
      background-color: #e0f0ff;
      color: #007bff;
    }

    .icon-success {
      background-color: #e0f8f0;
      color: #28a745;
    }

    .icon-warning {
      background-color: #fff3cd;
      color: #ffc107;
    }

    .icon-info {
      background-color: #e0f0ff;
      color: #17a2b8;
    }

    .stat-card-label {
      font-weight: 500;
      color: #6c757d;
    }

    .stat-card-value {
      font-size: 2.5rem;
      font-weight: 700;
    }

    .stat-card-footer {
      font-size: 1rem;
      color: #6c757d;
    }

    .trend-up {
      color: #28a745;
      margin-right: 0.5rem;
    }

    .chart-card {
      background-color: #ffffff;
      border-radius: 1rem;
      padding: 2rem;
      margin-top: 2rem;
      box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.05);
    }

    .chart-card-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 1rem;
    }

    .chart-card-title {
      font-size: 1.5rem;
      font-weight: 600;
    }

    .date-pill {
      background-color: #f1f3f5;
      border-radius: 999px;
      padding: 0.5rem 1.25rem;
      font-size: 1rem;
    }

    .chart-legend {
      display: flex;
      gap: 1.5rem;
      margin-top: 1rem;
    }

    .legend-item {
      display: flex;
      align-items: center;
      gap: 0.5rem;
      font-size: 1rem;
    }

    .legend-color {
      width: 1.25rem;
      height: 1.25rem;
      border-radius: 0.25rem;
    }

    .color-present {
      background-color: #06d6a0;
    }

    .color-absent {
      background-color: #ef476f;
    }

    .color-leave {
      background-color: #ffd166;
    }

    .color-salary {
      background-color: #4361ee;
    }

    .color-bonus {
      background-color: #7209b7;
    }

    .color-overtime {
      background-color: #f72585;
    }

    .summary-section {
      margin-top: 2rem;
    }

    .summary-title {
      font-size: 1.5rem;
      font-weight: 600;
      margin-bottom: 1rem;
    }

    .summary-card {
      background-color: #ffffff;
      border-radius: 1rem;
      padding: 2rem;
      box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.05);
    }

    .summary-item {
      display: flex;
      gap: 1rem;
      align-items: center;
    }

    .summary-icon {
      font-size: 1.75rem;
      padding: 0.75rem;
      border-radius: 999px;
      width: 4rem;
      height: 4rem;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .summary-data {
      flex-grow: 1;
    }

    .summary-label {
      font-size: 1rem;
      color: #6c757d;
    }

    .summary-value {
      font-size: 1.5rem;
      font-weight: 600;
      margin-bottom: 0.25rem;
    }

    /* Enlarge the chart canvas */
    .chart-container {
      height: 400px;
    }
  </style>
</head>
<body>
  <div class="dashboard-container">
    <div class="dashboard-header">
      <h1 class="dashboard-title">HR Analytics Dashboard</h1>
      <p class="dashboard-subtitle">Overview of employee attendance and payroll metrics</p>
    </div>

    <div class="row g-4">
      <div class="col-md-4">
        <div class="stat-card">
          <div class="stat-card-content">
            <div class="stat-card-icon icon-primary"><i class="fas fa-users"></i></div>
            <div class="stat-card-label">Total Employees</div>
            <h2 class="stat-card-value"><?= $employee_count ?></h2>
            <div class="stat-card-footer">
              <span class="trend-up"><i class="fas fa-arrow-up"></i> 3.2%</span>
              <span>Since last month</span>
            </div>
          </div>
        </div>
      </div>

      <div class="col-md-4">
        <div class="stat-card">
          <div class="stat-card-content">
            <div class="stat-card-icon icon-success"><i class="fas fa-clipboard-check"></i></div>
            <div class="stat-card-label">Present Today</div>
            <h2 class="stat-card-value"><?= $present_count ?></h2>
            <div class="stat-card-footer">
              <span class="trend-up"><i class="fas fa-arrow-up"></i> 2.8%</span>
              <span>Since yesterday</span>
            </div>
          </div>
        </div>
      </div>

      <div class="col-md-4">
        <div class="stat-card">
          <div class="stat-card-content">
            <div class="stat-card-icon icon-warning"><i class="fas fa-money-bill-wave"></i></div>
            <div class="stat-card-label">Payroll Records</div>
            <h2 class="stat-card-value"><?= $payroll_count ?></h2>
            <div class="stat-card-footer">
              <span class="trend-up"><i class="fas fa-arrow-up"></i> 5.1%</span>
              <span>Since last quarter</span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Charts section - Side by side charts -->
    <div class="row g-4 mt-2">
      <!-- Attendance Trends Chart -->
      <div class="col-md-6">
        <div class="chart-card">
          <div class="chart-card-header">
            <h5 class="chart-card-title">Attendance</h5>
            <div class="date-pill">Last 7 Days</div>
          </div>
          <div class="chart-container">
            <canvas id="attendanceChart"></canvas>
          </div>
          <div class="chart-legend px-4 pb-3">
            <div class="legend-item"><div class="legend-color color-present"></div><div>Present</div></div>
            <div class="legend-item"><div class="legend-color color-absent"></div><div>Absent</div></div>
            <div class="legend-item"><div class="legend-color color-leave"></div><div>Leave</div></div>
          </div>
        </div>
      </div>
      
      <!-- Payroll Graph -->
      <div class="col-md-6">
        <div class="chart-card">
          <div class="chart-card-header">
            <h5 class="chart-card-title">Payroll Distribution</h5>
            <div class="date-pill">Last 6 Months</div>
          </div>
          <div class="chart-container">
            <canvas id="payrollChart"></canvas>
          </div>
          <div class="chart-legend px-4 pb-3">
            <div class="legend-item"><div class="legend-color color-salary"></div><div>Base Salary</div></div>
            <div class="legend-item"><div class="legend-color color-bonus"></div><div>Bonuses</div></div>
            <div class="legend-item"><div class="legend-color color-overtime"></div><div>Overtime</div></div>
          </div>
        </div>
      </div>
    </div>

    <!-- Attendance Summary -->
    <div class="summary-section">
      <h5 class="summary-title">Attendance Summary</h5>
      <div class="row g-4">
        <div class="col-md-4">
          <div class="summary-card">
            <div class="summary-item">
              <div class="summary-icon icon-success"><i class="fas fa-check"></i></div>
              <div class="summary-data">
                <div class="summary-label">Present Rate</div>
                <div class="summary-value">
                  <?php 
                    $present_rate = $employee_count > 0 ? round(($present_count / $employee_count) * 100) : 0;
                    echo $present_rate . '%';
                  ?>
                </div>
                <div class="progress">
                  <div class="progress-bar bg-success" role="progressbar" style="width: <?= $present_rate ?>%" aria-valuenow="<?= $present_rate ?>" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
              </div>
            </div>
          </div>
        </div>
        
        <!-- Payroll Summary Card -->
        <div class="col-md-4">
          <div class="summary-card">
            <div class="summary-item">
              <div class="summary-icon icon-warning"><i class="fas fa-money-bill-wave"></i></div>
              <div class="summary-data">
                <div class="summary-label">Average Monthly Payroll</div>
                <div class="summary-value">
                  <?php 
                    $avg_payroll = count($salary_data) > 0 ? 
                      number_format(array_sum($salary_data) / count($salary_data), 0) : 0;
                    echo '₱' . $avg_payroll;
                  ?>
                </div>
                <small class="text-muted">Base salary excluding benefits</small>
              </div>
            </div>
          </div>
        </div>
        
        <!-- Overtime Summary Card -->
        <div class="col-md-4">
          <div class="summary-card">
            <div class="summary-item">
              <div class="summary-icon icon-info"><i class="fas fa-clock"></i></div>
              <div class="summary-data">
                <div class="summary-label">Monthly Overtime Costs</div>
                <div class="summary-value">
                  <?php 
                    $avg_overtime = count($overtime_data) > 0 ? 
                      number_format(array_sum($overtime_data) / count($overtime_data), 0) : 0;
                    echo '₱' . $avg_overtime;
                  ?>
                </div>
                <small class="text-muted">Average over last 6 months</small>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Chart Scripts -->
  <script>
    // Attendance Chart
    const attendanceCtx = document.getElementById('attendanceChart').getContext('2d');
    const attendanceChart = new Chart(attendanceCtx, {
      type: 'line',
      data: {
        labels: <?= json_encode($days) ?>,
        datasets: [
          {
            label: 'Present',
            data: <?= json_encode($present_data) ?>,
            borderColor: '#06d6a0',
            backgroundColor: 'rgba(6, 214, 160, 0.1)',
            fill: true,
            tension: 0.3
          },
          {
            label: 'Absent',
            data: <?= json_encode($absent_data) ?>,
            borderColor: '#ef476f',
            backgroundColor: 'rgba(239, 71, 111, 0.1)',
            fill: true,
            tension: 0.3
          },
          {
            label: 'Leave',
            data: <?= json_encode($leave_data) ?>,
            borderColor: '#ffd166',
            backgroundColor: 'rgba(255, 209, 102, 0.1)',
            fill: true,
            tension: 0.3
          }
        ]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
          y: {
            beginAtZero: true
          }
        }
      }
    });
    
    // Payroll Chart
    const payrollCtx = document.getElementById('payrollChart').getContext('2d');
    const payrollChart = new Chart(payrollCtx, {
      type: 'bar',
      data: {
        labels: <?= json_encode($months) ?>,
        datasets: [
          {
            label: 'Base Salary',
            data: <?= json_encode($salary_data) ?>,
            backgroundColor: '#4361ee',
          },
          {
            label: 'Bonuses',
            data: <?= json_encode($bonus_data) ?>,
            backgroundColor: '#7209b7',
          },
          {
            label: 'Overtime',
            data: <?= json_encode($overtime_data) ?>,
            backgroundColor: '#f72585',
          }
        ]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
          x: {
            stacked: true,
          },
          y: {
            stacked: true,
            beginAtZero: true,
            ticks: {
              callback: function(value) {
                return '₱' + value.toLocaleString();
              }
            }
          }
        },
        plugins: {
          tooltip: {
            callbacks: {
              label: function(context) {
                let label = context.dataset.label || '';
                if (label) {
                  label += ': ';
                }
                if (context.parsed.y !== null) {
                  label += '₱' + context.parsed.y.toLocaleString();
                }
                return label;
              }
            }
          }
        }
      }
    });
  </script>
</body>
</html>