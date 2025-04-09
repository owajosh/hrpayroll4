<?php
session_start();
include 'db.php'; // DB connection
require 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Function to send OTP via email
function sendOTPEmail($recipientEmail, $otp) {
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'nextfleet0@gmail.com';
        $mail->Password   = 'ciqm cwjn ixau psmo'; // App password
        $mail->SMTPSecure = 'tls';
        $mail->Port       = 587;

        $mail->setFrom('yourgmail@gmail.com', 'System Alert');
        $mail->addAddress($recipientEmail);

        $mail->isHTML(true);
        $mail->Subject = 'Your OTP for Admin Login';
        $mail->Body    = "
            <h3>Your OTP Code</h3>
            <p>Your OTP is: <strong>$otp</strong></p>
            <p>This OTP is valid for 5 minutes.</p>
        ";

        $mail->send();
    } catch (Exception $e) {
        error_log("Mailer Error: " . $mail->ErrorInfo);
    }
}

$error = '';

// ---------------------------
// OTP Verification Section
// ---------------------------
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['otp'])) {
        $enteredOTP = trim($_POST['otp']);

        if (!isset($_SESSION['otp_expiry'])) {
            $error = "Session expired. Please login again.";
        } elseif (time() > $_SESSION['otp_expiry']) {
            $error = "OTP expired. Please login again.";
            session_unset();
        } else {
            if ($enteredOTP == $_SESSION['otp']) {
                $_SESSION['user_role'] = 'admin';
                $_SESSION['admin_id'] = $_SESSION['pending_admin_id'];

                // Clear OTP session
                unset($_SESSION['otp']);
                unset($_SESSION['otp_expiry']);
                unset($_SESSION['pending_admin_id']);

                header("Location: php/admindashboard.php");
                exit();
            } else {
                $error = "Invalid OTP. Please try again.";
            }
        }
    }

    // ---------------------------
    // Email & Password Login
    // ---------------------------
    elseif (isset($_POST['email']) && isset($_POST['password'])) {
        $email = strtolower(mysqli_real_escape_string($conn, $_POST['email']));
        $password = $_POST['password'];

        $query = "SELECT * FROM admin WHERE LOWER(email)=?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);

            if (password_verify($password, $row['password'])) {
                $otp = rand(100000, 999999);
                $_SESSION['otp'] = $otp;
                $_SESSION['otp_expiry'] = time() + 300; // 5 min
                $_SESSION['pending_admin_id'] = $row['admin_id'];

                sendOTPEmail($row['email'], $otp);
            } else {
                $error = "Invalid Credentials! (Wrong password)";
            }
        } else {
            $error = "Invalid Credentials! (Email not found)";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
    <link rel="stylesheet" href="css/login.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="css/global.css?v=<?php echo time(); ?>">
</head>
<body>
    <div class="login-container d-flex justify-content-center align-items-center vh-100">
        <div class="card shadow" style="width: 400px;">
            <div class="system-title">
                Fleet Management System
            </div>
            
            <?php if (isset($_SESSION['pending_admin_id'])): ?>
                <h2>Verification Required</h2>
                <p class="text-muted text-center mb-4">
                    <i class="fas fa-envelope me-2"></i>
                    We've sent a 6-digit code to your email
                </p>
                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        <?php echo $error; ?>
                    </div>
                <?php endif; ?>
                <form method="POST" action="login.php">
                    <input type="text" name="otp" class="form-control" required maxlength="6" placeholder="• • • • • •">
                    <button type="submit" class="btn btn-login">
                        <i class="fas fa-check-circle me-2"></i>
                        Verify
                    </button>
                    <div class="text-center mt-3">
                        <a href="login.php" class="text-decoration-none text-muted small">
                            <i class="fas fa-arrow-left me-1"></i> Back to login
                        </a>
                    </div>
                </form>
            <?php else: ?>
                <h2>Admin Portal</h2>
                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        <?php echo $error; ?>
                    </div>
                <?php endif; ?>
                <form method="POST" action="login.php">
                    <div class="input-icon">
                        <i class="fas fa-envelope"></i>
                        <input type="email" name="email" class="form-control input-with-icon" required placeholder="Email Address">
                    </div>
                    <div class="input-icon">
                        <i class="fas fa-lock"></i>
                        <input type="password" name="password" class="form-control input-with-icon" required placeholder="Password">
                    </div>
                    <button type="submit" class="btn btn-login">
                        <i class="fas fa-sign-in-alt me-2"></i>
                        Login
                    </button>
                </form>
            <?php endif; ?>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>