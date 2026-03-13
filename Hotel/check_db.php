<?php
include "db.php";
$q = mysqli_query($conn, "DESCRIBE bookings");
while($r = mysqli_fetch_assoc($q)) {
    print_r($r);
}
?>
