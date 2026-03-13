<?php
include "db.php";
$q = mysqli_query($conn, "SELECT SUM(price) as total_income FROM bookings"); // total income
$row = mysqli_fetch_assoc($q);
echo "Total Income: " . $row['total_income'] . "\n";

$q2 = mysqli_query($conn, "SELECT SUM(price) as this_year FROM bookings WHERE YEAR(booked_on) = YEAR(CURDATE())");
if($q2) {
    $row2 = mysqli_fetch_assoc($q2);
    echo "This Year: " . $row2['this_year'] . "\n";
} else {
    echo mysqli_error($conn);
}
?>
