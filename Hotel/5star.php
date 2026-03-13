<?php
session_start();
include("db.php");

if (!isset($_GET['pid'])) {
    die("Invalid Package ID");
}

$pid = intval($_GET['pid']);
$package_sql = mysqli_query($conn, "SELECT title FROM packages WHERE id=$pid");
$package = mysqli_fetch_assoc($package_sql);

if (!$package) {
    die("Package not found");
}

// ✅ Fetch only 3 Star hotels belonging to this package
$hotels_sql = mysqli_query($conn, "SELECT * FROM hotels WHERE package_id = $pid AND category='3 Star' ORDER BY price ASC");

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>5 Star Hotels for <?php echo htmlspecialchars($package['title']); ?></title>
    <link href="bootstrap-5.3.7-dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
      .hotel-card img {
          height: 200px;
          object-fit: cover;
          border-radius: 10px 10px 0 0;
      }
      .hotel-card {
        transition: transform 0.2s;
        border: none;
        border-radius: 10px;
      }
      .hotel-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1);
      }
      .hotel-link {
        text-decoration: none;
        color: inherit;
      }
    </style>
</head>
<body style="background-color: #f8f9fa;">
<div class="container my-5">
    <h2 class="text-center mb-4 text-primary fw-bold">🏨 5 Star Hotels for: <?php echo htmlspecialchars($package['title']); ?></h2>
    <hr class="mb-5">
    
    <div class="row g-4">
    <?php if(mysqli_num_rows($hotels_sql) > 0): ?>
        <?php while($hotel = mysqli_fetch_assoc($hotels_sql)): ?>
            <div class="col-md-4">
                <a href="hotel_detail.php?pid=<?php echo $pid; ?>&hid=<?php echo $hotel['id']; ?>" class="hotel-link">
                    <div class="card shadow-sm hotel-card h-100">
                        <?php 
                        $images = explode(",", $hotel['images']);
                        ?>
<div id="carousel<?php echo $hotel['id']; ?>" class="carousel slide" data-bs-ride="carousel" data-bs-interval="3000">
  <div class="carousel-inner">
    <?php foreach($images as $index => $img):
        $active = ($index == 0) ? 'active' : '';
    ?>
      <div class="carousel-item <?php echo $active; ?>">
        <img src="Hotel/<?php echo trim($img); ?>" class="d-block w-100" style="height:200px; object-fit:cover; border-radius:10px 10px 0 0;">
      </div>
    <?php endforeach; ?>
  </div>
  <button class="carousel-control-prev" type="button" data-bs-target="#carousel<?php echo $hotel['id']; ?>" data-bs-slide="prev">
    <span class="carousel-control-prev-icon"></span>
  </button>
  <button class="carousel-control-next" type="button" data-bs-target="#carousel<?php echo $hotel['id']; ?>" data-bs-slide="next">
    <span class="carousel-control-next-icon"></span>
  </button>
</div>

                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title text-dark fw-bold"><?php echo htmlspecialchars($hotel['name']); ?></h5>
                            <p class="card-text text-muted small"><i class="bi bi-geo-alt-fill"></i> <?php echo htmlspecialchars($hotel['location']); ?></p>
                            <p class="card-text">
                                <span class="badge bg-warning text-dark">⭐ <?php echo htmlspecialchars($hotel['rating']); ?> / 5</span>
                            </p>
                            <p class="card-text fw-bold text-success mt-auto">₹<?php echo htmlspecialchars($hotel['price']); ?> <small class="text-muted">per night</small></p>
                        </div>
                    </div>
                </a>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <div class="alert alert-warning text-center w-100">No 5 Star Hotels available for this package yet.</div>
    <?php endif; ?>
    </div>
    
    <div class="text-center mt-5">
        <a href="book.php?pid=<?php echo $pid; ?>" class="btn btn-secondary rounded-pill px-4">← Change Hotel Star</a>
    </div>

</div>
<script src="bootstrap-5.3.7-dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
