<?php
	session_start();
	include("../db.php");
	if (!isset($_SESSION['admin'])) {
		header("Location: login.php");
		exit;
	}

	$user_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total_users FROM users"))['total_users'];
	$booking_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total_bookings FROM bookings"))['total_bookings'];
	$package_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total_packages FROM packages"))['total_packages'];
?>

<html>
<head>
  <title>Dashboard</title>
  <link href="bootstrap-5.3.7-dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <style>
    body {
      margin: 0;
      font-family: 'Poppins', sans-serif;
      background-color: #87b4c4;
    }

    .sidebar {
      height: 100vh;
      width: 240px;
      position: fixed;
      top: 0;
      left: 0;
      background: rgba(20, 30, 48, 0.85);
      backdrop-filter: blur(12px);
      padding: 30px 15px;
      color: white;
      border-right: 2px solid rgba(255, 255, 255, 0.1);
      box-shadow: 4px 0 15px rgba(0,0,0,0.2);
    }

    .sidebar h4 {
      font-weight: bold;
      margin-bottom: 40px;
      text-align: center;
      font-family: 'Pacifico', cursive;
      letter-spacing: 3px;
    }

    .sidebar a {
      display: block;
      padding: 12px 20px;
      margin-bottom: 12px;
      color: #ffffff;
      border-radius: 10px;
      text-decoration: none;
      transition: 0.3s ease;
      font-weight: 500;
    }

    .sidebar a:hover {
      background: linear-gradient(90deg, #42a5f5, #478ed1);
      transform: translateX(5px);
    }

    .main {
      margin-left: 260px;
      padding: 40px;
    }

    .dashboard-box {
      background-color: green;
      padding: 40px 30px;
      border-radius: 15px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
      text-align: center;
      margin-bottom: 30px;
      color: white;
    }

    .stat-box {
      padding: 30px;
      border-radius: 15px;
      color: white;
      text-align: center;
      margin-bottom: 20px;
      transition: transform 0.3s, box-shadow 0.3s;
    }

    .stat-box:hover {
      transform: translateY(-5px);
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
    }

    .bg-users {
      background: linear-gradient(135deg, #4e73df, #224abe);
    }

    .bg-bookings {
      background: linear-gradient(135deg, #1cc88a, #13855c);
    }

    .bg-packages {
      background: linear-gradient(135deg, #f6c23e, #dda20a);
    }

    .stat-box i {
      font-size: 40px;
      margin-bottom: 10px;
    }

    .stat-box h3 {
      font-size: 36px;
      margin: 10px 0 5px;
      font-weight: bold;
    }

    .stat-box p {
      margin: 0;
      font-size: 16px;
    }

    /* Chart Box Styling */
    .chart-container {
      background: #fff;
      padding: 20px;
      margin-top: 40px;
      border-radius: 15px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }
  </style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
  <h4><i class="bi bi-speedometer2"></i> Admin Panel</h4>
  <a href="dashboard.php"><i class="bi bi-house-door-fill me-2"></i> Dashboard</a>
  <a href="create-package.php"><i class="bi bi-plus-circle-fill me-2"></i> Create Package</a>
  <a href="view_packages.php"><i class="bi bi-card-list me-2"></i> All Packages</a>
  <a href="manage-user.php"><i class="bi bi-people-fill me-2"></i> Manage Users</a>
  <a href="manage-booking.php"><i class="bi bi-journal-check me-2"></i> Manage Bookings</a>
  <a href="logout.php"><i class="bi bi-box-arrow-right me-2"></i> Logout</a>
</div>

<!-- Main Content -->
<div class="main">
  <div class="dashboard-box">
    <h2><i class="bi bi-person-workspace me-2"></i> Welcome to Admin Dashboard</h2>
    <p>You have full control to manage packages, users, and bookings from this panel.</p>
  </div>

  <div class="row">
    <div class="col-md-4">
      <div class="stat-box bg-users">
        <i class="bi bi-people-fill"></i>
        <h3><?= $user_count ?></h3>
        <p>Total Users</p>
      </div>
    </div>
    <div class="col-md-4">
      <div class="stat-box bg-bookings">
        <i class="bi bi-journal-check"></i>
        <h3><?= $booking_count ?></h3>
        <p>Total Bookings</p>
      </div>
    </div>
    <div class="col-md-4">
      <div class="stat-box bg-packages">
        <i class="bi bi-box-seam"></i>
        <h3><?= $package_count ?></h3>
        <p>Total Packages</p>
      </div>
    </div>
  </div>

  <!-- Chart Section -->
  <div class="chart-container">
    <h4 class="text-center mb-4">Statistics Overview</h4>
    <canvas id="statsChart" height="100"></canvas>
  </div>
</div>

<!-- Chart.js Script -->
<script>
  const ctx = document.getElementById('statsChart').getContext('2d');
  new Chart(ctx, {
    type: 'bar', // change to 'pie' for a pie chart
    data: {
      labels: ['Users', 'Bookings', 'Packages'],
      datasets: [{
        label: 'Count',
        data: [<?= $user_count ?>, <?= $booking_count ?>, <?= $package_count ?>],
        backgroundColor: ['#4e73df', '#1cc88a', '#f6c23e']
      }]
    },
    options: {
      responsive: true,
      plugins: {
        legend: { display: false }
      },
      scales: {
        y: { beginAtZero: true }
      }
    }
  });
</script>

</body>
</html>
