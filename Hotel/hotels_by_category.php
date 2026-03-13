<?php
session_start();
include("db.php");

if (!isset($_GET['pid']) || !isset($_GET['cat'])) {
    die("Invalid Request");
}

$pid = intval($_GET['pid']);
$category = mysqli_real_escape_string($conn, $_GET['cat']);

$package_sql = mysqli_query($conn, "SELECT title FROM packages WHERE id=$pid");
$package = mysqli_fetch_assoc($package_sql);
if (!$package) {
    die("Package not found");
}

$hotels_sql = mysqli_query($conn, "
    SELECT * FROM hotels 
    WHERE package_id = '$pid' 
    AND category = '$category'
    ORDER BY price ASC
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php echo $category; ?> Hotels for <?php echo htmlspecialchars($package['title']); ?></title>

<link href="bootstrap-5.3.7-dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
<link rel="stylesheet" href="instyle.css">

<style>
  body {
      background: #fff;
      font-family: 'Segoe UI', sans-serif;
    }

/* HOTEL DESIGN */
.hotel-card img { height: 200px; object-fit: cover; border-radius: 10px 10px 0 0; }
.hotel-card { transition: transform 0.2s; border: none; border-radius: 10px; }
.hotel-card:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,0.1); }
.hotel-link { text-decoration: none; color: inherit; }
</style>
</head>

<body>

<?php
$logged_user_name = '';
if(isset($_SESSION['uid'])) {
    $u_q = mysqli_query($conn, "SELECT name FROM users WHERE id='".(int)$_SESSION['uid']."'");
    if($u_q && $u_row = mysqli_fetch_assoc($u_q)) $logged_user_name = $u_row['name'];
}
?>

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

<div class="container my-5">

    <h2 class="text-center mb-4 text-primary fw-bold"><?php echo $category; ?> Hotels: <?php echo htmlspecialchars($package['title']); ?></h2>
    <hr class="mb-5">

    <div class="row g-4">
    <?php if(mysqli_num_rows($hotels_sql) > 0): ?>
        <?php while($hotel = mysqli_fetch_assoc($hotels_sql)): ?>
            <div class="col-md-4">
                <a href="hotel_detail.php?pid=<?php echo $pid; ?>&hid=<?php echo $hotel['id']; ?>" class="hotel-link">
                    <div class="card shadow-sm hotel-card h-100">

                        <?php $images = explode(",", $hotel['images']); ?>

                        <div id="carousel<?php echo $hotel['id']; ?>" class="carousel slide" data-bs-interval="3000">
                          <div class="carousel-inner">
                            <?php foreach($images as $index => $img): ?>
                              <div class="carousel-item <?php echo $index == 0 ? 'active' : ''; ?>">
                                <img src="Hotel/<?php echo trim($img); ?>" class="d-block w-100">
                              </div>
                            <?php endforeach; ?>
                          </div>
                        </div>

                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title fw-bold"><?php echo $hotel['name']; ?></h5>
                            <p class="text-muted small"><?php echo $hotel['location']; ?></p>
                            <p><span class="badge bg-warning text-dark">⭐ <?php echo $hotel['rating']; ?> / 5</span></p>
                            <p class="fw-bold text-success mt-auto">₹<?php echo $hotel['price']; ?> <small class="text-muted">/ Night</small></p>
                        </div>

                    </div>
                </a>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <div class="alert alert-warning text-center w-100">No <?php echo $category; ?> Hotels Available.</div>
    <?php endif; ?>
    </div>

    <div class="text-center mt-5">
        <a href="book.php?pid=<?php echo $pid; ?>" class="btn btn-secondary rounded-pill px-4">← Choose Another Hotel Category</a>
    </div>

</div>

<script>
function goBack() { window.history.back(); }
</script>

<script src="bootstrap-5.3.7-dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
