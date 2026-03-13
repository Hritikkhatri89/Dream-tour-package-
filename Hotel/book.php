<?php
session_start();
include("db.php");

if (!isset($_SESSION['uid'])) {
    header("Location: login.php");
    exit;
}
$logged_user_name = '';
$u_q = mysqli_query($conn, "SELECT name FROM users WHERE id='".(int)$_SESSION['uid']."'");
if($u_q && $u_row = mysqli_fetch_assoc($u_q)) $logged_user_name = $u_row['name'];

if (isset($_GET['pid'])) {
    $pid = intval($_GET['pid']);
    $sql = mysqli_query($conn, "SELECT * FROM packages WHERE id=$pid");
    $package = mysqli_fetch_assoc($sql);
} else {
    die("Invalid Package ID");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Select Hotel</title>
<link href="bootstrap-5.3.7-dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="instyle.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

<style>
body {
    background: #fff;
    font-family: 'Poppins', sans-serif;
}


/* Navbar uses instyle.css defaults */

/* Booking Card */
.booking-card {
    border: none;
    border-radius: 25px;
    padding: 30px;
    background: #ffffff;
    max-width: 850px;
    margin: 30px auto;
    box-shadow: 0 10px 30px rgba(0,0,0,0.15);
}
.package-img {
    width: 100%;
    height: 330px;
    object-fit: cover;
    border-radius: 18px;
    border: 4px solid #fff;
    box-shadow: 0 5px 18px rgba(0,0,0,0.18);
}

/* Arrow Buttons */
.arrow-btn {
    position: absolute;
    top: 50%; transform: translateY(-50%);
    background: rgba(0,0,0,0.35);
    border: none; color: white;
    font-size: 32px; padding: 6px 14px;
    border-radius: 50px; cursor: pointer;
    transition: 0.3s;
}
.arrow-btn:hover { background: rgba(0,0,0,0.6); }
.left-arrow { left: 10px; }
.right-arrow { right: 10px; }

/* Hotel Buttons */
.hotel-btn { border-radius: 40px; padding: 12px 28px; margin: 6px; font-weight: 600; }
.hotel-3 { border: 2px solid #17a2b8; color: #17a2b8; background: #e8f9fc; }
.hotel-3:hover { background:#17a2b8; color:#fff; }
.hotel-4 { border: 2px solid #28a745; color:#28a745; background: #eafaf0; }
.hotel-4:hover { background:#28a745; color:#fff; }
.hotel-5 { border:2px solid #ffc107; color:#ffc107; background:#fff9e6; }
.hotel-5:hover { background:#ffc107; color:#000; }

.title-bar {
    background: linear-gradient(to right, #1565c0, #26a69a);
    color: white; text-align: center; padding: 15px;
    border-radius: 25px 25px 0 0; font-size: 22px; font-weight: 600;
}


</style>
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
        <?php else: ?>
          <a href="login.php" class="btn btn-login-premium">
             <i class="bi bi-person-circle fs-5"></i> Log In
          </a>
        <?php endif; ?>
      </div>
    </div>

  </div>
</nav>

<!-- ✅ Booking Card -->
<div class="container py-5">
<div class="booking-card">

<div class="title-bar">Choose Your Hotel 🏨</div>
<div class="mt-4">

<?php 
$images = [];
if (!empty($package['image'])) {
    $images = explode(',', $package['image']);
}
?>

<div class="row mb-4">
    <div class="col-12 text-center position-relative">
        <button class="arrow-btn left-arrow" onclick="prevImage()">&#10094;</button>
        <img id="mainPreview" src="img/<?php echo trim($images[0]); ?>" class="package-img">
        <button class="arrow-btn right-arrow" onclick="nextImage()">&#10095;</button>
    </div>
</div>

<h4 class="fw-bold text-primary text-center"><?php echo htmlspecialchars($package['title']); ?></h4>
<h5 class="text-success text-center mt-2">Price: ₹<?php echo htmlspecialchars($package['price']); ?></h5>

</div>
<div class="text-center mt-4">
    <a href="hotels_by_category.php?pid=<?php echo $pid; ?>&cat=3 Star" class="btn hotel-btn hotel-3">3 Star Hotels</a>
    <a href="hotels_by_category.php?pid=<?php echo $pid; ?>&cat=4 Star" class="btn hotel-btn hotel-4">4 Star Hotels</a>
    <a href="hotels_by_category.php?pid=<?php echo $pid; ?>&cat=5 Star" class="btn hotel-btn hotel-5">5 Star Hotels</a>
</div>

</div>
</div>

<script>
function goBack() {
    window.history.back();
}
let images = <?php echo json_encode($images); ?>;
let index = 0;

function changeImage(i){
    index = i;
    document.getElementById("mainPreview").src = "img/" + images[index].trim();
}

function nextImage(){
    index = (index + 1) % images.length;
    changeImage(index);
}

function prevImage(){
    index = (index - 1 + images.length) % images.length;
    changeImage(index);
}
</script>

<script src="bootstrap-5.3.7-dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
