<?php
session_start();
include("db.php");
$logged_user_name = '';
if(isset($_SESSION['uid'])) {
    $u_q = mysqli_query($conn, "SELECT name FROM users WHERE id='".(int)$_SESSION['uid']."'");
    if($u_q && $u_row = mysqli_fetch_assoc($u_q)) $logged_user_name = $u_row['name'];
}

if (isset($_POST['ajax_book']) && isset($_SESSION['uid'])) {
    $pid = intval($_POST['pid']);
    $uid = $_SESSION['uid'];

    $sql = "INSERT INTO bookings (user_id, package_id, status, booking_date)
            VALUES ('$uid', '$pid', 'confirm', NOW())";

    if (mysqli_query($conn, $sql)) {
        echo json_encode(["status" => "success", "message" => "Booking confirmed!"]);
    } else {
        echo json_encode(["status" => "error", "message" => mysqli_error($conn)]);
    }
    exit;
}

$packages = mysqli_query($conn, "SELECT * FROM packages");
if (!$packages) {
    die("SQL Error: " . mysqli_error($conn));
}
?>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Explore Tour Packages</title>
<link href="bootstrap-5.3.7-dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="instyle.css?v=1.1">
<style>
  /* BACK BUTTON */
    .page-header {
      background: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6)), url('img/Manali.jpg');
      background-size: cover;
      background-position: center;
      padding: 100px 0;
      color: white;
      text-align: center;
    }
    .back-btn {
      background: transparent;
      border: none;
      color: white;
      font-weight: 500;
      position: relative;
      padding: 0;
      font-size: 16px;
      transition: 0.3s;
      cursor: pointer;
    }
    .back-btn::after {
      content: '';
      position: absolute;
      left: 0; bottom: -3px;
      width: 0;
      height: 2px;
      background: linear-gradient(90deg, #f1a501, #ff6600);
      transition: width 0.3s ease;
    }
    .back-btn:hover::after { width: 100%; }
    .back-btn i { transition: transform 0.3s ease; }
    .back-btn:hover i { transform: translateX(-4px); }
/* Section Title */
.section-title {
    font-weight: 700;
    text-align: center;
    color: #2b3a55;
    margin-bottom: 50px;
    position: relative;
}
.section-title::after {
    content: '';
    display: block;
    width: 100px;
    height: 4px;
    background: linear-gradient(90deg, #ff8c00, #f1a501);
    margin: 10px auto 0;
    border-radius: 5px;
}

/* Search Box */
.search-box {
    text-align: center;
    margin-bottom: 40px;
}
.search-box input {
    width: 60%;
    border-radius: 30px;
    padding: 10px 20px;
    border: 2px solid #2b3a55;
    outline: none;
    transition: 0.3s;
}
.search-box input:focus {
    border-color: #f1a501;
    box-shadow: 0 0 8px rgba(241,165,1,0.5);
}

/* Override package-card from instyle.css to remove double background */
.package-card {
    background: transparent !important;
    backdrop-filter: none !important;
    box-shadow: none !important;
}

/* Card Design - Clean Square Box with Round Corners */
.card {
    border: 1px solid rgba(0,0,0,0.08);
    border-radius: 15px;
    background: #ffffff;
    box-shadow: 0 4px 15px rgba(0,0,0,0.05);
    transition: all 0.3s ease;
    overflow: hidden;
}
.card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.1);
}
.card-img-top {
    height: 220px;
    object-fit: cover;
    border-radius: 20px 20px 0 0;
    transition: 0.4s ease;
}
.card:hover .card-img-top {
    transform: scale(1.08);
}
.card-body {
    padding: 20px;
}
.card-title {
    color: #2b3a55;
    font-weight: 600;
}
.card p {
    font-size: 14px;
    margin-bottom: 6px;
}

