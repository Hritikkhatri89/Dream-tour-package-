<?php
session_start();
include("db.php");
$msg = "";

if (isset($_POST['login'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    // Check for Admin Login first
    $admin_check = mysqli_query($conn, "SELECT * FROM admin WHERE username='$email' AND password='$password'");
    if ($admin_check && mysqli_num_rows($admin_check) > 0) {
        $admin = mysqli_fetch_assoc($admin_check);
        $_SESSION['admin_email'] = $admin['username'];
        header("Location: admin/dashboard.php");
        exit();
    }

    // Normal User Login
    $res = mysqli_query($conn, "SELECT * FROM users WHERE email='$email' AND password='$password'");
    if ($res && mysqli_num_rows($res) > 0) {
        $user = mysqli_fetch_assoc($res);
        $_SESSION['uid'] = $user['id'];
        header("Location: index.php");
        exit();
    } else {
        $msg = "Invalid email or password";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Dream Tour & Travel</title>
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
    <div class="auth-left">
        <div class="auth-left-content">
            <span class="auth-logo-text">Dream Tour</span>
            <p class="auth-left-sub">Travel is the only purchase that enriches you in ways beyond material wealth.</p>
        </div>
    </div>

    <div class="auth-right">
        <div class="plane-path">
            <i class="bi bi-airplane"></i>
        </div>

        <h2>Welcome</h2>
        <p class="subtitle">Login to access your account</p>

        <?php if (!empty($msg)) echo "<div class='alert alert-danger py-2 text-center small mb-4' style='border-radius:12px;'>$msg</div>"; ?>

        <form method="POST">
            <div class="auth-form-group">
                <label>Email or Username</label>
                <div class="auth-input-wrap">
                    <i class="bi bi-envelope"></i>
                    <input type="text" name="email" class="auth-input" placeholder="example@mail.com" required>
                </div>
            </div>

            <div class="auth-form-group">
                <label>Password</label>
                <div class="auth-input-wrap">
                    <i class="bi bi-lock"></i>
                    <input type="password" name="password" class="auth-input" placeholder="**************" required>
                </div>
            </div>

            <div class="auth-action-link">
                <a href="forget_pass.php">Forgot password?</a>
            </div>

            <button type="submit" name="login" class="auth-btn">Sign In</button>

            <div class="auth-separator">
                <span>OR</span>
            </div>

            <div class="social-logins">
                <a href="https://accounts.google.com/signin" target="_blank" class="social-btn google"><i class="bi bi-google"></i></a>
                <a href="https://www.facebook.com/login" target="_blank" class="social-btn facebook"><i class="bi bi-facebook"></i></a>
                <a href="https://appleid.apple.com/sign-in" target="_blank" class="social-btn apple"><i class="bi bi-apple"></i></a>
            </div>

            <p class="auth-footer-text">
                Don't have an account? <a href="register.php">Register Now</a>
            </p>

            <div class="auth-illustrations">
                <i class="bi bi-geo-alt"></i>
                <i class="bi bi-compass"></i>
                <i class="bi bi-send"></i>
            </div>
        </form>
    </div>
</div>

<script src="bootstrap-5.3.7-dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
