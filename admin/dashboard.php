<?php
session_start();
include("../db.php");

// Admin login check
if (!isset($_SESSION['admin_email'])) {
    header("Location: ../login.php");
    exit();
}

// Function to safely count rows
function getCount($conn, $table) {
    $query = mysqli_query($conn, "SELECT COUNT(*) AS total FROM $table");
    if (!$query) {
        die("SQL Error in table '$table': " . mysqli_error($conn));
    }
    $result = mysqli_fetch_assoc($query);
    return $result['total'];
}

// Counts
$user_count = getCount($conn, "users");
$booking_count = getCount($conn, "bookings");
$package_count = getCount($conn, "packages");
$hotel_count = getCount($conn, "hotels");

// Income
$q_income = mysqli_query($conn, "SELECT SUM(price) as total FROM bookings WHERE status != 'Cancelled'");
$r_income = mysqli_fetch_assoc($q_income);
$total_income = $r_income['total'] ? round($r_income['total']) : 0;

$q_year = mysqli_query($conn, "SELECT SUM(price) as total FROM bookings WHERE YEAR(booked_on) = YEAR(CURDATE()) AND status != 'Cancelled'");
$r_year = mysqli_fetch_assoc($q_year);
$year_income = $r_year['total'] ? round($r_year['total']) : 0;
?>

<!DOCTYPE html>
<html>
<head>
  <title>Admin Dashboard</title>
  <link href="../bootstrap-5.3.7-dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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

     /* Main content */
     .main { margin-left: 260px; padding: 40px 50px; }

     /* Dashboard welcome box */
     .dashboard-box {
        background: linear-gradient(135deg, #0F172A, #1E3A5F);
        padding: 40px;
        border-radius: 20px;
        box-shadow: 0 10px 30px rgba(15, 23, 42, 0.15);
        text-align: left;
        margin-bottom: 40px;
        color: white;
     }
     .dashboard-box h2 { font-weight: 700; margin-bottom: 10px; }
     .dashboard-box p { color: rgba(255,255,255,0.7); }

     /* Stat boxes */
     .stat-box {
        padding: 25px;
        border-radius: 18px;
        background: white;
        text-align: left;
        margin-bottom: 25px;
        transition: all 0.3s ease;
        border: 1px solid #e2e8f0;
        box-shadow: 0 4px 6px rgba(0,0,0,0.02);
        display: flex;
        align-items: center;
     }
     .stat-box:hover { 
        transform: translateY(-5px); 
        box-shadow: 0 12px 20px rgba(0,0,0,0.05);
     }
     .icon-circle {
        width: 55px;
        height: 55px;
        border-radius: 15px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 20px;
        font-size: 1.5rem;
     }
     .bg-users .icon-circle { background: rgba(15, 185, 177, 0.1); color: #0fb9b1; }
     .bg-bookings .icon-circle { background: rgba(241, 165, 1, 0.1); color: #f1a501; }
     .bg-packages .icon-circle { background: rgba(99, 102, 241, 0.1); color: #6366f1; }
     .bg-hotels .icon-circle { background: rgba(236, 72, 153, 0.1); color: #ec4899; }
     .bg-income .icon-circle { background: rgba(16, 185, 129, 0.1); color: #10b981; }
     .bg-income-year .icon-circle { background: rgba(59, 130, 246, 0.1); color: #3b82f6; }

     .stat-content h3 { font-size: 1.8rem; margin: 0; font-weight: 700; color: #1e293b; }
     .stat-content p { margin: 0; font-size: 0.85rem; color: #64748b; font-weight: 500; text-transform: uppercase; }

     /* Chart container */
     .chart-container {
        background: white;
        padding: 35px;
        margin-top: 20px;
        border-radius: 20px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.03);
        border: 1px solid #e2e8f0;
     }
     .chart-container h4 { color: #1e293b; font-weight: 700; margin-bottom: 30px; }
  </style>
</head>
<body>
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
  <div class="dashboard-box">
    <h2><i class="bi bi-person-workspace me-2"></i> Welcome back, Admin</h2>
    <p>Manage your luxury travel empire efficiently from here.</p>
  </div>

  <div class="row g-4">
    <div class="col-xl-4 col-lg-6">
      <div class="stat-box bg-users h-100 mb-0">
        <div class="icon-circle"><i class="bi bi-people-fill"></i></div>
        <div class="stat-content">
          <h3><?= $user_count ?></h3>
          <p>Total Users</p>
        </div>
      </div>
    </div>
    
    <div class="col-xl-4 col-lg-6">
      <div class="stat-box bg-bookings h-100 mb-0">
        <div class="icon-circle"><i class="bi bi-journal-check"></i></div>
        <div class="stat-content">
          <h3><?= $booking_count ?></h3>
          <p>Total Bookings</p>
        </div>
      </div>
    </div>
    
    <div class="col-xl-4 col-lg-6">
      <div class="stat-box bg-packages h-100 mb-0">
        <div class="icon-circle"><i class="bi bi-box-seam-fill"></i></div>
        <div class="stat-content">
          <h3><?= $package_count ?></h3>
          <p>Total Packages</p>
        </div>
      </div>
    </div>
    
    <div class="col-xl-4 col-lg-6">
      <div class="stat-box bg-hotels h-100 mb-0">
        <div class="icon-circle"><i class="bi bi-buildings-fill"></i></div>
        <div class="stat-content">
          <h3><?= $hotel_count ?></h3>
          <p>Total Hotels</p>
        </div>
      </div>
    </div>
    
    <div class="col-xl-4 col-lg-6">
      <div class="stat-box bg-income h-100 mb-0">
        <div class="icon-circle"><i class="bi bi-cash-stack"></i></div>
        <div class="stat-content">
          <h3>₹<?= number_format($total_income) ?></h3>
          <p>Total Income</p>
        </div>
      </div>
    </div>
    
    <div class="col-xl-4 col-lg-6">
      <div class="stat-box bg-income-year h-100 mb-0">
        <div class="icon-circle"><i class="bi bi-graph-up-arrow"></i></div>
        <div class="stat-content">
          <h3>₹<?= number_format($year_income) ?></h3>
          <p>Income This Year</p>
        </div>
      </div>
    </div>
  </div>

  <div class="chart-container">
    <h4 class="text-center">Analytics Overview</h4>
    <canvas id="statsChart" height="100"></canvas>
  </div>
</div>

<script>
const ctx = document.getElementById('statsChart').getContext('2d');
new Chart(ctx, {
    type: 'bar',
    data: {
        labels: ['Users', 'Bookings', 'Packages', 'Hotels'],
        datasets: [{
            label: 'Count',
            data: [<?= $user_count ?>, <?= $booking_count ?>, <?= $package_count ?>, <?= $hotel_count ?>],
            backgroundColor: ['#0fb9b1', '#f1a501', '#6366f1', '#ec4899']
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: { y: { beginAtZero: true } }
    }
});
</script>

</body>
</html>
