
<?php
session_start();
include("db.php");

// Fetch logged-in user's name
$logged_user_name = '';
if(isset($_SESSION['uid'])) {
    $u_q = mysqli_query($conn, "SELECT name FROM users WHERE id='".(int)$_SESSION['uid']."'");
    if($u_q && $u_row = mysqli_fetch_assoc($u_q)) {
        $logged_user_name = $u_row['name'];
    }
}
?>

<html>
<head>
  <title>Dream Tour & Travel Management</title>
<link href="bootstrap-5.3.7-dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
  <link rel="stylesheet" href="instyle.css?v=1.1">
</head>
<body>
<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-custom py-3">
  <div class="container">
    
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
        <li class="nav-item"><a class="nav-link active" href="index.php">Home</a></li>
        <li class="nav-item"><a class="nav-link" href="about.php">About</a></li>
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

<!-- PREMIUM HERO SECTION -->
<div class="hero-wrapper" style="background-image: url('img/Manali.jpg');">
    <div class="hero-overlay"></div>

    <!-- Social Sidebar -->
    <div class="social-sidebar">
        <a href="#"><i class="bi bi-twitter"></i></a>
        <a href="#"><i class="bi bi-facebook"></i></a>
        <a href="#"><i class="bi bi-instagram"></i></a>
        <a href="#"><i class="bi bi-youtube"></i></a>
    </div>

    <div class="container position-relative">
        <div class="row align-items-center">
            <div class="col-lg-7">
                <div class="hero-content">
                    <h4 class="small-title">Say yes</h4>
                    <h1 class="main-title">TO YOUR <br> <span>VACATION</span></h1>
                    <p class="desc">Plan and book your perfect trip with expert advice, travel tips, destination information and inspiration from us.</p>
                    <a href="tourpackage.php" class="btn btn-hero">Find Out More</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Side Thumbnails -->
    <div class="hero-thumbs d-none d-lg-flex">
        <div class="thumb-card"><img src="img/otty.jpg" alt="Ooty"></div>
        <div class="thumb-card"><img src="img/goa.jpeg" alt="Goa"></div>
        <div class="thumb-card"><img src="img/dhr1.jpg" alt="Kashmir"></div>
        <div class="thumb-card"><img src="img/matheran.jpg" alt="Matheran"></div>
    </div>

    <!-- Search Container -->
    <div class="search-container shadow">
        <div class="search-field">
            <label>From:</label>
            <div class="input-group">
                <i class="bi bi-geo-alt"></i>
                <input type="text" id="searchFrom" placeholder="Your Location" value="Ahmedabad, India">
            </div>
        </div>
        
        <?php
        // Fetch package names for destination suggestions
        $loc_suggestions = [];
        $loc_query = mysqli_query($conn, "SELECT DISTINCT title FROM packages");
        if($loc_query) {
            while($loc_row = mysqli_fetch_assoc($loc_query)) {
                $loc_suggestions[] = trim($loc_row['title']);
            }
        }
        ?>

        <div class="search-field">
            <label>To:</label>
            <div class="input-group">
                <i class="bi bi-map"></i>
                <input type="text" id="searchDestination" list="destinationsList" placeholder="Destination, e.g. Goa" autocomplete="off">
                <datalist id="destinationsList">
                    <?php foreach($loc_suggestions as $loc): ?>
                        <option value="<?php echo htmlspecialchars($loc); ?>">
                    <?php endforeach; ?>
                </datalist>
            </div>
        </div>
        <div class="search-field">
            <label>Date:</label>
            <div class="input-group">
                <i class="bi bi-calendar3"></i>
                <input type="date" id="searchDate" min="<?php echo date('Y-m-d'); ?>">
            </div>
        </div>
        <button class="btn btn-search" onclick="handleSearch()">Search</button>
    </div>

    <script>
    function handleSearch() {
        const dest = document.getElementById('searchDestination').value;
        if(dest.trim() !== "") {
            window.location.href = "tourpackage.php?search=" + encodeURIComponent(dest);
        } else {
            window.location.href = "tourpackage.php";
        }
    }
    </script>
</div>

<div style="height: 100px;"></div> <!-- Spacer for Search Bar -->


<!-- Best Sellers Section -->
<div class="container py-5">
  <!-- Heading -->
  <div class="text-center mb-5">
    <h1 style="font-weight:800; font-size:2.8rem; color: #2b3a55;">
      Our Best Seller Packages
    </h1>
    <p class="text-muted mt-2">Explore our top-rated destinations chosen by happy travelers 🌟</p>
  </div>

  <!-- Package Cards -->
  <div class="row g-4">
    <?php
    $packages = [
      ["img/utrakhand.jpg", "6 Nights / 5 Days", "Dehradun & Uttarakhand Tour"],
      ["img/Goa2.jpg", "5 Nights / 6 Days", "Goa Highlights"],
      ["img/Otty.jpg", "8 Nights / 9 Days", "Ooty – Queen of Hills"]
    ];

    foreach($packages as $p){
      echo '
      <div class="col-md-4 col-sm-6">
        <div class="card package-card h-100 text-center border-0 shadow-lg">
          <div class="img-wrapper position-relative overflow-hidden">
            <img src="'.$p[0].'" class="card-img-top" alt="'.$p[2].'">
            <div class="overlay d-flex justify-content-center align-items-center">
              <a href="tourpackage.php" class="btn explore-btn">Explore Now</a>
            </div>
          </div>
          <div class="card-body">
            <div class="text-muted small mb-1">'.$p[1].'</div>
            <h5 class="fw-semibold">'.$p[2].'</h5>
          </div>
        </div>
      </div>';
    }
    ?>
  </div>
</div>

<!--  Customer Reviews Section -->
<div class="container my-5">
  <!-- Heading -->
  <div class="text-center mb-5">
    <h1 style="font-weight:800; font-size:2.5rem; color: #2b3a55;">
      What Happy Travelers Say
    </h1>
    <p class="text-muted">Real feedback from our satisfied explorers ✈️</p>
  </div>

  <!-- Review Cards -->
  <div class="row g-4 text-center justify-content-center">
    <?php
    $reviews = [
      ["img/p1.jpg",5,"Best tour experience ever! The hotel and cab were perfectly arranged.","Akshay Sharma"],
      ["img/p2.jpg",4,"Affordable and super convenient. Definitely going to book again!","Riya Gupta"],
      ["img/p3.jpg",5,"We had a wonderful honeymoon trip. Everything was managed smoothly.","Raj Kumar"]
    ];
    
    foreach($reviews as $r){
      $stars = str_repeat('<i class="bi bi-star-fill text-warning"></i>', $r[1]);
      echo '
      <div class="col-md-4 col-sm-6">
        <div class="review-card p-4 h-100 shadow-sm">
          <img src="'.$r[0].'" class="rounded-circle mb-3" alt="'.$r[3].'" style="width:90px; height:90px; object-fit:cover;">
          <div class="stars mb-2">'.$stars.'</div>
          <p class="fst-italic text-muted mb-2">“'.$r[2].'”</p>
          <h5 class="fw-bold text-dark">'.$r[3].'</h5>
        </div>
      </div>';
    }
    ?>
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
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

</body>
</html>
