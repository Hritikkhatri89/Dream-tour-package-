<?php
include("db.php");

$pid = isset($_GET['pid']) ? intval($_GET['pid']) : 0;
if ($pid == 0) {
    echo "Please select a package first!";
    exit;
}

// Fetch package name
$pkg = mysqli_query($conn, "SELECT title FROM packages WHERE id='$pid'");
$package_name = mysqli_fetch_assoc($pkg)['title'] ?? 'Unknown Package';

// Fetch hotels for this package
$hotels = mysqli_query($conn, "SELECT * FROM hotels WHERE package_id='$pid'");

echo "<h3>Hotels for Package: $package_name</h3>";
echo "<ul>";
while($h = mysqli_fetch_assoc($hotels)){
    echo "<li><a href='hotel_details.php?hid=".$h['id']."&pid=$pid'>".$h['name']."</a></li>";
}
echo "</ul>";
