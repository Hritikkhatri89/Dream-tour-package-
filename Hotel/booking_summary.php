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
$user_email = $user ? ($user['email'] ?? '') : '';

// Razorpay Key Placeholder
$razorpay_key = "rzp_test_SJzRsPh5bku7ii"; 

// Ensure price is a clean number for Razorpay
$clean_price = preg_replace('/[^0-9.]/', '', $booking['price']);
$amount_paise = (int)(round(floatval($clean_price) * 100));

$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
$domainName = $_SERVER['HTTP_HOST'];
$logo_url = $protocol . $domainName . "/ttms_final1/img/logo.png";
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Booking Summary</title>
<link href="bootstrap-5.3.7-dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
<link rel="stylesheet" href="instyle.css">

<style>
body { background: #f0f4f8; }

/* Main Card */
.card-booking { 
    max-width: 700px;
    margin: 50px auto;
    border-radius: 20px; 
    padding: 20px; 
    background: linear-gradient(135deg, #ffffff, #e0f7fa); 
    box-shadow: 0 10px 25px rgba(0,0,0,0.1); 
}

/* Header Box */
.card-header-box { 
    text-align:center; 
    background: #2b3a55; 
    color:white; 
    padding: 20px; 
    border-radius: 15px;
    margin-bottom: 20px;
}
.card-header-box img { 
    height: 70px; 
    width: 70px; 
    border-radius:50%; 
    margin-bottom:10px; 
    object-fit:cover;
}
.card-header-box h3 { margin:0; font-size:1.5rem; font-weight:700; }
.card-header-box p { margin:2px 0; font-size:0.9rem; }

/* Info Boxes */
.info-row {
    display:flex;
    align-items:center;
    padding: 10px 15px;
    border: 1px solid #b0bec5;
    border-radius: 15px;
    margin-bottom: 10px;
    background:#ffffff;
}
.info-icon { width:25px; text-align:center; }
.info-label { flex:1; font-weight:600; }
.info-value { flex:1; text-align:right; }

/* Buttons */
.btn-custom { border-radius:50px; padding:10px 30px; font-weight:600; }

/* CLEAN PRINT – No Header, No Footer */
@media print {

    @page {
        margin: 0 !important; /* removes browser footer/header margins */
    }

    body {
        margin: 0 !important;
        padding: 0 !important;
        background: white !important;
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
    }

    .no-print {
        display:none !important;
    }

    .card-booking {
        margin: 0 !important;
        border-radius: 0 !important;
        box-shadow:none !important;
        width: 100% !important;
        max-width: 100% !important;
        padding: 40px !important;
    }
}
</style>
</head>

<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-custom py-3 no-print">
  <div class="container">
    
    <!-- Back Button -->
    <button class="back-btn me-3" onclick="goBack()">
      <i class="bi bi-arrow-left me-1"></i> Back
    </button>
      
    <!-- Brand -->
    <a class="navbar-brand d-flex align-items-center" href="index.php">
      <img src="img/logo.png" alt="Logo">
      <span class="fs-4">Dream Tour & Travel </span>
    </a>

    <!-- Mobile Button -->
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent">
      <span class="navbar-toggler-icon"></span>
    </button>

    <!-- RIGHT NAVIGATION -->
    <div class="collapse navbar-collapse" id="navbarContent">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
        <li class="nav-item"><a class="nav-link" href="about.php">About</a></li>
        <li class="nav-item"><a class="nav-link" href="tourpackage.php">Destination</a></li>
        <li class="nav-item"><a class="nav-link" href="contact.php">Contact Us</a></li>
      </ul>
    </div>

  </div>
</nav>

<script>
function goBack() {
  if (document.referrer !== "") {
    window.history.back();
  } else {
    window.location.href = "index.php";
  }
}
</script>


<div class="card card-booking">


    <!-- Header Box -->
    <div class="card-header-box">
        <img src="img/logo.png" alt="Logo">
        <h3>Dream Tour & Travel Management System</h3>
        <p> 33, Gujrat Gas Circle, Adajan | +91 8980052655</p>
    </div>

    <?php if(isset($_SESSION['payment_success'])): ?>
        <div class="alert alert-success text-center">
            🎉 Payment Successful! Your booking is now confirmed.
        </div>
        <?php unset($_SESSION['payment_success']); ?>
    <?php endif; ?>

    <h3 class="text-center <?php echo ($booking['status'] == 'Paid' || $booking['status'] == 'Confirmed') ? 'text-success' : 'text-warning'; ?> mb-4">
        <?php if($booking['status'] == 'Paid' || $booking['status'] == 'Confirmed'): ?>
            <i class="bi bi-check-circle-fill"></i> Booking Confirmed!
        <?php else: ?>
            <i class="bi bi-hourglass-split"></i> Payment Pending
        <?php endif; ?>
    </h3>

    <!-- Booking Info -->
    <div class="info-row">
        <div class="info-icon"><i class="bi bi-person-circle text-primary"></i></div>
        <div class="info-label">User Name:</div>
        <div class="info-value"><?php echo $user_name; ?></div>
    </div>

    <div class="info-row">
        <div class="info-icon"><i class="bi bi-gift-fill text-warning"></i></div>
        <div class="info-label">Package:</div>
        <div class="info-value"><?php echo $booking['package_name']; ?></div>
    </div>

    <div class="info-row">
        <div class="info-icon"><i class="bi bi-building text-danger"></i></div>
        <div class="info-label">Hotel:</div>
        <div class="info-value">
            <?php echo $booking['hotel_name']; ?> (<?php echo $booking['hotel_star']; ?>⭐)
        </div>
    </div>

    <div class="info-row">
        <div class="info-icon"><i class="bi bi-calendar2-check-fill text-success"></i></div>
        <div class="info-label">Check-in:</div>
        <div class="info-value"><?php echo $booking['checkin']; ?></div>
    </div>

    <div class="info-row">
        <div class="info-icon"><i class="bi bi-calendar2-x-fill text-danger"></i></div>
        <div class="info-label">Check-out:</div>
        <div class="info-value"><?php echo $booking['checkout']; ?></div>
    </div>

    <div class="info-row">
        <div class="info-icon"><i class="bi bi-people-fill text-info"></i></div>
        <div class="info-label">Adults:</div>
        <div class="info-value"><?php echo $booking['adults']; ?></div>
    </div>

    <div class="info-row">
        <div class="info-icon"><i class="bi bi-person-fill text-secondary"></i></div>
        <div class="info-label">Children:</div>
        <div class="info-value"><?php echo $booking['children']; ?></div>
    </div>

    <div class="info-row">
        <div class="info-icon"><i class="bi bi-door-open-fill text-primary"></i></div>
        <div class="info-label">Rooms:</div>
        <div class="info-value"><?php echo $booking['rooms']; ?></div>
    </div>

    <div class="info-row">
        <div class="info-icon"><i class="bi bi-currency-rupee text-success"></i></div>
        <div class="info-label">Total Price:</div>
        <div class="info-value fw-bold text-success">₹<?php echo $booking['price']; ?></div>
    </div>

    <!-- Buttons -->
    <div class="text-center mt-4 d-flex flex-column align-items-center gap-3">
        
        <?php if($booking['status'] == 'Pending'): ?>
            <button id="pay-btn" class="btn btn-success btn-custom no-print py-3 px-5 shadow d-flex align-items-center gap-2">
                <img src="https://razorpay.com/favicon.png" height="20"> Pay Now (₹<?php echo $booking['price']; ?>)
            </button>
        <?php endif; ?>

        <div class="d-flex justify-content-center gap-3">
            <a href="index.php" class="btn btn-outline-secondary btn-custom no-print">
                <i class="bi bi-house-fill"></i> Back to Home
            </a>
        </div>
    </div>

</div>

<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>
document.getElementById('pay-btn').onclick = function(e) {
    e.preventDefault();
    
    var options = {
        "key": "<?php echo $razorpay_key; ?>", 
        "amount": "<?php echo $amount_paise; ?>", 
        "currency": "INR",
        "name": "Dream Tour & Travel",
        "description": "Booking for <?php echo $booking['package_name']; ?>",
        "image": "<?php echo $logo_url; ?>",
        "handler": function (response){
            window.location.href = "verify_payment.php?payment_id=" + response.razorpay_payment_id + "&bid=<?php echo $bid; ?>";
        },
        "prefill": {
            "name": "<?php echo $user_name; ?>",
            "email": "<?php echo $user_email; ?>"
        },
        "theme": {
            "color": "#2b3a55"
        }
    };
    
    try {
        var rzp1 = new Razorpay(options);
        rzp1.on('payment.failed', function (response) {
            alert("Payment could not be completed.\nInternational cards are not supported. Please contact our support team for help.");
        });
        rzp1.open();
    } catch (err) {
        alert("Razorpay failed to load. Please check your internet connection.");
        console.error(err);
    }
}
</script>

<script>
function goBack() {
  if (document.referrer !== "") {
    window.history.back();
  } else {
    window.location.href = "index.php";
  }
}
</script>

<script src="bootstrap-5.3.7-dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
