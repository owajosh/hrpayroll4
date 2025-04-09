<!-- sidebar.php -->
<!DOCTYPE html>
<html>
<head>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
        }

        .sidebar {
            width: 250px;
            background-color: blue;
            height: 100vh;
            padding-top: 20px;
            position: fixed;
            color: white;
        }

        .sidebar a {
            display: block;
            color: white;
            padding: 15px;
            text-decoration: none;
        }

        .sidebar a:hover {
            background-color: gray;
        }

        .content {
            margin-left: 250px;
            padding: 20px;
            width: 100%;
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <h3 style="text-align:center;">Dashboard</h3>
        <a href="employeedashboard.php">🏠 Home</a>
        <a href="attendance.php">📅 Attendance</a>
        <a href="view_payslip.php">📄 My Payslips</a>
        <a href="payroll.php">💰 Payroll</a>
        <a href="leaves.php">📜 Leave Requests</a>
        <a href="profile.php">👤 Profile</a>
        <a href="logout.php">🚪 Logout</a>
    </div>
</body>
</html>
