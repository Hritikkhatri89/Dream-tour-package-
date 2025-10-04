<?php
session_start();
include("db.php");

if (!isset($_SESSION['uid'])) {
    header("Location: login.php");
    exit;
}

// Package ka detail
if(isset($_GET['pid'])){
    $pid = intval($_GET['pid']);
    $sql = mysqli_query($conn, "SELECT * FROM packages WHERE id=$pid");
    $package = mysqli_fetch_assoc($sql);
}

// Booking Save
if(isset($_POST['confirm_booking'])){
    $uid = $_SESSION['uid'];
    $adults = $_POST['adults'];
    $children = $_POST['children'];
    $hotel = $_POST['hotel'];
    $checkin = $_POST['checkin'];
    $checkout = $_POST['checkout'];
    $flight = "Air India Express"; // Fixed

    $insert = "INSERT INTO bookings (user_id, package_id, adults, children, hotel, checkin, checkout, flight_name, status)
               VALUES ('$uid','$pid','$adults','$children','$hotel','$checkin','$checkout','$flight','Confirm')";
    if(mysqli_query($conn, $insert)){
        echo "<script>alert('Booking Confirmed!'); window.location='index.php';</script>";
    } else {
        echo "<script>alert('Error in Booking!');</script>";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Book Tour Package</title>
     <link href="bootstrap-5.3.7-dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-4">
    <div class="card shadow-lg p-3 mb-5 bg-white rounded">
        <div class="row g-0">
            <!-- Image -->
            <div class="col-md-5">
                <img src="<?php echo $package['image']; ?>" class="img-fluid rounded-start" alt="Tour Image">
            </div>

            <!-- Package Detail -->
            <div class="col-md-7 p-4">
                <h3><?php echo $package['name']; ?></h3>
                <h5 class="text-success">Price: ₹<?php echo $package['price']; ?></h5>

                <form method="POST">
                    <!-- Adults / Child -->
                    <div class="row mb-3">
                        <div class="col">
                            <label>Adults</label>
                            <input type="number" class="form-control" name="adults" min="1" required>
                        </div>
                        <div class="col">
                            <label>Children</label>
                            <input type="number" class="form-control" name="children" min="0" required>
                        </div>
                    </div>

                    <!-- Hotel Selection -->
                    <div class="mb-3">
                        <label>Hotel</label>
                        <select class="form-select" name="hotel" required>
                            <option value="Hotel Sunshine">Hotel Sunshine</option>
                            <option value="Hotel Paradise">Hotel Paradise</option>
                            <option value="Hotel Royal">Hotel Royal</option>
                            <option value="Hotel SeaView">Hotel SeaView</option>
                        </select>
                    </div>

                    <!-- Check-in / Check-out -->
                    <div class="row mb-3">
                        <div class="col">
                            <label>Check-In</label>
                            <input type="date" class="form-control" name="checkin" required>
                        </div>
                        <div class="col">
                            <label>Check-Out</label>
                            <input type="date" class="form-control" name="checkout" required>
                        </div>
                    </div>

                    <!-- Flight -->
                    <div class="mb-3">
                        <label>Flight Name (Fixed)</label>
                        <input type="text" class="form-control" value="Air India Express" readonly>
                    </div>

                    <!-- Pay Button -->
                    <button type="submit" name="confirm_booking" class="btn btn-primary w-100">Pay & Book</button>
                </form>
            </div>
        </div>
    </div>
</div>

</body>
</html>
