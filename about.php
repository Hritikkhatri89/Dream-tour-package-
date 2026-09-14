<?php
session_start();
include("db.php");
$logged_user_name = '';
if(isset($_SESSION['uid'])) {
    $u_q = mysqli_query($conn, "SELECT name FROM users WHERE id='".(int)$_SESSION['uid']."'");
    if($u_q && $u_row = mysqli_fetch_assoc($u_q)) $logged_user_name = $u_row['name'];
}
?>

<html>
<head>
  <title>About Us - Dream Tour & Travel Management</title>
  <link href="bootstrap-5.3.7-dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="instyle.css?v=1.1">
  <style>

    .about-section {
      padding: 80px 0;
    }
    .page-header {
      background: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6)), url('img/Manali.jpg');
      background-size: cover;
      background-position: center;
      padding: 100px 0;
      color: white;
      text-align: center;
    }
    .about-title {
      font-weight: 700;
      color: #2b3a55;
      margin-bottom: 25px;
      position: relative;
    }
    .about-title::after {
      content: "";
      display: block;
      width: 60px;
      height: 4px;
      background: #f1a501;
      margin-top: 8px;
      border-radius: 3px;
    }
    .about-text {
      color: #4a4a4a;
      font-size: 1.1rem;
      line-height: 1.8;
    }
    .about-img {
      border-radius: 16px;
      box-shadow: 0 10px 25px rgba(0,0,0,0.2);
      transition: 0.3s ease;
    }
    .about-img:hover {
      transform: scale(1.03);
    }
  </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-custom py-3">
  <div class="container">
    
    <!-- Back Button -->
    <button class="back-btn me-3" onclick="goBack()">
      <i class="bi bi-arrow-left me-1"></i> Back
    </button>
      
    <!-- Brand -->
    <a class="navbar-brand d-flex align-items-center" href="index.php">
      <img src="img/logo.png" alt="Logo">
      <span class="fs-4">Dream Tour & Travel </span>
    </a>

    <!-- Mobile Button -->
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent">
      <span class="navbar-toggler-icon"></span>
    </button>

    <!-- CENTER NAVIGATION -->
    <div class="collapse navbar-collapse" id="navbarContent">
      <ul class="navbar-nav mx-auto">
        <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
        <li class="nav-item"><a class="nav-link active" href="about.php">About</a></li>
        <li class="nav-item"><a class="nav-link" href="tourpackage.php">Destination</a></li>
        <li class="nav-item"><a class="nav-link" href="contact.php">Contact Us</a></li>
      </ul>

      <!-- RIGHT LOGIN -->
      <div class="d-flex align-items-center">
        <?php if(isset($_SESSION['uid'])): ?>
          <div class="dropdown">
            <a class="nav-link dropdown-toggle fw-bold" href="#" data-bs-toggle="dropdown" style="color:#2b3a55 !important;">
                <i class="bi bi-person-circle fs-5"></i> <?php echo htmlspecialchars($logged_user_name ?: 'My Account'); ?>
            </a>
            <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                <li><a class="dropdown-item" href="mybookings.php">My Bookings</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item text-danger" href="logout.php">Logout</a></li>
            </ul>
          </div>
        <?php elseif(isset($_SESSION['admin_email'])): ?>
          <div class="dropdown">
            <a class="nav-link dropdown-toggle fw-bold text-primary" href="#" data-bs-toggle="dropdown">
                <i class="bi bi-shield-lock-fill fs-5"></i> Admin Panel
            </a>
            <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                <li><a class="dropdown-item" href="admin/dashboard.php">Dashboard</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item text-danger" href="logout.php">Logout</a></li>
            </ul>
          </div>
        <?php else: ?>
          <a href="login.php" class="btn btn-login-premium">
             <i class="bi bi-person-circle fs-5"></i> Log In
          </a>
        <?php endif; ?>
      </div>
    </div>

  </div>
</nav>

<!-- Header Section -->
<div class="page-header">
    <div class="container">
        <h1 class="display-4 fw-bold">About Us</h1>
        <p class="lead">Know more about our mission and vision.</p>
    </div>
</div>

<script>
function goBack() {
  if (document.referrer !== "") {
    window.history.back();
  } else {
    window.location.href = "index.php";
  }
}
</script>


<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

<!-- 🏝️ ABOUT SECTION -->
<section class="about-section">
  <div class="container">
    <div class="row align-items-center g-5">
      <div class="col-md-6">
        <img src="img/about.jpg" class="img-fluid about-img">
      </div>
      <div class="col-md-6">
        <h2 class="about-title">About Dream Tour & Travel</h2>
        <p class="about-text">
          Welcome to <strong>Dream Tour & Travel Management</strong>, your trusted partner for unforgettable journeys!
        </p>
        <p class="about-text">
          We believe travel is not just about visiting places – it’s about creating memories that last a lifetime.
        </p>
        <p class="about-text">
          Let’s turn your dream destination into reality. 🌍✈️  
        </p>
      </div>
    </div>
  </div>
</section>

<!-- Footer -->
<footer class="pt-5 pb-3 mt-5">
  <div class="container position-relative" style="z-index: 1;">
    <div class="row text-center text-md-start g-4">
      <div class="col-md-4">
        <h4 class="fw-bold mb-3">Dream Tours Travel ✈️</h4>
        <p class="small text-white">Explore the world with comfort and confidence.  
        We design dream vacations tailored just for you.</p>
        <div class="d-flex justify-content-center justify-content-md-start mt-4">
          <a href="#" class="social-icon"><i class="bi bi-facebook"></i></a>
          <a href="#" class="social-icon"><i class="bi bi-instagram"></i></a>
          <a href="#" class="social-icon"><i class="bi bi-twitter-x"></i></a>
          <a href="#" class="social-icon"><i class="bi bi-youtube"></i></a>
        </div>
      </div>
      <div class="col-md-4">
        <h5 class="fw-semibold mb-4 text-warning">Quick Links</h5>
        <div class="d-flex flex-column gap-2">
            <a href="index.php" class="footer-link"><i class="bi bi-house-door-fill me-2"></i> Home</a>
            <a href="about.php" class="footer-link"><i class="bi bi-info-circle-fill me-2"></i> About Us</a>
            <a href="tourpackage.php" class="footer-link"><i class="bi bi-box-seam-fill me-2"></i> Tour Packages</a>
            <a href="contact.php" class="footer-link"><i class="bi bi-envelope-fill me-2"></i> Contact Us</a>
        </div>
      </div>
      <div class="col-md-4">
        <h5 class="fw-bold mb-4 text-warning">Contact Info</h5>
        <div class="d-flex flex-column gap-3">
            <p class="small mb-0 d-flex align-items-center text-white"><i class="bi bi-geo-alt-fill text-warning me-3 fs-5"></i> 33, Gujrat Gas Circle, Adajan</p>
            <p class="small mb-0 d-flex align-items-center text-white"><i class="bi bi-telephone-fill text-warning me-3 fs-5"></i> +91 89800 52655</p>
            <p class="small mb-0 d-flex align-items-center text-white"><i class="bi bi-envelope-at-fill text-warning me-3 fs-5"></i> dreamtours@gmail.com</p>
        </div>
      </div>
    </div>
	
    <hr class="border-light mt-5">
    <div class="text-center small text-white">
      © <?php echo date("Y"); ?> <span class="text-warning fw-bold">Dream Tour & Travel Management</span>. All Rights Reserved. 
    </div>
  </div>
</footer>

<script src="bootstrap-5.3.7-dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
