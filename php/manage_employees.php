<?php
include 'db.php';
// Securely fetch the employees from the database
$sql = "SELECT * FROM employee";
$stmt = $conn->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Employee List</title>
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Font Awesome for icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <!-- DataTables CSS with Bootstrap 5 integration -->
  <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
  <!-- Custom CSS for employee list and Poppins font -->
  <link rel="stylesheet" href="../css/employee.css">
  <link rel="stylesheet" href="../css/poppins.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background-color: #f5f7fa;
    }
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
      box-shadow: 0 5px 15px rgba(0,0,0,0.08);
      padding: 25px 30px;
      margin-top: 30px;
      margin-bottom: 30px;
    }
    h2 {
      color: #212529;
      font-weight: 600;
      border-bottom: 2px solid #4361ee;
      padding-bottom: 10px;
      margin-bottom: 25px;
    }
    .badge-department {
      padding: 5px 10px;
      border-radius: 20px;
      font-size: 0.85rem;
    }
    /* Example department colors */
    .marketing { background-color: #f8d7da; color: #721c24; }
    .it { background-color: #d1ecf1; color: #0c5460; }
    .hr { background-color: #d4edda; color: #155724; }
    .finance { background-color: #fff3cd; color: #856404; }
    /* Action button style */
    .action-btn {
      cursor: pointer;
      color: #4361ee;
      font-size: 1.2rem;
    }
    /* Responsive adjustments */
    @media (max-width:768px) {
      #mainContent {
        margin-left: 0;
      }
    }
    /* Elegant Modal Styling */
#employeeModal .modal-content {
  border: none;
  border-radius: 16px;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
  overflow: hidden;
}

#employeeModal .modal-header {
  background: linear-gradient(135deg, #4361ee, #3f37c9);
  color: white;
  border-bottom: none;
  padding: 20px 25px;
}

#employeeModal .modal-title {
  font-weight: 600;
  font-size: 1.4rem;
  letter-spacing: 0.5px;
}

#employeeModal .btn-close {
  color: white;
  opacity: 1;
  filter: brightness(0) invert(1);
  box-shadow: none;
}

#employeeModal .modal-body {
  padding: 30px 25px;
  background-color: #f8f9fa;
}

#employeeModal .row > div {
  margin-bottom: 16px;
  background-color: white;
  padding: 15px;
  border-radius: 10px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
  transition: all 0.2s ease;
}

#employeeModal .row > div:hover {
  box-shadow: 0 4px 12px rgba(67, 97, 238, 0.15);
  transform: translateY(-2px);
}

#employeeModal strong {
  color: #3a3a3a;
  font-weight: 600;
  display: block;
  font-size: 0.85rem;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  margin-bottom: 5px;
}

#employeeModal span {
  color: #555;
  font-size: 1.05rem;
}

#employeeModal .modal-footer {
  border-top: none;
  padding: 15px 25px 25px;
  background-color: #f8f9fa;
}

#employeeModal .btn-secondary {
  background-color: white;
  color: #4361ee;
  border: 2px solid #4361ee;
  border-radius: 8px;
  padding: 8px 24px;
  font-weight: 500;
  transition: all 0.2s ease;
}

