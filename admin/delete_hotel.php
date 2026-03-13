<?php
include("../db.php");

// ✅ Check id valid hai ya nahi
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    //  Delete images from folder
    $res = mysqli_query($conn, "SELECT images FROM hotels WHERE id=$id");
    $row = mysqli_fetch_assoc($res);
    if (!empty($row['images'])) {
        $imgs = explode(",", $row['images']);
        foreach ($imgs as $img) {
            $path = "../Hotel/" . trim($img);
            if (file_exists($path)) {
                unlink($path); // delete file
            }
        }
    }

    // ✅ Delete record from database
    mysqli_query($conn, "DELETE FROM hotels WHERE id=$id");

    echo "<script>
        alert('🗑️ Hotel deleted successfully!');
        window.location='view_hotel.php';
    </script>";
} else {
    echo "<script>
        alert('❌ Invalid request!');
        window.location='view_hotel.php';
    </script>";
}
?>

