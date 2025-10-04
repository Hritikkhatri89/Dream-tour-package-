<?php
session_start();
include("db.php");
?>

<html>
<head>
  <title>About Us - Dream Tour & Travel</title>
  <link href="bootstrap-5.3.7-dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    html, body {
      height: 100%;
      margin: 0;
    }
    body {
      display: flex;
      flex-direction: column;
      min-height: 100vh;
      font-family: 'Segoe UI', sans-serif;
      background: linear-gradient(to bottom, #fff8f2, #fefcf9);
    }
    main {
      flex: 1;
    }
    /* Navbar */
    .navbar-custom {
      background: linear-gradient(45deg, #2b3a55, #1f2a40) !important;
    }
    .navbar-custom .navbar-brand,
    .navbar-custom .nav-link {
      color: #fff !important;
      font-weight: 500;
      transition: color 0.3s ease;
    }
    .navbar-custom .nav-link:hover {
      color: #f1a501 !important;
    }
    .navbar-brand img {
      transition: transform 0.3s ease;
    }
    .navbar-brand:hover img {
      transform: rotate(-5deg) scale(1.05);
    }
    /* Heading */
    h2 {
      color: #181e4b;
      font-weight: 700;
      position: relative;
      display: inline-block;
    }
    h2::after {
      content: "";
      display: block;
      width: 60%;
      height: 4px;
      background: #f1a501;
      margin: 8px auto 0;
      border-radius: 2px;
    }
    /* About section */
    .about-text {
      color: #5e6282;
      font-size: 1.1rem;
      line-height: 1.7;
    }
    .about-text p {
      transition: transform 0.3s ease;
    }
    .about-text p:hover {
      transform: translateX(5px);
    }
    .about-img {
      border-radius: 15px;
      box-shadow: 0 8px 20px rgba(0,0,0,0.15);
      transition: transform 0.4s ease, box-shadow 0.4s ease;
    }
    .about-img:hover {
      transform: scale(1.03);
      box-shadow: 0 12px 30px rgba(0,0,0,0.25);
    }
    /* Footer */
    footer {
      background: linear-gradient(45deg, #1e293b, #101820);
      color: white;
      font-size: 0.9rem;
      letter-spacing: 0.5px;
    }
  </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-custom shadow-sm">
  <div class="container">
    <a class="navbar-brand d-flex align-items-center" href="index.php">
        <img src="img/logo.png" alt="Logo" height="40">
      <div class="fw-bold fs-5">Dream Tour & Travel Management</div>
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse justify-content-end" id="navbarContent">
      <ul class="navbar-nav">
        <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
        <li class="nav-item"><a class="nav-link active" href="about.php">About</a></li>
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

<!-- Main Content -->
<main>
  <div class="py-5">
    <div class="container text-center">
      <h2 class="mb-5">About Dream Tour & Travel</h2>
      <div class="row align-items-center">
        <div class="col-md-6 mb-4">
          <img src="img/about.jpg" alt="About Us" class="img-fluid about-img">
        </div>
        <div class="col-md-6 about-text text-start">
          <p class="fs-5">Welcome to <strong>Dream Tour & Travel</strong>, your trusted partner for unforgettable journeys.</p>
          <p>We specialize in crafting memorable travel experiences for families, couples, groups, and solo travelers. Whether it's a peaceful retreat or an adventurous getaway, we ensure comfort, safety, and excitement at every step.</p>
          <p>With our dedicated team, curated packages, and reliable support, your dream vacation is just one click away.</p>
        </div>
      </div>
    </div>
  </div>
</main>

<!-- Footer -->
<footer class="text-center py-3 mt-auto">
  &copy; <?php echo date("Y"); ?> Dream Tour & Travel Management. All rights reserved.
</footer>

<script src="bootstrap-5.3.7-dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