#employeeModal .btn-secondary:hover {
  background-color: #4361ee;
  color: white;
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(67, 97, 238, 0.25);
}
  </style>
  <!-- jQuery (required for DataTables) -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body class="bg-light">
  <?php include 'admin_sidebar.php'; ?>

  <!-- Main Content Container -->
  <div id="mainContent" class="p-3">
    <div class="container text-center">
      <h2 class="mb-4">Employee List</h2>
      
      <!-- Responsive Table -->
      <div class="table-responsive">
        <?php if ($result->num_rows > 0): ?>
          <table class="table table-hover align-middle" id="employeeTable">
            <thead>
              <tr>
                <th>Employee ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Contact</th>
                <th>Position</th>
                <th>Department</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              <?php while ($row = $result->fetch_assoc()) { 
                // Combine full name
                $fullName = $row['first_name'] . " " . ($row['middle_name'] ? $row['middle_name'] . " " : "") . $row['last_name'];
                // Determine department class
                $department = strtolower(htmlspecialchars($row['department']));
                $deptClass = in_array($department, ['marketing', 'it', 'hr', 'finance']) ? $department : '';
              ?>
                <tr>
                  <td><span class="employee-id"><?= htmlspecialchars($row['id']); ?></span></td>
                  <td><?= htmlspecialchars($fullName); ?></td>
                  <td><span class="email"><?= htmlspecialchars($row['email']); ?></span></td>
                  <td><?= htmlspecialchars($row['contact']); ?></td>
                  <td><?= htmlspecialchars($row['position']); ?></td>
                  <td>
                    <span class="badge-department <?= $deptClass; ?>">
                      <?= htmlspecialchars($row['department']); ?>
                    </span>
                  </td>
                  <td>
                    <!-- Eye icon action button -->
                    <i class="fas fa-eye action-btn viewEmployee" 
                      data-id="<?= $row['id']; ?>" 
                      data-firstname="<?= htmlspecialchars($row['first_name']); ?>" 
                      data-middlename="<?= htmlspecialchars($row['middle_name']); ?>" 
                      data-lastname="<?= htmlspecialchars($row['last_name']); ?>" 
                      data-email="<?= htmlspecialchars($row['email']); ?>" 
                      data-contact="<?= htmlspecialchars($row['contact']); ?>" 
                      data-address="<?= htmlspecialchars($row['address']); ?>" 
                      data-age="<?= $row['age']; ?>" 
                      data-dob="<?= $row['dob']; ?>" 
                      data-gender="<?= $row['gender']; ?>" 
                      data-position="<?= htmlspecialchars($row['position']); ?>" 
                      data-department="<?= htmlspecialchars($row['department']); ?>" 
                      data-datehired="<?= $row['date_hired']; ?>"
                      title="View Details"></i>
                  </td>
                </tr>
              <?php } ?>
            </tbody>
          </table>
        <?php else: ?>
          <!-- Empty state when no employees are found -->
          <div class="empty-state">
            <i class="bi bi-people"></i>
            <h4>No employees found</h4>
            <p>There are no employees in the database yet.</p>
          </div>
        <?php endif; ?>
      </div> <!-- table-responsive -->
    </div> <!-- container -->
  </div> <!-- mainContent -->

  <!-- Employee Details Modal (naka-embed sa file) -->
  <div class="modal fade" id="employeeModal" tabindex="-1" aria-labelledby="employeeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="employeeModalLabel">Employee Details</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <!-- Employee details content -->
          <div class="row">
            <div class="col-md-6 mb-2">
              <strong>ID:</strong> <span id="modalEmployeeId"></span>
            </div>
            <div class="col-md-6 mb-2">
              <strong>Name:</strong> <span id="modalFullName"></span>
            </div>
            <div class="col-md-6 mb-2">
              <strong>Email:</strong> <span id="modalEmail"></span>
            </div>
            <div class="col-md-6 mb-2">
              <strong>Contact:</strong> <span id="modalContact"></span>
            </div>
            <div class="col-md-6 mb-2">
              <strong>Address:</strong> <span id="modalAddress"></span>
            </div>
            <div class="col-md-6 mb-2">
              <strong>Age:</strong> <span id="modalAge"></span>
            </div>
            <div class="col-md-6 mb-2">
              <strong>Date of Birth:</strong> <span id="modalDOB"></span>
            </div>
            <div class="col-md-6 mb-2">
              <strong>Gender:</strong> <span id="modalGender"></span>
            </div>
            <div class="col-md-6 mb-2">
              <strong>Position:</strong> <span id="modalPosition"></span>
            </div>
            <div class="col-md-6 mb-2">
              <strong>Department:</strong> <span id="modalDepartment"></span>
            </div>
            <div class="col-md-6 mb-2">
              <strong>Date Hired:</strong> <span id="modalDateHired"></span>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <!-- You can add more actions here if needed -->
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>
  
  <!-- Bootstrap JS Bundle -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <!-- DataTables JS -->
  <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
  
  <!-- Script to initialize DataTables and handle modal population -->
  <script>
    $(document).ready(function() {
      $('#employeeTable').DataTable({
        autoWidth: false,
        // Additional configuration can be added here
      });
      
      // When the eye icon is clicked, populate the modal with employee details
      document.querySelectorAll('.viewEmployee').forEach(button => {
        button.addEventListener('click', function() {
          const id = this.getAttribute('data-id');
          const firstName = this.getAttribute('data-firstname');
          const middleName = this.getAttribute('data-middlename');
          const lastName = this.getAttribute('data-lastname');
          const email = this.getAttribute('data-email');
          const contact = this.getAttribute('data-contact');
          const address = this.getAttribute('data-address');
          const age = this.getAttribute('data-age');
          const dob = this.getAttribute('data-dob');
          const gender = this.getAttribute('data-gender');
          const position = this.getAttribute('data-position');
          const department = this.getAttribute('data-department');
          const dateHired = this.getAttribute('data-datehired');
          
          // Populate modal fields
          document.getElementById('modalEmployeeId').innerText = id;
          document.getElementById('modalFullName').innerText = firstName + ' ' + (middleName ? middleName + ' ' : '') + lastName;
          document.getElementById('modalEmail').innerText = email;
          document.getElementById('modalContact').innerText = contact;
          document.getElementById('modalAddress').innerText = address;
          document.getElementById('modalAge').innerText = age;
          document.getElementById('modalDOB').innerText = dob;
          document.getElementById('modalGender').innerText = gender;
          document.getElementById('modalPosition').innerText = position;
          document.getElementById('modalDepartment').innerText = department;
          document.getElementById('modalDateHired').innerText = dateHired;
          
          // Show modal
          const employeeModal = new bootstrap.Modal(document.getElementById('employeeModal'));
          employeeModal.show();
        });
      });
    });
  </script>
</body>
</html>
