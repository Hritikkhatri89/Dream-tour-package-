<?php
session_start();
include("../db.php");

// Admin login check
if (!isset($_SESSION['admin_email'])) {
    header("Location: ../login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>View Hotels | Admin Panel</title>
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

     .hotel-card {
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
     .hotel-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 12px 25px rgba(0,0,0,0.08);
     }
     .hotel-img {
        width: 100%;
        height: 180px;
        object-fit: cover;
     }
     .hotel-content { padding: 20px; flex-grow: 1; }
     .hotel-content h5 { font-weight: 700; color: #1e293b; font-size: 1.1rem; margin-bottom: 12px; }
     .category { font-size: 0.8rem; color: #0fb9b1; font-weight: 500; text-transform: uppercase; margin-bottom: 15px; display: block; }
     
     .info-item { font-size: 0.85rem; color: #64748b; margin-bottom: 8px; display: flex; align-items: flex-start; }
     .info-item i { color: #0fb9b1; margin-right: 10px; font-size: 1rem; }
     .price-tag { font-size: 1.2rem; font-weight: 700; color: #0fb9b1; margin-top: 15px; display: block; }
     .price-tag small { font-size: 0.8rem; color: #64748b; font-weight: 400; }
     
     .card-actions {
        padding: 15px 20px;
        background: #f8fafc;
        display: flex;
        gap: 10px;
        border-top: 1px solid #f1f5f9;
     }
     .btn-action {
        flex: 1;
        padding: 10px;
        border-radius: 25px;
        font-size: 0.85rem;
        font-weight: 500;
        text-align: center;
        text-decoration: none;
        transition: all 0.3s ease;
     }
     .btn-edit { background: white; color: #f59e0b; border: 1px solid #fed7aa; }
     .btn-edit:hover { background: #fff7ed; border-color: #f59e0b; }
     .btn-delete { background: white; color: #ef4444; border: 1px solid #fee2e2; }
     .btn-delete:hover { background: #fef2f2; border-color: #ef4444; }

     .btn-create {
        background: #0fb9b1;
        color: white;
        border: none;
        padding: 10px 25px;
        border-radius: 12px;
        font-weight: 500;
        box-shadow: 0 4px 12px rgba(15, 185, 177, 0.2);
        text-decoration: none;
     }
     .btn-create:hover { background: #0da59e; transform: translateY(-2px); color: white; }
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
            <h2 class="page-title">All Registered Hotels</h2>
            <p class="text-muted small">Manage hotel partners and their details</p>
        </div>
        <a href="add_hotel.php" class="btn-create shadow-sm">+ Add New Hotel</a>
    </div>

    <div class="row g-4">
    <?php
    $sql = mysqli_query($conn, "SELECT * FROM hotels ORDER BY id DESC");
    if (mysqli_num_rows($sql) > 0) {
        while ($row = mysqli_fetch_assoc($sql)) {
            $images = explode(",", $row['images']);
            $firstImage = "../Hotel/" . trim($images[0]);
            $pid = $row['package_id'];
            $pkg = mysqli_fetch_assoc(mysqli_query($conn, "SELECT title FROM packages WHERE id='$pid'"));
    ?>
        <div class="col-xl-3 col-lg-4 col-md-6">
            <div class="hotel-card border-0">
                <img src="<?= $firstImage ?>" class="hotel-img" alt="Hotel Image">
                
                <div class="hotel-content">
                    <span class="category"><?= htmlspecialchars($row['category']) ?> Star Hotel</span>
                    <h5><?= htmlspecialchars($row['name']) ?></h5>
                    
                    <div class="info-item mt-3">
                        <i class="bi bi-geo-alt-fill"></i>
                        <span class="text-truncate"><?= htmlspecialchars($row['location']) ?></span>
                    </div>
                    
                    <div class="info-item">
                        <i class="bi bi-box-seam-fill"></i>
                        <span class="text-truncate"><strong>Pkg:</strong> <?= htmlspecialchars($pkg['title'] ?? "Universal") ?></span>
                    </div>
                    
                    <span class="price-tag mt-3">₹<?= number_format($row['price']) ?> <small>/ night</small></span>
                </div>

                <div class="card-actions bg-white border-0 px-3 pb-3 pt-0">
                    <a href="edit_hotel.php?id=<?= $row['id'] ?>" class="btn-action btn-edit">Edit</a>
                    <a href="delete_hotel.php?id=<?= $row['id'] ?>" class="btn-action btn-delete" 
                       onclick="return confirm('Delete this hotel?');">Delete</a>
                </div>
            </div>
        </div>
    <?php
        }
    } else {
        echo "<div class='col-12 py-5 text-center text-muted'><p>No hotels found. Add your first hotel!</p></div>";
    }
    ?>
    </div>
</div>

<script src="../bootstrap-5.3.7-dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
