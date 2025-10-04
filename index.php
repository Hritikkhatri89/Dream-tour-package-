<?php
session_start();
include("db.php");
$msg = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST["name"];
    $email = $_POST["email"];
    $message = $_POST["message"];

    $sql = "INSERT INTO get_in_touch (name, email, message) VALUES ('$name', '$email', '$message')";
    if (mysqli_query($conn, $sql)) {
        $msg = "Thank you! Your details have been submitted.";
    } else {
        $msg = "Failed to submit. Try again.";
    }
}
?>

<html>
<head>
  <title>Dream Tour & Travel Management</title>
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
      height:40px;
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
	  .overlay {
    position: absolute;
    top: 0; left: 0;
    width: 100%; height: 100%;
    background: rgba(0,0,0,0.25);
  }
  .carousel-caption {
    background: rgba(0,0,0,0.5);
    padding: 15px;
    border-radius: 8px;
    bottom: 30px;
  }
  .carousel-caption h5 {
    font-size: 20px;
    font-weight: bold;
    color: #fff;
  }
  .carousel-caption p {
    font-size: 14px;
    margin: 0;
    color: #ddd;
  }
  .hero-btn {
    background: linear-gradient(135deg, #f1a501, #ff6600);
    color: #fff;
    font-weight: bold;
    border: none;
    border-radius: 8px;
    padding: 12px 24px;
    font-size: 16px;
    text-transform: uppercase;
    transition: all 0.3s ease;
    box-shadow: 0 4px 12px rgba(0,0,0,0.2);
  }
  .hero-btn:hover {
    background: linear-gradient(135deg, #d89000, #cc5200);
    color: #fff;
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(0,0,0,0.3);
  }
  </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-custom shadow-sm">
  <div class="container">
    <a class="navbar-brand d-flex align-items-center" href="index.php">
      
		     <img src="img/logo.png" alt="Logo" class="me-2" style="height:40px; width:auto; object-fit:contain;">
      <div class="fw-bold fs-5 text-white">Dream Tour & Travel Management</div>
    </a>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent">
    <span class="navbar-toggler-icon"></span>
  </button>
</nav>

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
<!-- Hero Section -->
<div class="container-fluid p-0">
  <div class="row g-0">
    <div class="col-12 position-relative">
      <div id="heroCarousel" class="carousel slide carousel-fade" data-bs-ride="carousel">
        <div class="carousel-inner">

          <!-- Slide 1 -->
          <div class="carousel-item active position-relative">
            <img src="img/otty.jpg" class="w-100" style="height:500px; object-fit:cover;">
            <div class="overlay position-absolute top-0 start-0 w-100 h-100"
                 style="background:rgba(0,0,0,0.35);"></div>
            <!-- Caption with Button -->
            <div class="carousel-caption text-center p-3"
                 style="bottom:40px; background:rgba(0,0,0,0.35); border-radius:10px; max-width:350px; margin:auto;">
              <h6 class="fw-bold text-warning mb-2" style="font-family: 'Segoe UI', sans-serif; letter-spacing:1px;">
                Ooty – Queen of Hills
              </h6>
              <small class="text-light d-block mb-2" style="font-style:italic;">
                Lush tea gardens & misty mountains await!
              </small>
              <a href="tourpackage.php" class="btn btn-warning btn-sm fw-bold shadow-sm">
                ✨ View Tour Packages
              </a>
            </div>
          </div>

          <!-- Slide 2 -->
          <div class="carousel-item position-relative">
            <img src="img/manali.jpg" class="w-100" style="height:500px; object-fit:cover;">
            <div class="overlay position-absolute top-0 start-0 w-100 h-100"
                 style="background:rgba(0,0,0,0.35);"></div>
            <div class="carousel-caption text-center p-3"
                 style="bottom:40px; background:rgba(0,0,0,0.35); border-radius:10px; max-width:350px; margin:auto;">
              <h6 class="fw-bold text-warning mb-2" style="font-family: 'Segoe UI', sans-serif; letter-spacing:1px;">
                Manali – Snow Paradise
              </h6>
              <small class="text-light d-block mb-2" style="font-style:italic;">
                Adventure & snow sports in the valleys!
              </small>
              <a href="tourpackage.php" class="btn btn-warning btn-sm fw-bold shadow-sm">
                ✨ View Tour Packages
              </a>
            </div>
          </div>

          <!-- Slide 3 -->
          <div class="carousel-item position-relative">
            <img src="img/goa.jpeg" class="w-100" style="height:500px; object-fit:cover;">
            <div class="overlay position-absolute top-0 start-0 w-100 h-100"
                 style="background:rgba(0,0,0,0.35);"></div>
            <div class="carousel-caption text-center p-3"
                 style="bottom:40px; background:rgba(0,0,0,0.35); border-radius:10px; max-width:350px; margin:auto;">
              <h6 class="fw-bold text-warning mb-2" style="font-family: 'Segoe UI', sans-serif; letter-spacing:1px;">
                Goa – Beaches & Nightlife
              </h6>
              <small class="text-light d-block mb-2" style="font-style:italic;">
                Sun, sand & vibrant nightlife!
              </small>
              <a href="tourpackage.php" class="btn btn-warning btn-sm fw-bold shadow-sm">
                ✨ View Tour Packages
              </a>
            </div>
          </div>

        </div>

        <!-- Controls -->
        <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
          <span class="carousel-control-prev-icon"></span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
          <span class="carousel-control-next-icon"></span>
        </button>
      </div>
    </div>
  </div>
</div>

<!-- Best Sellers -->
<div class="container py-5">
    <div class="text-center fw-bold mb-4">
        <div style="color:#2b3a55; display:inline-block;"><h1>Our</div>
        <div style="color:#f1a501; display:inline-block;"><h1>Best Sellers Packages</h1></div>
    </div>
    <div class="row text-center">
        <?php
        $packages = [
            ["img/himachal.jpg","6 Nights / 7 Days","Himachal Pradesh"],
            ["img/boat.jpg","5 Nights / 6 Days","Kerala With House Boat"],
            ["img/sikkim.jpg","8 Nights / 9 Days","Sikkim Darjeeling Tour"],
            ["img/anda.jpg","6 Nights / 7 Days","Andaman Trip"]
        ];
        foreach($packages as $p){
            echo '<div class="col-md-3 mb-4">
                    <div class="card h-100 shadow-sm">
                        <img src="'.$p[0].'" class="card-img-top">
                        <div class="card-body">
                            <div class="text-muted small mb-1">'.$p[1].'</div>
                            <div class="fw-semibold">'.$p[2].'</div>
                            <a href="tourpackage.php" class="btn btn-sm btn-outline-primary mt-2">Read More</a>
                        </div>
                    </div>
                </div>';
        }
        ?>
    </div>
</div>

<!-- Reviews -->
<div class="container mt-5">
    <div class="text-center fw-bold"><h1>Customer Reviews</h1></div>
    <div class="row text-center mt-4">
        <?php
        $reviews = [
            ["img/p1.jpg","🌟🌟🌟🌟🌟","Best tour experience ever! The hotel and cab were perfectly arranged.","Akshay Sharma","#e8f8f5","#b2dfdb"],
            ["img/p2.jpg","🌟🌟🌟🌟","Affordable and super convenient. Definitely going to book again!","Riya Gupta","#fef9e7","#f4e3b2"],
            ["img/p3.jpg","🌟🌟🌟🌟🌟","We had a wonderful honeymoon trip. Everything was managed smoothly.","Raj kumar","#fceae8","#f4c2bd"]
        ];
        foreach($reviews as $r){	
            echo '<div class="col-md-4 mb-3">
                    <div class="p-3 h-100" style="background-color:'.$r[4].'; border:1px solid '.$r[5].'; border-radius:12px;">
                        <img src="'.$r[0].'" class="rounded-circle mx-auto d-block" style="width:80px;height:80px;object-fit:cover;">
                        <div class="mb-1 fs-5 mt-2">'.$r[1].'</div>
                        <div>"'.$r[2].'"</div>
                        <div class="fw-bold mt-2">'.$r[3].'</div>
                    </div>
                </div>';
        }
        ?>
    </div>
</div>

<!-- Contact -->
<div class="container my-5" id="getintouch">
    <div class="text-center mb-4"><h1>Get in Touch</div>
    <?php if (!empty($msg)): ?>
    <div class="alert alert-info w-100 mx-auto" style="max-width:500px;">
        <?= htmlspecialchars($msg); ?>
    </div>
    <?php endif; ?>
    <div class="mx-auto p-4 shadow-sm" style="max-width:500px; border:1px solid #141412; border-radius:15px;">
        <form method="POST">
            <div class="mb-3">
                <label class="form-label">Name:</label>
                <input type="text" name="name" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Email:</label>
                <input type="email" name="email" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Message:</label>
                <textarea name="message" rows="4" class="form-control" required></textarea>
            </div>
            <button type="submit" class="btn btn-primary w-100 fw-bold">Submit</button>
        </form>
    </div>
</div>

<!-- Footer -->
<div style="background-color:#2b3a55; color:white;" class="py-4">
    <div class="container">
        <div class="row text-center text-md-start">
            <div class="col-md-4 border-end border-white">
                <div class="fw-bold">Company</div>
                <div><a href="about.php" class="text-white text-decoration-none">About</a></div>
                <div><a href="index.php" class="text-white text-decoration-none">Home</a></div>
                <div><a href="tourpackage.php" class="text-white text-decoration-none">Booking</a></div>
            </div>
            <div class="col-md-4 border-end border-white">
                <div class="fw-bold">Contact</div>
                <div><a href="#getintouch" class="text-white text-decoration-none">Get in Touch</a></div>
            </div>
            <div class="col-md-4">
                <div class="fw-bold">Details</div>
                <div>Name: Tour & Travel Pvt Ltd</div>
                <div>Address: 33-Gujrat Gas Circle, Adajan</div>
                <div>Phone: +91 89800 52655</div>
            </div>
        </div>
    </div>
</div>
<div class="text-center small py-2" style="background-color:#1e293b; color:white;">
    &copy; <?php echo date("Y"); ?> Dream Tour & Travel Management. All rights reserved.
</div>

<script src="bootstrap-5.3.7-dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
