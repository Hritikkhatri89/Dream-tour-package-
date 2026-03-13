<?php
session_start();
include("../db.php");

// Admin login check
if (!isset($_SESSION['admin_email'])) {
    header("Location: ../login.php");
    exit;
}

// Delete message if requested
if (isset($_GET['delete'])) {
    $delete_id = intval($_GET['delete']);
    mysqli_query($conn, "DELETE FROM get_in_touch WHERE id=$delete_id");
    header("Location: view-contact.php?msg=deleted");
    exit;
}

// Search functionality
$search = "";
if (isset($_GET['search'])) {
    $search = mysqli_real_escape_string($conn, $_GET['search']);
    $result = mysqli_query($conn, "SELECT * FROM get_in_touch 
                                   WHERE name LIKE '%$search%' 
                                   OR email LIKE '%$search%' 
                                   OR message LIKE '%$search%'
                                   ORDER BY id DESC");
} else {
    $result = mysqli_query($conn, "SELECT * FROM get_in_touch ORDER BY id DESC");
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Contact Messages</title>
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
            align-items: center; 
            margin-bottom: 35px; 
         }

         .search-container {
            flex: 1;
            max-width: 500px;
            position: relative;
         }
         .search-container input {
            padding: 12px 20px 12px 45px;
            border-radius: 15px;
            border: 1px solid #e2e8f0;
            background: white;
            width: 100%;
            transition: 0.3s;
         }
         .search-container i {
            position: absolute;
            left: 18px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
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

         .btn-delete {
            background: #fef2f2;
            color: #ef4444;
            border: 1px solid #fee2e2;
            padding: 8px 15px;
            border-radius: 10px;
            text-decoration: none;
            transition: 0.3s;
            font-weight: 500;
            font-size: 0.85rem;
         }
         .btn-delete:hover { background: #ef4444; color: white; }

         .alert { border-radius: 15px; font-weight: 500; margin-bottom: 25px; }
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
            <h2 class="page-title">Contact Messages</h2>
            <p class="text-muted small m-0">Inquiries and messages from website visitors</p>
        </div>
        
        <form method="GET" class="search-container d-flex gap-2">
            <div class="position-relative flex-grow-1">
                <i class="bi bi-search"></i>
                <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Search messages...">
            </div>
            <button type="submit" class="btn btn-dark rounded-pill px-4">Search</button>
            <?php if ($search): ?>
                <a href="view-contact.php" class="btn btn-outline-secondary rounded-pill px-3">Reset</a>
            <?php endif; ?>
        </form>
    </div>

    <?php if (isset($_GET['msg']) && $_GET['msg'] == 'deleted') { ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> Message deleted successfully!
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php } ?>

    <div class="table-container">
        <table class="table">
            <thead>
                <tr>
                    <th>From</th>
                    <th>Email</th>
                    <th>Message Details</th>
                    <th class="text-center">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (mysqli_num_rows($result) > 0) { ?>
                    <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                    <tr>
                        <td class="fw-bold"><?= htmlspecialchars($row['name']) ?></td>
                        <td><span class="text-primary"><?= htmlspecialchars($row['email']) ?></span></td>
                        <td><div class="text-muted small" style="max-width: 400px;"><?= nl2br(htmlspecialchars($row['message'])) ?></div></td>
                        <td class="text-center">
                            <a href="?delete=<?= $row['id'] ?>" class="btn-delete" 
                               onclick="return confirm('Delete this inquiry permanently?');">
                                <i class="bi bi-trash3-fill me-1"></i> Delete
                            </a>
                        </td>
                    </tr>
                    <?php } ?>
                <?php } else { ?>
                    <tr>
                        <td colspan="4" class="text-center py-5 text-muted italic">No inquiries found matching your search.</td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<script src="../bootstrap-5.3.7-dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>



