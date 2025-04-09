<?php
include 'db.php';

$id = $_GET['id'];

// Prepare SQL query for delete
$sql = "DELETE FROM employee WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);

// Execute the query and check if it was successful
if ($stmt->execute()) {
    // Success: Redirect to manage_employees.php with success status
    header("Location: manage_employees.php?status=success");
    exit;  // Ensure the script stops after redirect
} else {
    // Error: Redirect to manage_employees.php with error status
    header("Location: manage_employees.php?status=error");
    exit;
}
?>
