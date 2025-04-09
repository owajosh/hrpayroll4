<?php
include 'db.php';
include 'admin_sidebar.php';

$id = $_GET['id'];

// Prepared statement para sa SELECT
$sql = "SELECT * FROM employee WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

// Check kung na-click na ang update button
if (isset($_POST['update'])) {
    // Get user input
    $name = $_POST['name'];
    $email = $_POST['email'];
    $contact = $_POST['contact'];
    $position = $_POST['position'];
    $department = $_POST['department']; // Department field

    // Prepared statement para sa UPDATE
    $update_sql = "UPDATE employee SET name = ?, email = ?, contact = ?, position = ?, department = ? WHERE id = ?";
    $stmt_update = $conn->prepare($update_sql);
    $stmt_update->bind_param("sssssi", $name, $email, $contact, $position, $department, $id);

    // Execute the statement and check for success
    if ($stmt_update->execute()) {
        echo "Employee updated successfully!";
    } else {
        echo "Error: " . $stmt_update->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Employee</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

    <div class="container bg-white p-4 rounded shadow-sm">
        <h2>Update Employee Information</h2>

        <!-- Employee Update Form -->
        <form method="POST">
            <div class="mb-3">
                <label for="name" class="form-label">Name:</label>
                <input type="text" name="name" id="name" class="form-control" value="<?= htmlspecialchars($row['name']); ?>" required>
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">Email:</label>
                <input type="email" name="email" id="email" class="form-control" value="<?= htmlspecialchars($row['email']); ?>" required>
            </div>

            <div class="mb-3">
                <label for="contact" class="form-label">Contact:</label>
                <input type="text" name="contact" id="contact" class="form-control" value="<?= htmlspecialchars($row['contact']); ?>">
            </div>

            <div class="mb-3">
                <label for="position" class="form-label">Position:</label>
                <input type="text" name="position" id="position" class="form-control" value="<?= htmlspecialchars($row['position']); ?>" required>
            </div>

            <div class="mb-3">
                <label for="department" class="form-label">Department:</label>
                <input type="text" name="department" id="department" class="form-control" value="<?= htmlspecialchars($row['department']); ?>" required>
            </div>

            <button type="submit" name="update" class="btn btn-success">Update Employee</button>
        </form>
    </div>

</body>
</html>
