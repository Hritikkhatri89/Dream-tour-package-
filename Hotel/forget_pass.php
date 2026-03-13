<?php
session_start();
date_default_timezone_set('Asia/Kolkata');
include("db.php");

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require 'vendor/autoload.php';

$error = '';
$success = '';

// Clear previous reset session data
unset($_SESSION['otp_verified']);

if (isset($_POST['email'])) {
    $email = mysqli_real_escape_string($conn, trim($_POST['email']));

    // Check if email exists
    $check = mysqli_query($conn, "SELECT * FROM users WHERE email='$email'");
    if (mysqli_num_rows($check) == 0) {
        $error = "This email is not registered!";
    } else {
        // Generate 6-digit OTP
        $otp = rand(100000, 999999);
        $expire = date("Y-m-d H:i:s", strtotime("+10 minutes"));

        // Save OTP to DB
        mysqli_query($conn, "UPDATE users SET reset_code='$otp', code_expire='$expire' WHERE email='$email'");

        // Save session
        $_SESSION['reset_email'] = $email;

        // Send OTP via Email
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'dreamtour955@gmail.com';   
            $mail->Password   = 'fgfznrkqjrousypr';       
            $mail->SMTPSecure = 'tls';
            $mail->Port       = 587;

            $mail->setFrom('dreamtour955@gmail.com', 'Dream Tour & Travel');
            $mail->addAddress($email);
            $mail->Subject = 'Password Reset OTP - Dream Tour & Travel';
            $mail->isHTML(true);
            $mail->Body = "
            <div style='font-family:Segoe UI,sans-serif;max-width:500px;margin:auto;background:#f4f7fc;padding:30px;border-radius:10px;'>
                <div style='background:#1e3d7b;padding:20px;border-radius:8px 8px 0 0;text-align:center;'>
                    <h2 style='color:white;margin:0;'>Dream Tour & Travel</h2>
                    <p style='color:#aac4ff;margin:5px 0 0;'>Password Reset Request</p>
                </div>
                <div style='background:white;padding:30px;border-radius:0 0 8px 8px;'>
                    <p style='font-size:16px;color:#333;'>Hello,</p>
                    <p style='color:#555;'>We received a request to reset your password. Use the OTP below to verify your identity:</p>
                    <div style='text-align:center;margin:25px 0;'>
                        <span style='display:inline-block;background:#1e3d7b;color:white;font-size:32px;font-weight:bold;letter-spacing:10px;padding:15px 30px;border-radius:8px;'>$otp</span>
                    </div>
                    <p style='color:#888;font-size:13px;text-align:center;'> This OTP is valid for <strong>10 minutes</strong> only.</p>
                    <p style='color:#999;font-size:12px;margin-top:20px;'>If you did not request this, please ignore this email.</p>
                </div>
            </div>";

            $mail->send();
            header("Location: verify_otp.php");
            exit;
        } catch (Exception $e) {
            $error = "Email could not be sent. Error: " . $mail->ErrorInfo;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recover Password | Dream Tour & Travel</title>
    <link href="bootstrap-5.3.7-dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="instyle.css">
    <style>
        .auth-left {
            background-image: url('img/Manali.jpg');
        }
    </style>
</head>
<body class="auth-body">

<a href="index.php" class="auth-back-btn">
    <i class="bi bi-arrow-left"></i> Back to Home
</a>

<div class="auth-card">
    <!-- Left Section -->
    <div class="auth-left">
        <div class="auth-left-content">
            <span class="auth-logo-text">Dream Tour</span>
            <p class="auth-left-sub">Don't worry, it happens to the best of us. Let's get you back on track.</p>
        </div>
    </div>

    <!-- Right Section -->
    <div class="auth-right">
        <div class="plane-path">
            <i class="bi bi-airplane"></i>
        </div>

        <h2>Recover</h2>
        <p class="subtitle">Enter your email to receive an OTP</p>

        <!-- Step Indicator -->
        <div class="auth-steps">
            <div class="auth-step-item">
                <div class="auth-step-circle active">1</div>
                <div class="auth-step-label">Email</div>
            </div>
            <div class="auth-step-line"></div>
            <div class="auth-step-item">
                <div class="auth-step-circle">2</div>
                <div class="auth-step-label">OTP</div>
            </div>
            <div class="auth-step-line"></div>
            <div class="auth-step-item">
                <div class="auth-step-circle">3</div>
                <div class="auth-step-label">Reset</div>
            </div>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-danger py-2 text-center small mb-4">
                <?= $error ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="auth-form-group">
                <label>Registered Email</label>
                <div class="auth-input-wrap">
                    <i class="bi bi-envelope-at"></i>
                    <input type="email" name="email" class="auth-input" placeholder="example@mail.com" required>
                </div>
            </div>

            <button type="submit" class="auth-btn">Send OTP</button>

            <div class="auth-separator">
                <span>OR</span>
            </div>

            <div class="social-logins">
                <a href="https://accounts.google.com/signin" target="_blank" class="social-btn google"><i class="bi bi-google"></i></a>
                <a href="https://www.facebook.com/login" target="_blank" class="social-btn facebook"><i class="bi bi-facebook"></i></a>
                <a href="https://appleid.apple.com/sign-in" target="_blank" class="social-btn apple"><i class="bi bi-apple"></i></a>
            </div>

            <p class="auth-footer-text">
                Remembered your password? <a href="login.php">Back to Login</a>
            </p>

            <div class="auth-illustrations">
                <i class="bi bi-compass"></i>
                <i class="bi bi-cursor"></i>
                <i class="bi bi-send"></i>
            </div>
        </form>
    </div>
</div>

<script src="bootstrap-5.3.7-dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
