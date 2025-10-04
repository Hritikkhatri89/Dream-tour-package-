<?php
session_start();
include("../db.php");
$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user = $_POST['username'];
    $pass = $_POST['password'];
    $res = mysqli_query($conn, "SELECT * FROM admin WHERE username='$user' AND password='$pass'");
    if (mysqli_num_rows($res) > 0) {
        $_SESSION['admin'] = $user;
        header("Location: dashboard.php");
        exit;
    } else {
        $error = "Invalid Credentials";
    }
}
?>
<html>
<head>
  <title>Admin Login</title>
  <link href="../bootstrap-5.3.7-dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background: linear-gradient(120deg, #dbeafe, #f0f9ff);
      min-height: 100vh;
      margin: 0;
      font-family: 'Segoe UI', sans-serif;
      display: flex;
      flex-direction: column;
    }
    .navbar-custom {
      background-color: #2b3a55 ;
    }
    .navbar-custom .navbar-brand,
    .navbar-custom .nav-link {
      color: #fff !important;
      font-weight: 500;
    }
    .navbar-custom .nav-link:hover {
      color: #f1a501 !important;
    }
    main {
      flex: 1;
      display: flex;
      justify-content: center;
      align-items: center;
    }
    .login-box {
      background-color: white;
      padding: 30px 25px;
      border-radius: 12px;
      box-shadow: 0 10px 25px rgba(0,0,0,0.1);
      width: 100%;
      max-width: 350px;
    }
    .login-box h4 {
      text-align: center;
      color: #0d6efd;
      font-weight: 600;
      margin-bottom: 20px;
    }
    .form-control {
      font-size: 14px;
      border-radius: 6px;
    }
    .btn-login {
      background-color: #f1a501;
      color: white;
      font-weight: bold;
      border: none;
      border-radius: 6px;
    }
    .btn-login:hover {
      background-color: #d89000;
    }
    .alert {
      font-size: 14px;
      padding: 6px;
    }
  </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-custom shadow-sm" style="height:70px;">
  <div class="container">
    <a class="navbar-brand d-flex align-items-center" href="../index.php">
      <img src="../img/logo.png" alt="Logo" class="me-2" style="height:40px; width:auto; object-fit:contain;">
      <div class="fw-bold fs-5 mb-0">Dream Tour & Travel Management</div>
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent">
      <div class="navbar-toggler-icon"></div>
    </button>
    <div class="collapse navbar-collapse justify-content-end" id="navbarContent">
    
    </div>
  </div>
</nav>

<!-- main --!>
<main>
  <div class="login-box">
    <h4>Admin Login</h4>
    <?php if ($error): ?>
      <div class="alert alert-danger text-center"><?= $error ?></div>
    <?php endif; ?>
    <form method="POST">
      <div class="mb-3">
        <input type="text" name="username" class="form-control" placeholder="Username" required>
      </div>
      <div class="mb-3">
        <input type="password" name="password" class="form-control" placeholder="Password" required>
      </div>
      <button type="submit" class="btn btn-login w-100">Login</button>
    </form>
  </div>
</main>
<script src="bootstrap-5.3.7-dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
