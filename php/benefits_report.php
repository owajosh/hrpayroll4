<?php
include 'db.php';
include 'admin_sidebar.php';

// check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// fetch employees
$sql = "SELECT id, first_name, middle_name, last_name FROM employee";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Employee Benefits Report</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            padding: 20px;
            background-color: #f9f9f9;
            color: #333;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background-color: white;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
        }
        h2 {
            text-align: center;
            color: #2c3e50;
            margin-bottom: 30px;
            padding-bottom: 15px;
            border-bottom: 2px solid #eaeaea;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            box-shadow: 0 0 8px rgba(0, 0, 0, 0.05);
        }
        th, td {
            padding: 15px;
            text-align: left;
            border: 1px solid #e0e0e0;
        }
        th {
            background-color: #3498db;
            color: white;
            font-weight: 600;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        tr:hover {
            background-color: #e9f7fe;
        }
        .no-data {
            text-align: center;
            padding: 20px;
            color: #7f8c8d;
            font-style: italic;
        }
        ul {
            padding-left: 20px;
            margin: 0;
        }
        li {
            padding: 3px 0;
        }
        .benefits-list {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            list-style: none;
            padding: 0;
        }
        .benefit-item {
            background-color: #e0f7fa;
            padding: 6px 12px;
            border-radius: 30px;
            color: #0277bd;
            font-size: 14px;
            display: inline-block;
        }
        .total {
            font-weight: bold;
            color: #2e7d32;
            margin-top: 10px;
            display: block;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Employee Benefits Report</h2>

    <table>
        <tr>
            <th>Employee Name</th>
            <th>Benefits</th>
        </tr>

        <?php
        // Sample fixed amount per benefit
        $benefits = [
            "SSS" => 500,
            "PhilHealth" => 300,
            "Pag-IBIG" => 200,
            "Health Insurance" => 1000,
            "Leave Credits" => 1000,
            "13th Month Pay" => 1500
        ];

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $fullname = $row['first_name'] . ' ' .
                            ($row['middle_name'] ? $row['middle_name'] . ' ' : '') .
                            $row['last_name'];
                
                echo "<tr>";
                echo "<td>$fullname</td>";
                echo "<td><div class='benefits-list'>";

                $total = 0;
                foreach ($benefits as $benefitName => $amount) {
                    echo "<span class='benefit-item'>$benefitName: ₱" . number_format($amount, 2) . "</span>";
                    $total += $amount;
                }

                echo "<span class='total'>Total: ₱" . number_format($total, 2) . "</span>";
                echo "</div></td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='2' class='no-data'>No employees found.</td></tr>";
        }

        $conn->close();
        ?>
    </table>
</div>

</body>
</html>
