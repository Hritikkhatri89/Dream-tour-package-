<?php
session_start();
date_default_timezone_set('Asia/Kolkata'); // Force Indian time
include("db.php");

$error   = '';
$success = '';
$valid   = false;

// Get token from URL
if (!isset($_GET['token']) || empty($_GET['token'])) {
    header("Location: forget_pass.php");
    exit;
}

$token = mysqli_real_escape_string($conn, $_GET['token']);

// Validate token using PHP time (avoids MySQL timezone mismatch)
$result = mysqli_query($conn,
    "SELECT * FROM users WHERE reset_token='$token'"
);

$now = date("Y-m-d H:i:s");

if (mysqli_num_rows($result) > 0) {
    $user = mysqli_fetch_assoc($result);
    if ($user['token_expire'] > $now) {
        $email = $user['email'];
        $valid = true;
    } else {
        $error = "❌ This reset link has expired. Please request a new one.";
    }
} else {
    $error = "❌ This reset link is invalid. Please request a new one.";
}

// Handle form submission
if ($valid && isset($_POST['password'])) {
    $newpass  = mysqli_real_escape_string($conn, $_POST['password']);
    $confpass = mysqli_real_escape_string($conn, $_POST['confirm_password']);

    if (strlen($newpass) < 6) {
        $error = "❌ Password must be at least 6 characters!";
    } elseif ($newpass !== $confpass) {
        $error = "❌ Passwords do not match!";
    } else {
        // Store password (plain text to match login.php)
        $hashed = $newpass;

        // Update password, clear token
        mysqli_query($conn,
            "UPDATE users SET password='$hashed', reset_token=NULL, token_expire=NULL WHERE email='$email'"
        );

        $success = "✅ Password changed successfully! You can now login.";
        $valid   = false; // hide form after success
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password | Dream Tour & Travel</title>
    <link href="bootstrap-5.3.7-dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'Poppins', sans-serif; }
        body {
            min-height: 100vh;
            background: linear-gradient(135deg, #0f2557 0%, #1e3d7b 50%, #2d5fa6 100%);
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .card-box {
            background: white;
            border-radius: 20px;
            padding: 50px 45px;
            width: 100%;
            max-width: 450px;
            box-shadow: 0 25px 60px rgba(0,0,0,0.35);
            animation: fadeIn 0.5s ease;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .icon-circle {
            width: 80px; height: 80px;
            background: linear-gradient(135deg, #1e3d7b, #2d5fa6);
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 20px;
            box-shadow: 0 8px 20px rgba(30,61,123,0.4);
        }
        .icon-circle svg { width: 38px; height: 38px; fill: white; }
        h4 { color: #1e3d7b; font-weight: 700; text-align: center; }
        .subtitle { color: #888; font-size: 14px; text-align: center; margin-bottom: 25px; }
        .form-label { font-weight: 600; color: #444; font-size: 14px; }
        .input-group .form-control {
            border: 2px solid #e8e8e8;
            border-right: none;
            border-radius: 10px 0 0 10px;
            padding: 12px 15px;
            font-size: 14px;
        }
        .input-group .form-control:focus {
            border-color: #1e3d7b;
            box-shadow: none;
            z-index: 1;
        }
        .toggle-eye {
            border: 2px solid #e8e8e8;
            border-left: none;
            border-radius: 0 10px 10px 0;
            background: #f8f9fa;
            cursor: pointer;
            padding: 0 15px;
            transition: all 0.2s;
        }
        .toggle-eye:hover { background: #e9ecef; }
        .btn-reset {
            background: linear-gradient(135deg, #1e3d7b, #2d5fa6);
            color: white;
            border: none;
            border-radius: 10px;
            padding: 12px;
            font-size: 15px;
            font-weight: 600;
            width: 100%;
            margin-top: 10px;
            transition: all 0.3s;
        }
        .btn-reset:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(30,61,123,0.4);
            color: white;
        }
        .alert { border-radius: 10px; font-size: 14px; }
        /* Password strength bar */
        .strength-bar {
            height: 5px;
            border-radius: 5px;
            background: #eee;
            margin-top: 8px;
            overflow: hidden;
        }
        .strength-fill {
            height: 100%;
            border-radius: 5px;
            width: 0;
            transition: width 0.4s, background 0.4s;
        }
        .strength-label { font-size: 12px; margin-top: 4px; }
        .step-indicator { display: flex; justify-content: center; gap: 8px; margin-bottom: 25px; }
        .step { width: 30px; height: 5px; border-radius: 10px; background: #e0e0e0; }
        .step.active { background: #1e3d7b; }
        .success-icon {
            text-align: center;
            font-size: 60px;
            margin: 10px 0 20px;
        }
        .btn-login {
            background: linear-gradient(135deg, #27ae60, #2ecc71);
            color: white;
            border: none;
            border-radius: 10px;
            padding: 12px;
            font-size: 15px;
            font-weight: 600;
            width: 100%;
            text-decoration: none;
            display: block;
            text-align: center;
            transition: all 0.3s;
            margin-top: 15px;
        }
        .btn-login:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(39,174,96,0.4); color: white; }
    </style>
</head>
<body>
<div class="card-box">
    <!-- Step indicator -->
    <div class="step-indicator">
        <div class="step active"></div>
        <div class="step active"></div>
        <div class="step <?= $success ? 'active' : '' ?>"></div>
    </div>

    <?php if ($success): ?>
        <!-- SUCCESS STATE -->
        <div class="success-icon">🎉</div>
        <h4>Password Changed!</h4>
        <p class="subtitle">Your password has been updated successfully.</p>
        <div class="alert alert-success"><?= $success ?></div>
        <a href="login.php" class="btn-login">Go to Login →</a>

    <?php elseif (!$valid): ?>
        <!-- INVALID/EXPIRED LINK -->
        <div style="text-align:center;font-size:50px;margin:10px 0 20px;">⚠️</div>
        <h4>Link Expired!</h4>
        <p class="subtitle">This reset link is invalid or has expired.</p>
        <div class="alert alert-danger"><?= $error ?></div>
        <a href="forget_pass.php" class="btn-login" style="background:linear-gradient(135deg,#1e3d7b,#2d5fa6);">Request New Link →</a>

    <?php else: ?>
        <!-- RESET PASSWORD FORM -->
        <div class="icon-circle">
            <svg viewBox="0 0 24 24"><path d="M18 8h-1V6c0-2.76-2.24-5-5-5S7 3.24 7 6v2H6c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V10c0-1.1-.9-2-2-2zm-6 9c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2zm3.1-9H8.9V6c0-1.71 1.39-3.1 3.1-3.1 1.71 0 3.1 1.39 3.1 3.1v2z"/></svg>
        </div>

        <h4>Set New Password</h4>
        <p class="subtitle">Create a strong new password for your account.</p>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-3">
                <label class="form-label">🔒 New Password</label>
                <div class="input-group">
                    <input type="password" name="password" class="form-control" id="newPass" placeholder="Enter new password" required>
                    <button type="button" class="toggle-eye" onclick="togglePass('newPass', this)">👁️</button>
                </div>
                <div class="strength-bar"><div class="strength-fill" id="strengthFill"></div></div>
                <div class="strength-label" id="strengthLabel"></div>
            </div>

            <div class="mb-3">
                <label class="form-label">🔒 Confirm Password</label>
                <div class="input-group">
                    <input type="password" name="confirm_password" class="form-control" id="confPass" placeholder="Re-enter new password" required>
                    <button type="button" class="toggle-eye" onclick="togglePass('confPass', this)">👁️</button>
                </div>
                <div id="matchMsg" style="font-size:12px;margin-top:4px;"></div>
            </div>

            <button type="submit" class="btn-reset">🔐 Reset Password</button>
        </form>
    <?php endif; ?>
</div>

<script src="bootstrap-5.3.7-dist/js/bootstrap.bundle.min.js"></script>
<script>
function togglePass(id, btn) {
    const inp = document.getElementById(id);
    if (inp.type === 'password') {
        inp.type = 'text';
        btn.textContent = '🙈';
    } else {
        inp.type = 'password';
        btn.textContent = '👁️';
    }
}

// Password strength
const newPass = document.getElementById('newPass');
const fill    = document.getElementById('strengthFill');
const label   = document.getElementById('strengthLabel');
const confPass = document.getElementById('confPass');
const matchMsg = document.getElementById('matchMsg');

if (newPass) {
    newPass.addEventListener('input', () => {
        const val = newPass.value;
        let strength = 0;
        if (val.length >= 6) strength++;
        if (val.length >= 10) strength++;
        if (/[A-Z]/.test(val)) strength++;
        if (/[0-9]/.test(val)) strength++;
        if (/[^A-Za-z0-9]/.test(val)) strength++;

        const colors = ['#e74c3c','#e67e22','#f1c40f','#2ecc71','#27ae60'];
        const labels = ['Very Weak','Weak','Fair','Strong','Very Strong'];
        const w = (strength / 5) * 100;

        fill.style.width = w + '%';
        fill.style.background = colors[strength - 1] || '#eee';
        label.textContent = labels[strength - 1] || '';
        label.style.color = colors[strength - 1] || '#888';
    });
}

if (confPass) {
    confPass.addEventListener('input', () => {
        if (confPass.value === newPass.value) {
            matchMsg.textContent = '✅ Passwords match';
            matchMsg.style.color = '#27ae60';
        } else {
            matchMsg.textContent = '❌ Passwords do not match';
            matchMsg.style.color = '#e74c3c';
        }
    });
}
</script>
</body>
</html>