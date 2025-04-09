<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve and sanitize the form data
    $first_name  = htmlspecialchars($_POST['first_name']);
    $middle_name = htmlspecialchars($_POST['middle_name']);
    $last_name   = htmlspecialchars($_POST['last_name']);
    $email       = htmlspecialchars($_POST['email']);
    $contact     = htmlspecialchars($_POST['contact']);
    $address     = htmlspecialchars($_POST['address']);
    $age         = intval($_POST['age']);
    $dob         = htmlspecialchars($_POST['dob']);
    $gender      = htmlspecialchars($_POST['gender']);
    $position    = htmlspecialchars($_POST['position']);
    $department  = htmlspecialchars($_POST['department']);
    $date_hired  = htmlspecialchars($_POST['date_hired']);

    // Prepare the SQL statement to insert the data
    $sql = "INSERT INTO employee 
            (first_name, middle_name, last_name, email, contact, address, age, dob, gender, position, department, date_hired)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);

    if ($stmt === false) {
        die("Prepare failed: " . $conn->error);
    }

    // Bind the parameters to the SQL query
    $stmt->bind_param("ssssssisssss", $first_name, $middle_name, $last_name, $email, $contact, $address, $age, $dob, $gender, $position, $department, $date_hired);

    // Execute the query
    if ($stmt->execute()) {
        // Optionally, redirect to a success page or show a success message.
        header("Location: add_employee.php");
        exit;
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Add New Employee</title>
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Custom CSS -->
  <link rel="stylesheet" href="../css/employee.css">
  <link rel="stylesheet" href="../css/poppins.css">
</head>
<body class="bg-light">
  <?php include 'admin_sidebar.php'; ?>

  <div class="container my-5">
    <h2 class="mb-4">Add New Employee</h2>
    <form action="" method="POST">
      
      <!-- Personal Information Section -->
      <h4>Personal Information</h4>
      <div class="row mb-3">
        <div class="col-md-6">
          <label for="first_name" class="form-label">First Name</label>
          <input type="text" class="form-control" id="first_name" name="first_name" required>
        </div>
        <div class="col-md-6">
          <label for="middle_name" class="form-label">Middle Name</label>
          <input type="text" class="form-control" id="middle_name" name="middle_name">
        </div>
      </div>
      <div class="row mb-3">
        <div class="col-md-6">
          <label for="last_name" class="form-label">Last Name</label>
          <input type="text" class="form-control" id="last_name" name="last_name" required>
        </div>
        <div class="col-md-6">
          <label for="dob" class="form-label">Date of Birth</label>
          <input type="date" class="form-control" id="dob" name="dob" required>
        </div>
      </div>
      <div class="row mb-3">
        <div class="col-md-6">
          <label for="age" class="form-label">Age</label>
          <input type="number" class="form-control" id="age" name="age" required>
        </div>
        <div class="col-md-6">
          <label for="gender" class="form-label">Gender</label>
          <select class="form-select" id="gender" name="gender" required>
            <option value="">-- Select Gender --</option>
            <option value="male">Male</option>
            <option value="female">Female</option>
            <option value="other">Other</option>
          </select>
        </div>
      </div>
      
      <!-- Contact Information Section -->
      <h4 class="mt-5">Contact Information</h4>
      <div class="row mb-3">
        <div class="col-md-6">
          <label for="email" class="form-label">Email Address</label>
          <input type="email" class="form-control" id="email" name="email" required>
        </div>
        <div class="col-md-6">
          <label for="contact" class="form-label">Contact Number</label>
          <input type="text" class="form-control" id="contact" name="contact" required>
        </div>
      </div>
      <div class="row mb-3">
        <div class="col-12">
          <label for="address" class="form-label">Address</label>
          <textarea class="form-control" id="address" name="address" rows="3" required></textarea>
        </div>
      </div>
      
      <!-- Employment Details Section -->
      <h4 class="mt-5">Employment Details</h4>
      <div class="row mb-3">
        <div class="col-md-6">
          <label for="position" class="form-label">Position</label>
          <input type="text" class="form-control" id="position" name="position" required>
        </div>
        <div class="col-md-6">
          <label for="department" class="form-label">Department</label>
          <input type="text" class="form-control" id="department" name="department" required>
        </div>
      </div>
      <div class="row mb-3">
        <div class="col-md-6">
          <label for="date_hired" class="form-label">Date Hired</label>
          <input type="date" class="form-control" id="date_hired" name="date_hired" required>
        </div>
      </div>
      
      <!-- Form Submission -->
      <div class="mb-3">
        <button type="submit" class="btn btn-primary">Add Employee</button>
      </div>
    </form>
  </div>

  <!-- Bootstrap JS Bundle -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
