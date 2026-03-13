<?php
include "db.php";
$q = mysqli_query($conn, "SELECT id, package_name FROM bookings ORDER BY id DESC LIMIT 2");
while($row = mysqli_fetch_assoc($q)) {
    echo "ID: " . $row['id'] . "\nPKG RAW:\n" . substr($row['package_name'], 0, 300) . "\n----------------\n";
}
?>
