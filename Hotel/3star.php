<?php
session_start();
include("db.php");

if (!isset($_GET['pid'])) {
    die("Invalid Package ID");
}

$pid = intval($_GET['pid']);

// Fetch Package Title
$package_sql = mysqli_query($conn, "SELECT title FROM packages WHERE id=$pid");
$package = mysqli_fetch_assoc($package_sql);
if (!$package) {
    die("Package not found");
}

// Fetch only 3 Star Hotels
$hotels_sql = mysqli_query($conn, "
    SELECT * FROM hotels 
    WHERE package_id = '$pid' 
    AND category = '3 Star'
    ORDER BY price ASC
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>3 Star Hotels - <?php echo htmlspecialchars($package['title']); ?></title>

<link href="bootstrap-5.3.7-dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body {
    background: linear-gradient(135deg, #eef2ff 0%, #ffffff 100%);
    font-family: "Poppins", sans-serif;
}

.section-title {
    font-size: 30px;
    font-weight: 700;
    color: #0d6efd;
    text-align: center;
}

.hotel-card {
    border-radius: 16px;
    background: #ffffffcc;
    backdrop-filter: blur(8px);
    border: 1px solid rgba(0,0,0,0.05);
    transition: all 0.3s ease-in-out;
    overflow: hidden;
}

.hotel-card:hover {
    transform: translateY(-6px);
    box-shadow: 0px 12px 28px rgba(0,0,0,0.12);
}

.hotel-card img {
    height: 220px;
    object-fit: cover;
}

.hotel-link {
    text-decoration: none;
    color: inherit;
}

.price-text {
    font-size: 20px;
    font-weight: 700;
    color: #009e2a;
}

.badge-star {
    background: #ffca2c !important;
    font-size: 13px;
}

.carousel-indicators [data-bs-target] {
  background-color: #fff;
  width: 6px;
  height: 6px;
  border-radius: 50%;
}
</style>
</head>

<body>

<div class="container my-5">

    <h2 class="section-title">3 Star Hotel Options</h2>
    <p class="text-center text-muted mb-4"><?php echo htmlspecialchars($package['title']); ?></p>

    <div class="row g-4">

        <?php if(mysqli_num_rows($hotels_sql) > 0): ?>
        <?php while($hotel = mysqli_fetch_assoc($hotels_sql)): ?>
        
        <div class="col-md-4 col-sm-6">
            <a href="hotel_detail.php?pid=<?php echo $pid; ?>&hid=<?php echo $hotel['id']; ?>" class="hotel-link">
                <div class="hotel-card shadow-sm h-100">

                    <?php $images = explode(",", $hotel['images']); ?>

                    <div id="carousel<?php echo $hotel['id']; ?>" class="carousel slide" data-bs-ride="carousel">
                        <div class="carousel-inner">
                            <?php foreach($images as $index => $img): ?>
                                <div class="carousel-item <?php echo $index == 0 ? 'active' : ''; ?>">
                                    <img src="Hotel/<?php echo trim($img); ?>" class="d-block w-100">
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="p-3">
                        <h5 class="fw-bold mb-1"><?php echo $hotel['name']; ?></h5>
                        <p class="text-muted small mb-1"><?php echo $hotel['location']; ?></p>
                        <span class="badge badge-star text-dark">⭐ <?php echo $hotel['rating']; ?> / 5</span>

                        <p class="price-text mt-2">₹<?php echo $hotel['price']; ?> <small class="text-muted">/ Night</small></p>
                    </div>

                </div>
            </a>
        </div>

        <?php endwhile; else: ?>
        <div class="alert alert-warning text-center">No 3 Star Hotels Available.</div>
        <?php endif; ?>

    </div>

    <div class="text-center mt-5">
        <a href="book.php?pid=<?php echo $pid; ?>" class="btn btn-outline-primary px-4 py-2 rounded-pill">← Back to Hotel Categories</a>
    </div>

</div>

<script src="bootstrap-5.3.7-dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
