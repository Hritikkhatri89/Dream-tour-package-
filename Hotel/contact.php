<?php
session_start();
include("db.php");
$logged_user_name = '';
if(isset($_SESSION['uid'])) {
    $u_q = mysqli_query($conn, "SELECT name FROM users WHERE id='".(int)$_SESSION['uid']."'");
    if($u_q && $u_row = mysqli_fetch_assoc($u_q)) $logged_user_name = $u_row['name'];
}
$msg = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = mysqli_real_escape_string($conn, $_POST["name"]);
    $email = mysqli_real_escape_string($conn, $_POST["email"]);
    $message = mysqli_real_escape_string($conn, $_POST["message"]);

    $sql = "INSERT INTO get_in_touch (name, email, message) VALUES ('$name', '$email', '$message')";
    if (mysqli_query($conn, $sql)) {
        $msg = "Thank you! Your message has been sent successfully.";
    } else {
        $msg = "Failed to send message. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us | Dream Tour & Travel</title>
    <link href="bootstrap-5.3.7-dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="instyle.css?v=1.1">
    <style>
        .contact-header {
            background: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6)), url('img/Manali.jpg');
            background-size: cover;
            background-position: center;
            padding: 100px 0;
            color: white;
            text-align: center;
            margin-bottom: 50px;
        }
        .contact-info-card {
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
            height: 100%;
        }
        .info-item {
            display: flex;
            align-items: center;
            margin-bottom: 25px;
        }
        .info-icon {
            width: 50px;
            height: 50px;
            background: rgba(0, 186, 194, 0.1);
            color: #00bac2;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            margin-right: 20px;
            flex-shrink: 0;
        }
        .info-text h5 {
            margin: 0;
            font-size: 18px;
            font-weight: 700;
            color: #2b3a55;
        }
        .info-text p {
            margin: 0;
            color: #6c757d;
        }
    </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-custom py-3">
  <div class="container">
    
    <!-- Back Button -->
    <button class="back-btn me-3" onclick="window.history.back()">
      <i class="bi bi-arrow-left me-1"></i> Back
    </button>
      
    <!-- Brand -->
    <a class="navbar-brand d-flex align-items-center" href="index.php">
      <img src="img/logo.png" alt="Logo">
      <span class="fs-4">Dream Tour & Travel</span>
    </a>

    <!-- Mobile Button -->
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent">
      <span class="navbar-toggler-icon"></span>
    </button>

    <!-- CENTER NAVIGATION -->
    <div class="collapse navbar-collapse" id="navbarContent">
      <ul class="navbar-nav mx-auto">
        <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
        <li class="nav-item"><a class="nav-link" href="about.php">About</a></li>
        <li class="nav-item"><a class="nav-link" href="tourpackage.php">Destination</a></li>
        <li class="nav-item"><a class="nav-link active" href="contact.php">Contact Us</a></li>
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

<!-- Header -->
<div class="contact-header">
    <div class="container">
        <h1 class="display-4 fw-bold">Contact Us</h1>
        <p class="lead">We'd love to hear from you. Let's start a conversation.</p>
    </div>
</div>

<div class="container mb-5">
    <div class="row g-5">
        <!-- Contact Info -->
        <div class="col-lg-4">
            <div class="contact-info-card border-0 shadow-lg">
                <h3 class="fw-bold mb-4" style="color:#2b3a55;">Contact Info</h3>
                
                <div class="info-item">
                    <div class="info-icon"><i class="bi bi-geo-alt"></i></div>
                    <div class="info-text">
                        <h5>Our Office</h5>
                        <p>33, Gujrat Gas Circle, Adajan</p>
                    </div>
                </div>

                <div class="info-item">
                    <div class="info-icon"><i class="bi bi-telephone"></i></div>
                    <div class="info-text">
                        <h5>Phone Number</h5>
                        <p>+91 89800 52655</p>
                    </div>
                </div>

                <div class="info-item">
                    <div class="info-icon"><i class="bi bi-envelope"></i></div>
                    <div class="info-text">
                        <h5>Email Address</h5>
                        <p>dreamtours@gmail.com</p>
                    </div>
                </div>

                <div class="mt-4">
                    <h5 class="fw-bold mb-3">Follow Us</h5>
                    <div class="d-flex gap-3">
                        <a href="#" class="btn btn-outline-dark btn-sm rounded-circle"><i class="bi bi-facebook"></i></a>
                        <a href="#" class="btn btn-outline-dark btn-sm rounded-circle"><i class="bi bi-twitter"></i></a>
                        <a href="#" class="btn btn-outline-dark btn-sm rounded-circle"><i class="bi bi-instagram"></i></a>
                        <a href="#" class="btn btn-outline-dark btn-sm rounded-circle"><i class="bi bi-linkedin"></i></a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Contact Form (Direct transfer from index.php design) -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
                <div class="card-body p-5">
                    <div class="mb-4">
                        <h2 class="fw-bold" style="color:#2b3a55;">Get in <span style="color:#f1a501;">Touch</span></h2>
                        <p class="text-muted">We’d love to hear from you! Drop your message below and we’ll respond soon.</p>
                    </div>

                    <?php if (!empty($msg)): ?>
                    <div class="alert alert-success border-0 shadow-sm mb-4">
                        <i class="bi bi-check-circle-fill me-2"></i> <?= htmlspecialchars($msg); ?>
                    </div>
                    <?php endif; ?>

                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Full Name</label>
                            <input type="text" name="name" class="form-control form-control-lg rounded-3 border-light-subtle" placeholder="Enter your name" style="background:#f8f9fa;" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Email Address</label>
                            <input type="email" name="email" class="form-control form-control-lg rounded-3 border-light-subtle" placeholder="Enter your email" style="background:#f8f9fa;" required>
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Message</label>
                            <textarea name="message" rows="5" class="form-control form-control-lg rounded-3 border-light-subtle" placeholder="Write your message..." style="background:#f8f9fa;" required></textarea>
                        </div>
                        <button type="submit" class="btn w-100 py-3 fw-bold text-white shadow-sm" style="background:linear-gradient(135deg,#f1a501,#ff7b00); border:none; font-size:1.1rem; border-radius:10px;">
                            ✉️ Send Message
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Map -->
    <div class="map-section mt-5 border-0 rounded-4 overflow-hidden shadow-lg">
        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3671.697921504958!2d72.541334075141!3d23.034874415848263!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x395e84e859a1e0f3%3A0xc3f17387cc00632d!2sAhmedabad%20University!5e0!3m2!1sen!2sin!4v1709387000000!5m2!1sen!2sin" width="100%" height="400" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
    </div>
</div>

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
