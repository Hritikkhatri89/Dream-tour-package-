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
    <title>Forgot Password | Dream Tour & Travel</title>
    <link href="bootstrap-5.3.7-dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Poppins', sans-serif; }

        body {
            min-height: 100vh;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            color: #2b3a55;
        }

        /* Navbar */
        .navbar-custom {
            background: rgba(43, 58, 85, 0.95) !important;
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            padding: 10px 0;
            transition: all 0.4s ease;
            position: sticky;
            top: 0;
            z-index: 1050;
        }
        .navbar-custom .nav-link {
            color: rgba(255, 255, 255, 0.85) !important;
            font-weight: 500;
            margin: 0 5px;
            padding: 8px 15px !important;
            transition: all 0.3s ease;
            position: relative;
        }
        .navbar-custom .nav-link::after {
            content: "";
            position: absolute;
            width: 0;
            height: 2px;
            bottom: 2px;
            left: 50%;
            background-color: #f1a501;
            transition: all 0.3s ease;
            transform: translateX(-50%);
        }
        .navbar-custom .nav-link:hover {
            color: #f1a501 !important;
        }
        .navbar-custom .nav-link:hover::after {
            width: 30px;
        }
        .navbar-brand img {
            height: 40px;
            margin-right: 10px;
        }

        /* Main Content */
        .main-content {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: calc(100vh - 120px);
            padding: 30px 20px;
        }

        /* MAIN WRAPPER */
        .login-wrapper {
            width: 900px;
            height: 500px;
            display: flex;
            border-radius: 15px;
            overflow: hidden;
            background: white;
            box-shadow: 0 8px 25px rgba(0,0,0,0.25);
            animation: slideUp 0.5s ease;
        }

        /* LEFT IMAGE PANEL */
        .left-panel {
            width: 50%;
            background-image: url('img/leftlogin.jpg'); 
            background-size: cover;
            background-position: center;
            position: relative;
        }

        /* DARK OVERLAY */
        .left-panel::after {
            content: '';
            position: absolute;
            inset: 0;
            background: rgba(0,0,50,0.45);
        }

        /* QUOTE */
        .quote {
            position: absolute;
            bottom: 35px;
            width: 100%;
            text-align: center;
            color: white;
            font-size: 22px;
            font-weight: 700;
            line-height: 1.4;
            padding: 0 20px;
            z-index: 2;
            text-shadow: 0 2px 4px rgba(0,0,0,0.4);
        }

        /* RIGHT PANEL */
        .right-panel {
            width: 50%;
            padding: 40px 50px;
            background: #1e3d7b;
            color: white;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .icon-wrap {
            text-align: center;
            margin-bottom: 20px;
        }
        .icon-wrap .mailbox-icon {
            width: 50px;
            height: 50px;
            background: rgba(255,255,255,0.1);
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
        }

        h2 {
            color: white;
            font-size: 18px;
            font-weight: 700;
            text-align: center;
            margin-bottom: 5px;
        }
        .subtitle {
            color: rgba(255,255,255,0.8);
            font-size: 11px;
            text-align: center;
            margin-bottom: 20px;
            line-height: 1.4;
        }

        /* Step Indicator */
        .steps {
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 35px;
            position: relative;
        }
        .step-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            z-index: 2;
            width: 60px;
        }
        .step-circle {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 13px;
            font-weight: 700;
            background: rgba(255,255,255,0.1);
            color: rgba(255,255,255,0.4);
            margin-bottom: 8px;
        }
        .step-circle.active {
            background: white;
            color: #1e3d7b;
            box-shadow: 0 0 15px rgba(255,255,255,0.3);
        }
        .step-label {
            font-size: 10px;
            color: rgba(255,255,255,0.5);
            font-weight: 500;
        }
        .step-line {
            height: 1px;
            flex-grow: 1;
            background: rgba(255,255,255,0.2);
            margin: 0 -15px 23px -15px;
        }

        /* Form */
        .form-group { margin-bottom: 15px; }
        .form-group label {
            display: block;
            color: white;
            font-size: 11px;
            font-weight: 600;
            margin-bottom: 6px;
        }
        .input-wrap {
            position: relative;
            border-bottom: 1px solid rgba(255,255,255,0.3);
        }
        .input-wrap .input-icon {
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
            color: white;
            font-size: 18px;
        }
        .input-wrap input {
            width: 100%;
            background: transparent;
            border: none;
            padding: 10px 10px 10px 35px;
            font-size: 14px;
            color: #fff;
            outline: none;
        }
        .input-wrap input::placeholder { color: rgba(255,255,255,0.4); }

        .btn-send {
            width: 100%;
            background: white;
            color: #1e3d7b;
            border: none;
            border-radius: 30px;
            padding: 10px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            margin-top: 10px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        .btn-send:hover {
            background: #f0f0f0;
            transform: translateY(-1px);
        }

        .back-link {
            text-align: center;
            margin-top: 25px;
        }
        .back-link a {
            color: rgba(255,255,255,0.7);
            font-size: 14px;
            text-decoration: none;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }
        .back-link a:hover { color: white; }

        @media (max-width: 768px) {
            .login-wrapper {
                flex-direction: column;
                height: auto;
                width: 95%;
            }
            .left-panel {
                width: 100%;
                height: 200px;
            }
            .right-panel {
                width: 100%;
                padding: 30px 25px;
            }
        }
    </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-custom shadow-sm">
  <div class="container">
    <div class="d-flex align-items-center">
      <a href="login.php" class="text-white text-decoration-none me-4 fw-semibold back-btn">
        <i class="bi bi-arrow-left"></i> Back
      </a>
      <a class="navbar-brand d-flex align-items-center" href="index.php">
        <img src="img/logo.png" alt="Logo" class="me-2">
        <div class="fw-bold fs-5 text-white">Dream Tour & Travel Management</div>
      </a>
    </div>
  </div>
</nav>

<!-- Main Content -->
<div class="main-content">
    <div class="login-wrapper">
        <!-- LEFT IMAGE -->
        <div class="left-panel">
            <div class="quote">
                TRAVEL IS THE ONLY THING YOU BUY <br> THAT MAKES YOU RICHER
            </div>
        </div>

        <!-- RIGHT PANEL -->
        <div class="right-panel">
            <div class="icon-wrap">
                <div class="mailbox-icon">
                    <i class="bi bi-envelope"></i>
                </div>
            </div>

            <h2>Forgot Password</h2>
            <p class="subtitle">Enter your registered email address.<br>We'll send an OTP to verify your identity.</p>

            <!-- Step Indicator -->
            <div class="steps">
                <div class="step-item">
                    <div class="step-circle active">1</div>
                    <div class="step-label">Email</div>
                </div>
                <div class="step-line"></div>
                <div class="step-item">
                    <div class="step-circle">2</div>
                    <div class="step-label">OTP</div>
                </div>
                <div class="step-line"></div>
                <div class="step-item">
                    <div class="step-circle">3</div>
                    <div class="step-label">Reset</div>
                </div>
            </div>

            <?php if ($error): ?>
                <div class="alert alert-danger py-2 text-center" style="font-size: 13px; border-radius: 10px;">
                    <?= $error ?>
                </div>
            <?php endif; ?>

            <form method="POST">
                <div class="form-group">
                    <label>Email Address</label>
                    <div class="input-wrap">
                        <i class="bi bi-envelope-at input-icon"></i>
                        <input type="email" name="email" placeholder="Enter your registered email" required>
                    </div>
                </div>
                <button type="submit" class="btn-send">
                    <i class="bi bi-send"></i> Send OTP
                </button>
            </form>

            <div class="back-link">
                <a href="login.php"><i class="bi bi-arrow-left"></i> Back to Login</a>
            </div>
        </div>
    </div>
</div>

<script src="bootstrap-5.3.7-dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>