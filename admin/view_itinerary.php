<?php
session_start();
include("../db.php");

// Admin login check
if (!isset($_SESSION['admin_email'])) {
    header("Location: ../login.php");
    exit;
}

// Delete functionality
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    mysqli_query($conn, "DELETE FROM itineraries WHERE id = $delete_id");
    echo "<script>alert('Itinerary deleted successfully!'); window.location='view_itinerary.php';</script>";
    exit;
}

// Fetch itineraries with package info
$itineraries = mysqli_query($conn, "
    SELECT i.id, p.title AS package_title, i.day_or_night, i.plan, i.package_id
    FROM itineraries i
    JOIN packages p ON i.package_id = p.id
    ORDER BY i.package_id, i.id ASC
");
?>

<html>
<head>

    <title>View Itineraries - Admin</title>
    <link href="../bootstrap-5.3.7-dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
<link href ="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
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
     .page-title { font-weight: 700; color: #1e293b; margin-bottom: 5px; }

     .top-bar { 
        display: flex; 
        justify-content: space-between; 
        align-items: flex-end; 
        margin-bottom: 35px; 
     }

     .table-container {
        background: white;
        border-radius: 20px;
        overflow: hidden;
        border: 1px solid #e2e8f0;
        box-shadow: 0 4px 15px rgba(0,0,0,0.02);
     }
     .table { margin-bottom: 0; }
     .table thead th {
        background: #f8fafc;
        color: #64748b;
        font-weight: 500;
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 1px;
        padding: 18px 25px;
        border-bottom: 1px solid #e2e8f0;
     }
     .table tbody td {
        padding: 20px 25px;
        color: #1e293b;
        font-size: 0.85rem;
        vertical-align: middle;
        border-bottom: 1px solid #f1f5f9;
     }
     .package-badge {
        background: rgba(15, 185, 177, 0.1);
        color: #0fb9b1;
        padding: 6px 12px;
        border-radius: 8px;
        font-weight: 500;
        font-size: 0.85rem;
     }
     .day-text { font-weight: 700; color: #1e293b; }
     .plan-text { color: #64748b; font-size: 0.9rem; line-height: 1.6; }

     .action-btns { display: flex; gap: 10px; }
     .btn-action {
        width: 35px;
        height: 35px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 10px;
        text-decoration: none;
        transition: all 0.3s ease;
        font-size: 1.1rem;
     }
     .btn-edit { background: #fffbeb; color: #f59e0b; border: 1px solid #fef3c7; }
     .btn-edit:hover { background: #f59e0b; color: white; }
     .btn-delete { background: #fef2f2; color: #ef4444; border: 1px solid #fee2e2; }
     .btn-delete:hover { background: #ef4444; color: white; }

     .btn-create {
        background: #0fb9b1;
        color: white;
        border: none;
        padding: 10px 22px;
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
            <h2 class="page-title">Manage Tour Itineraries</h2>
            <p class="text-muted small">Organize day-by-day plans for your packages</p>
        </div>
        <a href="add_itinerary.php" class="btn-create">+ Add New Day Plan</a>
    </div>

    <div class="table-container">
        <table class="table">
            <thead>
                <tr>
                    <th>Package Name</th>
                    <th>Day / Night</th>
                    <th>Full Plan Details</th>
                    <th class="text-center">Manage</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = mysqli_fetch_assoc($itineraries)) { ?>
                    <tr>
                        <td><span class="package-badge"><?= htmlspecialchars($row['package_title']) ?></span></td>
                        <td><span class="day-text"><?= htmlspecialchars($row['day_or_night']) ?></span></td>
                        <td><div class="plan-text"><?= nl2br(htmlspecialchars($row['plan'])) ?></div></td>
                        <td>
                            <div class="action-btns justify-content-center">
                                <a href="edit_itinerary.php?id=<?= $row['id'] ?>&pid=<?= $row['package_id'] ?>" 
                                   class="btn-action btn-edit" title="Edit Plan">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                                <a href="view_itinerary.php?delete_id=<?= $row['id'] ?>" 
                                   onclick="return confirm('Delete this itinerary day?');"
                                   class="btn-action btn-delete" title="Delete Plan">
                                    <i class="bi bi-trash3-fill"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                <?php } ?>
                <?php if(mysqli_num_rows($itineraries) == 0) echo "<tr><td colspan='4' class='text-center py-5 text-muted'>No itinerary plans found.</td></tr>"; ?>
            </tbody>
        </table>
    </div>
</div>

<script src="../bootstrap-5.3.7-dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>



