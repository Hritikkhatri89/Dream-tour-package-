<?php
include "db.php";
$q = mysqli_query($conn, "SELECT id, package_name FROM bookings ORDER BY id DESC LIMIT 5");
while($row = mysqli_fetch_assoc($q)) {
    echo "ID: " . $row['id'] . "\nPKG: " . substr(strip_tags($row['package_name']), 0, 100) . "\n----------------\n";
}
?>
