<?php
include("../db.php");

// 🔹 Delete package
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];

    // delete related bookings
    mysqli_query($conn, "DELETE FROM bookings WHERE package_id = $id");

    // delete package itself
    if (mysqli_query($conn, "DELETE FROM packages WHERE id = $id")) {
        echo "<script>alert('Package deleted successfully'); window.location='view_packages.php';</script>";
        exit;
    } else {
        echo "<script>alert('Error deleting package'); window.location='view_packages.php';</script>";
        exit;
    }
}

// 🔹 Fetch all packages
$result = mysqli_query($conn, "SELECT * FROM packages");
?>

<!DOCTYPE html>
<html>
<head>
  <title>View Packages</title>
  <link href="../bootstrap-5.3.7-dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <style>
     body { 
        font-family: 'Outfit', 'Poppins', sans-serif; 
        background: #f4f7fa; 
        margin: 0; 
        color: #1e293b;
        overflow-x: hidden;
     }

     /* Sidebar */

     .sidebar {
        height: 100vh;
        width: 260px;
        position: fixed;
        top: 0;
        left: 0;
        background: #0F172A; 
        padding: 20px 15px;
        color: white;
        box-shadow: 4px 0 20px rgba(0,0,0,0.1);
        z-index: 1000;
        overflow-y: auto;
     }
     .sidebar h4 {
        font-weight: 700;
        margin-bottom: 25px;
        text-align: center;
        text-transform: uppercase;
        letter-spacing: 2px;
        color: #0fb9b1;
        font-size: 1.4rem;
        font-family: 'Outfit';
     }
     .sidebar a {
        font-family: 'Outfit', sans-serif;
        display: flex;
        align-items: center;
        padding: 10px 15px;
        margin-bottom: 10px;
        color: #94a3b8;
        border-radius: 12px;
        text-decoration: none;
        transition: all 0.3s ease;
        font-weight: 500;
        font-size: 0.85rem;
     }
     .sidebar a i { margin-right: 15px; font-size: 1.2rem; }
     .sidebar a:hover, .sidebar a.active {
        background: rgba(255, 255, 255, 0.05);
        color: white;
        transform: translateX(5px);
     }
     .sidebar a.active { background: #0fb9b1; color: white; }
     .sidebar a.logout { margin-top: 30px; color: #ef4444; }

     .main { margin-left: 260px; padding: 40px 50px; }
     .page-title { font-weight: 700; color: #1e293b; margin-bottom: 5px; }
     
     .top-bar { 
        display: flex; 
        justify-content: space-between; 
        align-items: flex-end; 
        margin-bottom: 35px; 
     }

     .package-card {
        background: white;
        border-radius: 20px;
        overflow: hidden;
        border: 1px solid #e2e8f0;
        transition: all 0.3s ease;
        height: 100%;
        display: flex;
        flex-direction: column;
        box-shadow: 0 4px 15px rgba(0,0,0,0.02);
     }
     .package-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 12px 25px rgba(0,0,0,0.08);
     }
     .package-img {
        width: 100%;
        height: 180px;
        object-fit: cover;
     }
     .package-content { padding: 20px; flex-grow: 1; }
     .package-content h5 { font-weight: 700; color: #1e293b; font-size: 1.1rem; margin-bottom: 12px; }
     .pkg-info { font-size: 0.85rem; color: #64748b; margin-bottom: 6px; display: flex; align-items: center; }
     .pkg-info i { color: #0fb9b1; margin-right: 8px; font-size: 0.9rem; }
     .price-tag { font-size: 1.2rem; font-weight: 700; color: #0fb9b1; margin-top: 15px; display: block; }
     
     .card-actions {
        padding: 5px 20px;
        background: #f8fafc;
        display: flex;
        gap: 10px;
        border-top: 1px solid #f1f5f9;
     }
     .btn-edit { background: #fff; color: #f59e0b; border: 1px solid #fed7aa; }
     .btn-edit:hover { background: #fff7ed; border-color: #f59e0b; }
     .btn-delete { background: #fff; color: #ef4444; border: 1px solid #fee2e2; }
     .btn-delete:hover { background: #fef2f2; border-color: #ef4444; }
     
     .btn-create {
        background: #0fb9b1;
        color: white;
        border: none;
        padding: 10px 20px;
        border-radius: 12px;
        font-weight: 500;
        box-shadow: 0 4px 12px rgba(15, 185, 177, 0.2);
     }
     .btn-create:hover { background: #0da59e; transform: translateY(-2px); }

     .btn-back {
        background: #fff;
        color: #64748b;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 8px 20px;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        margin-bottom: 25px;
        font-weight: 500;
        transition: all 0.3s ease;
     }
     .btn-back:hover { background: #f8fafc; color: #1e293b; transform: translateY(-2px); }
  </style>
</head>
<body>




<!-- Sidebar -->
<div class="sidebar">
  <h4>Admin Panel</h4>
  <a href="dashboard.php" class="<?= (basename($_SERVER['PHP_SELF']) == 'dashboard.php') ? 'active' : '' ?>"><i class="bi bi-grid-fill"></i> Dashboard</a>
  <a href="create-package.php" class="<?= (basename($_SERVER['PHP_SELF']) == 'create-package.php') ? 'active' : '' ?>"><i class="bi bi-plus-square-fill"></i> Create Package</a>
  <a href="view_packages.php" class="<?= (in_array(basename($_SERVER['PHP_SELF']), ['view_packages.php', 'edit_package.php'])) ? 'active' : '' ?>"><i class="bi bi-stack"></i> All Packages</a>
  <a href="add_itinerary.php" class="<?= (basename($_SERVER['PHP_SELF']) == 'add_itinerary.php') ? 'active' : '' ?>"><i class="bi bi-calendar-plus"></i> Add Itinerary</a>
  <a href="view_itinerary.php" class="<?= (in_array(basename($_SERVER['PHP_SELF']), ['view_itinerary.php', 'edit_itinerary.php'])) ? 'active' : '' ?>"><i class="bi bi-calendar2-week"></i> View Itineraries</a>
  <a href="add_hotel.php" class="<?= (basename($_SERVER['PHP_SELF']) == 'add_hotel.php') ? 'active' : '' ?>"><i class="bi bi-building-add"></i> Add Hotel</a>
  <a href="view_hotel.php" class="<?= (in_array(basename($_SERVER['PHP_SELF']), ['view_hotel.php', 'edit_hotel.php'])) ? 'active' : '' ?>"><i class="bi bi-buildings"></i> View Hotels</a>
  <a href="manage-user.php" class="<?= (basename($_SERVER['PHP_SELF']) == 'manage-user.php') ? 'active' : '' ?>"><i class="bi bi-people-fill"></i> Manage Users</a>
  <a href="manage-booking.php" class="<?= (basename($_SERVER['PHP_SELF']) == 'manage-booking.php') ? 'active' : '' ?>"><i class="bi bi-journal-check"></i> Manage Bookings</a>
  <a href="view-contact.php" class="<?= (basename($_SERVER['PHP_SELF']) == 'view-contact.php') ? 'active' : '' ?>"><i class="bi bi-envelope-fill"></i> Messages</a>
  <a href="logout.php" class="logout"><i class="bi bi-box-arrow-right"></i> Logout</a>
</div>

<!-- Main Content -->
<div class="main">
  <div class="top-bar">
    <div>
      <h2 class="page-title">All Tour Packages</h2>
      <p class="text-muted small">Manage your current travel offerings</p>
    </div>
    <a href="create-package.php" class="btn-create text-decoration-none shadow-sm">+ Create New Package</a>
  </div>

  <div class="row">
    <?php if ($result && mysqli_num_rows($result) > 0): ?>
      <?php while ($row = mysqli_fetch_assoc($result)): ?>
        <div class="col-xl-3 col-lg-4 col-md-6 mb-4">
          <div class="package-card border-0">
            <?php
              $images = explode(',', $row['image']);
              $firstImage = trim($images[0]);
              $imgPath = "../img/" . $firstImage;

              if (!empty($firstImage) && file_exists($imgPath)) {
                  echo "<img src='" . htmlspecialchars($imgPath) . "' class='package-img' alt='Package Image'>";
              } else {
                  echo "<div class='bg-light text-muted text-center package-img d-flex align-items-center justify-content-center'>No Image</div>";
              }
            ?>

            <div class="package-content">
              <h5><?= htmlspecialchars($row['title']); ?></h5>
              <div class="pkg-info"><i class="bi bi-tag-fill"></i> <?= htmlspecialchars($row['type']); ?></div>
              <div class="pkg-info"><i class="bi bi-clock-fill"></i> <?= htmlspecialchars($row['duration']); ?></div>
              <span class="price-tag">₹<?= number_format($row['price']); ?></span>
            </div>

            <div class="card-actions px-3 pt-2 bg-white small">
              <a href="add_itinerary.php?package_id=<?= $row['id']; ?>" class="btn flex-grow-1 btn-outline-primary rounded-pill py-1 small">
                <i class="bi bi-map me-1"></i> Plan
              </a>
              <a href="add_hotel.php?package_id=<?= $row['id']; ?>" class="btn flex-grow-1 btn-outline-secondary rounded-pill py-1 small">
                <i class="bi bi-building me-1"></i> Hotel
              </a>
            </div>

            <div class="card-actions px-3 pb-3 pt-2 border-0 bg-white">
              <a href="create-package.php?edit=<?= $row['id']; ?>" class="btn flex-grow-1 btn-edit rounded-pill text-decoration-none py-2">
                <i class="bi bi-pencil me-1"></i>
              </a>
              <a href="view_packages.php?delete=<?= $row['id']; ?>" onclick="return confirm('Delete this package?');" class="btn flex-grow-1 btn-delete rounded-pill text-decoration-none py-2">
                <i class="bi bi-trash me-1"></i>
              </a>
            </div>
          </div>
        </div>
      <?php endwhile; ?>
    <?php else: ?>
      <div class="col-12 text-center py-5">
        <p class="text-muted">No packages found. Start by creating one!</p>
      </div>
    <?php endif; ?>
  </div>
</div>

</body>
</html>
