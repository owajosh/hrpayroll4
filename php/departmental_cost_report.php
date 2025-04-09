<?php
include 'db.php';
include 'admin_sidebar.php';


?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Departmental Cost Report</title>
  <!-- Bootstrap CSS for styling -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Font Awesome for icons -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
  <style>
    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      color: #333;
      background-color: #f8f9fa;
      margin: 0;
      padding: 20px;
    }
    
    .card {
      border-radius: 10px;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
      margin-bottom: 30px;
      border: none;
    }
    
    .report-header {
      text-align: center;
      padding: 25px 0;
      background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
      color: white;
      border-radius: 10px 10px 0 0;
    }
    
    .report-header h1 {
      margin-bottom: 5px;
      font-weight: 600;
    }
    
    .report-header p {
      margin: 0;
      font-size: 0.9rem;
      opacity: 0.9;
    }
    
    .summary-section {
      background-color: white;
      padding: 20px;
      border-radius: 10px;
      margin-bottom: 20px;
    }
    
    .summary-box {
      padding: 15px;
      text-align: center;
      border-radius: 8px;
      background-color: #f8f9fa;
      box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
      height: 100%;
    }
    
    .summary-box i {
      font-size: 24px;
      margin-bottom: 10px;
      color: #2575fc;
    }
    
    .summary-box h3 {
      font-size: 1.8rem;
      font-weight: 600;
      margin: 10px 0;
      color: #333;
    }
    
    .summary-box p {
      color: #666;
      margin: 0;
    }
    
    .report-table {
      width: 100%;
      border-collapse: separate;
      border-spacing: 0;
      background-color: white;
      border-radius: 8px;
      overflow: hidden;
    }
    
    .report-table th, .report-table td {
      padding: 12px 15px;
      text-align: center;
      border-bottom: 1px solid #e0e0e0;
    }
    
    .report-table th {
      background-color: #343a40;
      color: white;
      font-weight: 500;
      text-transform: uppercase;
      font-size: 0.85rem;
      letter-spacing: 1px;
    }
    
    .report-table tr:last-child td {
      border-bottom: none;
    }
    
    .report-table tr:hover {
      background-color: #f8f9fa;
    }
    
    .print-btn {
      padding: 10px 20px;
      border-radius: 50px;
      background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
      border: none;
      font-weight: 500;
      letter-spacing: 1px;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
      transition: all 0.3s ease;
    }
    
    .print-btn:hover {
      transform: translateY(-2px);
      box-shadow: 0 6px 10px rgba(0, 0, 0, 0.15);
    }
    
    .print-btn i {
      margin-right: 8px;
    }
    
    @media print {
      .no-print {
        display: none;
      }
      
      body {
        background-color: white;
        padding: 0;
        margin: 0;
      }
      
      .card {
        box-shadow: none;
        margin: 0;
      }
      
      .report-header {
        background: #f0f0f0;
        color: #333;
      }
      
      .summary-box {
        box-shadow: none;
        border: 1px solid #e0e0e0;
      }
      
      .container {
        max-width: 100%;
        padding: 0;
      }
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="card">
      <!-- Report Header -->
      <div class="report-header">
        <h1><i class="fas fa-chart-pie me-2"></i>Departmental Cost Report</h1>
        <p>Generated on <span id="reportDate"></span></p>
      </div>
      
      <div class="card-body">
        <!-- Summary Section -->
        <div class="summary-section mb-4">
          <h4 class="mb-3">Overview</h4>
          <div class="row g-3">
            <div class="col-md-6">
              <div class="summary-box">
                <i class="fas fa-building"></i>
                <p>Total Departments</p>
                <h3 id="totalDepartments">0</h3>
              </div>
            </div>
            <div class="col-md-6">
              <div class="summary-box">
                <i class="fas fa-money-bill-wave"></i>
                <p>Grand Total Cost</p>
                <h3 id="grandTotalCost">₱0.00</h3>
              </div>
            </div>
          </div>
        </div>
        
        <h4 class="mb-3">Department </h4>
        <!-- Detailed Department Table -->
        <div class="table-responsive">
          <table class="report-table">
            <thead>
              <tr>
                <th>Department</th>
                <th>No. of Employees</th>
                <th>Total Salary Cost (PHP)</th>
              </tr>
            </thead>
            <tbody id="departmentTableBody">
              <!-- Rows will be inserted dynamically -->
            </tbody>
          </table>
        </div>
        
        <!-- Print Button (hide on print) -->
        <div class="no-print text-center mt-4">
          <button class="btn btn-primary print-btn" onclick="window.print()">
            <i class="fas fa-print"></i> Print Report
          </button>
        </div>
      </div>
    </div>
  </div>
  
  <!-- JavaScript to simulate data and generate the report -->
  <script>
    // Simulated employee data. In practice, you'll get these values from your database.
    // Note: The employee table doesn't include salary, so here we simulate salary for demonstration.
    const employees = [
      { id: 1, first_name: "Juan", last_name: "Dela Cruz", department: "HR", salary: 30000 },
      { id: 2, first_name: "Maria", last_name: "Santos", department: "HR", salary: 35000 },
      { id: 3, first_name: "Pedro", last_name: "Reyes", department: "Engineering", salary: 50000 },
      { id: 4, first_name: "Ana", last_name: "Lopez", department: "Engineering", salary: 55000 },
      { id: 5, first_name: "Jose", last_name: "Garcia", department: "Sales", salary: 40000 },
      { id: 6, first_name: "Liza", last_name: "Martinez", department: "Sales", salary: 45000 },
      { id: 7, first_name: "Ramon", last_name: "Torres", department: "Engineering", salary: 60000 }
    ];
    
    // Group employee data by department
    const departmentData = {};
    employees.forEach(emp => {
      if (!departmentData[emp.department]) {
        departmentData[emp.department] = {
          count: 0,
          totalSalary: 0
        };
      }
      departmentData[emp.department].count++;
      departmentData[emp.department].totalSalary += emp.salary;
    });
    
    // Update the summary and table
    let grandTotalCost = 0;
    const tableBody = document.getElementById('departmentTableBody');
    for (const dept in departmentData) {
      grandTotalCost += departmentData[dept].totalSalary;
      const row = document.createElement('tr');
      
      // Department cell
      const deptCell = document.createElement('td');
      deptCell.textContent = dept;
      row.appendChild(deptCell);
      
      // Employee count cell
      const countCell = document.createElement('td');
      countCell.textContent = departmentData[dept].count;
      row.appendChild(countCell);
      
      // Total salary cell
      const salaryCell = document.createElement('td');
      salaryCell.textContent = departmentData[dept].totalSalary.toLocaleString('en-US', { style: 'currency', currency: 'PHP' });
      row.appendChild(salaryCell);
      
      tableBody.appendChild(row);
    }
    
    // Update overall summary
    document.getElementById('totalDepartments').textContent = Object.keys(departmentData).length;
    document.getElementById('grandTotalCost').textContent = grandTotalCost.toLocaleString('en-US', { style: 'currency', currency: 'PHP' });
    
    // Set report date
    document.getElementById('reportDate').textContent = new Date().toLocaleDateString();
  </script>
</body>
</html>