<?php
include 'db.php';  // Database connection
include 'admin_sidebar.php';  // Admin sidebar file

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_attendance'])) {
    $employee_name = $_POST['employee_name'];
    $time_in = $_POST['time_in'];  // Capture time_in from form
    $time_out = $_POST['time_out'];  // Capture time_out from form
    $status = $_POST['status'];

    // Insert data into the attendance table
    $sql = "INSERT INTO attendance (employee_name, time_in, time_out, status)
            VALUES ('$employee_name', '$time_in', '$time_out', '$status')";

    if ($conn->query($sql) === TRUE) {
        echo "Attendance recorded successfully!";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Attendance</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<div class="container">
    <h1 class="mt-4">Add Attendance</h1>

    <!-- Form to Add Attendance -->
    <form method="POST">
        <div class="mb-3">
            <label for="employee_name" class="form-label">Employee Name</label>
            <input type="text" class="form-control" name="employee_name" required>
        </div>
        <div class="mb-3">
            <label for="time_in" class="form-label">Time In</label>
            <input type="datetime-local" class="form-control" name="time_in" required>
        </div>
        <div class="mb-3">
            <label for="time_out" class="form-label">Time Out</label>
            <input type="datetime-local" class="form-control" name="time_out">
        </div>
        <div class="mb-3">
            <label for="status" class="form-label">Status</label>
            <select class="form-control" name="status" required>
                <option value="Present">Present</option>
                <option value="Absent">Absent</option>
            </select>
        </div>

        <button type="submit" class="btn btn-primary" name="add_attendance">Add Attendance</button>
    </form>
</div>

<!-- Bootstrap JS and dependencies -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>

</body>
</html>
