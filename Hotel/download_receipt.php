<?php
session_start();
include("db.php");

if (!isset($_GET['booking_id'])) {
    die("Invalid Booking ID");
}

$bid = intval($_GET['booking_id']);

$booking = mysqli_fetch_assoc($sql);

if (!$booking) die("Booking not found");

// Fetch user name
$user_id = $booking['user_id'];
$user_sql = mysqli_query($conn, "SELECT name FROM users WHERE id='$user_id'");
$user = mysqli_fetch_assoc($user_sql);
$user_name = $user ? $user['name'] : 'Guest';
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Booking Receipt - <?php echo $booking['package_name']; ?></title>
<link href="bootstrap-5.3.7-dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
<style>
body { background: #f0f4f8; }
.navbar-custom { background: #2b3a55; color:white; padding:1rem; border-radius:0 0 20px 20px; text-align:center; }
.navbar-custom .brand-logo { height:70px; border-radius:50%; object-fit:cover; margin-bottom:5px; }
.navbar-custom .brand-text { font-size: 1.5rem; font-weight:700; }
.navbar-custom .brand-subtext { font-size: 0.9rem; color:#f1f1f1; }
.receipt-box { max-width:650px; margin:30px auto; background:#fff; padding:30px; border-radius:12px; border:1px solid #dee2e6; box-shadow:0 10px 25px rgba(0,0,0,0.1);}
.receipt-box h3 { font-family:'Segoe UI', sans-serif; margin-bottom:20px;}
.receipt-row { padding:8px 0; border-bottom:1px dashed #b0bec5;}
.receipt-row:last-child { border-bottom:none;}
@media print {
    @page { margin:0; }
    body { margin:0; }
    .no-print { display:none !important; }
}
@media print {

    /* Hide Header */
    .navbar-custom {
        display: none !important;
        visibility: hidden !important;
    }

    /* Hide Footer buttons */
    .no-print {
        display: none !important;
        visibility: hidden !important;
    }

    /* Remove page margins */
    @page {
        margin: 0;
    }

    body {
        margin: 0;
        padding: 0;
        background: white;
    }

    /* Expand receipt full page */
    .receipt-box {
        box-shadow: none !important;
        border: none !important;
        width: 100% !important;
        max-width: 100% !important;
        padding: 20px !important;
        margin: 0 !important;
    }
}

</style>
</head>
<body>

<!-- Navbar -->
<div class="navbar-custom">
    <img src="img/logo.png" class="brand-logo" alt="Logo">
    <div class="brand-text">Dream Tour & Travel Management System</div>
    <div class="brand-subtext">123, MG Road, New Delhi | +91 9876543210</div>
</div>

<!-- Receipt Content -->
<div class="receipt-box">
    <h3 class="text-center text-primary"><i class="bi bi-receipt"></i> Booking Receipt</h3>

    <div class="receipt-row"><strong>Booking ID:</strong> <?php echo $booking['id']; ?></div>
    <div class="receipt-row"><strong>User Name:</strong> <?php echo $user_name; ?></div>
    <div class="receipt-row"><strong>Package:</strong> <?php echo $booking['package_name']; ?></div>
    <div class="receipt-row"><strong>Hotel:</strong> <?php echo $booking['hotel_name']; ?> (<?php echo $booking['hotel_star']; ?>⭐)</div>
    <div class="receipt-row"><strong>Check-in:</strong> <?php echo $booking['checkin']; ?></div>
    <div class="receipt-row"><strong>Check-out:</strong> <?php echo $booking['checkout']; ?></div>
    <div class="receipt-row"><strong>Adults:</strong> <?php echo $booking['adults']; ?></div>
    <div class="receipt-row"><strong>Children:</strong> <?php echo $booking['children']; ?></div>
    <div class="receipt-row"><strong>Total Price:</strong> ₹<?php echo $booking['price']; ?></div>

    <div class="text-center mt-4">
        <button onclick="window.print()" class="btn btn-primary btn-custom no-print"><i class="bi bi-printer-fill"></i> Print / Download PDF</button>
        <a href="index.php" class="btn btn-outline-secondary btn-custom no-print"><i class="bi bi-house-fill"></i> Back to Home</a>
    </div>
</div>

</body>
</html>
