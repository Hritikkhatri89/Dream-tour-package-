<?php
session_start();
include("db.php");

if (!isset($_GET['bid'])) {
    die("Invalid Booking ID");
}

$bid = intval($_GET['bid']);
$sql = mysqli_query($conn, "SELECT * FROM bookings WHERE id='$bid'");
$booking = mysqli_fetch_assoc($sql);

if (!$booking) die("Booking not found");

$user_id = $booking['user_id'];
$user_sql = mysqli_query($conn, "SELECT name FROM users WHERE id='$user_id'");
$user = mysqli_fetch_assoc($user_sql);
$user_name = $user ? $user['name'] : 'Guest';
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Receipt - #<?php echo $bid; ?></title>
<link href="bootstrap-5.3.7-dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
<style>
    body { background: white; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
    .invoice-box {
        max-width: 800px;
        margin: auto;
        padding: 30px;
        border: 1px solid #eee;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.15);
        font-size: 16px;
        line-height: 24px;
        color: #555;
    }
    .invoice-header {
        background: #2b3a55;
        color: white;
        padding: 20px;
        border-radius: 10px;
        text-align: center;
        margin-bottom: 20px;
    }
    .invoice-header img { height: 60px; margin-bottom: 10px; }
    .bill-title { text-align: center; margin-bottom: 30px; color: #2b3a55; font-weight: bold; }
    .info-table { width: 100%; border-collapse: collapse; margin-top: 20px; }
    .info-table td { padding: 12px; border-bottom: 1px solid #eee; }
    .info-label { font-weight: bold; color: #333; width: 40%; }
    .info-value { text-align: right; }
    .total-row { font-size: 20px; font-weight: bold; color: #28a745; }
    
    @media print {
        .no-print { display: none !important; }
        .invoice-box { box-shadow: none; border: none; padding: 0; }
        @page { margin: 1cm; }
    }
</style>
</head>
<body onload="<?php if(isset($_GET['print'])) echo 'window.print()'; ?>">

<div class="container py-4 no-print">
    <button onclick="window.print()" class="btn btn-primary rounded-pill px-4">
        <i class="bi bi-printer"></i> Print Receipt
    </button>
    <button onclick="window.close()" class="btn btn-outline-secondary rounded-pill px-4 ms-2">
        Close Window
    </button>
</div>

<div class="invoice-box">
    <div class="invoice-header">
        <img src="img/logo.png" alt="Logo">
        <h3 class="m-0">Dream Tour & Travel Management</h3>
        <p class="m-0" style="font-size:13px;">33, Gujrat Gas Circle, Adajan | +91 8980052655</p>
    </div>

    <h4 class="bill-title">OFFICIAL BOOKING RECEIPT</h4>

    <table class="info-table">
        <tr>
            <td class="info-label">Booking ID:</td>
            <td class="info-value">#<?php echo $bid; ?></td>
        </tr>
        <tr>
            <td class="info-label">Customer Name:</td>
            <td class="info-value"><?php echo $user_name; ?></td>
        </tr>
        <tr>
            <td class="info-label">Tour Package:</td>
            <td class="info-value"><?php echo $booking['package_name']; ?></td>
        </tr>
        <tr>
            <td class="info-label">Hotel:</td>
            <td class="info-value"><?php echo $booking['hotel_name']; ?> (<?php echo $booking['hotel_star']; ?>⭐)</td>
        </tr>
        <tr>
            <td class="info-label">Check-in:</td>
            <td class="info-value"><?php echo $booking['checkin']; ?></td>
        </tr>
        <tr>
            <td class="info-label">Check-out:</td>
            <td class="info-value"><?php echo $booking['checkout']; ?></td>
        </tr>
        <tr>
            <td class="info-label">Guests:</td>
            <td class="info-value"><?php echo $booking['adults']; ?> Adults, <?php echo $booking['children']; ?> Children</td>
        </tr>
        <tr>
            <td class="info-label">Rooms:</td>
            <td class="info-value"><?php echo $booking['rooms']; ?></td>
        </tr>
        <tr class="total-row">
            <td class="info-label">Total Amount Paid:</td>
            <td class="info-value">₹<?php echo number_format($booking['price']); ?></td>
        </tr>
        <tr>
            <td class="info-label">Status:</td>
            <td class="info-value text-success font-weight-bold"><?php echo $booking['status']; ?></td>
        </tr>
        <tr>
            <td class="info-label">Booked On:</td>
            <td class="info-value"><?php echo $booking['booked_on']; ?></td>
        </tr>
    </table>

    <div class="text-center mt-5" style="font-size:12px; color:#888; border-top:1px dashed #ddd; padding-top:20px;">
        <p>This is a computer-generated receipt. No signature required.</p>
        <p>Thank you for traveling with Dream Tour & Travel!</p>
    </div>
</div>

</body>
</html>
