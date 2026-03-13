<?php
session_start();
include("db.php");
$logged_user_name = '';
if(isset($_SESSION['uid'])) {
    $u_q = mysqli_query($conn, "SELECT name FROM users WHERE id='".(int)$_SESSION['uid']."'");
    if($u_q && $u_row = mysqli_fetch_assoc($u_q)) $logged_user_name = $u_row['name'];
}

// 🔹 Package ID check
if (!isset($_GET['pid']) || empty($_GET['pid'])) {
    header("Location: tourpackage.php");
    exit;
}

$pid = intval($_GET['pid']);
$result = mysqli_query($conn, "SELECT * FROM packages WHERE id = $pid");

// 🔹 Invalid Package check
if (!$result || mysqli_num_rows($result) == 0) {
    echo "<h3>Package not found!</h3>";
    exit;
}

$package = mysqli_fetch_assoc($result);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title><?php echo htmlspecialchars($package['title']); ?> - Dream Tour & Travel</title>
<link href="bootstrap-5.3.7-dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
<link rel="stylesheet" href="instyle.css">
<link rel="stylesheet" href="viewm.css">
</head>
<body style="background:#fff;">

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

<!-- 🔹 PACKAGE DETAILS SECTION -->
<div class="container py-5">
  <div class="package-section">

    <div class="left-gallery">
      <div id="packageCarousel" class="carousel slide" data-bs-ride="carousel">
        <div class="carousel-inner">
          <?php
            $images = explode(',', $package['image']);
            $active = true;
            foreach ($images as $img):
              $img = trim($img);
          ?>
          <div class="carousel-item <?= $active ? 'active' : '' ?>">
            <img src="img/<?= htmlspecialchars($img) ?>" alt="">
          </div>
          <?php 
            $active = false;
            endforeach; 
          ?>
        </div>

        <button class="carousel-control-prev" type="button" data-bs-target="#packageCarousel" data-bs-slide="prev">
          <span class="carousel-control-prev-icon"></span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#packageCarousel" data-bs-slide="next">
          <span class="carousel-control-next-icon"></span>
        </button>

        <!-- 🔹 Thumbnail Row -->
        <div class="thumbnail-row">
          <?php foreach ($images as $index => $img): ?>
            <img src="img/<?= htmlspecialchars(trim($img)) ?>" 
                 class="thumbnail-img <?= $index==0?'active':'' ?>" 
                 onclick="
                   // Remove active from carousel
                   document.querySelectorAll('#packageCarousel .carousel-item').forEach(item => item.classList.remove('active'));
                   document.querySelectorAll('#packageCarousel .carousel-item')[<?= $index ?>].classList.add('active');

                   // Update thumbnail borders
                   document.querySelectorAll('.thumbnail-img').forEach(t => t.classList.remove('active'));
                   this.classList.add('active');
                 ">
          <?php endforeach; ?>
        </div>

      </div>
    </div>

    <div class="right-details">
      <h2 class="package-title"><?= htmlspecialchars($package['title']); ?></h2>

      <div class="package-info">
        <p><strong>Type:</strong> <?= htmlspecialchars($package['type']); ?></p>
        <p><strong>Duration:</strong> <?= htmlspecialchars($package['duration']); ?></p>
        <p><strong>Price:</strong> ₹<?= htmlspecialchars($package['price']); ?></p>
        <p><?= nl2br(htmlspecialchars($package['description'])); ?></p>
      </div>

      <?php if(isset($_SESSION['uid'])): ?>
        <a class="btn-book" href="book.php?pid=<?= $package['id']; ?>">Book Now</a>
      <?php else: ?>
        <a class="btn-book" href="login.php">Login to Book</a>
      <?php endif; ?>
    </div>

  </div>

  <!-- Itinerary -->
  <div class="itinerary mt-5">
    <h4>🗓️ Day Wise Itinerary</h4>

    <?php
    $itinerary_res = mysqli_query($conn, "SELECT * FROM itineraries WHERE package_id=$pid ORDER BY id ASC");

    if (mysqli_num_rows($itinerary_res) > 0) {
      while ($row = mysqli_fetch_assoc($itinerary_res)) {
        echo "
          <div class='timeline-content'>
            <p>".nl2br(htmlspecialchars($row['plan']))."</p>
          </div>
        ";
      }
    } else {
      echo "<p>No itinerary available.</p>";
    }
    ?>
  </div>
</div>

<script src="bootstrap-5.3.7-dist/js/bootstrap.bundle.min.js"></script>

<script>
function goBack() {
  if (document.referrer !== "") {
    window.history.back();
  } else {
    window.location.href = "tourpackage.php";
  }
}
</script>

</body>
</html>
