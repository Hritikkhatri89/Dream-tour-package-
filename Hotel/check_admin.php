<?php
include("db.php");
$query = mysqli_query($conn, "SELECT * FROM admin");
if (!$query) { echo "Table 'admin' does not exist or error: " . mysqli_error($conn); }
else {
    while($row = mysqli_fetch_assoc($query)) {
        print_r($row);
    }
}
?>
