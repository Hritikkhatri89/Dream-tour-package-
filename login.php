<?php
session_start();
include("db.php");

if (isset($_POST['email'])) {
    $email = $_POST['email'];
    $pass = $_POST['password'];
    $sql = "SELECT * FROM users WHERE email='$email' AND password='$pass'";
    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $_SESSION['uid'] = $row['id'];
        header("Location: index.php");
    } else {
        $error = "Invalid email or password!";
    }
}
?>
<html>
<head>
  <title>User Login</title>
  <link href="bootstrap-5.3.7-dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background: linear-gradient(135deg, #fdfcfb, #e2d1c3);
      font-family: 'Segoe UI', sans-serif;
    }
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
    .login-card {
      background-color: #ffffff;
      border-radius: 12px;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
      padding: 30px;
      width: 320px;
    }
    .login-btn {
      background-color: #f1a501;
      border: none;
      font-weight: 600;
    }
    .login-btn:hover {
      background-color: #d89000;
    }
  </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-custom shadow-sm">
  <div class="container">
    <a class="navbar-brand d-flex align-items-center" href="index.php">
            <img src="img/logo.png" alt="Logo" height="50000" weight="auto">
      <div class="fw-bold fs-5 text-white">Dream Tour & Travel Management</div>
    </a>
   <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent">
  <div class="navbar-toggler-icon"></div>
</button>

    <div class="collapse navbar-collapse justify-content-end" id="navbarContent">
      <ul class="navbar-nav">
        <li class="nav-item"><a class="nav-link text-white" href="index.php">Home</a></li>
        <li class="nav-item"><a class="nav-link text-white" href="about.php">About</a></li>
        <li class="nav-item"><a class="nav-link text-white" href="tourpackage.php">Tour Packages</a></li>
        <?php if(isset($_SESSION['uid'])): ?>
          <li class="nav-item"><a class="nav-link text-white" href="logout.php">Logout</a></li>
        <?php else: ?>
          <li class="nav-item"><a class="nav-link text-white" href="login.php">Login</a></li>
        <?php endif; ?>
        <li class="nav-item"><a class="nav-link text-white" href="admin/login.php">Admin</a></li>
      </ul>
    </div>
  </div>
</nav>

<!-- Login Box -->
<div class="container d-flex justify-content-center align-items-center" style="height: 80vh;">
  <div class="login-card">
    <h5 class="text-center mb-3">User Login</h5>
   
    <?php if (isset($error)) echo "<div class='alert alert-danger py-1 text-center'>$error</div>"; ?>

    <form method="post">
      <input type="email" name="email" class="form-control mb-2 form-control-sm" placeholder="Email" required>
      <input type="password" name="password" class="form-control mb-3 form-control-sm" placeholder="Password" required>
      <button type="submit" class="btn login-btn w-100 btn-sm">Login</button>
    </form>

    <div class="text-end mt-2">
      <a href="register.php" class="text-decoration-none small">Create Account</a>
    </div>
  </div>
</div>
<script src="bootstrap-5.3.7-dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
