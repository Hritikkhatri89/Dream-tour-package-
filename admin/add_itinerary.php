<?php
session_start();
include("../db.php");

// Admin login check
if (!isset($_SESSION['admin_email'])) {
    header("Location: ../login.php");
    exit;
}

// Handle Add Itinerary
$message = "";
if(isset($_POST['add_submit'])){
    $package_id = intval($_POST['package_id']);
    $day_or_night = $_POST['day_or_night'];
    $plan = $_POST['plan'];

    // Normalize new lines
    $plan = str_replace("\r\n", "\n", $plan);

    $stmt = $conn->prepare("INSERT INTO itineraries (package_id, day_or_night, plan) 
                            VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $package_id, $day_or_night, $plan);

    if($stmt->execute()){
        $message = "Itinerary added successfully!";
    } else {
        $message = "Error: ".$stmt->error;
    }
    $stmt->close();
}

// Handle Delete
if(isset($_GET['delete'])){
    $id = intval($_GET['delete']);
    mysqli_query($conn, "DELETE FROM itineraries WHERE id=$id");
    header("Location: itinerary.php");
    exit;
}

// Fetch Packages
$packages = mysqli_query($conn, "SELECT id, title FROM packages ORDER BY title ASC");

// Fetch all itineraries
$itineraries = mysqli_query($conn, 
    "SELECT i.id, p.title AS package_title, i.day_or_night, i.plan 
     FROM itineraries i
     JOIN packages p ON i.package_id = p.id
     ORDER BY i.package_id, i.id ASC"
);
?>

<html>
<head>
    <title>Manage Itineraries - Admin</title>
    <link href="../bootstrap-5.3.7-dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
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
     
     .form-container {
        background: white;
        padding: 40px;
        border-radius: 24px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.03);
        border: 1px solid #e2e8f0;
     }
     
     .form-title { font-weight: 700; color: #1e293b; margin-bottom: 35px; }
     
     .form-label { font-weight: 500; color: #64748b; font-size: 0.85rem; margin-bottom: 8px; text-transform: uppercase; letter-spacing: 1px; }

     .form-control, .form-select {
        padding: 12px 18px;
        border-radius: 12px;
        border: 1px solid #e2e8f0;
        background: #f8fafc;
        color: #1e293b;
        font-size: 0.85rem;
        transition: all 0.3s ease;
     }
     .form-control:focus, .form-select:focus {
        background: #fff;
        border-color: #0fb9b1;
        box-shadow: 0 0 0 4px rgba(15, 185, 177, 0.1);
     }
     
     .btn-submit {
        background: #0fb9b1;
        color: white;
        border: none;
        padding: 14px 40px;
        border-radius: 12px;
        font-weight: 500;
        font-size: 1rem;
        transition: all 0.3s ease;
        box-shadow: 0 4px 12px rgba(15, 185, 177, 0.2);
        width: 100%;
        margin-top: 20px;
     }
     .btn-submit:hover { background: #0da59e; transform: translateY(-2px); box-shadow: 0 6px 15px rgba(15, 185, 177, 0.3); }

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

     .alert { border-radius: 12px; font-weight: 500; padding: 15px 20px; margin-bottom: 30px; }
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
    <a href="view_itinerary.php" class="btn-back">
        <i class="bi bi-arrow-left me-2"></i> Back to Itineraries
    </a>

    <div class="form-container col-lg-8 mx-auto">
        <h2 class="form-title text-center"><i class="bi bi-map-fill me-2 text-primary"></i> Add Day-wise Plan</h2>

        <?php if($message) echo "<div class='alert alert-success'><i class='bi bi-check-circle-fill me-2'></i> $message</div>"; ?>

        <form method="POST">
            <div class="mb-4">
                <label class="form-label">Select Package</label>
                <select name="package_id" class="form-select" required>
                    <option value="">-- Choose Package --</option>
                    <?php while($pkg = mysqli_fetch_assoc($packages)){ ?>
                        <option value="<?= $pkg['id'] ?>"><?= htmlspecialchars($pkg['title']) ?></option>
                    <?php } ?>
                </select>
                <div class="form-text mt-2 small text-muted">Select the tour package this itinerary belongs to.</div>
            </div>

            <div class="mb-4">
                <label class="form-label">Day & Destination</label>
                <input type="text" name="day_or_night" class="form-control" placeholder="e.g. Day 1: Arrival & Local Sightseeing" required>
            </div>

            <div class="mb-4">
                <label class="form-label">Detailed Plan (Markdown-ish)</label>
                <textarea name="plan" class="form-control" rows="8" placeholder="Enter each point on a new line:&#10;Beach: Sunset visit&#10;Lunch: Authentic Goan food&#10;Evening: Cruise" required></textarea>
                <div class="form-text mt-2 small text-muted">Use a new line for each activity point.</div>
            </div>

            <button type="submit" name="add_submit" class="btn-submit">
                <i class="bi bi-plus-circle me-2"></i> Save Itinerary Day
            </button>
        </form>
    </div>
</div>

    

<script src="../bootstrap-5.3.7-dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>



