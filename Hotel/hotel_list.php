<?php
session_start();
include("db.php");

if (!isset($_SESSION['uid'])) { header("Location: login.php"); exit; }

if (!isset($_GET['pid']) || !isset($_GET['star'])) die("Invalid request");

$pid = intval($_GET['pid']);
$star = intval($_GET['star']);

// get tour/package
$tourSql = mysqli_query($conn, "SELECT * FROM packages WHERE id=$pid");
$tour = mysqli_fetch_assoc($tourSql);
if (!$tour) die("Tour not found");

// get hotels
$hotelSql = mysqli_query($conn, "SELECT * FROM hotels WHERE star_rating=$star");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Select Hotel</title>
<link href="bootstrap-5.3.7-dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container py-5">
<h3 class="text-center mb-4">Select Hotel for <?php echo $tour['title']; ?></h3>
<div class="row">
<?php while($hotel = mysqli_fetch_assoc($hotelSql)) { 
$hotelImg = explode(',',$hotel['images'])[0];
?>
<div class="col-md-4 mb-4">
    <div class="card shadow-sm">
        <img src="Hotel/<?php echo $hotelImg; ?>" class="card-img-top" height="200" style="object-fit:cover;">
        <div class="card-body text-center">
            <h5><?php echo $hotel['name']; ?></h5>
            <p>₹<?php echo $hotel['price']; ?> per night</p>
            <a href="hotel_detail.php?pid=<?php echo $pid; ?>&hid=<?php echo $hotel['id']; ?>" class="btn btn-success">Select Hotel</a>
        </div>
    </div>
</div>
<?php } ?>
</div>
</div>
</body>
</html>
