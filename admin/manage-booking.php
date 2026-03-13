<?php
include("../db.php");
session_start();


if (isset($_GET['confirm'])) {
    $id = intval($_GET['confirm']);
    mysqli_query($conn, "UPDATE bookings SET status='Confirm' WHERE id=$id");
    header("Location: manage-booking.php");
    exit;
}

if (isset($_GET['cancel'])) {
    $id = intval($_GET['cancel']);
    mysqli_query($conn, "UPDATE bookings SET status='Cancelled' WHERE id=$id");
    header("Location: manage-booking.php");
    exit;
}

if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    mysqli_query($conn, "DELETE FROM bookings WHERE id=$id");
    header("Location: manage-booking.php");
    exit;
}

$query = "
SELECT bookings.*, users.name AS user_name 
FROM bookings 
LEFT JOIN users ON bookings.user_id = users.id
ORDER BY bookings.id DESC
";

$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Manage Bookings</title>

  <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
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
     .page-title { font-weight: 700; color: #1e293b; margin-bottom: 30px; }

     .table-container {
        background: white;
        padding: 30px;
        border-radius: 20px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 10px 30px rgba(0,0,0,0.03);
        overflow-x: auto;
     }
     .table { color: #1e293b; border-color: #f1f5f9; font-size: 0.85rem; }
     .table thead { background: #f8fafc; color: #64748b; border-color: transparent; }
     .table thead th { border: none; padding: 15px; font-weight: 500; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 1px; }
     .table tbody td { padding: 12px 15px; border-color: #f1f5f9; vertical-align: middle; }
     .table tbody tr:hover { background: #fcfdfe; }

     .badge {
        padding: 6px 12px;
        border-radius: 8px;
        font-weight: 500;
        font-size: 0.7rem;
     }
     .badge-pending { background: #fff7ed; color: #f59e0b; border: 1px solid #ffedd5; }
     .badge-confirmed { background: #f0fdf4; color: #10b981; border: 1px solid #dcfce7; }
     .badge-cancelled { background: #fef2f2; color: #ef4444; border: 1px solid #fee2e2; }

     .btn-action {
        padding: 6px 10px;
        border-radius: 8px;
        font-size: 0.75rem;
        font-weight: 500;
        transition: all 0.3s ease;
        margin: 2px;
        text-decoration: none;
        display: inline-block;
     }
     .btn-confirm { background: #10b981; color: white; }
     .btn-cancel { background: #f59e0b; color: white; }
     .btn-delete { background: white; color: #ef4444; border: 1px solid #fee2e2; }
     .btn-action:hover { transform: translateY(-2px); box-shadow: 0 4px 8px rgba(0,0,0,0.1); }

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
 <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
<!-- Main Page -->
<div class="main">
    <a href="dashboard.php" class="btn-back">
        <i class="bi bi-arrow-left me-2"></i> Back to Dashboard
    </a>
    <h3 class="page-title">📅 Manage Bookings</h3>

    <div class="table-container">
    <table class="table text-center align-middle">
        <thead>
            <tr>
                <th>ID</th>
                <th>Guest</th>
                <th>Pkg</th>
                <th>Hotel</th>
                <th>Image</th>
                <th>Rms</th>
                <th>Adults / Children</th>
                <th>Price</th>
                <th>Dates</th>
                <th>Status</th>
                <th width="180px">Action</th>
            </tr>
        </thead>

        <tbody>
        <?php while ($row = mysqli_fetch_assoc($result)) { ?>

            <?php 
                $imgs = explode(",", $row['image']);
                $firstImage = $imgs[0];
            ?>

            <tr>
                <td class="text-muted fw-bold"><?= $row['id'] ?></td>
                <td><strong class="text-dark"><?= $row['user_name'] ?></strong></td>
                <td><span class="badge bg-light text-dark border">ID: <?= $row['package_id'] ?></span></td>
                <td><div class="small fw-bold text-primary"><?= $row['hotel_name'] ?></div></td>

                <td>
                    <img src="../Hotel/<?= $firstImage ?>" 
                         width="60" height="45" 
                         style="object-fit:cover;border-radius:8px; border: 1px solid rgba(255,255,255,0.1);">
                </td>

                <td><span class="fw-bold"><?= $row['rooms'] ?></span></td>

                <td>
                    <div class="small">Adults: <?= $row['adults'] ?></div>
                    <div class="small">Children: <?= $row['children'] ?></div>
                </td>
                
                <td><span class="text-success fw-bold">₹<?= number_format($row['price']) ?></span></td>
                
                <td>
                    <div style="font-size: 0.75rem;">In: <?= $row['checkin'] ?></div>
                    <div style="font-size: 0.75rem;">Out: <?= $row['checkout'] ?></div>
                </td>

                <td>
                    <?php if ($row['status'] == 'Pending') { ?>
                        <span class="badge badge-pending">Pending</span>
                    <?php } elseif ($row['status'] == 'Confirm' || $row['status'] == 'Paid') { ?>
                        <span class="badge badge-confirmed">Confirmed</span>
                    <?php } else { ?>
                        <span class="badge badge-cancelled">Cancelled</span>
                    <?php } ?>
                </td>

                <td>
                    <div class="d-flex flex-wrap justify-content-center">
                        <?php if ($row['status'] == 'Pending') { ?>
                            <a href="?confirm=<?= $row['id'] ?>" class="btn-action btn-confirm">Confirm</a>
                            <a href="?cancel=<?= $row['id'] ?>" class="btn-action btn-cancel">Cancel</a>
                        <?php } ?>

                        <a href="?delete=<?= $row['id'] ?>" 
                           class="btn-action btn-delete"
                           onclick="return confirm('Delete booking?');">
                           <i class="bi bi-trash-fill"></i>
                        </a>
                    </div>
                </td>
            </tr>

        <?php } ?>
        </tbody>
    </table>
    </div>
</div>

</body>
</html>



