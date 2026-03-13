<?php
session_start();
date_default_timezone_set('Asia/Kolkata');
include("db.php");

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require 'vendor/autoload.php';

// If no email in session, redirect back
if (!isset($_SESSION['reset_email'])) {
    header("Location: forget_pass.php");
    exit;
}

$email = $_SESSION['reset_email'];
$error = '';
$success = '';
$otp_verified = isset($_SESSION['otp_verified']) ? true : false;
$password_changed = false;

// ===== STEP 3: HANDLE PASSWORD RESET =====
if (isset($_POST['new_password']) && isset($_POST['confirm_password']) && isset($_SESSION['otp_verified'])) {
    $new_pass = trim($_POST['new_password']);
    $conf_pass = trim($_POST['confirm_password']);
    $safe_email = mysqli_real_escape_string($conn, $email);

    if (strlen($new_pass) < 6) {
        $error = "Password must be at least 6 characters!";
        $otp_verified = true;
    } elseif ($new_pass !== $conf_pass) {
        $error = "Passwords do not match!";
        $otp_verified = true;
    } else {
        // Update password (plain text to match login.php)
        mysqli_query($conn,
            "UPDATE users SET password='$new_pass', reset_code=NULL, code_expire=NULL, reset_token=NULL, token_expire=NULL WHERE email='$safe_email'"
        );
        $password_changed = true;
        $otp_verified = false;
        // Clear all session data
        unset($_SESSION['otp_verified']);
        unset($_SESSION['reset_email']);
    }
}

// ===== STEP 2: VERIFY OTP =====
if (isset($_POST['otp']) && !empty(trim($_POST['otp'])) && !$otp_verified && !$password_changed) {
    $otp_input = trim($_POST['otp']);
    $safe_email = mysqli_real_escape_string($conn, $email);

    $result = mysqli_query($conn,
        "SELECT reset_code, code_expire FROM users WHERE email='$safe_email'"
    );

    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $db_otp = trim($row['reset_code'] ?? '');
        $db_expire = $row['code_expire'];
        $now = date("Y-m-d H:i:s");

        if ($db_otp && $db_otp == $otp_input && $db_expire > $now) {
            // OTP Correct → Show password form
            $otp_verified = true;
            $_SESSION['otp_verified'] = true;
            $success = "OTP verified successfully!";
        } elseif (!$db_otp || $db_otp != $otp_input) {
            $error = "Wrong OTP! Please enter the correct OTP.";
        } else {
            $error = "OTP has expired! Please click 'Resend OTP' to get a new one.";
        }
    } else {
        $error = "No OTP found for this email. Please request a new one.";
    }
}

// ===== RESEND OTP =====
if (isset($_POST['resend'])) {
    $otp = rand(100000, 999999);
    $expire = date("Y-m-d H:i:s", strtotime("+10 minutes"));
    $safe_email = mysqli_real_escape_string($conn, $email);
    mysqli_query($conn, "UPDATE users SET reset_code='$otp', code_expire='$expire' WHERE email='$safe_email'");

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
        $mail->Subject = 'Your New OTP - Dream Tour & Travel';
        $mail->isHTML(true);
        $mail->Body = "<div style='font-family:Segoe UI;text-align:center;padding:30px;'>
            <h2 style='color:#1e3d7b;'>Your New OTP</h2>
            <div style='display:inline-block;background:#1e3d7b;color:white;font-size:32px;font-weight:bold;letter-spacing:10px;padding:15px 30px;border-radius:8px;'>$otp</div>
            <p style='color:#888;margin-top:15px;'>Valid for 10 minutes.</p>
        </div>";
        $mail->send();
        $success = "New OTP sent to your email!";
    } catch (Exception $e) {
        $error = "Could not resend OTP.";
    }
}

