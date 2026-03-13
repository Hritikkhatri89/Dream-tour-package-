<?php
include "db.php";
$q = mysqli_query($conn, "SELECT * FROM bookings LIMIT 1");
if ($q) {
    print_r(mysqli_fetch_assoc($q));
} else {
    echo mysqli_error($conn);
}
?>
