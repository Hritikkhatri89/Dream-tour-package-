<?php
session_start();
include("db.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $name = $_POST['name'];
  $email = $_POST['email'];
  $pass = $_POST['password'];
  
  $check = mysqli_query($conn, "SELECT * FROM users WHERE email='$email'");
  if (mysqli_num_rows($check) > 0) {
    $error = "Email already registered!";
  } else {
    $insert = mysqli_query($conn, "INSERT INTO users (name, email, password) VALUES ('$name', '$email', '$pass')");
    if ($insert) {
      $_SESSION['uid'] = mysqli_insert_id($conn);
      header("Location: index.php");
      exit;
    } else {
      $error = "Registration failed. Please try again.";
    }
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register | Dream Tour & Travel</title>
    <link href="bootstrap-5.3.7-dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&family=Alex+Brush&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="instyle.css">
    <style>
        .auth-left {
            background-image: url('img/ke1.jpg');
        }
    </style>
</head>
<body class="auth-body">

<a href="index.php" class="auth-back-btn">
    <i class="bi bi-arrow-left"></i> Back to Home
</a>

<div class="auth-card">
    <!-- Form on Left -->
    <div class="auth-right">
        <div class="plane-path">
            <i class="bi bi-airplane"></i>
        </div>

        <h2>Register</h2>
        <p class="subtitle">Join us to explore the world</p>

        <?php if (isset($error)): ?>
            <div class="alert alert-danger py-2 text-center small mb-4" style="border-radius:12px;">
                <?= $error ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="auth-form-group">
                <label>Full Name</label>
                <div class="auth-input-wrap">
                    <i class="bi bi-person"></i>
                    <input type="text" name="name" class="auth-input" placeholder="Enter your name" required>
                </div>
            </div>

            <div class="auth-form-group">
                <label>Email Address</label>
                <div class="auth-input-wrap">
                    <i class="bi bi-envelope"></i>
                    <input type="email" name="email" class="auth-input" placeholder="example@mail.com" required>
                </div>
            </div>

            <div class="auth-form-group">
                <label>Password</label>
                <div class="auth-input-wrap">
                    <i class="bi bi-lock"></i>
                    <input type="password" name="password" class="auth-input" placeholder="**************" required>
                </div>
            </div>

            <button type="submit" name="register" class="auth-btn">Create Account</button>

            <div class="auth-separator">
                <span>OR</span>
            </div>

            <div class="social-logins">
                <a href="https://accounts.google.com/signin" target="_blank" class="social-btn google"><i class="bi bi-google"></i></a>
                <a href="https://www.facebook.com/login" target="_blank" class="social-btn facebook"><i class="bi bi-facebook"></i></a>
                <a href="https://appleid.apple.com/sign-in" target="_blank" class="social-btn apple"><i class="bi bi-apple"></i></a>
            </div>

            <p class="auth-footer-text">
                Already have an account? <a href="login.php">Login Now</a>
            </p>

            <div class="auth-illustrations">
                <i class="bi bi-airplane-engines"></i>
                <i class="bi bi-map"></i>
                <i class="bi bi-luggage"></i>
            </div>
        </form>
    </div>

    <!-- Image on Right -->
    <div class="auth-left">
        <div class="auth-left-content">
            <span class="auth-logo-text">Dream Tour</span>
            <p class="auth-left-sub">Join us today and explore the world with our premium travel packages.</p>
        </div>
    </div>
</div>

<script src="bootstrap-5.3.7-dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