// Determine current step
$step = 2;
if ($otp_verified) $step = 3;
if ($password_changed) $step = 4;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify OTP | Dream Tour & Travel</title>
    <link href="bootstrap-5.3.7-dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&family=Alex+Brush&display=swap" rel="stylesheet">
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
            <p class="auth-left-sub">TRAVEL IS THE ONLY THING YOU BUY THAT MAKES YOU RICHER</p>
        </div>
    </div>

    <!-- Right Section -->
    <div class="auth-right">
        <div class="plane-path">
            <i class="bi bi-airplane"></i>
        </div>

        <h2>Forgot Password</h2>
        <p class="subtitle">
            <?php 
                if ($password_changed) echo 'Your password has been updated successfully.';
                elseif ($otp_verified) echo 'Create your new password';
                else echo 'Enter the OTP sent to your email';
            ?>
        </p>

        <!-- Step Indicator -->
        <div class="auth-steps">
            <div class="auth-step-item">
                <div class="auth-step-circle active" style="background: #28a745; color: white; border: none;"><i class="bi bi-check"></i></div>
                <div class="auth-step-label">Email</div>
            </div>
            <div class="auth-step-line" style="background: #28a745;"></div>
            <div class="auth-step-item">
                <div class="auth-step-circle <?php echo ($step >= 2 && !$otp_verified) ? 'active' : (($otp_verified || $password_changed) ? 'done' : ''); ?>" 
                     style="<?php echo ($otp_verified || $password_changed) ? 'background: #28a745; color: white; border: none;' : ''; ?>">
                    <?php echo ($otp_verified || $password_changed) ? '<i class="bi bi-check"></i>' : '2'; ?>
                </div>
                <div class="auth-step-label">OTP</div>
            </div>
            <div class="auth-step-line" style="<?php echo ($otp_verified || $password_changed) ? 'background: #28a745;' : ''; ?>"></div>
            <div class="auth-step-item">
                <div class="auth-step-circle <?php echo ($step == 3) ? 'active' : ''; ?>">3</div>
                <div class="auth-step-label">Reset</div>
            </div>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-danger py-2 text-center small mb-4" style="border-radius:12px;">
                <?= $error ?>
            </div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="alert alert-success py-2 text-center small mb-4" style="border-radius:12px; background: rgba(40,167,69,0.2); border: 1px solid rgba(40,167,69,0.3); color: #2ecc71;">
                <?= $success ?>
            </div>
        <?php endif; ?>

        <?php if ($password_changed): ?>
            <!-- Success Message -->
            <div class="text-center mb-4">
                <i class="bi bi-patch-check-fill text-success" style="font-size: 60px;"></i>
                <p class="mt-3">Password reset successful!</p>
            </div>
            <a href="login.php" class="auth-btn" style="text-decoration:none; display:block; text-align:center;">SIGN IN NOW</a>

        <?php elseif ($otp_verified): ?>
            <!-- Reset Password Form -->
            <form method="POST">
                <div class="auth-form-group">
                    <label>New Password</label>
                    <div class="auth-input-container">
                        <input type="password" name="new_password" id="newPass" class="auth-input" placeholder="Min 6 characters" required>
                    </div>
                </div>

                <div class="auth-form-group">
                    <label>Confirm Password</label>
                    <div class="auth-input-container">
                        <input type="password" name="confirm_password" id="confPass" class="auth-input" placeholder="Repeat password" required>
                    </div>
                </div>

                <button type="submit" class="auth-btn">RESET PASSWORD</button>
            </form>

        <?php else: ?>
            <!-- OTP Entry Form -->
            <div class="mb-4 text-center">
                <span style="background: rgba(255,255,255,0.1); padding: 8px 15px; border-radius: 10px; font-size: 13px;">
                    <i class="bi bi-envelope-at me-2"></i> <?= htmlspecialchars($email) ?>
                </span>
            </div>

            <form method="POST" id="otpForm" onsubmit="return combineOtp()">
                <div class="auth-otp-group" style="display: flex; gap: 10px; justify-content: center; margin-bottom: 25px;">
                    <style>
                        .otp-digit {
                            width: 45px;
                            height: 55px;
                            background: rgba(255, 255, 255, 0.08);
                            border: none;
                            border-bottom: 2px solid rgba(255, 255, 255, 0.2);
                            color: white;
                            text-align: center;
                            font-size: 24px;
                            font-weight: 700;
                            border-radius: 4px;
                            outline: none;
                            transition: 0.3s;
                        }
                        .otp-digit:focus {
                            border-bottom-color: white;
                            background: rgba(255, 255, 255, 0.15);
                        }
                    </style>
                    <input type="text" class="otp-digit" maxlength="1" id="o1" autofocus>
                    <input type="text" class="otp-digit" maxlength="1" id="o2">
                    <input type="text" class="otp-digit" maxlength="1" id="o3">
                    <input type="text" class="otp-digit" maxlength="1" id="o4">
                    <input type="text" class="otp-digit" maxlength="1" id="o5">
                    <input type="text" class="otp-digit" maxlength="1" id="o6">
                </div>
                <input type="hidden" name="otp" id="hiddenOtp">

                <div class="text-center mb-4">
                    <p style="font-size: 13px; color: rgba(255,255,255,0.6);">Expires in: <span id="timer" style="color:#ff6b6b; font-weight:700;">02:00</span></p>
                </div>

                <button type="submit" class="auth-btn">VERIFY OTP</button>
            </form>

            <div class="text-center mt-4">
                <p style="font-size: 13px; color: rgba(255,255,255,0.6); margin-bottom: 5px;">Didn't receive code?</p>
                <form method="POST">
                    <button type="submit" name="resend" style="background:transparent; border:1px solid rgba(255,255,255,0.3); color:white; border-radius:20px; padding:4px 15px; font-size:12px; cursor:pointer;">Resend OTP</button>
                </form>
            </div>
        <?php endif; ?>

        <div class="text-center mt-4">
            <a href="forget_pass.php" class="auth-footer-link" style="font-size: 13px; color: rgba(255,255,255,0.7); text-decoration: none;">Change Email</a>
        </div>

        <div class="auth-illustrations">
            <i class="bi bi-shield-lock"></i>
            <i class="bi bi-envelope-open"></i>
            <i class="bi bi-key"></i>
        </div>
    </div>
</div>

<script src="bootstrap-5.3.7-dist/js/bootstrap.bundle.min.js"></script>
<script>
// OTP Auto-move logic
const boxes = document.querySelectorAll('.otp-digit');
boxes.forEach((box, i) => {
    box.addEventListener('input', () => {
        box.value = box.value.replace(/[^0-9]/g, '');
        if (box.value.length === 1 && i < boxes.length - 1) {
            boxes[i + 1].focus();
        }
    });
    box.addEventListener('keydown', (e) => {
        if (e.key === 'Backspace' && box.value === '' && i > 0) {
            boxes[i - 1].focus();
        }
    });
});

function combineOtp() {
    let otp = '';
    boxes.forEach(b => otp += b.value);
    document.getElementById('hiddenOtp').value = otp;
    return true;
}

// Timer logic
let seconds = 120;
const timerEl = document.getElementById('timer');
if (timerEl) {
    const interval = setInterval(() => {
        seconds--;
        const m = Math.floor(seconds / 60).toString().padStart(2, '0');
        const s = (seconds % 60).toString().padStart(2, '0');
        timerEl.textContent = m + ':' + s;
        if (seconds <= 0) {
            clearInterval(interval);
            timerEl.textContent = 'Expired!';
        }
    }, 1000);
}
</script>
</body>
</html>
