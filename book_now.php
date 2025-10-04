<?php
session_start();
include("db.php");

if (!isset($_SESSION['uid'])) {
    header("Location: login.php");
    exit;
}

if (isset($_GET['pid'])) {
    $pid = intval($_GET['pid']);
    $sql = mysqli_query($conn, "SELECT * FROM packages WHERE id=$pid");
    $package = mysqli_fetch_assoc($sql);
}
?>

<html>
<head>
<title>Book Tour Package</title>
<link href="bootstrap-5.3.7-dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body {
    background: #f4f6f9;
}
.booking-card {
    border: none;
    border-radius: 20px;
    padding: 25px;
    margin-bottom: 20px;
    background: #fff;
    max-width: 900px;
    margin: auto;
    box-shadow: 0 6px 20px rgba(0,0,0,0.12);
    transition: 0.3s;
}
.booking-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 10px 28px rgba(0,0,0,0.18);
}
.package-img {
    width: 100%;
    height: 200px;
    object-fit: cover;
    border-radius: 15px;
}
.form-control.sm-input {
    height: 38px;
    font-size: 14px; 
    padding: 5px 10px;
    border-radius: 10px;
}
.hotel-btn {
    border-radius: 30px;
    padding: 8px 20px;
    margin: 4px;
    transition: 0.3s;
    font-weight: 500;
}
.hotel-btn:hover {
    transform: scale(1.05);
}
.hotel-3 { border-color:#17a2b8; color:#17a2b8; }
.hotel-3:hover { background:#17a2b8; color:#fff; }
.hotel-4 { border-color:#28a745; color:#28a745; }
.hotel-4:hover { background:#28a745; color:#fff; }
.hotel-5 { border-color:#ffc107; color:#ffc107; }
.hotel-5:hover { background:#ffc107; color:#fff; }
</style>
</head>
<body>
<div class="container py-5">

<div class="booking-card">
    <div class="row g-4 align-items-center">
        
        <!-- Left Side -->
        <div class="col-md-5 text-center">
            <img src="img/<?php echo $package['image']; ?>" class="package-img mb-3">
            <h5 class="fw-bold"><?php echo $package['title']; ?></h5>
            <h6 class="text-success mt-2"><b>Price:</b> ₹<?php echo $package['price']; ?></h6>
        </div>

        <!-- Right Side -->
        <div class="col-md-7">
            <div class="row mb-3">
                <!-- Adults -->
                <div class="col-6">
                    <label class="form-label">Adults</label>
                    <input type="text" class="form-control sm-input" min="1" value="1">
                </div>
                <!-- Children -->
                <div class="col-6">
                    <label class="form-label">Children</label>
                    <input type="text" class="form-control sm-input" min="0" value="0">
                </div>
            </div>

            <!-- Hotel Section -->
            <h6 class="mt-3"> Select Hotel:</h6>
            <a href="3star_hotels.php" class="btn btn-outline-info hotel-btn hotel-3">3 Star</a>
            <a href="4star_hotels.php" class="btn btn-outline-success hotel-btn hotel-4">4 Star</a>
            <a href="5star_hotels.php" class="btn btn-outline-warning hotel-btn hotel-5">5 Star</a>
        </div>
    </div>
</div>

</div>
</body>
</html>