/* Buttons */
.btn-book {
    background: linear-gradient(135deg, #f1a501, #ff6600);
    border: none;
    font-weight: 600;
    color: white;
    border-radius: 30px;
    padding: 8px 18px;
    transition: all 0.3s ease;
}
.btn-book:hover {
    background: linear-gradient(135deg, #ff8800, #ff4d00);
    transform: scale(1.05);
}
.btn-outline {
    border: 2px solid #2b3a55;
    color: #2b3a55;
    border-radius: 30px;
    padding: 8px 18px;
    transition: all 0.3s ease;
}
.btn-outline:hover {
    background: #2b3a55;
    color: white;
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
        <li class="nav-item"><a class="nav-link active" href="tourpackage.php">Destination</a></li>
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
        <h1 class="display-4 fw-bold">Our Destinations</h1>
        <p class="lead">Explore the beauty of the world with our curated packages.</p>
    </div>
</div>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

<!-- Search Box -->
<div class="container py-4">
  <div class="search-box">
    <input type="text" placeholder="🔍 Search for destination, type or duration..." id="searchInput">
  </div>
</div>

<!-- Package Section -->
<div class="container pb-5">
  <h3 class="section-title">✨ Explore Our Exclusive Packages ✨</h3>
  <div class="row g-4" id="packageContainer">
    <?php while($row = mysqli_fetch_assoc($packages)) { 
        $images = explode(",", $row['image']);
        $carousel_id = "carousel".$row['id'];
    ?>
    <div class="col-md-4 package-card">
      <div class="card h-100">

        <!-- Image Carousel -->
        <div id="<?php echo $carousel_id; ?>" class="carousel slide" data-bs-ride="carousel">

        <a href="view_more.php?pid=<?= $row['id']; ?>" style="text-decoration:none;">

            <div class="carousel-inner">
                <?php 
                $active = "active";
                foreach($images as $img) {
                    if(trim($img)=="") continue;
                ?>
                <div class="carousel-item <?= $active; ?>">
                    <img src="img/<?= $img; ?>" class="d-block w-100 card-img-top"
                     style="height:220px;object-fit:cover;"
                     onerror="this.src='img/placeholder.png';">
                </div>
                <?php 
                    $active = ""; 
                } ?>
            </div>
        </a>

        <button class="carousel-control-prev" type="button" data-bs-target="#<?= $carousel_id; ?>" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" style="filter: invert(1);"></span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#<?= $carousel_id; ?>" data-bs-slide="next">
            <span class="carousel-control-next-icon" style="filter: invert(1);"></span>
        </button>

        </div>

        <div class="card-body">
            <h5 class="card-title"><?= $row['title']; ?></h5>
            <p><b>Type:</b> <?= $row['type']; ?></p>
            <p><b>Duration:</b> <?= $row['duration']; ?></p>
            <p><b>Price:</b> ₹<?= $row['price']; ?></p>

            <div class="d-flex justify-content-between mt-3">
            <?php if (isset($_SESSION['uid'])) { ?>
                <a href="book.php?pid=<?= $row['id']; ?>" class="btn btn-book btn-sm">Book Now</a>
            <?php } else { ?>
                <a href="login.php" class="btn btn-outline btn-sm w-100">Login to Book</a>
            <?php } ?>
            </div>

        </div>

      </div>
    </div>
    <?php } ?>
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
<script>
// Search Filter
function applyFilter(query) {
    let filter = query.toLowerCase();
    let cards = document.querySelectorAll(".package-card");
    cards.forEach(card => {
        card.style.display = card.textContent.toLowerCase().includes(filter) ? "" : "none";
    });
}

document.getElementById("searchInput").addEventListener("keyup", function() {
    applyFilter(this.value);
});

// Auto-filter if search param exists
window.onload = function() {
    const urlParams = new URLSearchParams(window.location.search);
    const searchQuery = urlParams.get('search');
    if (searchQuery) {
        document.getElementById("searchInput").value = searchQuery;
        applyFilter(searchQuery);
    }
};

// Sync Back function
function goBack() {
  if (document.referrer !== "") {
    window.history.back();
  } else {
    window.location.href = "index.php";
  }
}
</script>

<script src="bootstrap-5.3.7-dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
