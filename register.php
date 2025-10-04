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
<html>
<head>
  <title>Create Account</title>
 <link href="bootstrap-5.3.7-dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background: linear-gradient(135deg, #fdfcfb, #e2d1c3);
      font-family: 'Segoe UI', sans-serif;
    }

    /* Navbar Style */
    .navbar-custom {
      background-color: #2b3a55 !important;
    }
    .navbar-custom .navbar-brand span,
    .navbar-custom .nav-link {
      color: #fff !important;
      font-weight: 500;
    }
    .navbar-custom .nav-link:hover {
      color: #f1a501 !important;
    }
    .navbar-brand img {
      height: 40px;
      margin-right: 10px;
    }

    .register-card {
      background-color: #ffffff;
      border-radius: 12px;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
      padding: 30px;
      width: 350px;
    }
    .register-btn {
      background-color: #f1a501;
      border: none;
      font-weight: 600;
    }
    .register-btn:hover {
      background-color: #d89000;
    }
  </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-custom shadow-sm">
  <div class="container">
    <a class="navbar-brand d-flex align-items-center" href="index.php">
      <img src="img/logo.png" alt="Logo">
      <span class="fw-bold fs-5">Dream Tour & Travel Management</span>
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse justify-content-end" id="navbarContent">
      <ul class="navbar-nav">
        <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
        <li class="nav-item"><a class="nav-link" href="about.php">About</a></li>
        <li class="nav-item"><a class="nav-link" href="tourpackage.php">Tour Packages</a></li>
        <?php if(isset($_SESSION['uid'])): ?>
          <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
        <?php else: ?>
          <li class="nav-item"><a class="nav-link" href="login.php">Login</a></li>
        <?php endif; ?>
        <li class="nav-item"><a class="nav-link" href="admin/login.php">Admin</a></li>
      </ul>
    </div>
  </div>
</nav>

<!-- Register Form -->
<div class="container d-flex justify-content-center align-items-center" style="height: 85vh;">
  <div class="register-card">
    <h5 class="text-center mb-3">Create Account</h5>

    <?php if (isset($error)) echo "<div class='alert alert-danger py-1 text-center'>$error</div>"; ?>

    <form method="post">
      <input type="text" name="name" class="form-control mb-2 form-control-sm" placeholder="Full Name" required>
      <input type="email" name="email" class="form-control mb-2 form-control-sm" placeholder="Email" required>
      <input type="password" name="password" class="form-control mb-3 form-control-sm" placeholder="Password" required>
      <button type="submit" class="btn register-btn w-100 btn-sm">Register</button>
    </form>

    <div class="text-end mt-2">
      <a href="login.php" class="text-decoration-none small">Already have an account?</a>
    </div>
  </div>
</div>
<script src="bootstrap-5.3.7-dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
