<?php
include("../db.php");

if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    mysqli_query($conn, "DELETE FROM bookings WHERE package_id = $id");
    if (mysqli_query($conn, "DELETE FROM packages WHERE id = $id")) {
        echo "<script>alert('Package deleted successfully'); window.location='view_packages.php';</script>";
        exit;
    } else {
        echo "<script>alert('Error deleting package'); window.location='view_packages.php';</script>";
        exit;
    }
}
$result = mysqli_query($conn, "SELECT * FROM packages");
?>
<html>
<head>
  <title>View Packages</title>
  <link href="../bootstrap-5.3.7-dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body { background-color: #87b4c4; }
    .card {
      border-radius: 1rem;
      background-color: #e1eef2;
      box-shadow: 0 2px 6px rgba(0,0,0,0.1);
      padding: 0.8rem; /* chhota padding */
      height: 300px; /* fixed smaller height */
      overflow: hidden; /* extra text hide ho jaye */
    }
    .package-img {
      width: 100%;
      height: 100px; /* chhoti image */
      object-fit: cover;
      border-radius: 0.5rem;
    }
    h5 {
      font-size: 1rem; /* chhota title */
      margin-bottom: 0.3rem;
    }
    label {
      font-weight: 600;
      margin-right: 5px;
      font-size: 0.85rem; /* chhota label */
    }
    p {
      font-size: 0.85rem; /* chhota text */
      margin-bottom: 0.25rem;
    }
    .btn-sm {
      padding: 0.25rem 0.5rem;
      font-size: 0.75rem;
    }
  </style>
</head>
<body>

<div class="container py-5">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h2>All Tour Packages</h2>
    <a href="create-package.php" class="btn btn-success">+ Create Package</a>
  </div>

  <div class="row">
    <?php if ($result && mysqli_num_rows($result) > 0): ?>
      <?php while ($row = mysqli_fetch_assoc($result)): ?>
        <div class="col-md-3 col-sm-6 mb-4"> 
          <div class="card h-100">
            <?php
              $imgPath = "../img/" . $row['image'];
              if (!empty($row['image']) && file_exists($imgPath)) {
                  echo "<img src='" . htmlspecialchars($imgPath) . "' class='package-img mb-2' alt='Package Image'>";
              } else {
                  echo "<div class='bg-secondary text-white text-center package-img d-flex align-items-center justify-content-center mb-2'>No Image</div>";
              }
            ?>
            <h5><?= htmlspecialchars($row['title']); ?></h5>
            <p><label>Type:</label> <?= htmlspecialchars($row['type']); ?></p>
            <p><label>Duration:</label> <?= htmlspecialchars($row['duration']); ?></p>
            <p><label>Price:</label> ₹<?= htmlspecialchars($row['price']); ?></p>
            <p><label>Highlights:</label> <?= htmlspecialchars($row['highlights']); ?></p>
            <p><label>Description:</label><br><?= nl2br(htmlspecialchars($row['description'])); ?></p>

            <a href="create-package.php?edit=<?= $row['id']; ?>" class="btn btn-warning btn-sm">Edit</a>
            <a href="view_packages.php?delete=<?= $row['id']; ?>" onclick="return confirm('Are you sure?');" class="btn btn-danger btn-sm">Delete</a>
          </div>
        </div>
      <?php endwhile; ?>
    <?php else: ?>
      <p class="text-center">No packages found.</p>
    <?php endif; ?>
  </div>
</div>

</body>
</html>
