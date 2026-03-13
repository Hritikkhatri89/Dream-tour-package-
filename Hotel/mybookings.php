<?php
session_start();
include("db.php");

if (!isset($_SESSION['uid'])) {
    header("Location: ../login.php");
    exit;
}
$logged_user_name = '';
$u_q = mysqli_query($conn, "SELECT name FROM users WHERE id='".(int)$_SESSION['uid']."'");
if($u_q && $u_row = mysqli_fetch_assoc($u_q)) $logged_user_name = $u_row['name'];

$uid = $_SESSION['uid'];

$sql = "
    SELECT b.*, 
           p.title AS package_name
    FROM bookings b
    LEFT JOIN packages p ON b.package_id = p.id
    WHERE b.user_id='$uid'
    ORDER BY b.id DESC
";

$result = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html>
<head>
<title>My Bookings</title>
<link href="bootstrap-5.3.7-dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="instyle.css">

<style>
body { background:#f5f6fa; font-family: Poppins; }
.container-box { max-width: 700px; margin: 30px auto; }
.card-box {
    background:white;
    border-radius:12px;
    box-shadow:0 2px 6px rgba(0,0,0,0.08);
    margin-bottom:15px;
    padding:15px;
    font-size:14px;
}
.card-box h4 {
    font-size:16px;
    margin-bottom:8px;
}
.card-box p {
    margin:3px 0;
}
.status {
    padding:2px 8px;
    border-radius:1px;
    color:black;
    font-weight:500;
    font-size:12px;
}
.paid { background:#28a745; }
.confirmed { background:#007bff; }
.cancelled { background:#dc3545; }
</style>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
<script src="bootstrap-5.3.7-dist/js/bootstrap.bundle.min.js"></script>
</head>

<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-custom py-3">
  <div class="container">
    
    <!-- Back Button -->
    <button class="back-btn me-3" onclick="goBack()">
      <i class="bi bi-arrow-left me-1"></i> Back
    </button>
      
    <!-- Brand -->
    <a class="navbar-brand d-flex align-items-center" href="index.php">
      <img src="img/logo.png" alt="Logo">
      <span class="fs-4">Dream Tour</span>
    </a>

    <!-- Mobile Button -->
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent">
      <span class="navbar-toggler-icon"></span>
    </button>

    <!-- CENTER NAVIGATION -->
    <div class="collapse navbar-collapse" id="navbarContent">
      <ul class="navbar-nav mx-auto">
        <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
        <li class="nav-item"><a class="nav-link" href="about.php">About</a></li>
        <li class="nav-item"><a class="nav-link" href="tourpackage.php">Destination</a></li>
        <li class="nav-item"><a class="nav-link" href="contact.php">Contact Us</a></li>
      </ul>

      <!-- RIGHT LOGIN -->
      <div class="d-flex align-items-center">
        <?php if(isset($_SESSION['uid'])): ?>
          <div class="dropdown">
            <a class="nav-link dropdown-toggle fw-bold active" href="#" data-bs-toggle="dropdown" style="color:#01c3c3 !important;">
                <i class="bi bi-person-circle fs-5"></i> <?php echo htmlspecialchars($logged_user_name ?: 'My Account'); ?>
            </a>
            <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                <li><a class="dropdown-item active" href="mybookings.php">My Bookings</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item text-danger" href="logout.php">Logout</a></li>
            </ul>
          </div>
        <?php else: ?>
          <a href="login.php" class="btn btn-login-premium">
             <i class="bi bi-person-circle fs-5"></i> Log In
          </a>
        <?php endif; ?>
      </div>
    </div>

  </div>
</nav>

<div class="container-box">
    <h2 class="text-center mb-4" style="font-size:22px;">📜 My Bookings</h2>

    <?php if (mysqli_num_rows($result) == 0): ?>
        <p class="text-center text-muted">No Bookings Found.</p>
    <?php endif; ?>

    <?php while ($row = mysqli_fetch_assoc($result)): ?>
    
    <?php 
        $statusClass = strtolower($row['status']);
        $clean_price = preg_replace('/[^0-9.]/', '', $row['price']);
        $amount_paise = (int)(round(floatval($clean_price) * 100));
    ?>

    <div class="card-box">

        <h4 class="fw-bold text-primary"><?php echo $row['package_name']; ?></h4>

        <?php if (!empty($row['hotel_name'])): ?>
        <p><b>Hotel:</b> <?php echo $row['hotel_name']; ?> (<?php echo $row['hotel_star']; ?>⭐)</p>
        <?php endif; ?>

        <?php if (!empty($row['checkin']) && !empty($row['checkout'])): ?>
            <p><b>Check-in:</b> <?= $row['checkin']; ?> | <b>Check-out:</b> <?= $row['checkout']; ?></p>
        <?php endif; ?>

        <p><b>Adults:</b> <?= $row['adults']; ?> | <b>Children:</b> <?= $row['children']; ?></p>

        <p><b>Total Price:</b> <span class="text-success fw-bold">₹<?= number_format($row['price']); ?></span></p>

        <p><b>Status:</b> 
            <span class="status <?= $statusClass; ?>"><?= ucfirst($row['status']); ?></span>
            <?php if(strtolower($row['status']) == 'pending'): ?>
                <button 
                    onclick="payNow(<?= $row['id']; ?>, <?= $amount_paise; ?>, '<?= addslashes($row['package_name']); ?>')" 
                    class="btn btn-sm btn-success ms-2 py-0 px-2 d-inline-flex align-items-center gap-1" 
                    style="font-size:11px;">
                    <img src="https://razorpay.com/favicon.png" height="12"> Pay Now
                </button>
            <?php endif; ?>
        </p>

        <div class="d-flex gap-2 mt-2">
            <?php if(strtolower($row['status']) == 'paid' || strtolower($row['status']) == 'confirmed' || strtolower($row['status']) == 'confirm'): ?>
            <a href="booking_summary.php?bid=<?= $row['id']; ?>&print=1" class="btn btn-sm btn-outline-success" style="border-radius:20px; font-size:12px;" onclick="event.preventDefault(); var w=window.open(this.href,'_blank'); setTimeout(function(){w.print();},1000);">
                <i class="bi bi-download"></i> Download Receipt
            </a>
            <?php endif; ?>
        </div>

    </div>

    <?php endwhile; ?>

</div>

<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>
function payNow(bid, amount_paise, packageName) {
    if (typeof Razorpay === 'undefined') {
        alert("Razorpay script not loaded. Please check your internet connection and refresh.");
        return;
    }

    var options = {
        "key": "rzp_test_SJzRsPh5bku7ii", 
        "amount": amount_paise, 
        "currency": "INR",
        "name": "Dream Tour & Travel",
        "description": "Payment for " + packageName,
        "image": "img/logo.png",
        "handler": function (response){
            window.location.href = "verify_payment.php?payment_id=" + response.razorpay_payment_id + "&bid=" + bid;
        },
        "prefill": {
            "name": "<?= $logged_user_name; ?>",
            "email": "<?= $_SESSION['email'] ?? ''; ?>"
        },
        "theme": {
            "color": "#2b3a55"
        }
    };
    
    try {
        var rzp1 = new Razorpay(options);
        rzp1.open();
    } catch(err) {
        alert("Error: " + err.message);
    }
}

function goBack() {
  if (document.referrer !== "") {
    window.history.back();
  } else {
    window.location.href = "index.php";
  }
}
</script>
</body>
</html>
